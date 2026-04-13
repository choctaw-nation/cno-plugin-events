<?php
/**
 * Plugin Loader
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

use ChoctawNation\Events\WP\Admin\Admin_Screen;
use ChoctawNation\Events\WP\Plugin_Settings;

/** The Plugin Loader */
final class Plugin_Loader {
	/**
	 * Option key used to mark that an activation redirect is needed.
	 *
	 * @var string
	 */
	public const ACTIVATION_REDIRECT_OPTION = 'cno_events_activation_redirect';

	// phpcs:ignore
	public function __construct(string $cpt_slug = 'choctaw-events', string $rewrite = 'events') {
		// add_action( 'init', array( $this, 'init_cpt' ) );
		// include_once __DIR__ . '/acf/classes/class-event-venue.php';
		// add_filter( 'template_include', array( $this, 'update_template_loader' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		// add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );
		// add_action( 'pre_get_posts', array( $this, 'custom_archive_query' ) );
	}

	/**
	 * Initializes the Plugin
	 *
	 * @return void
	 */
	public function activate(): void {
		Plugin_Settings::initialize_options();
		update_option( self::ACTIVATION_REDIRECT_OPTION, '1', false );

		// $this->init_cpt();
		flush_rewrite_rules();
	}

	/**
	 * Handles Plugin Deactivation
	 * (this is a callback function for the `register_deactivation_hook` function)
	 *
	 * @return void
	 */
	public function deactivate(): void {
		// $scripts = array( 'choctaw-events-add-to-calendar', 'choctaw-events-search' );
		// foreach ( $scripts as $script ) {
		// wp_deregister_script( $script );
		// }
		// $image_sizes = array( 'choctaw-events-preview', 'choctaw-events-single' );
		// foreach ( $image_sizes as $size ) {
		// remove_image_size( $size );
		// }
		// $post_types = array( $this->cpt_slug );
		// foreach ( $post_types as $post_type ) {
		// unregister_post_type( $post_type );
		// }
		// $taxonomies = array( 'choctaw-events-category', 'choctaw-events-venue' );
		// foreach ( $taxonomies as $taxonomy ) {
		// unregister_taxonomy( $taxonomy );
		// }
		flush_rewrite_rules();
	}

	/**
	 * Boots plugin runtime hooks and components.
	 *
	 * @return void
	 */
	public function load_plugin(): void {
		$admin_screen = new Admin_Screen();
		$admin_screen->register_hooks();
	}
}
