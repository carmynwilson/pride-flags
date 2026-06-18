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
 * Version:     1.1.0
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
define( 'PRIDE_FLAGS_VERSION', '1.1.0' );
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

// Include Plugin Update Checker by YahnisElsts
require PRIDE_FLAGS_PATH . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$prideFlagsUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/carmynwilson/pride-flags/',
	__FILE__,
	'pride-flags'
);

// Set the branch that contains the stable release.
$prideFlagsUpdateChecker->setBranch( 'main' );

// GitHub Personal Access Token (required while the repo is private).
$prideFlagsUpdateChecker->setAuthentication( 'github_pat_xxxxxx' );
