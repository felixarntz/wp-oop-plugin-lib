<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Container
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group meta
 */
class Meta_Container_Tests extends Test_Case {

	private $repository;
	private $container;

	public function set_up() {
		parent::set_up();
		$this->repository = new Meta_Repository( 'post' );
		$this->container  = new Meta_Container();

		$this->container->set(
			'test_meta',
			function () {
				return new Meta_Key(
					$this->repository,
					'test_meta',
					array(
						'type'        => 'string',
						'description' => 'Test meta key',
						'default'     => 'test-value',
					)
				);
			}
		);
	}

	public function test_has() {
		$this->assertTrue( $this->container->has( 'test_meta' ) );
		$this->assertFalse( $this->container->has( 'missing_meta' ) );
	}

	public function test_get_simple() {
		$meta_key = $this->container->get( 'test_meta' );
		$this->assertInstanceOf( Meta_Key::class, $meta_key );
		$this->assertSame( 'test_meta', $meta_key->get_key() );
	}

	public function test_get_with_missing() {
		$this->expectException( Not_Found_Exception::class );
		$this->container->get( 'missing_meta' );
	}

	public function test_get_with_invalid_type() {
		$this->container->set(
			'invalid_type_meta',
			static function () {
				return new \stdClass();
			}
		);

		$this->expectException( Invalid_Type_Exception::class );
		$this->container->get( 'invalid_type_meta' );
	}

	public function test_set_simple() {
		$this->assertFalse( $this->container->has( 'integer_meta' ) );
		$this->container->set(
			'integer_meta',
			function () {
				return new Meta_Key(
					$this->repository,
					'integer_meta',
					array(
						'type'    => 'integer',
						'default' => 23,
					)
				);
			}
		);
		$this->assertTrue( $this->container->has( 'integer_meta' ) );
		$this->assertInstanceOf( Meta_Key::class, $this->container->get( 'integer_meta' ) );
	}

	public function test_set_with_override() {
		$this->assertTrue( $this->container->has( 'test_meta' ) );
		$this->assertSame( 'test-value', $this->container->get( 'test_meta' )->get_value( 1 ) );
		$this->container->set(
			'test_meta',
			function () {
				return new Meta_Key(
					$this->repository,
					'test_meta',
					array(
						'type'    => 'integer',
						'default' => 23,
					)
				);
			}
		);
		$this->assertSame( 23, $this->container->get( 'test_meta' )->get_value( 1 ) );
	}

	public function test_set_by_args() {
		$this->assertFalse( $this->container->has( 'integer_meta' ) );
		$this->container->set_by_args(
			'integer_meta',
			$this->repository,
			array(
				'type'    => 'integer',
				'default' => 23,
			)
		);
		$this->assertTrue( $this->container->has( 'integer_meta' ) );
		$this->assertInstanceOf( Meta_Key::class, $this->container->get( 'integer_meta' ) );
	}

	public function test_unset() {
		// Resolve the meta key prior to its removal.
		$this->container->get( 'test_meta' );

		// Remove it.
		$this->container->unset( 'test_meta' );
		$this->assertFalse( $this->container->has( 'test_meta' ) );

		// Ensure the already resolved instance was wiped as intended.
		$this->expectException( Not_Found_Exception::class );
		$this->container->get( 'test_meta' );
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

	public function test_offsetSet() {
		$this->assertFalse( $this->container->has( 'integer_meta' ) );
		$this->container['integer_meta'] = function () {
			return new Meta_Key(
				$this->repository,
				'integer_meta',
				array(
					'type'    => 'integer',
					'default' => 23,
				)
			);
		};
		$this->assertTrue( $this->container->has( 'integer_meta' ) );
	}

	public function test_offsetUnset() {
		unset( $this->container['test_meta'] );
		$this->assertFalse( $this->container->has( 'test_meta' ) );
	}

	public function test_create_entity_aware() {
		$entity_aware_container = $this->container->create_entity_aware( 1 );
		$this->assertSame( 1, $entity_aware_container->get_entity_id() );
		$this->assertSame( $this->container, $this->get_hidden_property_value( $entity_aware_container, 'wrapped_container' ) );
	}
}
