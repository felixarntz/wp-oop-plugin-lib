<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Key
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group meta
 */
class Entity_Aware_Meta_Key_Tests extends Test_Case {

	private static $post_id;

	private $repository;
	private $meta_key;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create();
	}

	public static function wpTearDownAfterClass() {
		wp_delete_post( self::$post_id, true );
	}

	public function set_up() {
		parent::set_up();
		$this->repository = new Meta_Repository( 'post' );

		$this->meta_key = new Entity_Aware_Meta_Key(
			new Meta_Key(
				$this->repository,
				'test_meta',
				array( 'type' => 'string' )
			),
			self::$post_id
		);
	}

	public function test_has_value() {
		$this->assertFalse( $this->meta_key->has_value() );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertTrue( $this->meta_key->has_value() );
	}

	public function test_get_value() {
		$this->assertSame( '', $this->meta_key->get_value() );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertSame( 'some-value', $this->meta_key->get_value() );
	}

	public function test_update_value() {
		$this->assertSame( '', get_post_meta( self::$post_id, 'test_meta', true ) );

		$this->meta_key->update_value( 'new-value' );
		$this->assertSame( 'new-value', get_post_meta( self::$post_id, 'test_meta', true ) );
	}

	public function test_delete_value() {
		update_post_meta( self::$post_id, 'test_meta', 'some-value' );

		$this->meta_key->delete_value();
		$this->assertSame( '', get_post_meta( self::$post_id, 'test_meta', true ) );
	}

	public function test_get_key() {
		$this->assertSame( 'test_meta', $this->meta_key->get_key() );
	}

	public function test_get_entity_id() {
		$this->assertSame( self::$post_id, $this->meta_key->get_entity_id() );
	}
}
