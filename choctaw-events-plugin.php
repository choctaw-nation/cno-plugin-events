<?php
/**
 * Plugin Name: Choctaw Events Plugin
 * Plugin URI: https://github.com/choctaw-nation/cno-plugin-events
 * Description: Choctaw Events Plugin creates the Events and displays them in a nice way.
 * Version: 3.2.2
 * Author: Choctaw Nation of Oklahoma
 * Author URI: https://www.choctawnation.com
 * Text Domain: cno
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 8.1
 * Requires at least: 6.0
 * Requires Plugins: acf
 *
 * @package ChoctawNation
 * @subpackage Events
 */

use ChoctawNation\Events\Plugin_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

register_activation_hook(
	__FILE__,
	function (): void {
		require_once __DIR__ . '/inc/class-plugin-loader.php';
		$plugin_loader = new Plugin_Loader();
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	function (): void {
		// deregister scripts
		// deregister post types
		// deregister taxonomies
		// deregister image sizes
		flush_rewrite_rules();
	}
);
