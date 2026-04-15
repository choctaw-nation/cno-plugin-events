<?php
/**
 * Settings REST Router
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP\Admin;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use ChoctawNation\Events\WP\Plugin_Settings;

/**
 * Registers REST API routes for plugin settings management.
 */
class Settings_Rest_Router extends WP_REST_Controller {
	/**
	 * Registers REST API routes for plugin settings.
	 */
	public function register_routes() {
		$namespace = 'cno-events/v1';
		$route     = '/settings';

		register_rest_route(
			$namespace,
			$route,
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => fn() => current_user_can( 'manage_options' ),
				),
			)
		);
	}

	/**
	 * Returns current plugin settings.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response
	 */
	public function get_settings( WP_REST_Request $request ): WP_REST_Response {
		Plugin_Settings::initialize_options();
		return rest_ensure_response( Plugin_Settings::get_options() );
	}

	/**
	 * Updates plugin settings.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response
	 */
	public function update_settings( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params();
		if ( ! is_array( $params ) ) {
			return rest_ensure_response( Plugin_Settings::get_options() );
		}

		// Reuse sanitization to validate incoming payload.
		$sanitized = Plugin_Settings::sanitize_options( $params );

		update_option( Plugin_Settings::OPTION_KEY, $sanitized, false );

		return rest_ensure_response( $sanitized );
	}
}
