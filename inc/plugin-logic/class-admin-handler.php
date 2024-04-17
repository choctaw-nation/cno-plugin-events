<?php
/**
 * The Admin Handler
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

use DateTime;
use DateTimeZone;

/** Load the Post Type Builder */
require_once __DIR__ . '/class-post-type-builder.php';

/** Handles the WP Hooks & Filters logic */
class Admin_Handler extends Post_Type_Builder {
	/** Handles the WordPress Admin Columns Hooks & Filters */
	protected function init() {
		$this->add_acf_date_columns();
		$this->make_acf_columns_sortable();
		$this->add_cpt_to_search_loop();
		$this->add_event_category_to_columns();
		$this->handle_yoast_metabox_appearance();
		$this->schedule_event_expiry();
	}

	/**
	 * Adds event category to columns.
	 *
	 * @return void
	 */
	private function add_event_category_to_columns() {
		add_filter( 'manage_posts_columns', array( $this, 'choctaw_events_admin_column' ) );
		add_filter( 'manage_custom_post_type_columns', array( $this, 'choctaw_events_admin_column' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'choctaw_events_admin_column_data' ), 10, 2 );
		add_action( 'manage_custom_post_type_custom_column', array( $this, 'choctaw_events_admin_column_data' ), 10, 2 );
	}

	/**
	 * Adds a new column to the admin events list table.
	 *
	 * @param array $columns The existing columns in the events list table.
	 * @return array The updated columns with the new 'Category' column.
	 */
	public function choctaw_events_admin_column( array $columns ) {
		$columns['choctaw-events'] = 'Category';
		return $columns;
	}

	/**
	 * Retrieves the data for the admin column of the Choctaw events custom post type.
	 *
	 * @param string $column The name of the column to retrieve data for.
	 * @param int    $post_id The ID of the post to retrieve data for.
	 */
	public function choctaw_events_admin_column_data( string $column, int $post_id ) {
		if ( 'choctaw-events' === $column ) {
			$terms = get_the_terms( $post_id, 'choctaw-events-category' );

			if ( $terms && ! is_wp_error( $terms ) ) {
				$term_names = array();
				foreach ( $terms as $term ) {
					$term_names[] = "<a href='edit.php?post_type=choctaw-events&{$term->taxonomy}={$term->slug}'>{$term->name}</a>";
				}
				echo implode( ', ', $term_names );
			} else {
				echo '';
			}
		}
	}

	/** Adds ACF Start & End Date Columns */
	private function add_acf_date_columns() {
		add_filter( 'manage_edit-choctaw-events_columns', array( $this, 'add_acf_to_columns_array' ) );
		add_action( 'manage_choctaw-events_posts_custom_column', array( $this, 'echo_acf_value_to_column' ), 10, 2 );
	}

	/**
	 * Adds ACF Start & End Date to Admin Columns
	 *
	 * @param array $columns the Admin Columns
	 */
	public function add_acf_to_columns_array( array $columns ) {
		$columns['start_date'] = 'Start Date';
		$columns['end_date']   = 'End Date';
		return $columns;
	}

	/**
	 * Displays ACF Start & End Date on Admin Edit screen
	 *
	 * @param string $column the column
	 * @param int    $post_id the post id
	 */
	public function echo_acf_value_to_column( string $column, int $post_id ) {
		$field = get_field( 'event_details', $post_id );
		switch ( $column ) {
			case 'start_date':
				echo $field['time_and_date']['start_date'] ?? '';
				break;
			case 'end_date':
				echo $field['time_and_date']['end_date'] ?? '';
				break;
		}
	}

	/** Makes the ACF columns sortable */
	private function make_acf_columns_sortable() {
		add_filter( 'manage_edit-choctaw-events_sortable_columns', array( $this, 'declare_sortable_acf_field_column' ) );
		add_action( 'pre_get_posts', array( $this, 'add_acf_to_column_query' ) );
	}

	/**
	 * Make the ACF Field column sortable
	 *
	 * @param array $columns the columns
	 */
	public function declare_sortable_acf_field_column( array $columns ) {
		$columns['start_date'] = 'start_date';
		$columns['end_date']   = 'end_date';
		return $columns;
	}

	/**
	 * Sort the posts based on the ACF Field column
	 *
	 * @param \WP_Query $query the query
	 */
	public function add_acf_to_column_query( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'start_date' === $orderby ) {
			$query->set( 'meta_key', 'event_details_time_and_date_start_date' ); // Replace 'acf_field' with the custom field name
			$query->set( 'orderby', 'meta_value' );
		}

		if ( 'end_date' === $orderby ) {
			$query->set( 'meta_key', 'event_details_time_and_date_end_date' ); // Replace 'acf_field' with the custom field name
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/** Add Custom Post Type to WP Search */
	private function add_cpt_to_search_loop() {
		add_action( 'pre_get_posts', array( $this, 'include_choctaw_events_post_type_in_search' ) );
	}

	/** Callback Function: Adds Custom Post Type to WP Query
	 *
	 * @param \WP_Query $query the curent query
	 */
	public function include_choctaw_events_post_type_in_search( \WP_Query $query ) {
		if ( $query->is_search && ! is_admin() ) {
			$query->set( 'post_type', array( 'choctaw-events' ) );
		}
	}

	/** Create a cron job to expire events */
	protected function schedule_event_expiry() {

		if ( ! wp_next_scheduled( 'expire_choctaw_event_posts' ) ) {
			wp_schedule_event( time(), 'hourly', 'expire_choctaw_event_posts' );
		}

		add_action( 'expire_choctaw_event_posts', array( $this, 'expire_choctaw_events' ), 100 );
	}

	/**
	 * Compares today to the ACF date/time fields and updates the post status to "draft" if the event is expired */
	public function expire_choctaw_events() {
		$timezone = new DateTimeZone( 'America/Chicago' );
		$today    = new DateTime( 'now', $timezone );
		$events   = get_posts(
			array(
				'post_type'      => array( 'choctaw-events' ),
				'posts_per_page' => -1,
			)
		);

		foreach ( $events as $event ) {
			$event_details    = get_field( 'event_details', $event->ID );
			$event_is_expired = $this->get_event_expiry( $event_details['time_and_date'] ) < $today;

			if ( $event_is_expired ) {
				$postdata = array(
					'ID'          => $event->ID,
					'post_status' => 'draft',
				);
				wp_update_post( $postdata );
			}
		}
	}

	/**
	 * Get the event expiry date
	 *
	 * @param array $time_and_date the time and date
	 * @return DateTime
	 */
	private function get_event_expiry( array $time_and_date ): DateTime {
		$timezone = new DateTimeZone( 'America/Chicago' );
		if ( $time_and_date['is_all_day'] ) {
			if ( $time_and_date['end_date'] ) {
				$expiry = new \DateTime( $time_and_date['end_date'], $timezone );
			} else {
				$expiry = new \DateTime( $time_and_date['start_date'], $timezone );
				$expiry->modify( '+1 day' );
			}
		} else {
			if ( empty( $time_and_date['end_date'] ) ) {
				$time_and_date['end_date'] = $time_and_date['start_date'];
			}
			if ( empty( $time_and_date['end_time'] ) ) {
				$time_and_date['end_time'] = '11:59pm';
			}
			$expiry_datetime = $time_and_date['end_date'] . ( empty( $time_and_date['end_time'] ) ? '' : ' ' . $time_and_date['end_time'] );
			$expiry          = new DateTime( $expiry_datetime, $timezone );
		}
		return $expiry;
	}

	/**
	 * Handle the appearance of the Yoast Metabox
	 */
	private function handle_yoast_metabox_appearance() {
		add_filter(
			'wpseo_metabox_prio',
			function (): string {
				return 'low';
			}
		);
	}
}
