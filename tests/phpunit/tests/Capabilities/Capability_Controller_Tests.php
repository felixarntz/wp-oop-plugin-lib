<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Base_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

class Capability_Controller_Tests extends Test_Case {

	private static $admin_id;

	private $container;
	private $controller;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$admin_id = $factory->user->create( array( 'role' => 'administrator' ) );
	}

	public static function wpTearDownAfterClass() {
		if ( is_multisite() ) {
			wpmu_delete_user( self::$admin_id );
		} else {
			wp_delete_user( self::$admin_id );
		}
	}

	public function set_up() {
		parent::set_up();

		wp_set_current_user( self::$admin_id );

		$this->container = new Capability_Container();
		$this->container->set(
			'with_one_required_cap',
			static function () {
				return new Base_Capability( 'with_one_required_cap', array( 'manage_options' ) );
			}
		);
		$this->container->set(
			'with_multiple_required_caps',
			static function () {
				return new Base_Capability( 'with_multiple_required_caps', array( 'edit_posts', 'upload_files' ) );
			}
		);
		$this->container->set(
			'without_required_caps',
			static function () {
				return new Base_Capability( 'without_required_caps', array() );
			}
		);
		$this->container->set(
			'meta_cap',
			static function () {
				return new Meta_Capability( 'meta_cap', static function () {
					return array( 'manage_options' );
				} );
			}
		);

		$this->controller = new Capability_Controller( $this->container );
	}

	/**
	 * @dataProvider data_grant_cap_for_base_caps
	 */
	public function test_grant_cap_for_base_caps( $cap, $required_caps, $expected_map, $expected_exception = null ) {
		if ( $expected_exception ) {
			$this->expectException( $expected_exception );
		}
		$this->controller->grant_cap_for_base_caps( $cap, $required_caps );
		$this->assertSameSetsWithIndex( $expected_map, $this->controller->get_required_base_caps_map() );
	}

	public function data_grant_cap_for_base_caps() {
		return array(
			'add required caps'              => array(
				'without_required_caps',
				array( 'edit_theme_options' ),
				array(
					'with_one_required_cap'       => array( 'manage_options' ),
					'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
					'without_required_caps'       => array( 'edit_theme_options' ),
				),
				null,
			),
			'remove required caps'           => array(
				'with_one_required_cap',
				array(),
				array(
					'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
				),
				null,
			),
			'change required caps (replace)' => array(
				'with_one_required_cap',
				array( 'list_users' ),
				array(
					'with_one_required_cap'       => array( 'list_users' ),
					'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
				),
				null,
			),
			'change required caps (add)'     => array(
				'with_multiple_required_caps',
				array( 'edit_posts', 'upload_files', 'manage_categories' ),
				array(
					'with_one_required_cap'       => array( 'manage_options' ),
					'with_multiple_required_caps' => array( 'edit_posts', 'upload_files', 'manage_categories' ),
				),
				null,
			),
			'change required caps (remove)'  => array(
				'with_multiple_required_caps',
				array( 'upload_files' ),
				array(
					'with_one_required_cap'       => array( 'manage_options' ),
					'with_multiple_required_caps' => array( 'upload_files' ),
				),
				null,
			),
			'access non existent cap'        => array(
				'non_existent_cap',
				array( 'manage_options' ),
				array(),
				Not_Found_Exception::class,
			),
			'access meta cap'                => array(
				'meta_cap',
				array( 'manage_options' ),
				array(),
				Not_Found_Exception::class,
			),
		);
	}

	/**
	 * @dataProvider data_set_meta_map_callback
	 */
	public function test_set_meta_map_callback( $cap, $map_callback, $expected_map, $expected_exception = null ) {
		if ( $expected_exception ) {
			$this->expectException( $expected_exception );
		}
		$this->controller->set_meta_map_callback( $cap, $map_callback );

		$callbacks_map = $this->controller->get_meta_map_callbacks_map();
		$resolved_map  = array();
		foreach ( $callbacks_map as $cap => $map_callback ) {
			$resolved_map[ $cap ] = $map_callback();
		}
		$this->assertSameSetsWithIndex( $expected_map, $resolved_map );
	}

	public function data_set_meta_map_callback() {
		return array(
			'update map callback'              => array(
				'meta_cap',
				static function () {
					return array( 'edit_theme_options' );
				},
				array(
					'meta_cap' => array( 'edit_theme_options' ),
				),
				null,
			),
			'update map callback empty'       => array(
				'meta_cap',
				static function () {
					return array();
				},
				array(
					'meta_cap' => array(),
				),
				null,
			),
			'access non existent cap'        => array(
				'non_existent_cap',
				static function () {
					return array( 'manage_options' );
				},
				array(),
				Not_Found_Exception::class,
			),
			'access base cap'                => array(
				'with_one_required_cap',
				static function () {
					return array( 'manage_options' );
				},
				array(),
				Not_Found_Exception::class,
			),
		);
	}

	public function test_get_required_base_caps_map() {
		$this->assertSameSetsWithIndex(
			array(
				'with_one_required_cap'       => array( 'manage_options' ),
				'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
			),
			$this->controller->get_required_base_caps_map()
		);
	}

	public function test_get_meta_map_callbacks_map() {
		$callbacks_map = $this->controller->get_meta_map_callbacks_map();
		$resolved_map  = array();
		foreach ( $callbacks_map as $cap => $map_callback ) {
			$resolved_map[ $cap ] = $map_callback();
		}
		$this->assertSameSetsWithIndex(
			array(
				'meta_cap' => array( 'manage_options' ),
			),
			$resolved_map
		);
	}

	public function test_filter_user_has_cap() {
		add_filter( 'user_has_cap', array( $this->controller, 'filter_user_has_cap' ) );

		$this->assertTrue( current_user_can( 'with_one_required_cap' ) );
		$this->assertTrue( current_user_can( 'with_multiple_required_caps' ) );
		$this->assertFalse( current_user_can( 'without_required_caps' ) );
		$this->assertFalse( current_user_can( 'meta_cap' ) ); // This does not work because the 'map_meta_cap' filter is not applied.
	}

	public function test_filter_map_meta_cap() {
		add_filter( 'map_meta_cap', array( $this->controller, 'filter_map_meta_cap' ), 10, 4 );

		$this->assertTrue( current_user_can( 'meta_cap' ) );
	}

	public function test_filter_map_meta_cap_with_args() {
		$this->container->set(
			'meta_cap_with_args',
			static function () {
				return new Meta_Capability( 'meta_cap_with_args', static function ( int $user_id, int $some_number = 0 ) {
					if ( 42 === $some_number ) {
						return array( 'manage_options' );
					}
					return array( 'do_not_allow' );
				} );
			}
		);

		add_filter( 'map_meta_cap', array( $this->controller, 'filter_map_meta_cap' ), 10, 4 );

		$this->assertTrue( current_user_can( 'meta_cap_with_args', 42 ) );
		$this->assertFalse( current_user_can( 'meta_cap_with_args', 41 ) );
	}
}
