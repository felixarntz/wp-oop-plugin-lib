<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Mutable_Input
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Mutable_Input;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Mutable_Input_Tests extends Test_Case {

	private $input;

	public function set_up() {
		parent::set_up();

		$this->input = new Mutable_Input();
	}

	public function test_filter() {
		// Other than in the Input class, the Mutable_Input class allows modifying the input values via superglobals.
		$_GET['foo'] = 'bar';
		$this->assertSame( 'bar', $this->input->filter( INPUT_GET, 'foo' ) );

		$_SERVER['bar'] = 'baz';
		$this->assertSame( 'baz', $this->input->filter( INPUT_SERVER, 'bar' ) );
	}
}
