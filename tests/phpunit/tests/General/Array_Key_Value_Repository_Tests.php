<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Key_Value_Repository
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Array_Key_Value_Repository_Tests extends Test_Case {

	private $repository;

	public function set_up() {
		parent::set_up();

		$this->repository = new Array_Key_Value_Repository(
			array(
				'foo' => 'bar',
				'baz' => 42,
			)
		);
	}

	public function test_exists() {
		$this->assertTrue( $this->repository->exists( 'foo' ) );
		$this->assertTrue( $this->repository->exists( 'baz' ) );
		$this->assertFalse( $this->repository->exists( 'qux' ) );
	}

	public function test_get() {
		$this->assertSame( 'bar', $this->repository->get( 'foo' ) );
		$this->assertSame( 42, $this->repository->get( 'baz' ) );
		$this->assertNull( $this->repository->get( 'qux' ) );
	}

	public function test_update() {
		$this->repository->update( 'foo', 'qux' );
		$this->assertSame( 'qux', $this->repository->get( 'foo' ) );
	}

	public function test_delete() {
		$this->repository->delete( 'foo' );
		$this->assertFalse( $this->repository->exists( 'foo' ) );
		$this->assertNull( $this->repository->get( 'foo' ) );
	}
}
