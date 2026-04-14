<?php
/**
 * Tests Event_Handler expiry behavior.
 *
 * @package ChoctawNation
 */

namespace ChoctawNation\Tests;

use ChoctawNation\Events\Jobs\Event_Handler;
use WP_UnitTestCase;

class Test_Event_Handler extends WP_UnitTestCase {
	private string $cpt = 'event';
	private Event_Handler $handler;

	public function set_up() {
		parent::set_up();
		$this->handler = new Event_Handler( $this->cpt );
	}

	private function set_field( int $post_id, string $key, $value ) {
		if ( function_exists( 'update_field' ) ) {
			update_field( $key, $value, $post_id );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	public function test_expire_all_day_event_sets_post_to_draft() {
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => $this->cpt,
				'post_status' => 'publish',
			)
		);

		$this->set_field( $post_id, 'is_all_day', true );
		$this->set_field( $post_id, 'start_date', '2000-01-01' );
		$this->set_field( $post_id, 'end_date', '2000-01-02' );

		$this->handler->expire_choctaw_events();

		$this->assertSame( 'draft', get_post( $post_id )->post_status );
	}

	public function test_non_expired_event_remains_published() {
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => $this->cpt,
				'post_status' => 'publish',
			)
		);

		$this->set_field( $post_id, 'is_all_day', false );
		$this->set_field( $post_id, 'start_date', '2100-01-01' );
		$this->set_field( $post_id, 'end_date', '2100-01-02' );

		$this->handler->expire_choctaw_events();

		$this->assertSame( 'publish', get_post( $post_id )->post_status );
	}

	public function test_event_with_no_end_date_uses_start_date_and_expires() {
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => $this->cpt,
				'post_status' => 'publish',
			)
		);

		$this->set_field( $post_id, 'is_all_day', false );
		$this->set_field( $post_id, 'start_date', '2000-01-01' );
		$this->set_field( $post_id, 'end_date', '' );
		$this->set_field( $post_id, 'end_time', '' );

		$this->handler->expire_choctaw_events();

		$this->assertSame( 'draft', get_post( $post_id )->post_status );
	}
}