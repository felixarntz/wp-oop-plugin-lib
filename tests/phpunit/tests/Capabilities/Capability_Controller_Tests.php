<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Capability_Controller_Tests extends Test_Case {

	private $controller;

	public function set_up() {
		parent::set_up();

		$container = new Capability_Container();
		$container->set(
			'with_one_required_cap',
			static function () {
				return new Capability( 'with_one_required_cap', array( 'manage_options' ) );
			}
		);
		$container->set(
			'with_multiple_required_caps',
			static function () {
				return new Capability( 'with_multiple_required_caps', array( 'edit_posts', 'upload_files' ) );
			}
		);
		$container->set(
			'without_required_caps',
			static function () {
				return new Capability( 'without_required_caps', array() );
			}
		);

		$this->controller = new Capability_Controller( $container );
	}

	/**
	 * @dataProvider data_grant_cap_for_base_caps
	 */
	public function test_grant_cap_for_base_caps( $cap, $required_caps, $expected_caps_map, $expected_exception = null ) {
		if ( $expected_exception ) {
			$this->expectException( $expected_exception );
		}
		$this->controller->grant_cap_for_base_caps( $cap, $required_caps );
		$this->assertSameSetsWithIndex( $expected_caps_map, $this->controller->get_required_caps_map() );
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
				array(
					'with_one_required_cap'       => array( 'manage_options' ),
					'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
				),
				Not_Found_Exception::class,
			),
		);
	}

	public function test_get_controlled_caps() {
		$this->assertSameSets(
			array(
				'with_one_required_cap',
				'with_multiple_required_caps',
				'without_required_caps',
			),
			$this->controller->get_controlled_caps()
		);
	}

	public function test_get_required_caps_map() {
		$this->assertSameSetsWithIndex(
			array(
				'with_one_required_cap'       => array( 'manage_options' ),
				'with_multiple_required_caps' => array( 'edit_posts', 'upload_files' ),
			),
			$this->controller->get_required_caps_map()
		);
	}
}
