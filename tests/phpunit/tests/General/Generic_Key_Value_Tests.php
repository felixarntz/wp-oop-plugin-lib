<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Generic_Key_Value
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Generic_Key_Value;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Generic_Key_Value_Tests extends Test_Case {

	private $repository;

	public function set_up() {
		parent::set_up();

		$this->repository = new Array_Key_Value_Repository(
			array(
				'foo' => 'hello',
				'bar' => true,
				'baz' => 42,
			)
		);
	}

	/**
	 * @dataProvider data_has_value
	 */
	public function test_has_value( $key, $expected ) {
		$key_value = new Generic_Key_Value( $this->repository, $key );
		if ( $expected ) {
			$this->assertTrue( $key_value->has_value() );
		} else {
			$this->assertFalse( $key_value->has_value() );
		}
	}

	public function data_has_value() {
		return array(
			'foo' => array( 'foo', true ),
			'bar' => array( 'bar', true ),
			'baz' => array( 'baz', true ),
			'qux' => array( 'qux', false ),
		);
	}

	/**
	 * @dataProvider data_get_value
	 */
	public function test_get_value( $key, $default, $expected ) {
		$key_value = new Generic_Key_Value( $this->repository, $key, $default );
		$this->assertSame( $expected, $key_value->get_value() );
	}

	public function data_get_value() {
		return array(
			'foo'              => array( 'foo', null, 'hello' ),
			'bar'              => array( 'bar', null, true ),
			'baz'              => array( 'baz', null, 42 ),
			'baz with default' => array( 'baz', 23, 42 ),
			'qux'              => array( 'qux', null, null ),
			'qux with default' => array( 'qux', 'default', 'default' ),
		);
	}

	public function test_update_value() {
		$foo = new Generic_Key_Value( $this->repository, 'foo' );
		$this->assertTrue( $foo->update_value( 'world' ) );
		$this->assertSame( 'world', $this->repository->get( 'foo' ) );

		$qux = new Generic_Key_Value( $this->repository, 'qux' );
		$this->assertTrue( $qux->update_value( 3.5 ) );
		$this->assertSame( 3.5, $this->repository->get( 'qux' ) );
	}

	public function test_delete_value() {
		$foo = new Generic_Key_Value( $this->repository, 'foo' );
		$this->assertTrue( $foo->delete_value() );
		$this->assertFalse( $this->repository->exists( 'foo' ) );

		$qux = new Generic_Key_Value( $this->repository, 'qux' );
		$this->assertFalse( $qux->delete_value() );
	}

	/**
	 * @dataProvider data_get_key
	 */
	public function test_get_key( $key ) {
		$key_value = new Generic_Key_Value( $this->repository, $key );
		$this->assertSame( $key, $key_value->get_key() );
	}

	public function data_get_key() {
		return array(
			'foo' => array( 'foo' ),
			'bar' => array( 'bar' ),
			'baz' => array( 'baz' ),
			'qux' => array( 'qux' ),
		);
	}
}
