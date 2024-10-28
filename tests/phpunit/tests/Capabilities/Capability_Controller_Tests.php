<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Base_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group capabilities
 */
class Capability_Controller_Tests extends Test_Case {

	private $container;
	private $controller;

	public function set_up() {
		parent::set_up();

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
	public function test_grant_cap_for_base_caps( $cap, $required_caps, $expected_exception = null ) {
		if ( $expected_exception ) {
			$this->expectException( $expected_exception );
		}
		$this->controller->grant_cap_for_base_caps( $cap, $required_caps );
		$this->assertSame( $required_caps, $this->container->get( $cap )->get_required_caps() );
	}

	public function data_grant_cap_for_base_caps() {
		return array(
			'add required caps'              => array(
				'without_required_caps',
				array( 'edit_theme_options' ),
				null,
			),
			'remove required caps'           => array(
				'with_one_required_cap',
				array(),
				null,
			),
			'change required caps (replace)' => array(
				'with_one_required_cap',
				array( 'list_users' ),
				null,
			),
			'change required caps (add)'     => array(
				'with_multiple_required_caps',
				array( 'edit_posts', 'upload_files', 'manage_categories' ),
				null,
			),
			'change required caps (remove)'  => array(
				'with_multiple_required_caps',
				array( 'upload_files' ),
				null,
			),
			'access non existent cap'        => array(
				'non_existent_cap',
				array( 'manage_options' ),
				Not_Found_Exception::class,
			),
			'access meta cap'                => array(
				'meta_cap',
				array( 'manage_options' ),
				Not_Found_Exception::class,
			),
		);
	}

	/**
	 * @dataProvider data_set_meta_map_callback
	 */
	public function test_set_meta_map_callback( $cap, $map_callback, $expected_caps, $expected_exception = null ) {
		if ( $expected_exception ) {
			$this->expectException( $expected_exception );
		}
		$this->controller->set_meta_map_callback( $cap, $map_callback );

		$cap_map_callback = $this->container->get( $cap )->get_map_callback();
		$this->assertSame( $expected_caps, $cap_map_callback() );
	}

	public function data_set_meta_map_callback() {
		return array(
			'update map callback'              => array(
				'meta_cap',
				static function () {
					return array( 'edit_theme_options' );
				},
				array( 'edit_theme_options' ),
				null,
			),
			'update map callback empty'       => array(
				'meta_cap',
				static function () {
					return array();
				},
				array(),
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
}
