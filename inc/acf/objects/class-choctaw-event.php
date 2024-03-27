<?php
/**
 * The Choctaw Event ACF Object
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

/**
 * The ACF Object for the Choctaw Events Post Type
 */
class Choctaw_Event {
	/**
	 * The Event (post) ID
	 *
	 * @var $event_id
	 */
	private int $event_id;

	/**
	 * The Event ACF Fields
	 *
	 * @var array $event
	 */
	private array $event;

	/**
	 * The name of the event.
	 *
	 * @var string $name
	 */
	private string $name;

	/**
	 * The description of the event.
	 *
	 * @var string $description
	 */
	private string $description;

	/**
	 * The start date of the event.
	 *
	 * @var ?\DateTime $start_date
	 */
	private ?\DateTime $start_date;

	/**
	 * The start time of the event.
	 *
	 * @var ?\DateTime $start_time
	 */
	private ?\DateTime $start_time;

	/**
	 * The end date of the event.
	 *
	 * @var ?\DateTime $end_date
	 */
	private ?\DateTime $end_date;

	/**
	 * The end time of the event.
	 *
	 * @var ?\DateTime $end_time
	 */
	private ?\DateTime $end_time;

	/**
	 * The website URL for the event (nullable).
	 *
	 * @var ?string
	 */
	private ?string $website;

	/**
	 * Indicates if the event is an all-day event.
	 *
	 * @var bool $is_all_day;
	 */
	public bool $is_all_day;

	/** Whether or not an event has a time attached
	 *
	 * @var bool $has_time;
	 */
	public bool $has_time;

	/**
	 * Whether the event spans multiple days
	 *
	 * @var bool
	 */
	public bool $is_multiday_event = false;

	/** Whether event has category.
	 *
	 * @var bool $has_category
	 */
	public bool $has_category = false;

	/**
	 * The Event's Categories
	 *
	 * @var array $categories
	 */
	public array $categories;

	/**
	 * The Venue
	 *
	 * @var Event_Venue $venue
	 */
	public ?Event_Venue $venue;

	/**
	 * The archive_content field
	 *
	 * @var ?string $excerpt
	 */
	private ?string $excerpt;

	/**
	 * Whether or not an event post has an excerpt
	 *
	 * @var bool $has_excerpt
	 */
	public bool $has_excerpt;

	/**
	 * Constructor method to build the object and its API
	 *
	 * @param array $event The ACF Group ("event_details") field
	 * @param int   $event_id The post ID
	 */
	public function __construct( array $event, int $event_id ) {
		$this->event_id = $event_id;
		$this->event    = $event;
		$this->set_the_details();
		$this->set_the_terms();
		if ( empty( get_field( 'archive_content', $event_id ) ) ) {
			$this->excerpt     = null;
			$this->has_excerpt = false;
		} else {
			$this->excerpt     = get_field( 'archive_content', $event_id );
			$this->has_excerpt = true;

		}
	}

	/**
	 * Sets the sub-fields to properties
	 */
	private function set_the_details() {
		$this->name        = get_the_title( $this->event_id );
		$this->description = acf_esc_html( $this->event['event_description'] );
		$this->is_all_day  = $this->event['time_and_date']['is_all_day'];
		$this->website     = empty( $this->event['event_website'] ) ? null : esc_url( $this->event['event_website'] );
		$this->set_the_venue();
		$this->set_the_date_times( $this->event['time_and_date'] );
	}

	/**
	 * Sets the Dates (and Times) of the event
	 *
	 * @param array $date_time the Dates and Times ACF Subgroup
	 */
	private function set_the_date_times( array $date_time ) {
		$timezone = new \DateTimeZone( 'America/Chicago' );
		if ( $this->is_all_day ) {
			$this->start_date = \DateTime::createFromFormat( 'm/d/Y', $date_time['start_date'], $timezone );
			$this->start_time = null;
			$this->end_date   = empty( $date_time['end_date'] ) ? null : \DateTime::createFromFormat( 'm/d/Y', $date_time['end_date'], $timezone );
			$this->end_time   = null;
		} else {
			$this->start_date = \DateTime::createFromFormat( 'm/d/Y g:i a', $date_time['start_date'] . ' ' . $date_time['start_time'], $timezone );
			$this->start_time = \DateTime::createFromFormat( 'm/d/Y g:i a', $date_time['start_date'] . ' ' . $date_time['start_time'], $timezone );
			$this->end_date   = empty( $date_time['end_date'] ) ? null : \DateTime::createFromFormat( 'm/d/Y g:i a', $date_time['end_date'] . ' ' . $date_time['end_time'], $timezone );
			$this->end_time   = empty( $date_time['end_date'] ) ? null : \DateTime::createFromFormat( 'm/d/Y g:i a', $date_time['end_date'] . ' ' . $date_time['end_time'], $timezone );
		}
		if ( $this->end_date && $this->start_date > $this->end_date && $this->start_date !== $this->end_date ) {
			$this->is_multiday_event = true;
		}
		if ( null === $this->start_time ) {
			$this->has_time = false;
		} else {
			$this->has_time = true;
		}
	}

	/**
	 * Gets the linked Venue tax and assigns it to the event property
	 */
	private function set_the_venue() {
		$this->venue = null;
		if ( taxonomy_exists( 'choctaw-events-venue' ) ) {
			require_once __DIR__ . '/class-event-venue.php';
			$venue = get_the_terms( $this->event_id, 'choctaw-events-venue' );
			if ( $venue ) {
				$this->venue = new Event_Venue( $venue[0] );
			}
		}
	}

	/**
	 * Sets the class's "Category" and "Veneus" props
	 */
	private function set_the_terms() {
		$categories = get_the_terms( $this->event_id, 'category' );
		if ( false === $categories ) {
			$this->categories = array();
		} else {
			foreach ( $categories as $category ) {
				$array              = array(
					'name' => $category->name,
					'slug' => $category->slug,
				);
				$this->categories[] = $array;
			}
			if ( count( $this->categories ) > 0 ) {
				$this->has_category = true;
			}
		}
	}

	/**
	 * Get the event name
	 *
	 * @return string The event name
	 */
	public function get_the_name(): string {
		return esc_textarea( $this->name );
	}

	/**
	 * Get the event description
	 *
	 * @return string The event description
	 */
	public function get_the_description(): string {
		return $this->description;
	}

	/**
	 * Get the event start date and time
	 *
	 * @param string $format the PHP time format
	 * @return string The event start date and time
	 */
	public function get_the_start_date_time( string $format = 'M d, Y @ g:i a' ): ?string {
		$date = null;
		if ( $this->start_time ) {
			$date = $this->start_time->format( $format );
		}
		return $date;
	}

	/**
	 * Get the event start date
	 *
	 * @param string $format the PHP date format
	 * @return string The event start date
	 */
	public function get_the_start_date( string $format = 'M d, Y' ): string {
			return $this->start_date->format( $format );
	}

	/**
	 * Get the event end date and time
	 *
	 * @param string $format the PHP time format
	 * @return string|null The event end date and time (or null if not set)
	 */
	public function get_the_end_date_time( string $format = 'M d, Y @ g:i a' ): ?string {
		$date = null;
		if ( $this->end_time ) {
			$date = $this->end_time->format( $format );
		}
		return $date;
	}

	/**
	 * Get the event end date and time
	 *
	 * @param string $format the PHP time format
	 * @return string|null The event end date and time (or null if not set)
	 */
	public function get_the_end_date( string $format = 'M d, Y @ g:i a' ): ?string {
		$date = null;
		if ( $this->end_date ) {
			$date = $this->end_date->format( $format );
		}
		return $date;
	}

	/**
	 * Get the event website URL
	 *
	 * @return string The event website URL
	 */
	public function get_the_website(): string {
		return esc_url( $this->website );
	}

	/** Gets the category (as an array) */
	public function get_the_category(): array {
		return $this->categories;
	}

	/**
	 * Returns Start and End Dates. If Dates are the same, only start is returned.
	 *
	 * @param string $format date format for the output
	 * @return string
	 */
	public function get_the_dates( $format = 'M d' ): string {
		$start = $this->get_the_start_date( 'Y-m-d' );
		$end   = $this->get_the_end_date( 'Y-m-d' );
		if ( ! $end || $start === $end ) {
			return $this->get_the_start_date( $format );
		} else {
			$start_date = $this->get_the_start_date( $format );
			$end_date   = $this->get_the_end_date( $format );
			return "{$start_date} &ndash; {$end_date}";
		}
	}
	/**
	 * Returns Start and End times. If times are the same, only start is returned.
	 *
	 * @param string $format time format for the output
	 * @param bool   $hide_minutes if minutes should be hidden when equal to 0
	 * @return string
	 */
	public function get_the_times( $format = 'g:i a', $hide_minutes = false ): string {
		$start = $this->get_the_start_date_time();
		$end   = $this->get_the_end_date_time();

		$start_format = $format;
		$end_format   = $format;

		if ( $hide_minutes ) {
			$start_mins = $this->get_the_start_date_time( 'i' );
			$end_mins   = $this->get_the_end_date_time( 'i' );

			if ( '00' === $start_mins ) {
				$start_format = str_replace( ':i', '', $start_format );
			}

			if ( '00' === $end_mins ) {
				$end_format = str_replace( ':i', '', $end_format );
			}
		}

		if ( ! $end || $start === $end ) {
			return $this->get_the_start_date_time( $start_format );
		} else {
			$start_time = $this->get_the_start_date_time( $start_format );
			$end_time   = $this->get_the_end_date_time( $end_format );
			return "{$start_time} &ndash; {$end_time}";
		}
	}
	/**
	 * Gets the "Add to Calendar" button
	 *
	 * @param string $btn_class the HTML classes to add
	 * @param string $text the button text
	 * @return string
	 */
	public function get_the_add_to_calendar_button( $btn_class = 'btn btn-primary mt-5 w-auto', $text = 'Add to Calendar' ): string {
		$js_date_string_format = 'Y-m-d';
		$js_time_string_format = 'Y-m-d\TH:i:s.uP';

		$end = '';
		if ( $this->is_all_day ) {
			$start = $this->get_the_start_date( $js_date_string_format );
			$end   = ( $this->end_date ) ? $this->get_the_end_date( $js_date_string_format ) : $start;
		} else {
			$start = $this->start_time ? $this->get_the_start_date_time( $js_time_string_format ) : $this->get_the_start_date( $js_date_string_format );
			if ( $this->end_time ) {
				$end = $this->get_the_end_date_time( $js_time_string_format );
			} elseif ( $this->end_date ) {
				$end = $this->get_the_end_date( $js_date_string_format );
			}
		}

		$button = "<button type='button' id='add-to-calendar' class='{$btn_class}' data-event-start='{$start}'" . ( ! empty( $end ) ? "data-event-end='{$end}'" : '' ) . "data-is-all-day='{$this->is_all_day}'>{$text}</button>";
		return $button;
	}

	/**
	 * Echo the event name.
	 */
	public function the_name() {
		echo $this->get_the_name();
	}

	/**
	 * Echo the event description
	 */
	public function the_description() {
		echo $this->get_the_description();
	}

	/**
	 * Echo the event start date and time.
	 *
	 * @param string $format the PHP time format
	 */
	public function the_start_date_time( string $format = 'M d, Y @ g:i a' ) {
		echo $this->get_the_start_date_time( $format );
	}

	/**
	 * Echo the event start date and time.
	 *
	 * @param string $format the PHP time format
	 */
	public function the_start_date( string $format = 'M d, Y' ) {
		echo $this->get_the_start_date( $format );
	}

	/**
	 * Echo the event end date and time.
	 *
	 * @param string $format the PHP time format
	 */
	public function the_end_date_time( string $format = 'M d, Y @ g:i a' ) {
		echo $this->get_the_end_date_time( $format );
	}

	/**
	 * Echoes the event full anchor tag of the website.
	 */
	public function the_website() {
		$url = $this->get_the_website();
		if ( $url ) {
			echo "<a href='{$url}' target='_blank' rel='noopener noreferrer' id='event-website'>{$url}</a>";
		}
	}

	/**
	 * Echoes each category in the object's `categories` prop as an anchor
	 */
	public function the_category() {
		foreach ( $this->categories as $category ) {
			echo "<a href='/{$category['slug']}'>{$category['name']}</a>";
		}
	}

	/**
	 * Echoes Start and End Dates. If Dates are the same, only start is returned.
	 *
	 * @param string $format date format for the output
	 */
	public function the_dates( $format = 'M d' ) {
		echo $this->get_the_dates( $format );
	}

	/**
	 * Echoes Start and End times. If times are the same, only start is returned.
	 *
	 * @param string $format time format for the output
	 * @param bool   $hide_minutes if minutes should be hidden in when equal to 0
	 */
	public function the_times( $format = 'g:i a', $hide_minutes = false ) {
		echo $this->get_the_times( $format, $hide_minutes );
	}

	/**
	 * Echoes the "Add to Calendar" Button
	 *
	 * @param string $btn_class the HTML classes to add
	 * @param string $text the button text
	 */
	public function the_add_to_calendar_button( $btn_class = 'btn btn-primary mt-5 w-auto', $text = 'Add to Calendar' ) {
		echo $this->get_the_add_to_calendar_button( $btn_class, $text );
	}

	/**
	 * Gets the excerpt
	 */
	public function get_the_excerpt(): string {
		return $this->excerpt;
	}

	/**
	 * Echoes the excerpt
	 */
	public function the_excerpt() {
		return $this->get_the_excerpt();
	}
}
