<?php
/**
 * The Choctaw Event Venue (Taxonomy) ACF Object
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

/**
 * The ACF Object for the Event Venue taxonomy
 */
class Event_Venue {
	/**
	 * The name of the venue.
	 *
	 * @var string $name
	 */
	private string $name;

	/**
	 * The street address of the venue.
	 *
	 * @var ?string $street_address
	 */
	private ?string $street_address;

	/**
	 * The city where the venue is located.
	 *
	 * @var ?string $city
	 */
	private ?string $city;

	/**
	 * The state where the venue is located.
	 *
	 * @var string $state
	 */
	public ?string $state;

	/**
	 * The ZIP code of the venue.
	 *
	 * @var ?int $zip_code
	 */
	public ?int $zip_code;

	/**
	 * The phone number of the venue.
	 *
	 * @var ?string $phone
	 */
	private ?string $phone;

	/**
	 * The website URL of the venue (nullable).
	 *
	 * @var ?string $website
	 */
	private ?string $website;

	/**
	 * Constructor method to build the object and its API
	 *
	 * @param \WP_Term $venue The venue WP_Term object (linked via ACF)
	 */
	public function __construct( \WP_Term $venue ) {
		$this->name = $venue->name;
		$venue_info = get_field( 'venue_information', $venue );
		$this->set_the_info( $venue_info );
	}

	/**
	 * Sets the sub-fields to properties
	 *
	 * @param array $info The ACF Group Fields
	 */
	private function set_the_info( ?array $info ) {
		if ( ! $info ) {
			return null;
		}
		$this->street_address = empty( $info['street_address'] ) ? null : esc_textarea( $info['street_address'] );
		$this->city           = empty( $info['city'] ) ? null : esc_textarea( $info['city'] );
		$this->state          = empty( $info['state'] ) ? null : $info['state'];
		$this->zip_code       = $this->zip_code_check( $info['zip_code'] );
		$this->phone          = empty( $info['phone'] ) ? null : esc_textarea( $info['phone'] );
		$this->website        = empty( $info['website'] ) ? null : esc_url( $info['website'] );
	}

	/**
	 * Simple 5-digit zip integer check
	 *
	 * @param int $zip the user-submitted zip code
	 * @return int|null the zip code or null
	 */
	private function zip_code_check( int $zip ): ?int {
		if ( is_int( $zip ) && strlen( (string) $zip ) === 5 ) {
			return $zip;
		} else {
			return null;
		}
	}

	/**
	 * Get the venue name
	 *
	 * @return string The venue name
	 */
	public function get_the_name(): string {
		return $this->name;
	}

	/**
	 * Get the venue street address
	 *
	 * @return ?string The venue street address
	 */
	public function get_the_street_address(): ?string {
			return $this->street_address;
	}

	/**
	 * Get the venue city
	 *
	 * @return string The venue city
	 */
	public function get_the_city(): string {
		return $this->city;
	}

	/**
	 * Gets the full address
	 *
	 * @return ?string the address
	 */
	public function get_the_address(): ?string {
		$street = $this->get_the_street_address();
		$city   = $this->get_the_city();
		if ( ! empty( $this->zip_code ) && $street && $city ) {
			return "{$street}\n{$city}, {$this->state} {$this->zip_code}";
		} else {
			return null;
		}
	}

	/**
	 * Get the venue phone number
	 *
	 * @return ?string The venue phone number (or null if not set)
	 */
	public function get_the_phone(): ?string {
		return $this->phone;
	}

	/**
	 * Get the venue website URL
	 *
	 * @return ?string The venue website URL (or null if not set)
	 */
	public function get_the_website(): ?string {
		return $this->website;
	}

	/**
	 * Echo the venue name
	 *
	 * @return void
	 */
	public function the_name(): void {
		echo $this->get_the_name();
	}

	/**
	 * Echo the venue street address
	 *
	 * @return void
	 */
	public function the_street_address(): void {
		echo $this->get_the_street_address();
	}

	/**
	 * Echo the venue city
	 *
	 * @return void
	 */
	public function the_city(): void {
		echo $this->get_the_city();
	}

	/**
	 * Echo the full address
	 *
	 * @return void
	 */
	public function the_address(): void {
		echo $this->get_the_address();
	}

	/**
	 * Echo the venue phone number
	 *
	 * @return void
	 */
	public function the_phone(): void {
		echo $this->get_the_phone();
	}

	/**
	 * Echo the venue website URL
	 *
	 * @return void
	 */
	public function the_website(): void {
		$url = $this->get_the_website();
		if ( $url ) {
			echo "<a href='{$url}' target='_blank' rel='noopener noreferrer' id='venue-website'>{$url}</a>";
		}
	}
}