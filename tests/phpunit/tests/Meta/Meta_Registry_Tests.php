<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group meta
 */
class Meta_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();
		$this->registry = new Meta_Registry( 'post' );
	}

	public function test_register() {
		$this->assertFalse( registered_meta_key_exists( 'post', 'test_meta_key' ) );

		$this->registry->register( 'test_meta_key', array(
			'type'        => 'string',
			'description' => 'Test meta key',
			'default'     => 'default-value',
		) );

		$this->assertTrue( registered_meta_key_exists( 'post', 'test_meta_key' ) );
	}

	public function test_is_registered() {
		$this->assertFalse( $this->registry->is_registered( 'test_meta_key' ) );

		register_meta( 'post', 'test_meta_key', array(
			'type'        => 'string',
			'description' => 'Test meta key',
			'default'     => 'default-value',
		) );

		$this->assertTrue( $this->registry->is_registered( 'test_meta_key' ) );
	}

	public function test_get_registered() {
		$this->assertNull( $this->registry->get_registered( 'test_meta_key' ) );

		register_meta( 'post', 'test_meta_key', array(
			'type'        => 'string',
			'description' => 'Test meta key',
			'default'     => 'default-value',
		) );

		$meta_key = $this->registry->get_registered( 'test_meta_key' );
		$this->assertInstanceOf( \stdClass::class, $meta_key );
		$this->assertObjectHasProperty( 'type', $meta_key );
		$this->assertObjectHasProperty( 'description', $meta_key );
		$this->assertObjectHasProperty( 'default', $meta_key );
		$this->assertSame( 'string', $meta_key->type );
		$this->assertSame( 'Test meta key', $meta_key->description );
		$this->assertSame( 'default-value', $meta_key->default );
	}

	public function test_get_all_registered() {
		$this->assertSame( get_registered_meta_keys( 'post' ), $this->registry->get_all_registered() );
	}
}
