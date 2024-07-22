<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Input
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Input;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Input_Tests extends Test_Case {

	private $input;

	public function set_up() {
		parent::set_up();

		$this->input = new Input();
	}

	public function test_filter() {
		// The regular input handling is immutable, so this test simply verifies that (with two examples).
		$_GET['foo'] = 'bar';
		$this->assertNull( $this->input->filter( INPUT_GET, 'foo' ) );
		$this->assertFalse( $this->input->filter( INPUT_GET, 'foo', FILTER_DEFAULT, FILTER_NULL_ON_FAILURE ) );

		$_SERVER['bar'] = 'baz';
		$this->assertNull( $this->input->filter( INPUT_SERVER, 'bar' ) );
		$this->assertFalse( $this->input->filter( INPUT_SERVER, 'bar', FILTER_DEFAULT, FILTER_NULL_ON_FAILURE ) );
	}
}
