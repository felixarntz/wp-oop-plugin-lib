<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Capability_Tests extends Test_Case {

	public function test_set_required_caps() {
		$modified_caps = array( 'manage_options', 'edit_published_posts' );

		$cap = new Capability( 'custom_cap', array( 'edit_posts' ) );
		$cap->set_required_caps( $modified_caps );

		$this->assertSameSets( $modified_caps, $cap->get_required_caps() );
	}

	public function test_get_required_caps() {
		$initial_caps = array( 'edit_theme_options' );

		$cap = new Capability( 'custom_cap', $initial_caps );

		$this->assertSameSets( $initial_caps, $cap->get_required_caps() );
	}

	public function test_get_key() {
		$cap = new Capability( 'my_custom_cap', array( 'manage_options' ) );
		$this->assertSame( 'my_custom_cap', $cap->get_key() );
	}
}
