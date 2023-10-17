<?php
/**
 * The Choctaw Event Venue (Taxonomy) ACF Object
 *
 * @since 1.0
 * @package ChoctawNation
 */

/**
 * The ACF Object for the Event Venue taxonomy
 */
class Event_Venue {
	/**
	 * The name of the venue.
	 *
	 * @var string  */
	private string|bool $name;

	/**
	 * The street address of the venue.
	 *
	 * @var string  */
	private string|bool $street_address;

	/**
	 * The city where the venue is located.
	 *
	 * @var string  */
	private string|bool $city;

	/**
	 * The state where the venue is located.
	 *
	 * @var string  */
	public string|bool $state;

	/**
	 * The ZIP code of the venue.
	 *
	 * @var int  */
	public int|bool $zip_code;

	/**
	 * The phone number of the venue.
	 *
	 * @var string|bool  */
	private string|bool $phone;

	/**
	 * The website URL of the venue (nullable).
	 *
	 * @var string|null  */
	private string|bool $website;

	/**
	 * Constructor method to build the object and its API
	 *
	 * @param WP_Term $venue The venue WP_Term object (linked via ACF)
	 */
	public function __construct( WP_Term $venue ) {
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
		$this->street_address = $info['street_address'];
		$this->city           = $info['city'];
		$this->state          = $info['state'];
		$this->zip_code       = $this->zip_code_check( $info['zip_code'] );
		$this->phone          = $info['phone'] ?? null;
		$this->website        = $info['website'] ?? null;
	}

	/**
	 * Simple 5-digit zip integer check
	 *
	 * @param int $zip the user-submitted zip code
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
		return esc_textarea( $this->name );
	}

	/**
	 * Get the venue street address
	 *
	 * @return string The venue street address
	 */
	public function get_the_street_address(): string|bool {
			return esc_textarea( empty( $this->street_address ) );
	}

	/**
	 * Get the venue city
	 *
	 * @return string The venue city
	 */
	public function get_the_city(): string|bool {
		return esc_textarea( empty( $this->city ) );
	}

	/**
	 * Gets the full address
	 *
	 * @return string the address
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
	 * @return string|null The venue phone number (or null if not set)
	 */
	public function get_the_phone(): string|bool {
		return esc_textarea( empty( $this->phone ) );
	}

	/**
	 * Get the venue website URL
	 *
	 * @return string|null The venue website URL (or null if not set)
	 */
	public function get_the_website(): string|bool {
		return esc_url( empty( $this->website ) );
	}

	/**
	 * Echo the venue name
	 */
	public function the_name() {
		echo $this->get_the_name();
	}

	/**
	 * Echo the venue street address
	 */
	public function the_street_address() {
		echo $this->get_the_street_address();
	}

	/**
	 * Echo the venue city
	 */
	public function the_city() {
		echo $this->get_the_city();
	}

	/** Echo the full address */
	public function the_address() {
		echo $this->get_the_address();
	}

	/**
	 * Echo the venue phone number
	 */
	public function the_phone() {
		echo $this->get_the_phone();
	}

	/**
	 * Echo the venue website URL
	 */
	public function the_website() {
		$url = $this->get_the_website();
		if ( $url ) {
			echo "<a href='{$url}' target='_blank' rel='noopener noreferrer' id='venue-website'>{$url}</a>";
		}
	}
}
