<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Registry
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Array_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();

		$this->registry = new Array_Registry(
			array(
				'foo' => array(
					'arg1'  => 'val1',
					'arg2'  => 'val2',
					'force' => true,
				),
				'bar' => array(
					'arg1'  => 'val3',
					'arg2'  => 'val4',
					'force' => false,
				),
			)
		);
	}

	public function test_register() {
		$this->assertTrue(
			$this->registry->register(
				'baz',
				array(
					'arg1' => 'val5',
					'arg2' => 'val6',
				)
			)
		);
		$this->assertTrue( $this->registry->is_registered( 'baz' ) );
	}

	public function test_is_registered() {
		$this->assertTrue( $this->registry->is_registered( 'foo' ) );
		$this->assertTrue( $this->registry->is_registered( 'bar' ) );
		$this->assertFalse( $this->registry->is_registered( 'baz' ) );
	}

	public function test_get_registered() {
		$foo = $this->registry->get_registered( 'foo' );
		$this->assertIsObject( $foo );
		$this->assertObjectHasProperty( 'arg1', $foo );
		$this->assertObjectHasProperty( 'arg2', $foo );
		$this->assertObjectHasProperty( 'force', $foo );
		$this->assertSame( 'val1', $foo->arg1 );
		$this->assertSame( 'val2', $foo->arg2 );
		$this->assertTrue( $foo->force );

		$bar = $this->registry->get_registered( 'bar' );
		$this->assertIsObject( $bar );
		$this->assertObjectHasProperty( 'arg1', $bar );
		$this->assertObjectHasProperty( 'arg2', $bar );
		$this->assertObjectHasProperty( 'force', $bar );
		$this->assertSame( 'val3', $bar->arg1 );
		$this->assertSame( 'val4', $bar->arg2 );
		$this->assertFalse( $bar->force );

		$this->assertNull( $this->registry->get_registered( 'baz' ) );
	}

	public function test_get_all_registered() {
		$items = $this->registry->get_all_registered();
		$this->assertIsArray( $items );
		$this->assertCount( 2, $items );
		$this->assertArrayHasKey( 'foo', $items );
		$this->assertArrayHasKey( 'bar', $items );
		$this->assertIsObject( $items['foo'] );
		$this->assertIsObject( $items['bar'] );
		$this->assertSame(
			array(
				'arg1'  => 'val1',
				'arg2'  => 'val2',
				'force' => true,
			),
			(array) $items['foo']
		);
		$this->assertSame(
			array(
				'arg1'  => 'val3',
				'arg2'  => 'val4',
				'force' => false,
			),
			(array) $items['bar']
		);
	}

	public function test_to_array() {
		$this->assertSame( $this->registry->get_all_registered(), $this->registry->to_array() );
	}

	public function test_offsetExists() {
		$this->assertTrue( isset( $this->registry['foo'] ) );
		$this->assertTrue( isset( $this->registry['bar'] ) );
		$this->assertFalse( isset( $this->registry['baz'] ) );
	}

	public function test_offsetGet() {
		$this->assertSame( $this->registry->get_registered( 'foo' ), $this->registry['foo'] );
		$this->assertSame( $this->registry->get_registered( 'bar' ), $this->registry['bar'] );
		$this->assertNull( $this->registry['baz'] );
	}

	public function test_offsetSet() {
		$this->registry['baz'] = array(
			'arg1' => 'val5',
			'arg2' => 'val6',
		);
		$this->assertTrue( $this->registry->is_registered( 'baz' ) );
	}
}
