<?php
/**
 * Admin screen and settings registration for Artist API.
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP\Admin;

use ChoctawNation\Events\Plugin_Loader;
use ChoctawNation\Events\WP\Plugin_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Screen
 *
 * Handles admin menu registration and settings fields.
 */
class Admin_Screen {
	/**
	 * Top-level menu slug.
	 *
	 * @var string
	 */
	public const MENU_PAGE_SLUG = 'cno-events-plugin';

	/**
	 * Settings API option group.
	 *
	 * @var string
	 */
	public const OPTION_GROUP = 'cno_events_settings_group';

	/**
	 * Settings API section id.
	 *
	 * @var string
	 */
	public const SECTION_ID = 'cno_events_main_section';

	/**
	 * Register admin menu and submenu pages.
	 *
	 * @return void
	 */
	public function register_menus(): void {
		add_menu_page(
			'Choctaw Events Plugin',
			'Choctaw Events Plugin',
			'manage_options',
			self::MENU_PAGE_SLUG,
			array( $this, 'redirect_parent_page_to_settings' ),
			'dashicons-calendar-alt',
			65
		);

		add_submenu_page(
			self::MENU_PAGE_SLUG,
			'Settings',
			'Settings',
			'manage_options',
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);

		remove_submenu_page( self::MENU_PAGE_SLUG, self::MENU_PAGE_SLUG );
	}

	/**
	 * Redirects the top-level parent page to the settings child page.
	 *
	 * @return void
	 */
	public function redirect_parent_page_to_settings(): void {
		wp_safe_redirect( $this->get_settings_page_url() );
		exit;
	}

	/**
	 * Registers plugin settings, section, and fields.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			Plugin_Settings::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( Plugin_Settings::class, 'sanitize_options' ),
				'default'           => Plugin_Settings::get_default_options(),
			)
		);
	}

	/**
	 * Enqueues admin assets conditionally on the plugin settings page.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( 'choctaw-events-plugin_page_cno-events-settings' !== $hook_suffix ) {
			return;
		}

		$root_dir    = dirname( __DIR__, 3 );
		$plugin_file = $root_dir . '/cno-plugin-events.php';
		$root_url    = plugin_dir_url( $plugin_file );
		$script_url  = $root_url . 'build/choctaw-events-admin.js';
		$asset_file  = file_exists( $root_dir . '/build/choctaw-events-admin.asset.php' ) ? require $root_dir . '/build/choctaw-events-admin.asset.php' : null;
		wp_enqueue_script( 'choctaw-events-admin', $script_url, $asset_file['dependencies'], $asset_file['version'], array( 'strategy' => 'defer' ) );
		wp_add_inline_script(
			'choctaw-events-admin',
			sprintf(
				'const cnoEventsAdmin = %s;',
				wp_json_encode(
					array(
						'apiNonce' => wp_create_nonce( 'wp_rest' ),
						'apiUrl'   => esc_url_raw( rest_url( 'cno-events/v1/settings' ) ),
						'settings' => Plugin_Settings::get_options(),
					)
				)
			),
			'before'
		);
	}

	/**
	 * Redirects admin user to plugin settings page after activation.
	 *
	 * @return void
	 */
	public function maybe_redirect_after_activation(): void {
		if ( ! $this->should_redirect_after_activation() ) {
			return;
		}

		delete_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION );

		wp_safe_redirect( $this->get_settings_page_url() );
		exit;
	}

	/**
	 * Determines whether activation redirect should occur.
	 *
	 * @return bool
	 */
	public function should_redirect_after_activation(): bool {
		$should_redirect = '1' === get_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION, '0' );
		if ( ! $should_redirect ) {
			return false;
		}
		if ( ! is_admin() || wp_doing_ajax() ) {
			return false;
		}
		if ( null !== filter_input( INPUT_GET, 'activate-multi', FILTER_DEFAULT ) ) {
			return false;
		}
		return current_user_can( 'manage_options' );
	}

	/**
	 * Returns settings page URL used after activation.
	 *
	 * @return string
	 */
	public function get_settings_page_url(): string {
		return add_query_arg(
			array(
				'page' => Plugin_Settings::SETTINGS_PAGE_SLUG,
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Renders the plugin settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		Plugin_Settings::initialize_options();
		echo '<div class="wrap"><h1>Choctaw Events Plugin Settings</h1>';
		settings_errors( Plugin_Settings::OPTION_KEY );

		// Render React mount point.
		echo '<div id="cno-plugin-events-admin-root"></div>';
		echo '</div>';
	}
}
