<?php
/**
 * Plugin Loader
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

use ChoctawNation\Events\WP\Admin\Admin_Screen;
use ChoctawNation\Events\WP\Admin\Settings_Rest_Router;
use ChoctawNation\Events\WP\Plugin_Settings;

/** The Plugin Loader */
final class Plugin_Loader {
	/**
	 * Option key used to mark that an activation redirect is needed.
	 *
	 * @var string
	 */
	public const ACTIVATION_REDIRECT_OPTION = 'cno_events_activation_redirect';

	/**
	 * Initializes the Plugin
	 *
	 * @return void
	 */
	public function activate(): void {
		Plugin_Settings::initialize_options();
		update_option( self::ACTIVATION_REDIRECT_OPTION, '1', false );

	}

	/**
	 * Handles Plugin Deactivation
	 * (this is a callback function for the `register_deactivation_hook` function)
	 *
	 * @return void
	 */
	public function deactivate(): void {
		$options = Plugin_Settings::get_options();
		if ( ! $options['post_type_is_enabled'] ) {
			return;
		}
		unregister_post_type( $options['post_type_slug'] );
		$scripts = array( 'choctaw-events-add-to-calendar', 'choctaw-events-search' );
		foreach ( $scripts as $script ) {
			wp_deregister_script( $script );
		}

		$taxonomies = array( 'choctaw-events-category', 'choctaw-events-venue' );
		foreach ( $taxonomies as $taxonomy ) {
			unregister_taxonomy( $taxonomy );
		}
		flush_rewrite_rules();
	}

	/**
	 * Boots plugin runtime hooks and components.
	 *
	 * @return void
	 */
	public function load_plugin(): void {
		$options = Plugin_Settings::get_options();
		$this->load_admin_screen();
		if ( ! $options['post_type_is_enabled'] ) {
			return;
		}
		$this->init_cpt( $options );
		if ( $options['load_acf_fields'] ) {
			$this->load_acf_fields( $options['post_type_slug'] );
			$this->load_admin_columns( $options['post_type_slug'], false );
			$scheduler = new WP\Scheduler( new Jobs\Event_Handler( $options['post_type_slug'] ) );
			$scheduler->schedule_event_expiry( $options['cron_time'] );
		}
	}

	/**
	 * Loads the Admin Screen component and registers its hooks.
	 */
	private function load_admin_screen(): void {
		$router = new Settings_Rest_Router();
		add_action( 'rest_api_init', array( $router, 'register_routes' ) );
		$admin_screen = new Admin_Screen();
		add_action( 'admin_menu', array( $admin_screen, 'register_menus' ) );
		add_action( 'admin_init', array( $admin_screen, 'register_settings' ) );
		add_action( 'admin_init', array( $admin_screen, 'maybe_redirect_after_activation' ) );
		add_action( 'admin_enqueue_scripts', array( $admin_screen, 'enqueue_assets' ) );
	}

	/**
	 * Initializes the CPT if enabled in settings.
	 *
	 * @param array<string, mixed> $options The plugin options to use for CPT initialization.
	 */
	private function init_cpt( array $options ): void {
		$cpt = new WP\CPT\Post_Type_Creator( $options );
		add_action( 'init', array( $cpt, 'load_cpt' ) );
		if ( $options['load_acf_fields'] ) {
			$modifier = new Post_Type_Modifier( $options['post_type_slug'] );
			add_action( 'pre_get_posts', array( $modifier, 'custom_archive_query' ) );
			add_action( 'pre_get_posts', array( $modifier, 'include_choctaw_events_post_type_in_search' ) );
		}
	}

	/**
	 * Loads ACF fields if ACF is active.
	 *
	 * @param string $slug The slug of the CPT to associate ACF fields with.
	 */
	private function load_acf_fields( string $slug ): void {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			$acf_handler = new Custom_Fields( $slug );
			$acf_handler->init_default_fields();
		}
	}

	/**
	 * Initializes taxonomies for the CPT (not implemented yet).
	 */
	private function init_taxonomies(): void {
		_doing_it_wrong( __METHOD__, 'Not Implemented yet', '1.0.0' );
	}

	/**
	 * Loads Admin Columns if ACF is active.
	 *
	 * @param string $slug The slug of the CPT to associate Admin Columns with.
	 * @param bool   $load_taxonomies Whether to load taxonomy columns as well.
	 */
	private function load_admin_columns( string $slug, bool $load_taxonomies ): void {
		$admin_columns = new WP\Admin\Admin_Columns( $slug, $load_taxonomies );
		add_action( 'admin_init', array( $admin_columns, 'init' ) );
	}
}
