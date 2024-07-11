<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Base_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Capability_Container_Tests extends Test_Case {

	private $container;

	public function set_up() {
		parent::set_up();
		$this->container = new Capability_Container();
		$this->container->set(
			'demo_base_cap',
			static function () {
				return new Base_Capability( 'demo_base_cap', array( 'manage_options' ) );
			}
		);
		$this->container->set(
			'demo_meta_cap',
			static function () {
				return new Meta_Capability( 'demo_meta_cap', static function () {
					return array( 'demo_base_cap' );
				} );
			}
		);
		$this->container->set(
			'invalid_type_cap',
			static function () {
				return new \stdClass();
			}
		);
	}

	/**
	 * @dataProvider data_has
	 */
	public function test_has( $cap, $expected ) {
		if ( $expected ) {
			$this->assertTrue( $this->container->has( $cap ) );
		} else {
			$this->assertFalse( $this->container->has( $cap ) );
		}
	}

	public function data_has() {
		return array(
			'non existent cap'                 => array( 'non_existent_cap', false ),
			'existing cap using invalid class' => array( 'invalid_type_cap', true ),
			'existing demo base cap'           => array( 'demo_base_cap', true ),
			'existing demo meta cap'           => array( 'demo_meta_cap', true ),
		);
	}

	/**
	 * @dataProvider data_get
	 */
	public function test_get( $cap, $expected_class = null ) {
		if ( is_subclass_of( $expected_class, \Exception::class ) ) {
			$this->expectException( $expected_class );
		}
		$result = $this->container->get( $cap );
		$this->assertInstanceOf( $expected_class, $result );
		$this->assertSame( $cap, $result->get_key() );
	}

	public function data_get() {
		return array(
			'non existent cap'                 => array( 'non_existent_cap', Not_Found_Exception::class ),
			'existing cap using invalid class' => array( 'invalid_type_cap', Invalid_Type_Exception::class ),
			'existing demo base cap'           => array( 'demo_base_cap', Base_Capability::class ),
			'existing demo meta cap'           => array( 'demo_meta_cap', Meta_Capability::class ),
		);
	}

	/**
	 * @dataProvider data_set
	 */
	public function test_set( $cap, $creator, $expected_required_caps ) {
		$this->container->set( $cap, $creator );

		$result = $this->container->get( $cap );
		$this->assertSame( $cap, $result->get_key() );
		if ( $result instanceof Meta_Capability ) {
			$map_callback = $result->get_map_callback();
			$this->assertSameSets( $expected_required_caps, $map_callback() );
		} else {
			$this->assertSameSets( $expected_required_caps, $result->get_required_caps() );
		}
	}

	public function data_set() {
		return array(
			'new cap to set'                     => array(
				'new_cap',
				static function () {
					return new Base_Capability( 'new_cap', array( 'manage_categories', 'assign_categories' ) );
				},
				array( 'manage_categories', 'assign_categories' )
			),
			'existing demo base cap to override' => array(
				'demo_base_cap',
				static function () {
					return new Base_Capability( 'demo_base_cap', array( 'edit_posts' ) );
				},
				array( 'edit_posts' )
			),
			'existing demo meta cap to override' => array(
				'demo_meta_cap',
				static function () {
					return new Meta_Capability( 'demo_meta_cap', static function () {
						return array( 'edit_posts' );
					} );
				},
				array( 'edit_posts' )
			),
		);
	}

	/**
	 * @dataProvider data_set_by_args
	 */
	public function test_set_by_args( $cap, $required_caps, $expected_required_caps ) {
		$this->container->set_by_args( $cap, $expected_required_caps );

		$result = $this->container->get( $cap );
		$this->assertSame( $cap, $result->get_key() );
		if ( $result instanceof Meta_Capability ) {
			$map_callback = $result->get_map_callback();
			$this->assertSameSets( $expected_required_caps, $map_callback() );
		} else {
			$this->assertSameSets( $expected_required_caps, $result->get_required_caps() );
		}
	}

	public function data_set_by_args() {
		return array(
			'new cap to set'                     => array(
				'new_cap',
				array( 'edit_theme_options' ),
				array( 'edit_theme_options' )
			),
			'existing demo base cap to override' => array(
				'demo_base_cap',
				array( 'list_users' ),
				array( 'list_users' )
			),
			'existing demo meta cap to override' => array(
				'demo_meta_cap',
				static function () {
					return array( 'list_users' );
				},
				array( 'list_users' )
			),
		);
	}

	/**
	 * @dataProvider data_unset
	 */
	public function test_unset( $cap, $existed_before = true ) {
		if ( $existed_before ) {
			$this->assertTrue( $this->container->has( $cap ) );
		} else {
			$this->assertFalse( $this->container->has( $cap ) );
		}

		$this->container->unset( $cap );

		$this->assertFalse( $this->container->has( $cap ) );
		$this->expectException( Not_Found_Exception::class );
		$this->container->get( $cap );
	}

	public function data_unset() {
		return array(
			'non existent cap to unset'       => array( 'non_existent_cap', false ),
			'existing demo base cap to unset' => array( 'demo_base_cap', true ),
			'existing demo meta cap to unset' => array( 'demo_meta_cap', true ),
		);
	}

	public function test_get_keys() {
		$this->assertSameSets( array( 'demo_base_cap', 'demo_meta_cap', 'invalid_type_cap' ), $this->container->get_keys() );
	}

	/**
	 * @dataProvider data_has
	 */
	public function test_offsetExists( $cap, $expected ) {
		if ( $expected ) {
			$this->assertTrue( isset( $this->container[ $cap ] ) );
		} else {
			$this->assertFalse( isset( $this->container[ $cap ] ) );
		}
	}

	/**
	 * @dataProvider data_get
	 */
	public function test_offsetGet( $cap, $expected_class ) {
		if ( is_subclass_of( $expected_class, \Exception::class ) ) {
			$this->expectException( $expected_class );
		}
		$result = $this->container[ $cap ];
		$this->assertInstanceOf( $expected_class, $result );
		$this->assertSame( $cap, $result->get_key() );
	}

	/**
	 * @dataProvider data_set
	 */
	public function test_offsetSet( $cap, $creator, $expected_required_caps ) {
		$this->container[ $cap ] = $creator;

		$result = $this->container[ $cap ];
		$this->assertSame( $cap, $result->get_key() );
		if ( $result instanceof Meta_Capability ) {
			$map_callback = $result->get_map_callback();
			$this->assertSameSets( $expected_required_caps, $map_callback() );
		} else {
			$this->assertSameSets( $expected_required_caps, $result->get_required_caps() );
		}
	}

	/**
	 * @dataProvider data_unset
	 */
	public function test_offsetUnset( $cap, $existed_before = true ) {
		if ( $existed_before ) {
			$this->assertTrue( isset( $this->container[ $cap ] ) );
		} else {
			$this->assertFalse( isset( $this->container[ $cap ] ) );
		}

		unset( $this->container[ $cap ] );

		$this->assertFalse( isset( $this->container[ $cap ] ) );
		$this->expectException( Not_Found_Exception::class );
		$this->container[ $cap ];
	}
}
