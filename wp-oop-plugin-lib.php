<?php
/**
 * Plugin Name: WP OOP Plugin Lib Demo Plugin
 * Plugin URI: https://github.com/felixarntz/wp-oop-plugin-lib
 * Description: Empty plugin which just loads the library files.
 * Requires at least: 6.0
 * Requires PHP: 7.2
 * Version: 1.0.0
 * Author: Felix Arntz
 * Author URI: https://felix-arntz.me
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: wp-oop-plugin-lib
 *
 * @package wp-oop-plugin-lib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the plugin autoloader.
 *
 * @since 1.0.0
 *
 * @return bool True on success, false on failure.
 */
function wp_oop_plugin_lib_register_autoloader() {
	static $registered = null;

	// Prevent multiple executions.
	if ( null !== $registered ) {
		return $registered;
	}

	// Check for the Composer autoloader.
	$autoload_file = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	if ( file_exists( $autoload_file ) ) {
		require_once $autoload_file;

		$registered = true;
		return true;
	}

	// Otherwise, the autoloader is missing.
	$registered = false;
	return false;
}

wp_oop_plugin_lib_register_autoloader();
