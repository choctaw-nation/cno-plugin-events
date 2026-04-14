<?php
/**
 * Class: Scheduler
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP;

use ChoctawNation\Events\Jobs\Event_Handler;

/**
 * Scheduler class to manage event scheduling and cron jobs
 */
class Scheduler {
	/**
	 * Event handler instance
	 *
	 * @var Event_Handler $event_handler
	 */
	private Event_Handler $event_handler;

	/**
	 * Cron key for expiring events
	 *
	 * @var string $cron_key
	 */
	private string $cron_key = 'expire_choctaw_event_posts';

	/**
	 * Constructor
	 *
	 * @param Event_Handler $event_handler The event handler instance
	 */
	public function __construct( Event_Handler $event_handler ) {
		$this->event_handler = $event_handler;
	}

	/** Create a cron job to expire events */
	public function schedule_event_expiry() {
		if ( ! wp_next_scheduled( $this->cron_key ) ) {
			wp_schedule_event( time(), 'hourly', $this->cron_key );
		}
		add_action( $this->cron_key, array( $this->event_handler, 'expire_choctaw_events' ) );
	}
}
