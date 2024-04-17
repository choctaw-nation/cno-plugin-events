<?php
/**
 * Plugin Name: Choctaw Events Plugin
 * Plugin URI: https://github.com/choctaw-nation/cno-plugin-events
 * Description: Choctaw Events Plugin creates the Events and displays them in a nice way.
 * Version: 3.2.1
 * Author: Choctaw Nation of Oklahoma
 * Author URI: https://www.choctawnation.com
 * Text Domain: cno
 * License: GPLv3 or later
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
