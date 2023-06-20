<?php
/**
 * Plugin Name: CNO Events
 * Description: A simple events plugin.
 * Author: Choctaw Nation of Oklahoma
 * Author URI: https://choctawnation.com
 * Version: 1.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Text Domain: cno
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require plugin_dir_path( __FILE__ ) . '/php/class-cno-events-plugin.php';

$cno_events_plugin = new CNO_Events_Plugin();