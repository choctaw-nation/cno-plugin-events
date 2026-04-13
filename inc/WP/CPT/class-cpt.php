<?php
/**
 * Events CPT
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

use ChoctawNation\Events\WP\Plugin_Settings;

/**
 * Generates the Events CPT
 */
class CPT {
	/**
	 * The Labels array
	 *
	 * @var $labels
	 */
	private array $cpt_labels = array(
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
	 * The Event Slug
	 *
	 * @var string $slug
	 */
	public string $slug;

	/**
	 * The event slug rewrite
	 *
	 * @var string $rewrite
	 */
	private string $rewrite;

	/**
	 * Generates the CPT (and its category Taxonomy) with optional $slug
	 *
	 * @param string $slug default "choctaw-events"
	 * @param string $rewrite default "events"
	 */
	public function __construct( string $slug = 'choctaw-events', string $rewrite = 'events' ) {
		$this->slug    = $slug;
		$this->rewrite = $rewrite;
	}

	/** Publicly callable function that registers the CPT & Taxonomies */
	public function init() {
		$this->init_cpt();
		$this->init_category_taxonomy();
	}

	/** Init the CPT */
	private function init_cpt() {
		if ( Plugin_Settings::is_post_type_slug_conflicting( $this->slug ) ) {
			add_action( 'admin_notices', array( $this, 'render_slug_conflict_notice' ) );
			return;
		}

		$args = array(
			'labels'        => $this->cpt_labels,
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'rest_base'     => 'choctaw-events',
			'rewrite'       => array( 'slug' => $this->rewrite ),
			'supports'      => array(
				'title',
				'thumbnail',
				'revisions',
				'author',
			),
			'taxonomies'    => array( 'post_tag' ),
			'menu_icon'     => 'dashicons-calendar',
			'menu_position' => 6,
		);
		register_post_type( $this->slug, $args );
	}

	/**
	 * Renders warning notice when post type slug conflicts.
	 *
	 * @return void
	 */
	public function render_slug_conflict_notice(): void {
		$conflicting_post_type = Plugin_Settings::get_conflicting_post_type( $this->slug );

		echo '<div class="notice notice-warning"><p>';
		printf(
			/* translators: 1: conflicting post type slug, 2: conflicting post type label. */
			esc_html__( 'Choctaw Events Plugin did not register its post type because slug "%1$s" is already used by "%2$s".', 'cno' ),
			esc_html( $this->slug ),
			esc_html( $conflicting_post_type?->labels->name ?? $this->slug )
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
		register_taxonomy( 'choctaw-events-category', $this->slug, $args );
	}
}
