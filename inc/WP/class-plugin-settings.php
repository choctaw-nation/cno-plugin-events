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
			'post_type_slug'         => 'event',
			'post_type_label_single' => 'Event',
			'post_type_label_plural' => 'Events',
			'has_archive'            => false,
			'archive_slug'           => '',
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
}
