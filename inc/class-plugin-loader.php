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

/** Inits the Plugin */
final class Plugin_Loader extends Admin_Handler {
	/**
	 * Die if no ACF, else build the plugin.
	 *
	 * @param string $cpt_slug the Events CPT Slug / ID (defaults to "choctaw-events" for plugin compatibility)
	 * @param string $rewrite the CPT rewrite (defaults to "events" for logical permalinks)
	 */
	public function __construct( string $cpt_slug = 'choctaw-events', string $rewrite = 'events' ) {
		parent::__construct( $cpt_slug, $rewrite );
		parent::init();
		add_filter( 'template_include', array( $this, 'update_template_loader' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		include_once __DIR__ . '/acf/objects/class-event-venue.php';
		// register_activation_hook( dirname( __DIR__ ) . '/index.php', array( $this, 'activate_plugin' ) );
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );
		add_action( 'pre_get_posts', array( $this, 'custom_archive_query' ) );
		add_filter( 'register_taxonomy_args', array( $this, 'add_venue_to_graphql' ), 10, 2 );
	}

	/**
	 * Filter the WordPress Template Lookup to view the Plugin folder first
	 *
	 * @param string $template the template path
	 */
	public function update_template_loader( string $template ): string {
		$is_single  = is_singular( 'choctaw-events' );
		$is_archive = is_post_type_archive( 'choctaw-events' );
		// $is_search  = is_search();
		if ( $is_single ) {
			$template = $this->get_the_template( 'single' );
		}
		if ( $is_archive ) {
			$template = $this->get_the_template( 'archive' );
			// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_search_tsx' ) );
		}
		// if ( is_search() ) {
		// $template = $this->get_the_search_page( $template );
		// }
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
	 * Returns the Plugin Archive.php Path (if exists)
	 */
	private function get_the_search_page(): string|\WP_Error {
		$search_page = dirname( __DIR__, 1 ) . '/templates/search.php';
		if ( file_exists( $search_page ) ) {
			return $search_page;
		} else {
			return new \WP_Error( 'Choctaw Events Error', 'Search page not found!' );
		}
	}

	/** Enqueues the "Add to Calendar" logic */
	public function register_scripts() {
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

	/** Enqueues the "Search" logic powered by TypeScript React (.tsx) */
	public function enqueue_search_tsx() {
		$asset_file = require_once dirname( __DIR__, 1 ) . '/dist/choctaw-events-search.asset.php';
		wp_enqueue_script(
			'choctaw-events-search',
			plugin_dir_url( __DIR__ ) . 'dist/choctaw-events-search.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array( 'strategy' => 'defer' )
		);
	}

	/** Registers image sizes for Single and Archive pages */
	public function register_image_sizes() {
		add_image_size( 'choctaw-events-preview', 1392, 784 );
		add_image_size( 'choctaw-events-single', 2592, 1458 );
	}

	/**
	 * Updates the Archive Page loop to display posts via ACF field instead of publish date
	 *
	 * @param \WP_Query $query the current query
	 */
	public function custom_archive_query( \WP_Query $query ) {
		$is_archive = $query->is_post_type_archive( 'choctaw-events' );
		if ( $is_archive && $query->is_main_query() ) {
			$query->set( 'meta_key', 'event_details_time_and_date_start_date' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
		}
	}

	/** Registers Venues taxonommy to GraphQL if exists */
	public function add_venue_to_graphql( array $args, string $taxonomy ) {
		if ( 'choctaw-events-venue' === $taxonomy ) {
			$args['show_in_graphql']     = true;
			$args['graphql_single_name'] = 'choctawEventsVenue';
			$args['graphql_plural_name'] = 'choctawEventsVenues';
		}

		return $args;
	}
}