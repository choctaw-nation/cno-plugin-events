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


/**
 * Generates an `<a>` with or without a `target` attribute depending on the presence of the HTTP prefix in the string. Also, escapes both the text and url.
 *
 * @param string $url the internal/external url
 * @param string $text the text between the anchor tags.
 * @return string the HTML
 */
function cno_create_external_link( string $url, string $text ):string {
	$url          = esc_url( $url );
	$is_external  = strpos( $url, 'http://' ) === 0 || strpos( $url, 'https://' ) === 0;
	$markup_start = '<a href="' . $url . '" class="cno-event__learn-more"' . ( $is_external ? 'target="_blank" rel="noopener noreferrer">' : '>' );
	return $markup_start . esc_textarea( $text ) . '</a>';
}