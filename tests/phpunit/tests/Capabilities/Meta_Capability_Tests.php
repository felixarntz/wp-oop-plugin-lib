<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group capabilities
 */
class Meta_Capability_Tests extends Test_Case {

	public function test_set_map_callback() {
		$modified_caps = array( 'manage_options', 'edit_published_posts' );

		$cap = new Meta_Capability( 'custom_cap', static function () {
			return array( 'edit_posts' );
		} );
		$cap->set_map_callback( static function () use ( $modified_caps) {
			return $modified_caps;
		} );

		$map_callback = $cap->get_map_callback();
		$this->assertSameSets( $modified_caps, $map_callback() );
	}

	public function test_get_map_callback() {
		$initial_caps = array( 'edit_theme_options' );

		$cap = new Meta_Capability( 'custom_cap', static function () use ( $initial_caps ) {
			return $initial_caps;
		} );

		$map_callback = $cap->get_map_callback();
		$this->assertSameSets( $initial_caps, $map_callback() );
	}

	public function test_get_key() {
		$cap = new Meta_Capability( 'my_custom_cap', static function () {
			return 'manage_options';
		} );
		$this->assertSame( 'my_custom_cap', $cap->get_key() );
	}
}
