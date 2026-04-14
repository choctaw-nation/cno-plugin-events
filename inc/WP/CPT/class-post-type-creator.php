<?php
/**
 * Events CPT
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP\CPT;

use ChoctawNation\Events\WP\Plugin_Settings;

/**
 * Generates the Events CPT
 */
class Post_Type_Creator {
	/**
	 * Labels for the "Categories" Taxonomy
	 *
	 * @var $venue_labels
	 */
	private array $event_category_labels = array(
		'name'          => 'Event Categories',
		'singular_name' => 'Event Category',
		'menu_name'     => 'Event Categories',
	);

	/**
	 * Plugin options for CPT configuration.
	 *
	 * @var array<string, mixed> $options
	 */
	private array $options;

	/**
	 * Constructor
	 *
	 * @param array<string, mixed> $options Plugin options for CPT configuration.
	 */
	public function __construct( array $options ) {
		$this->options = $options;
	}

	/** Init the CPT */
	public function load_cpt() {
		if ( Plugin_Settings::is_post_type_slug_conflicting( $this->options['post_type_slug'] ) ) {
			add_action( 'admin_notices', array( $this, 'render_slug_conflict_notice' ) );
			return;
		}

		$args = array(
			'labels'        => $this->generate_labels(),
			'public'        => true,
			'show_in_rest'  => true,
			'supports'      => array(
				'title',
				'thumbnail',
				'revisions',
				'author',
				'excerpt',
				'editor',
				'custom-fields',
			),
			'menu_icon'     => 'dashicons-calendar',
			'menu_position' => 5,
		);
		if ( $this->options['has_archive'] ) {
			$args['has_archive'] = empty( $this->options['archive_slug'] ) ? true : $this->options['archive_slug'];
		}
		if ( ! empty( $this->options['post_type_slug'] ) && sanitize_title( $this->options['post_type_label_plural'] ) !== $this->options['post_type_slug'] ) {
			$args['rewrite'] = array( 'slug' => $this->options['post_type_slug'] );
		}
		register_post_type( $this->options['post_type_slug'], $args );
	}

	/**
	 * Renders warning notice when post type slug conflicts.
	 *
	 * @return void
	 */
	public function render_slug_conflict_notice(): void {
		$conflicting_post_type = Plugin_Settings::get_conflicting_post_type( $this->options['post_type_slug'] );

		echo '<div class="notice notice-warning"><p>';
		printf(
			esc_html( 'Choctaw Events Plugin did not register its post type because slug "%1$s" is already used by "%2$s".' ),
			esc_html( $this->options['post_type_slug'] ),
			esc_html( $conflicting_post_type?->labels->name ?? $this->options['post_type_slug'] )
		);
		echo '</p></div>';
	}

	/** Init custom event category taxonomy (for non-conflicting event categories outside the WP Core categories) */
	private function init_category_taxonomy() {
		$args = array(
			'labels'       => $this->event_category_labels,
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'events-category' ),
		);
		register_taxonomy( 'choctaw-events-category', $this->options['post_type_slug'], $args );
	}

	/**
	 * Generates CPT labels based on settings with fallbacks to defaults.
	 *
	 * @return array<string, string>
	 */
	private function generate_labels(): array {
		$defaults = array(
			'name'               => 'Events',
			'singular_name'      => 'Event',
			'add_new'            => 'Add New Event',
			'add_new_item'       => 'Add New Event',
			'edit_item'          => 'Edit Event',
			'new_item'           => 'New Event',
			'all_items'          => 'All Events',
			'view_item'          => 'View Event',
			'search_items'       => 'Search Events',
			'not_found'          => 'No events found',
			'not_found_in_trash' => 'No events found in Trash',
			'menu_name'          => 'Events',
		);
		// phpcs:disable Universal.Operators.DisallowShortTernary.Found
		return array(
			'name'               => $this->options['post_type_label_plural'] ?: $defaults['name'],
			'singular_name'      => $this->options['post_type_label_single'] ?: $defaults['singular_name'],
			'add_new'            => sprintf( 'Add New %s', $this->options['post_type_label_single'] ?: $defaults['singular_name'] ),
			'add_new_item'       => sprintf( 'Add New %s', $this->options['post_type_label_single'] ?: $defaults['singular_name'] ),
			'edit_item'          => sprintf( 'Edit %s', $this->options['post_type_label_single'] ?: $defaults['singular_name'] ),
			'new_item'           => sprintf( 'New %s', $this->options['post_type_label_single'] ?: $defaults['singular_name'] ),
			'all_items'          => sprintf( 'All %s', $this->options['post_type_label_plural'] ?: $defaults['name'] ),
			'view_item'          => sprintf( 'View %s', $this->options['post_type_label_single'] ?: $defaults['singular_name'] ),
			'search_items'       => sprintf( 'Search %s', $this->options['post_type_label_plural'] ?: $defaults['name'] ),
			'not_found'          => sprintf( 'No %s found', strtolower( $this->options['post_type_label_plural'] ) ?: strtolower( $defaults['name'] ) ),
			'not_found_in_trash' => sprintf( 'No %s found in Trash', strtolower( $this->options['post_type_label_plural'] ) ?: strtolower( $defaults['name'] ) ),
			'menu_name'          => $this->options['post_type_label_plural'] ?: $defaults['name'],
		);
		// phpcs:enable Universal.Operators.DisallowShortTernary.Found
	}
}