<?php
/**
 * Class: Scheduler
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP;

class Scheduler {
	/** Create a cron job to expire events */
	private function schedule_event_expiry() {
		if ( ! wp_next_scheduled( 'expire_choctaw_event_posts' ) ) {
			wp_schedule_event( time(), 'hourly', 'expire_choctaw_event_posts' );
		}
		add_action( 'expire_choctaw_event_posts', array( $this, 'expire_choctaw_events' ), 100 );
	}
}