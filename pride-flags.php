<?php
/**
 * Pride Flags
 *
 * @package   pride_flags
 * @copyright Copyright (C) 2026, Carmyn Wilson
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Pride Flags
 * Version:     1.1.2
 * Plugin URI:  https://carmyn.me/?pride-flags
 * Description: A simple [pride flag=""] shortcode that renders pride & identity flags. Defaults to the Progress Pride flag. Includes a built-in flag library reference in the dashboard.
 * Author:      Carmyn Wilson
 * Author URI:  https://carmyn.me
 * Text Domain: pride-flags
 * License:     GPL v3
 * Requires at least: 6.0.0
 * Requires PHP: 8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'PRIDE_FLAGS_VERSION', '1.1.2' );
define( 'PRIDE_FLAGS_PATH', plugin_dir_path( __FILE__ ) );
define( 'PRIDE_FLAGS_URL', plugin_dir_url( __FILE__ ) );

// Do the plugin stuff
require_once PRIDE_FLAGS_PATH . 'inc/registry.php';
require_once PRIDE_FLAGS_PATH . 'inc/shortcode.php';
require_once PRIDE_FLAGS_PATH . 'inc/admin-library.php';

/**
 * Front-end styles for the [pride] shortcode. Registered always,
 * enqueued on demand by the shortcode so we never load CSS on pages
 * that don't use a flag.
 */
function pride_flags_register_assets() {
	wp_register_style(
		'pride-flags',
		PRIDE_FLAGS_URL . 'assets/css/pride-flags.css',
		[],
		PRIDE_FLAGS_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'pride_flags_register_assets' );

// Plugin updates from update.wizardofads.dev.
require PRIDE_FLAGS_PATH . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( ! function_exists( 'woa_updates_request' ) ) {
	function woa_updates_request( $args, $url ) {
		if ( 0 === strpos( $url, 'https://update.wizardofads.dev/' ) && is_array( $args['headers'] ?? array() ) ) {
			$args['headers']['X-Update-Site-Title'] = rawurlencode( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
			$key = get_option( 'woa_update_key' );
			if ( $key ) {
				$args['headers']['X-Update-Key'] = $key;
			} else {
				$pair = get_option( 'woa_update_pair' );
				if ( ! $pair ) {
					$pair = wp_generate_password( 40, false, false );
					update_option( 'woa_update_pair', $pair, false );
				}
				$args['headers']['X-Update-Pair'] = $pair;
			}
		}
		return $args;
	}
	add_filter( 'http_request_args', 'woa_updates_request', 10, 2 );

	function woa_updates_response( $response, $args, $url ) {
		if ( 0 === strpos( $url, 'https://update.wizardofads.dev/' ) && ! is_wp_error( $response ) && ! get_option( 'woa_update_key' ) ) {
			$key = wp_remote_retrieve_header( $response, 'x-update-key-issued' );
			if ( is_string( $key ) && preg_match( '/^[a-z0-9][a-z0-9-]{0,62}\.[A-Za-z0-9_-]{43}$/', $key ) ) {
				update_option( 'woa_update_key', $key, false );
				delete_option( 'woa_update_pair' );
			}
		}
		return $response;
	}
	add_filter( 'http_response', 'woa_updates_response', 10, 3 );
}

$prideFlagsUpdateChecker = PucFactory::buildUpdateChecker(
	'https://update.wizardofads.dev/v1/plugin/' . basename( __DIR__ ) . '/info.json',
	__FILE__,
	basename( __DIR__ )
);
