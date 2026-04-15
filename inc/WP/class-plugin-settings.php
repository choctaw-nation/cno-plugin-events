<?php
/**
 * Plugin Settings management for Choctaw Events Plugin.
 *
 * Provides a single structured WordPress option that stores credentials
 * for both production and staging environments, as well as the active
 * environment selector.
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages plugin settings stored as a single WordPress option.
 */
class Plugin_Settings {
	/**
	 * The WordPress option key used to store all plugin settings.
	 *
	 * @var string
	 */
	public const OPTION_KEY = 'cno_events_plugin_options';

	/**
	 * Admin page slug used for plugin settings.
	 *
	 * @var string
	 */
	public const SETTINGS_PAGE_SLUG = 'cno-events-settings';

	/**
	 * Returns default plugin options for initial setup.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_default_options(): array {
		return array(
			'post_type_is_enabled'   => false,
			'load_acf_fields'        => true,
			'enable_block_editor'    => true,
			'cron_time'              => '20:00',
			'post_type_slug'         => 'choctaw-event',
			'post_type_rewrite_slug' => 'event',
			'post_type_label_single' => 'Event',
			'post_type_label_plural' => 'Events',
			'has_archive'            => true,
			'archive_slug'           => 'events',
		);
	}

	/**
	 * Initializes the plugin options if they do not exist.
	 *
	 * @return array<string, mixed>
	 */
	public static function initialize_options(): array {
		$defaults = self::get_default_options();
		$current  = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$merged = array_merge( $defaults, $current );

		if ( $merged !== $current ) {
			update_option( self::OPTION_KEY, $merged, false );
		}

		return $merged;
	}

	/**
	 * Returns current options with defaults applied.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_options(): array {
		$options = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		return array_merge( self::get_default_options(), $options );
	}

	/**
	 * Determines whether a post type slug conflicts with an existing registration.
	 *
	 * @param string $slug Post type slug to validate.
	 * @return bool
	 */
	public static function is_post_type_slug_conflicting( string $slug ): bool {
		$normalized_slug = sanitize_title( $slug );

		if ( '' === $normalized_slug ) {
			return true;
		}

		return post_type_exists( $normalized_slug );
	}

	/**
	 * Returns the post type object currently using the provided slug.
	 *
	 * @param string $slug Post type slug.
	 * @return \WP_Post_Type|null
	 */
	public static function get_conflicting_post_type( string $slug ): ?\WP_Post_Type {
		$normalized_slug = sanitize_title( $slug );

		if ( '' === $normalized_slug || ! post_type_exists( $normalized_slug ) ) {
			return null;
		}

		$post_type_object = get_post_type_object( $normalized_slug );

		return ( $post_type_object instanceof \WP_Post_Type ) ? $post_type_object : null;
	}

	/**
	 * Sanitizes incoming settings values.
	 *
	 * @param mixed $input Incoming settings payload.
	 * @return array<string, mixed>
	 */
	public static function sanitize_options( mixed $input ): array {
		$defaults = self::get_default_options();
		$current  = self::get_options();

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		$sanitized = $defaults;

		$sanitized['post_type_is_enabled'] = ! empty( $input['post_type_is_enabled'] );
		$sanitized['load_acf_fields']      = ! empty( $input['load_acf_fields'] );
		$sanitized['enable_block_editor']  = ! empty( $input['enable_block_editor'] );
		$sanitized['cron_time']            = ! empty( $input['cron_time'] ) ? $input['cron_time'] : $defaults['cron_time'];

		$sanitized['post_type_slug']         = sanitize_title( (string) ( $input['post_type_slug'] ?? $defaults['post_type_slug'] ) );
		$sanitized['post_type_rewrite_slug'] = sanitize_title( (string) ( $input['post_type_rewrite_slug'] ?? $defaults['post_type_rewrite_slug'] ) );
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

		if ( self::is_post_type_slug_conflicting( $sanitized['post_type_slug'] ) && $sanitized['post_type_slug'] !== (string) $current['post_type_slug'] ) {
			$conflicting_post_type = self::get_conflicting_post_type( $sanitized['post_type_slug'] );

			add_settings_error(
				self::OPTION_KEY,
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
}
