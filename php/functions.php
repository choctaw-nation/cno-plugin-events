<?php
class CNOEventsPlugin {
	public function __construct() {
		add_action( 'init', array( $this, 'register_event_custom_post_type' ) );
		include plugin_dir_path( __FILE__ ) . '/acf-fields.php';
		add_filter( 'template_include', array( $this, 'include_templates' ) );
		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_event_styles' ) );
		}
	}
	public function include_templates( $template ) {
		if ( get_post_type() == 'events' && is_single() ) {
			$template = locate_template( array( 'templates/single-events.php' ), false, false );
			if ( ! $template ) {
				$template = dirname( __FILE__, 2 ) . '/templates/single-events.php';
			}
		}
		if ( get_post_type() == 'events' && is_archive() ) {
			$template = locate_template( array( 'templates/archive-events.php' ), false, false );
			if ( ! $template ) {
				$template = dirname( __FILE__, 2 ) . '/templates/archive-events.php';
			}
		}
		return $template;
	}

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
	public function enqueue_event_styles() {
		wp_enqueue_style( 'cno-events-global', plugin_dir_url( 'cno-events/build/style-global.css' ) . 'style-global.css', array(), '1.0' );
	}
}