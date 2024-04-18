<?php
/**
 * Plugin Loader
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

/** Load the Parent Class */
require_once __DIR__ . '/plugin-logic/class-admin-handler.php';

/** The Plugin Loader */
final class Plugin_Loader extends Admin_Handler {
	/**
	 * Initializes the Plugin
	 *
	 * @return void
	 */
	public function activate(): void {
		$this->init();
		add_filter( 'template_include', array( $this, 'update_template_loader' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		include_once __DIR__ . '/acf/classes/class-event-venue.php';
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );
		add_action( 'pre_get_posts', array( $this, 'custom_archive_query' ) );
		add_filter( 'register_taxonomy_args', array( $this, 'add_venue_to_graphql' ), 10, 2 );
		flush_rewrite_rules();
	}

	/**
	 * Handles Plugin Deactivation
	 * (this is a callback function for the `register_deactivation_hook` function)
	 *
	 * @return void
	 */
	public function deactivate(): void {
		$scripts = array( 'choctaw-events-add-to-calendar', 'choctaw-events-search' );
		foreach ( $scripts as $script ) {
			wp_deregister_script( $script );
		}
		$image_sizes = array( 'choctaw-events-preview', 'choctaw-events-single' );
		foreach ( $image_sizes as $size ) {
			remove_image_size( $size );
		}
		$post_types = array( $this->cpt_slug );
		foreach ( $post_types as $post_type ) {
			unregister_post_type( $post_type );
		}
		$taxonomies = array( 'choctaw-events-category', 'choctaw-events-venue' );
		foreach ( $taxonomies as $taxonomy ) {
			unregister_taxonomy( $taxonomy );
		}
		flush_rewrite_rules();
	}

	/**
	 * Filter the WordPress Template Lookup to view the Plugin folder first
	 *
	 * @param string $template the template path
	 */
	public function update_template_loader( string $template ): string {
		$is_single  = is_singular( $this->cpt_slug );
		$is_archive = is_post_type_archive( $this->cpt_slug );
		if ( $is_single ) {
			$template = $this->get_the_template( 'single' );
		}
		if ( $is_archive ) {
			$template = $this->get_the_template( 'archive' );
		}
		return $template;
	}

	/** Gets the appropriate template
	 *
	 * @param string $type "single" or "archive"
	 * @return string|\WP_Error the template path
	 */
	private function get_the_template( string $type ): string|\WP_Error {
		$template_override = get_stylesheet_directory() . "/templates/{$type}-{$this->cpt_slug}.php";
		$template          = file_exists( $template_override ) ? $template_override : dirname( __DIR__, 1 ) . "/templates/{$type}-{$this->cpt_slug}.php";
		if ( file_exists( $template ) ) {
			return $template;
		} else {
			return new \WP_Error( 'Choctaw Events Error', "{$type} template not found!" );
		}
	}

	/**
	 *  Enqueues the "Add to Calendar" logic
	 *
	 * @return void
	 */
	public function register_scripts(): void {
		$asset_file = require_once dirname( __DIR__, 1 ) . '/dist/choctaw-events.asset.php';
		wp_register_script(
			'choctaw-events-add-to-calendar',
			plugin_dir_url( __DIR__ ) . 'dist/choctaw-events.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array( 'strategy' => 'defer' )
		);

		$search_asset_file = require_once dirname( __DIR__, 1 ) . '/dist/choctaw-events-search.asset.php';
		$deps              = array_merge( array( 'main' ), $search_asset_file['dependencies'] );
		wp_register_script(
			'choctaw-events-search',
			plugin_dir_url( __DIR__ ) . 'dist/choctaw-events-search.js',
			$deps,
			$search_asset_file['version'],
			array( 'strategy' => 'defer' )
		);
		wp_localize_script( 'choctaw-events-search', 'cnoEventSearchData', array( 'rootUrl' => home_url() ) );
	}

	/** Registers image sizes for Single and Archive pages
	 *
	 * @return void
	 */
	public function register_image_sizes(): void {
		add_image_size( 'choctaw-events-preview', 1392, 784 );
		add_image_size( 'choctaw-events-single', 2592, 1458 );
	}

	/**
	 * Updates the Archive Page loop to display posts via ACF field instead of publish date
	 *
	 * @param \WP_Query $query the current query
	 * @return void
	 */
	public function custom_archive_query( \WP_Query $query ): void {
		$is_archive = $query->is_post_type_archive( $this->cpt_slug );
		if ( $is_archive && $query->is_main_query() ) {
			$query->set( 'meta_key', 'event_details_time_and_date_start_date' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
		}
	}

	/** Registers Venues taxonomy to GraphQL if exists
	 *
	 * @param array  $args Array of arguments for registering a taxonomy. See the register_taxonomy() function for accepted arguments.
	 * @param string $taxonomy  Taxonomy key.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_taxonomy/
	 * @return array $args
	 */
	public function add_venue_to_graphql( array $args, string $taxonomy ): array {
		if ( 'choctaw-events-venue' === $taxonomy ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'choctawEventsVenue';
			$args['graphql_plural_name'] = 'choctawEventsVenues';
		}
		return $args;
	}
}
