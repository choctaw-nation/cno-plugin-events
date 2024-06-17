<?php
/**
 * The Post Type Builder
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

use ChoctawNation\Events\CPT;

/**
 * Builds the Post Type w/ default ACF fields
 */
class Post_Type_Builder {
	/**
	 * The cpt slug
	 *
	 * @var string $cpt_slug
	 */
	protected string $cpt_slug;

	/**
	 * The CPT Rewrite
	 *
	 * @var string $rewrite
	 */
	protected string $rewrite;

	/**
	 * Die if no ACF, else build the plugin.
	 *
	 * @param string $cpt_slug the Events CPT Slug / ID (defaults to "choctaw-events" for plugin compatibility)
	 * @param string $rewrite the CPT rewrite (defaults to "events" for logical permalinks)
	 */
	public function __construct( string $cpt_slug = 'choctaw-events', string $rewrite = 'events' ) {
		if ( ! class_exists( 'ACF' ) ) {
			$plugin_error = new \WP_Error( 'Choctaw Events Error', 'ACF not installed!' );
			echo $plugin_error->get_error_messages( 'Choctaw Events Error' );
			die;
		}
		$this->cpt_slug = $cpt_slug;
		$this->rewrite  = $rewrite;
		$this->init_acf();
		include_once dirname( __DIR__ ) . '/acf/classes/class-choctaw-event.php';
	}

	/** Inits the CPT */
	public function init_cpt() {
		require_once __DIR__ . '/class-cpt.php';
		$cpt = new CPT( $this->cpt_slug, $this->rewrite );
		$cpt->init();
	}

	/** Inits the ACF Fields */
	private function init_acf() {
		require_once __DIR__ . '/class-custom-fields.php';
		new Custom_Fields();
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
		$template          = file_exists( $template_override ) ? $template_override : dirname( __DIR__, 2 ) . "/templates/{$type}-{$this->cpt_slug}.php";
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
		$asset_file = require_once dirname( __DIR__, 2 ) . '/dist/choctaw-events.asset.php';
		wp_enqueue_script(
			'choctaw-events-add-to-calendar',
			plugin_dir_url( __DIR__ ) . 'dist/choctaw-events.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array( 'strategy' => 'defer' )
		);

		$search_asset_file = require_once dirname( __DIR__, 2 ) . '/dist/choctaw-events-search.asset.php';
		$deps              = array_merge( array( 'main' ), $search_asset_file['dependencies'] );
		wp_enqueue_script(
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
}
