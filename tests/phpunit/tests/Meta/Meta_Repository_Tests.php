<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group meta
 */
class Meta_Repository_Tests extends Test_Case {

	private static $post_id;

	private $repository;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create();
	}

	public static function wpTearDownAfterClass() {
		wp_delete_post( self::$post_id, true );
	}

	public function set_up() {
		parent::set_up();
		$this->repository = new Meta_Repository( 'post' );
	}

	public function test_exists() {
		$this->assertFalse( $this->repository->exists( self::$post_id, 'test_meta' ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertTrue( $this->repository->exists( self::$post_id, 'test_meta' ) );
	}

	public function test_get_simple() {
		$this->assertNull( $this->repository->get( self::$post_id, 'test_meta' ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertSame( 'some-value', $this->repository->get( self::$post_id, 'test_meta' ) );
	}

	public function test_get_with_default() {
		$this->assertSame( 'default-value', $this->repository->get( self::$post_id, 'test_meta', 'default-value' ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertSame( 'some-value', $this->repository->get( self::$post_id, 'test_meta', 'default-value' ) );
	}

	public function test_get_without_single() {
		$this->repository->set_single( 'test_meta', false );

		$this->assertSame( array(), $this->repository->get( self::$post_id, 'test_meta' ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertSame( array( 'some-value' ), $this->repository->get( self::$post_id, 'test_meta' ) );
	}

	public function test_get_without_single_with_default() {
		$this->repository->set_single( 'test_meta', false );

		$this->assertSame( array( 'default-value' ), $this->repository->get( self::$post_id, 'test_meta', 'default-value' ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertSame( array( 'some-value' ), $this->repository->get( self::$post_id, 'test_meta', 'default-value' ) );
	}

	public function test_get_with_explicit_single() {
		/*
		 * True is also the default, so this technically shouldn't change anything in the behavior.
		 * Therefore this test is basically the same as test_get_simple().
		 */
		$this->repository->set_single( 'test_meta', true );

		$this->assertNull( $this->repository->get( self::$post_id, 'test_meta' ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertSame( 'some-value', $this->repository->get( self::$post_id, 'test_meta' ) );
	}

	public function test_update_simple() {
		$this->assertSame( '', get_post_meta( self::$post_id, 'test_meta', true ) );

		$this->assertTrue( $this->repository->update( self::$post_id, 'test_meta', 'some-value' ) );
		$this->assertSame( 'some-value', get_post_meta( self::$post_id, 'test_meta', true ) );
	}

	public function test_update_without_single_with_one_value() {
		$this->repository->set_single( 'test_meta', false );

		$this->assertSame( array(), get_post_meta( self::$post_id, 'test_meta' ) );
		$this->assertTrue( $this->repository->update( self::$post_id, 'test_meta', 'some-value' ) );
		$this->assertSame( array( 'some-value' ), get_post_meta( self::$post_id, 'test_meta' ) );
	}

	public function test_update_without_single_with_multiple_values() {
		$this->repository->set_single( 'test_meta', false );

		$this->assertSame( array(), get_post_meta( self::$post_id, 'test_meta' ) );
		$this->assertTrue( $this->repository->update( self::$post_id, 'test_meta', array( 'some-value-1', 'some-value-2', 'some-value-3' ) ) );
		$this->assertSame( array( 'some-value-1', 'some-value-2', 'some-value-3' ), get_post_meta( self::$post_id, 'test_meta' ) );
	}

	public function test_update_without_single_with_associative_array_value() {
		$this->repository->set_single( 'test_meta', false );

		$this->assertSame( array(), get_post_meta( self::$post_id, 'test_meta' ) );
		$this->assertTrue( $this->repository->update( self::$post_id, 'test_meta', array( 'test_key' => 'test_value' ) ) );
		$this->assertSame( array( array( 'test_key' => 'test_value' ) ), get_post_meta( self::$post_id, 'test_meta' ) );
	}

	public function test_update_without_single_with_indexed_array_value() {
		$this->repository->set_single( 'test_meta', false );

		$this->assertSame( array(), get_post_meta( self::$post_id, 'test_meta' ) );

		/*
		 * Storing indexed arrays in a multi-value meta key is an edge-case, and it currently requires the values to be
		 * wrapped into another array when calling the update() method.
		 */
		$this->assertTrue( $this->repository->update( self::$post_id, 'test_meta', array( array( 'some-value-1', 'some-value-2' ) ) ) );
		$this->assertSame( array( array( 'some-value-1', 'some-value-2' ) ), get_post_meta( self::$post_id, 'test_meta' ) );
	}

	public function test_delete() {
		update_post_meta( self::$post_id, 'test_meta', 'some-value' );

		$this->assertTrue( $this->repository->delete( self::$post_id, 'test_meta' ) );
		$this->assertSame( '', get_post_meta( self::$post_id, 'test_meta', true ) );
	}

	public function test_delete_all() {
		update_post_meta( self::$post_id, 'test_meta', 'some-value' );

		$this->assertTrue( $this->repository->delete_all( self::$post_id ) );
		$this->assertSame( array(), get_post_meta( self::$post_id ) );
	}

	public function test_prime_caches() {
		wp_cache_delete( self::$post_id, 'post_meta' );
		$this->assertTrue( $this->repository->prime_caches( array( self::$post_id ) ) );
		$this->assertNotFalse( wp_cache_get( self::$post_id, 'post_meta' ) );
	}

	public function test_get_and_set_single() {
		$this->assertTrue( $this->repository->get_single( 'test_meta' ) );

		$this->repository->set_single( 'test_meta', false );
		$this->assertFalse( $this->repository->get_single( 'test_meta' ) );

		$this->repository->set_single( 'test_meta', true );
		$this->assertTrue( $this->repository->get_single( 'test_meta' ) );
	}
}
