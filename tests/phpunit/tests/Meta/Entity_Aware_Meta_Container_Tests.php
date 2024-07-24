<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group meta
 */
class Entity_Aware_Meta_Container_Tests extends Test_Case {

	private static $post_id;

	private $repository;
	private $container;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create();
	}

	public static function wpTearDownAfterClass() {
		wp_delete_post( self::$post_id, true );
	}

	public function set_up() {
		parent::set_up();
		$this->repository = new Meta_Repository( 'post' );

		$meta_container = new Meta_Container();
		$meta_container->set(
			'test_meta',
			function () {
				return new Meta_Key(
					$this->repository,
					'test_meta',
					array( 'type' => 'string' )
				);
			}
		);

		$this->container = new Entity_Aware_Meta_Container( $meta_container, self::$post_id );
	}

	public function test_has() {
		$this->assertTrue( $this->container->has( 'test_meta' ) );
		$this->assertFalse( $this->container->has( 'missing_meta' ) );
	}

	public function test_get_simple() {
		$meta_key = $this->container->get( 'test_meta' );
		$this->assertInstanceOf( Entity_Aware_Meta_Key::class, $meta_key );
		$this->assertSame( 'test_meta', $meta_key->get_key() );
		$this->assertSame( self::$post_id, $meta_key->get_entity_id() );
	}

	public function test_get_with_missing() {
		$this->expectException( Not_Found_Exception::class );
		$this->container->get( 'missing_meta' );
	}

	public function test_get_keys() {
		$this->assertSame(
			array( 'test_meta' ),
			$this->container->get_keys()
		);
	}

	public function test_offsetExists() {
		$this->assertSame( $this->container->has( 'test_meta' ), isset( $this->container['test_meta'] ) );
	}

	public function test_offsetGet() {
		$this->assertSame( $this->container->get( 'test_meta' ), $this->container['test_meta'] );
	}

	public function test_get_entity_id() {
		$this->assertSame( self::$post_id, $this->container->get_entity_id() );
	}
}
