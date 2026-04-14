<?php
/**
 * Tests Scheduler class.
 *
 * @package ChoctawNation
 */

namespace ChoctawNation\Tests;

use ChoctawNation\Events\WP\Scheduler;
use ChoctawNation\Events\Jobs\Event_Handler;
use WP_UnitTestCase;

/**
 * Test Scheduler
 */
class Test_Scheduler extends WP_UnitTestCase {
	/**
	 * The Event_Handler mock used for testing
	 *
	 * @var Event_Handler $handler
	 */
	private Event_Handler $handler;

	/**
	 * The Scheduler instance being tested
	 *
	 * @var Scheduler $scheduler
	 */
	private Scheduler $scheduler;
	/**
	 * The cron hook name used for scheduling event expiry
	 *
	 * @var string $hook
	 */
	private $hook = 'expire_choctaw_event_posts';

	public function set_up() {
		parent::set_up();
		$this->handler   = $this->getMockBuilder( Event_Handler::class )->disableOriginalConstructor()->getMock();
		$this->scheduler = new Scheduler( $this->handler );
		wp_clear_scheduled_hook( $this->hook );
	}

	public function tear_down() {
		wp_clear_scheduled_hook( $this->hook );
		parent::tear_down();
	}

	public function test_schedule_event_expiry_schedules_cron_and_adds_action() {
		$this->assertFalse( wp_next_scheduled( $this->hook ) );
		$this->scheduler->schedule_event_expiry();

		$first = wp_next_scheduled( $this->hook );
		$this->assertNotFalse( $first );
		$this->assertNotFalse( has_action( $this->hook, array( $this->handler, 'expire_choctaw_events' ) ) );

		// Calling again should not create a different scheduled timestamp
		$this->scheduler->schedule_event_expiry();
		$second = wp_next_scheduled( $this->hook );
		$this->assertSame( $first, $second );
	}
}