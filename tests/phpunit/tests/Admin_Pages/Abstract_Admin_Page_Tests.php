<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Abstract_Admin_Page
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Admin_Pages;

use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page\Abstract_Admin_Page_Implementation_With_Args;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Abstract_Admin_Page_Tests extends Test_Case {

	public function test_constructor() {
		$admin_page = new Abstract_Admin_Page_Implementation_With_Args(
			array(
				'slug'       => 'my_admin_page',
				'title'      => 'My Admin Page',
				'capability' => 'manage_options',
			)
		);

		$this->assertSame( 'my_admin_page', $this->invoke_hidden_method( $admin_page, 'slug' ) );
		$this->assertSame( 'My Admin Page', $this->invoke_hidden_method( $admin_page, 'title' ) );
		$this->assertSame( 'manage_options', $this->invoke_hidden_method( $admin_page, 'capability' ) );
	}

	public function test_get_slug() {
		$admin_page = new Abstract_Admin_Page_Implementation_With_Args(
			array( 'slug' => 'admin_page_slug' )
		);

		$this->assertSame( 'admin_page_slug', $admin_page->get_slug() );
	}

	public function test_get_title() {
		$admin_page = new Abstract_Admin_Page_Implementation_With_Args(
			array( 'title' => 'Admin Page Title' )
		);

		$this->assertSame( 'Admin Page Title', $admin_page->get_title() );
	}

	public function test_get_capability() {
		$admin_page = new Abstract_Admin_Page_Implementation_With_Args(
			array( 'capability' => 'do_something' )
		);

		$this->assertSame( 'do_something', $admin_page->get_capability() );
	}

	public function test_load() {
		$admin_page = new Abstract_Admin_Page_Implementation_With_Args(
			array(
				'load_callback' => function () {
					add_action( 'admin_enqueue_scripts', 'non_existing_test_callback' );
				}
			)
		);

		$admin_page->load();
		$this->assertTrue( (bool) has_action( 'admin_enqueue_scripts', 'non_existing_test_callback' ) );
	}

	public function test_render() {
		$admin_page = new Abstract_Admin_Page_Implementation_With_Args(
			array(
				'render_callback' => function () {
					echo '<div class="wrap"><p>Admin page content.</p></div>';
				}
			)
		);

		$this->assertSame(
			'<div class="wrap"><p>Admin page content.</p></div>',
			get_echo( array( $admin_page, 'render' ) )
		);
	}
}
