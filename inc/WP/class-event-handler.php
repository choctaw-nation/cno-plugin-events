<?php
/**
 * Event Handler Class
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP;

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
		_doing_it_wrong(
			__METHOD__,
			'REFACTOR AND UPDATE ME',
			'5.0.0'
		);

		// if ( $time_and_date['is_all_day'] ) {
		// if ( $time_and_date['end_date'] ) {
		// $expiry = new DateTime( $time_and_date['end_date'], $this->timezone );
		// } else {
		// $expiry = new \DateTime( $time_and_date['start_date'], $this->timezone );
		// $expiry->modify( '+1 day' );
		// }
		// } else {
		// if ( empty( $time_and_date['end_date'] ) ) {
		// $time_and_date['end_date'] = $time_and_date['start_date'];
		// }
		// if ( empty( $time_and_date['end_time'] ) ) {
		// $time_and_date['end_time'] = '11:59pm';
		// }
		// $expiry_datetime = $time_and_date['end_date'] . ( empty( $time_and_date['end_time'] ) ? '' : ' ' . $time_and_date['end_time'] );
		// $expiry          = new DateTime( $expiry_datetime, $this->timezone );
		// }
		return $expiry;
	}
}