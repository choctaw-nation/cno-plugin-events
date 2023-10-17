<?php
/**
 * Plugin Loader
 *
 * @since 1.0
 * @package ChoctawNation
 */

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
		include_once __DIR__ . '/acf/objects/class-event-venue.php';
		register_activation_hook( dirname( __DIR__ ) . '/index.php', array( $this, 'activate_plugin' ) );
	}


	/**
	 * Filter the WordPress Template Lookup to view the Plugin folder first
	 *
	 * @param string $template the template path
	 */
	public function update_template_loader( string $template ): string {
		$is_single  = is_singular( 'choctaw-events' );
		$is_archive = is_archive( 'choctaw-events' );
		$is_search  = is_search();
		if ( $is_single ) {
			$template = $this->get_the_template( 'single' );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_single_js' ) );
		}
		if ( $is_archive ) {
			$template = $this->get_the_template( 'archive' );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_search_tsx' ) );
		}
		if ( is_search() ) {
			$template = $this->get_the_search_page( $template );

		}
		return $template;
	}

	/** Gets the appropriate template
	 *
	 * @param string $type "single" or "archive"
	 * @return string|WP_Error the template path
	 */
	private function get_the_template( string $type ): string|WP_Error {
		$template_override = get_stylesheet_directory() . "/templates/{$type}-{$this->cpt_slug}.php";
		$template          = file_exists( $template_override ) ? $template_override : dirname( __DIR__, 1 ) . "/templates/{$type}-{$this->cpt_slug}.php";
		if ( file_exists( $template ) ) {
			return $template;
		} else {
			return new WP_Error( 'Choctaw Events Error', "{$type} template not found!" );
		}
	}



	/**
	 * Returns the Plugin Archive.php Path (if exists)
	 */
	private function get_the_search_page(): string|WP_Error {
		$search_page = dirname( __DIR__, 1 ) . '/templates/search.php';
		global $wp_query;
		// $post_type = $wp_query->
		if ( file_exists( $search_page ) ) {

			return $search_page;
		} else {
			return new WP_Error( 'Choctaw Events Error', 'Search page not found!' );
		}
	}

	/** Enqueues the "Add to Calendar" logic */
	public function enqueue_single_js() {
		$asset_file = require_once dirname( __DIR__, 1 ) . '/dist/choctaw-events.asset.php';
		wp_enqueue_script(
			'choctaw-events',
			plugin_dir_url( __DIR__ ) . 'dist/choctaw-events.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array( 'strategy' => 'defer' )
		);
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
}