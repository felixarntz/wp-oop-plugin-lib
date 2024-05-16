<?php
/**
 * PHPUnit bootstrap file
 *
 * @package wp-oop-plugin-lib
 */

define( 'TESTS_WP_OOP_PLUGIN_LIB_DIR', dirname( dirname( __DIR__ ) ) );

require_once TESTS_WP_OOP_PLUGIN_LIB_DIR . '/vendor/autoload.php';

// Detect where to load the WordPress tests environment from.
if ( false !== getenv( 'WP_TESTS_DIR' ) ) {
	$_test_root = getenv( 'WP_TESTS_DIR' );
} elseif ( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
	$_test_root = getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit';
} elseif ( false !== getenv( 'WP_PHPUNIT__DIR' ) ) {
	$_test_root = getenv( 'WP_PHPUNIT__DIR' );
} else { // Fallback.
	$_test_root = '/tmp/wordpress-tests-lib';
}

// Force empty test plugin containing the library to be active.
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( basename( TESTS_WP_OOP_PLUGIN_LIB_DIR ) . '/wp-oop-plugin-lib.php' ),
);

// Start up the WP testing environment.
require $_test_root . '/includes/bootstrap.php';
