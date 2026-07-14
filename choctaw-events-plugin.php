<?php
/**
 * Plugin Name: [Choctaw Landing] Choctaw Events Plugin
 * Plugin URI: https://github.com/choctaw-nation/cno-plugin-events
 * Description: Choctaw Events Plugin creates the Events and displays them in a nice way.
 * Version: 5.0.0
 * Author: Choctaw Nation of Oklahoma
 * Author URI: https://www.choctawnation.com
 * Text Domain: cno
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 8.2
 * Requires at least: 6.7.0
 * Requires Plugins: advanced-custom-fields-pro
 * Tested up to: 7.0.1
 *
 * @package ChoctawNation
 * @subpackage Events
 */

use ChoctawNation\Events\Plugin_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$cno_autoload_path = __DIR__ . '/vendor/autoload.php';

if ( ! file_exists( $cno_autoload_path ) ) {
	add_action(
		'admin_notices',
		static function () {
			echo '<div class="notice notice-error"><p>Choctaw Events Plugin is missing required dependencies. Please run Composer install or deploy the plugin with its vendor directory included.</p></div>';
		}
	);

	return;
}

require_once $cno_autoload_path;
$cno_plugin = new Plugin_Loader();

// Plugin Lifecycle Hooks
register_activation_hook( __FILE__, array( $cno_plugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( $cno_plugin, 'deactivate' ) );

// Load the Plugin
add_action( 'plugins_loaded', array( $cno_plugin, 'load_plugin' ) );
