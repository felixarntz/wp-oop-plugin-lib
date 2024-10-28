<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Filters
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Base_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Filters;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group capabilities
 */
class Capability_Filters_Tests extends Test_Case {

	private static $admin_id;

	private $container;
	private $filters;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$admin_id = $factory->user->create( array( 'role' => 'administrator' ) );
	}

	public static function wpTearDownAfterClass() {
		self::delete_user( self::$admin_id );
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

		$this->filters = new Capability_Filters( $this->container );
	}

	public function test_add_hooks() {
		$this->filters->add_hooks();

		$this->assertHasFilterCallback( 'user_has_cap', array( $this->filters, 'filter_user_has_cap' ) );
		$this->assertHasFilterCallback( 'map_meta_cap', array( $this->filters, 'filter_map_meta_cap' ) );
	}

	public function test_get_required_base_caps_map() {
		$this->assertSameSetsWithIndex(
			array(
				'with_one_required_cap'       => array( 'manage_options' ),
				'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
			),
			$this->filters->get_required_base_caps_map()
		);
	}

	public function test_get_meta_map_callbacks_map() {
		$callbacks_map = $this->filters->get_meta_map_callbacks_map();
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
		add_filter( 'user_has_cap', array( $this->filters, 'filter_user_has_cap' ) );

		$this->assertTrue( current_user_can( 'with_one_required_cap' ) );
		$this->assertTrue( current_user_can( 'with_multiple_required_caps' ) );
		$this->assertFalse( current_user_can( 'without_required_caps' ) );
		$this->assertFalse( current_user_can( 'meta_cap' ) ); // This does not work because the 'map_meta_cap' filter is not applied.
	}

	public function test_filter_map_meta_cap() {
		add_filter( 'map_meta_cap', array( $this->filters, 'filter_map_meta_cap' ), 10, 4 );

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

		add_filter( 'map_meta_cap', array( $this->filters, 'filter_map_meta_cap' ), 10, 4 );

		$this->assertTrue( current_user_can( 'meta_cap_with_args', 42 ) );
		$this->assertFalse( current_user_can( 'meta_cap_with_args', 41 ) );
	}
}
