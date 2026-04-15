<?php
/**
 * The Post Type Modifier
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

/**
 * Builds the Post Type w/ default ACF fields
 */
class Post_Type_Modifier {
	/**
	 * The cpt slug
	 *
	 * @var string $cpt_slug
	 */
	protected string $cpt_slug;

	/**
	 * Die if no ACF, else build the plugin.
	 *
	 * @param string $cpt_slug the Events CPT Slug / ID (defaults to "choctaw-events" for plugin compatibility)
	 */
	public function __construct( string $cpt_slug,  ) {
		$this->cpt_slug = $cpt_slug;
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