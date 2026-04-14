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
	 * Registers admin hooks for settings UI and activation redirect.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_after_activation' ) );
	}

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
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => Plugin_Settings::get_default_options(),
			)
		);

		add_settings_field(
			'post_type_is_enabled',
			esc_html( 'Enable Post Type' ),
			array( $this, 'render_post_type_is_enabled_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);

		add_settings_field(
			'load_acf_fields',
			esc_html( 'Enable ACF Fields' ),
			array( $this, 'render_load_acf_fields_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);

		add_settings_section(
			self::SECTION_ID,
			esc_html( 'Post Type Configuration' ),
			array( $this, 'render_section_description' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG
		);

		add_settings_field(
			'post_type_slug',
			esc_html( 'Post Type Slug' ),
			array( $this, 'render_post_type_slug_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);

		add_settings_field(
			'post_type_label_single',
			esc_html( 'Single Label' ),
			array( $this, 'render_post_type_label_single_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);

		add_settings_field(
			'post_type_label_plural',
			esc_html( 'Plural Label' ),
			array( $this, 'render_post_type_label_plural_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);

		add_settings_field(
			'has_archive',
			esc_html( 'Enable Archive' ),
			array( $this, 'render_has_archive_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);

		add_settings_field(
			'archive_slug',
			esc_html( 'Archive Slug (Optional)' ),
			array( $this, 'render_archive_slug_field' ),
			Plugin_Settings::SETTINGS_PAGE_SLUG,
			self::SECTION_ID
		);
	}

	/**
	 * Sanitizes incoming settings values.
	 *
	 * @param mixed $input Incoming settings payload.
	 * @return array<string, mixed>
	 */
	public function sanitize_options( mixed $input ): array {
		$defaults = Plugin_Settings::get_default_options();
		$current  = Plugin_Settings::get_options();

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		$sanitized = $defaults;

		$sanitized['post_type_is_enabled']   = ! empty( $input['post_type_is_enabled'] );
		$sanitized['load_acf_fields']        = ! empty( $input['load_acf_fields'] );
		$sanitized['post_type_slug']         = sanitize_title( (string) ( $input['post_type_slug'] ?? $defaults['post_type_slug'] ) );
		$sanitized['post_type_label_single'] = sanitize_text_field( (string) ( $input['post_type_label_single'] ?? $defaults['post_type_label_single'] ) );
		$sanitized['post_type_label_plural'] = sanitize_text_field( (string) ( $input['post_type_label_plural'] ?? $defaults['post_type_label_plural'] ) );
		$sanitized['has_archive']            = ! empty( $input['has_archive'] );
		$archive_slug                        = sanitize_title( (string) ( $input['archive_slug'] ?? '' ) );
		$sanitized['archive_slug']           = $sanitized['has_archive'] ? $archive_slug : '';

		if ( '' === $sanitized['post_type_slug'] ) {
			$sanitized['post_type_slug'] = $defaults['post_type_slug'];
		}

		if ( '' === $sanitized['post_type_label_single'] ) {
			$sanitized['post_type_label_single'] = $defaults['post_type_label_single'];
		}

		if ( '' === $sanitized['post_type_label_plural'] ) {
			$sanitized['post_type_label_plural'] = $defaults['post_type_label_plural'];
		}

		if ( Plugin_Settings::is_post_type_slug_conflicting( $sanitized['post_type_slug'] ) && $sanitized['post_type_slug'] !== (string) $current['post_type_slug'] ) {
			$conflicting_post_type = Plugin_Settings::get_conflicting_post_type( $sanitized['post_type_slug'] );

			add_settings_error(
				Plugin_Settings::OPTION_KEY,
				'cno_events_slug_conflict',
				sprintf(
					esc_html( 'Cannot save post type slug "%1$s" because it conflicts with existing post type "%2$s".' ),
					esc_html( $sanitized['post_type_slug'] ),
					esc_html( $conflicting_post_type?->labels->name ?? $sanitized['post_type_slug'] )
				),
				'error'
			);

			$sanitized['post_type_slug'] = (string) $current['post_type_slug'];
		}

		return $sanitized;
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
		$options = Plugin_Settings::get_options();

		echo '<div class="wrap"><h1>Choctaw Events Settings</h1>';

		if ( Plugin_Settings::is_post_type_slug_conflicting( (string) $options['post_type_slug'] ) ) {
			$conflicting_post_type = Plugin_Settings::get_conflicting_post_type( (string) $options['post_type_slug'] );
			echo '<div class="notice notice-warning"><p>';
			printf(
				esc_html( 'Warning: configured post type slug "%1$s" conflicts with existing post type "%2$s". Please choose a different slug before enabling registration.' ),
				esc_html( (string) $options['post_type_slug'] ),
				esc_html( $conflicting_post_type?->labels->name ?? (string) $options['post_type_slug'] )
			);
			echo '</p></div>';
		}

		settings_errors( Plugin_Settings::OPTION_KEY );
		echo '<form action="options.php" method="post">';
		settings_fields( self::OPTION_GROUP );
		do_settings_sections( Plugin_Settings::SETTINGS_PAGE_SLUG );
		submit_button( esc_html( 'Save Settings' ) );
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Renders section description text.
	 *
	 * @return void
	 */
	public function render_section_description(): void {
		echo '<p>' . esc_html( 'Configure the Events post type defaults. Changes are saved in plugin options.' ) . '</p>';
	}

	/**
	 * Renders post type slug setting field.
	 *
	 * @return void
	 */
	public function render_post_type_slug_field(): void {
		$options = Plugin_Settings::get_options();
		$this->render_text_input( 'post_type_slug', (string) $options['post_type_slug'], 'event' );
	}

	/**
	 * Renders single label setting field.
	 *
	 * @return void
	 */
	public function render_post_type_label_single_field(): void {
		$options = Plugin_Settings::get_options();
		$this->render_text_input( 'post_type_label_single', (string) $options['post_type_label_single'], 'Event' );
	}

	/**
	 * Renders plural label setting field.
	 *
	 * @return void
	 */
	public function render_post_type_label_plural_field(): void {
		$options = Plugin_Settings::get_options();
		$this->render_text_input( 'post_type_label_plural', (string) $options['post_type_label_plural'], 'Events' );
	}

	/**
	 * Renders has_archive checkbox field.
	 *
	 * @return void
	 */
	public function render_has_archive_field(): void {
		$options = Plugin_Settings::get_options();
		$checked = ! empty( $options['has_archive'] );

		echo '<label>';
		echo '<input type="checkbox" name="' . esc_attr( Plugin_Settings::OPTION_KEY ) . '[has_archive]" value="1" ' . checked( $checked, true, false ) . ' /> ';
		echo esc_html( 'Enable archive page for this post type' );
		echo '</label>';
	}

	/**
	 * Renders has_archive checkbox field.
	 *
	 * @return void
	 */
	public function render_post_type_is_enabled_field(): void {
		$options = Plugin_Settings::get_options();
		$checked = ! empty( $options['post_type_is_enabled'] );

		echo '<label>';
		echo '<input type="checkbox" name="' . esc_attr( Plugin_Settings::OPTION_KEY ) . '[post_type_is_enabled]" value="1" ' . checked( $checked, true, false ) . ' /> ';
		echo esc_html( 'Enable this post type' );
		echo '</label>';
	}

	/**
	 * Renders load_acf_fields checkbox field.
	 *
	 * @return void
	 */
	public function render_load_acf_fields_field(): void {
		$options = Plugin_Settings::get_options();
		$checked = ! empty( $options['load_acf_fields'] );
		echo '<label>';
		echo '<input type="checkbox" name="' . esc_attr( Plugin_Settings::OPTION_KEY ) . '[load_acf_fields]" value="1" ' . checked( $checked, true, false ) . ' /> ';
		echo esc_html( 'Enable ACF Fields' );
		echo '</label>';
	}

	/**
	 * Renders archive slug setting field.
	 *
	 * @return void
	 */
	public function render_archive_slug_field(): void {
		$options = Plugin_Settings::get_options();
		$this->render_text_input( 'archive_slug', (string) $options['archive_slug'], 'events' );
		echo '<p class="description">' . esc_html( 'Leave blank to use WordPress default archive behavior.' ) . '</p>';
	}

	/**
	 * Renders a text input field for plugin options.
	 *
	 * @param string $field_key Option array key.
	 * @param string $value Current value.
	 * @param string $placeholder Input placeholder.
	 * @return void
	 */
	private function render_text_input( string $field_key, string $value, string $placeholder ): void {
		echo '<input type="text" class="regular-text" name="' . esc_attr( Plugin_Settings::OPTION_KEY ) . '[' . esc_attr( $field_key ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
	}
}