<?php
/**
 * The Post Type Builder
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

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
	public function __construct( string $cpt_slug, string $rewrite ) {
		$this->cpt_slug = $cpt_slug;
		$this->rewrite  = $rewrite;
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
	 *  Registers the JS
	 */
	public function register_scripts(): void {
		$this->register_add_to_calendar_assets();
	}

	/** Register Add to Calendar JS */
	private function register_add_to_calendar_assets(): void {
		$asset_file = require dirname( __DIR__, 2 ) . '/dist/choctaw-events.asset.php';
		wp_register_script(
			'choctaw-events-add-to-calendar',
			plugin_dir_url( dirname( __DIR__ ) ) . 'dist/choctaw-events.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			array( 'strategy' => 'defer' )
		);
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
			$query->set( 'meta_key', 'start_date' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
		}
	}
}