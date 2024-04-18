<?php
/**
 * Plugin Name: Choctaw Events Plugin
 * Plugin URI: https://github.com/choctaw-nation/cno-plugin-events
 * Description: Choctaw Events Plugin creates the Events and displays them in a nice way.
 * Version: 3.2.3
 * Author: Choctaw Nation of Oklahoma
 * Author URI: https://www.choctawnation.com
 * Text Domain: cno
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 8.1
 * Requires at least: 6.0
 * Requires Plugins: advanced-custom-fields-pro
 *
 * @package ChoctawNation
 * @subpackage Events
 */

use ChoctawNation\Events\Plugin_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/inc/class-plugin-loader.php';
$plugin_loader = new Plugin_Loader();

register_activation_hook(
	__FILE__,
	array( $plugin_loader, 'activate' )
);

register_deactivation_hook(
	__FILE__,
	array( $plugin_loader, 'deactivate' )
);
