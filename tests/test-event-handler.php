<?php
/**
 * Tests Event_Handler expiry behavior.
 *
 * @package ChoctawNation
 */

namespace ChoctawNation\Tests;

use ChoctawNation\Events\Jobs\Event_Handler;
use DateTimeImmutable;
use WP_UnitTestCase;

/**
 * Test Event Handler
 */
class Test_Event_Handler extends WP_UnitTestCase {

	/**
	 * The custom post type slug for events
	 *
	 * @var string $cpt
	 */
	private string $cpt = 'event';

	/**
	 * The Event_Handler instance being tested
	 *
	 * @var Event_Handler $handler
	 */
	private Event_Handler $handler;

	/**
	 * Set up the test environment
	 */
	public function set_up() {
		parent::set_up();
		$this->handler = new Event_Handler( $this->cpt );
	}

	/**
	 * Helper method to set ACF fields for testing
	 *
	 * @param int    $post_id the post ID
	 * @param string $key the field key
	 * @param mixed  $value the value to set
	 */
	private function set_field( int $post_id, string $key, $value ) {
		if ( function_exists( 'update_field' ) ) {
			update_field( $key, $value, $post_id );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Parameterized test for various event expiry scenarios.
	 *
	 * @dataProvider data_events
	 *
	 * @param array  $fields          Associative ACF/meta fields to set on the post.
	 * @param string $expected_status Expected post_status after expiry run.
	 */
	public function test_expiry_behavior( array $fields, string $expected_status ) {
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => $this->cpt,
				'post_status' => 'publish',
			)
		);

		foreach ( $fields as $key => $value ) {
			$this->set_field( $post_id, $key, $value );
		}

		$this->handler->expire_choctaw_events();

		$this->assertSame( $expected_status, get_post( $post_id )->post_status );
	}

	/**
	 * Data provider with different event field combinations and expected outcomes.
	 *
	 * @return array[]
	 */
	public function data_events(): array {
		$now               = new DateTimeImmutable( 'now', wp_timezone() );
		$past_start_date   = $now->modify( '-2 day' )->format( 'Y-m-d' );
		$past_end_date     = $now->modify( '-1 day' )->format( 'Y-m-d' );
		$future_start_date = $now->modify( '+1 day' )->format( 'Y-m-d' );
		$future_end_date   = $now->modify( '+2 day' )->format( 'Y-m-d' );
		return array(
			'past all day event should expire'          => array(
				array(
					'is_all_day' => true,
					'start_date' => $past_start_date,
					'end_date'   => $past_end_date,
				),
				'draft',
			),
			'future all day event should be published'  => array(
				array(
					'is_all_day' => true,
					'start_date' => $future_start_date,
					'end_date'   => $future_end_date,
				),
				'publish',
			),
			'current all day event w/o time should be published' => array(
				array(
					'is_all_day' => true,
					'start_date' => $now->format( 'Y-m-d' ),
					'end_date'   => '',
				),
				'publish',
			),
			'future event should be published'          => array(
				array(
					'is_all_day' => false,
					'start_date' => $future_start_date,
					'end_date'   => $future_end_date,
				),
				'publish',
			),
			'past event without end date should expire' => array(
				array(
					'is_all_day' => false,
					'start_date' => $past_start_date,
					'end_date'   => '',
					'end_time'   => '',
				),
				'draft',
			),
			'past event with time < now should expire'  => array(
				array(
					'is_all_day' => false,
					'start_date' => $past_start_date,
					'end_date'   => $now->format( 'Y-m-d' ),
					'end_time'   => '00:00',
				),
				'draft',
			),
			'past event with time == now should expire' => array(
				array(
					'is_all_day' => false,
					'start_date' => $past_start_date,
					'end_date'   => $now->format( 'Y-m-d' ),
					'end_time'   => $now->format( 'H:i' ),
				),
				'draft',
			),
			'current event with no end time should expire if now < start time +1 hr' => array(
				array(
					'is_all_day' => false,
					'start_date' => $now->modify( '-2 hour' )->format( 'Y-m-d' ),
					'end_date'   => $now->format( 'Y-m-d' ),
					'end_time'   => '',
				),
				'draft',
			),
			'ongoing event that ends in the future should be published' => array(
				array(
					'is_all_day' => false,
					'start_date' => $past_start_date,
					'start_time' => $now->format( 'H:i' ),
					'end_date'   => $future_end_date,
				),
				'publish',
			),
		);
	}
}