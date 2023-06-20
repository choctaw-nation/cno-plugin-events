<?php
/**
 * Class CNO_Events_Plugin
 * Description: The Events plugin class.
 *
 * @var bool whether single/archive is of this post type
 */
class CNO_Events_Plugin {
	/** @var bool whether single/archive is of this post type  */
	protected bool $is_events_post_type;
	/** Load the plugin */
	public function __construct() {
		include plugin_dir_path( __FILE__ ) . '/acf-fields.php';
		$this->is_events_post_type = get_post_type() === 'events';
		add_action( 'init', array( $this, 'register_event_custom_post_type' ) );
		add_filter( 'template_include', array( $this, 'include_templates' ) );
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_event_styles' ) );
		}
	}
	/**
	 * Allows template overrides.
	 *
	 * @param string $template path to template
	 */
	public function include_templates( string $template ) {
		if ( $this->is_events_post_type && is_single() ) {
			$template = locate_template( array( 'templates/single-events.php' ), false, false );
			if ( ! $template ) {
				$template = dirname( __FILE__, 2 ) . '/templates/single-events.php';
			}
		}
		if ( $this->is_events_post_type && is_archive() ) {
			$template = locate_template( array( 'templates/archive-events.php' ), false, false );
			if ( ! $template ) {
				$template = dirname( __FILE__, 2 ) . '/templates/archive-events.php';
			}
		}
		return $template;
	}

	/** Register the Events CPT.
	 *
	 * @param array $args the CPT args
	 */
	public function register_event_custom_post_type( $args = array() ) {
		$post_type_labels = array(
			'name'               => 'Events',
			'singular_name'      => 'Event',
			'menu_name'          => 'Events',
			'parent_item_colon'  => 'Parent Event',
			'all_items'          => 'All Events',
			'view_item'          => 'View Event',
			'view_items'         => 'View Events',
			'add_new_item'       => 'Add New Event',
			'add_new'            => 'Add Event',
			'edit_item'          => 'Edit Event',
			'update_item'        => 'Update Event',
			'search_items'       => 'Search Events',
			'not_found'          => 'Not Found',
			'not_found_in_trash' => 'Not found in Trash',
		);
		if ( empty( $args ) ) {
			$args = array(
				'labels'              => $post_type_labels,
				'hierarchical'        => false,
				'description'         => 'Events',
				'supports'            => array( 'title', 'thumbnail', 'revisions', 'excerpt' ),
				'show_ui'             => true,
				'show_in_rest'        => true,
				'show_in_menu'        => true,
				'menu_position'       => 25,
				'menu_icon'           => 'dashicons-calendar-alt',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'query_var'           => true,
				'can_export'          => true,
				'public'              => true,
				'has_archive'         => true,
				'show_in_graphql'     => true,
				'graphql_single_name' => 'event',
				'graphql_plural_name' => 'events',
			);
		}
		register_post_type( 'events', $args );
	}

	/** Load styles on pages */
	public function enqueue_event_styles() {
		if ( $this->is_events_post_type && is_single() ) {
			wp_enqueue_style( 'cno-events-global', plugin_dir_url( 'cno-events/build/style-global.css' ) . 'style-global.css', array(), '1.0' );

		}
		if ( $this->is_events_post_type && is_archive() ) {
			wp_enqueue_style( 'cno-events-global', plugin_dir_url( 'cno-events/build/style-global.css' ) . 'style-global.css', array(), '1.0' );
		}
	}
}