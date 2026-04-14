<?php
/**
 * Event Handler Class
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\Jobs;

use DateTime;
use DateTimeZone;

/**
 * Event Handler
 */
class Event_Handler {
	/**
	 * The timezone to use for date comparisons
	 *
	 * @var DateTimeZone $timezone
	 */
	private DateTimeZone $timezone;

	/**
	 * The custom post type slug for events
	 *
	 * @var string $cpt_slug
	 */
	private string $cpt_slug;

	/**
	 * Today's date for comparison
	 *
	 * @var DateTime $today
	 */
	private DateTime $today;
	/**
	 * Constructor
	 *
	 * @param string $slug The custom post type slug for events
	 */
	public function __construct( string $slug ) {
		$this->cpt_slug = $slug;
		$this->timezone = wp_timezone();
		$this->today    = new DateTime( 'now', $this->timezone );
	}

	/**
	 * Compares today to the ACF date/time fields and updates the post status to "draft" if the event is expired
	 */
	public function expire_choctaw_events() {
		$events = get_posts(
			array(
				'post_type'      => array( $this->cpt_slug ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post_status'    => 'publish',
			)
		);

		foreach ( $events as $event_id ) {
			if ( $this->get_is_event_expired( $event_id ) ) {
				$postdata = array(
					'ID'          => $event_id,
					'post_status' => 'draft',
				);
				wp_update_post( $postdata );
			}
		}
	}


	/**
	 * Get the event expiry date
	 *
	 * @param int $event_id the event ID
	 * @return bool
	 */
	private function get_is_event_expired( int $event_id ): bool {
		$is_all_day = get_field( 'is_all_day', $event_id );
		$start_date = get_field( 'start_date', $event_id );
		$start_time = get_field( 'start_time', $event_id );
		$end_date   = get_field( 'end_date', $event_id );
		$end_time   = get_field( 'end_time', $event_id );
		if ( $is_all_day ) {
			$default_end_time = ' 23:59:59';
			$end              = new DateTime( ( $end_date ?: $start_date ) . $default_end_time, $this->timezone ); // phpcs:ignore Universal.Operators.DisallowShortTernary.Found
			return $this->today > $end;
		}
		if ( empty( $end_date ) ) {
			$end_date = $start_date;
		}
		if ( empty( $end_time ) ) {
			$expiry = new DateTime( "{$end_date} {$start_time}", $this->timezone );
			$expiry->modify( '+1 hour' );
		} else {
			$expiry = new DateTime( "{$end_date} {$end_time}", $this->timezone );
		}
		return $this->today > $expiry;
	}
}