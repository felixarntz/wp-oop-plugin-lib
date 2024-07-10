<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Menu
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Admin_Pages;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Menu;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page\Generic_Admin_Page;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

class Admin_Menu_Tests extends Test_Case {

	private static $admin_id;

	private $built_in_menu;
	private $third_party_menu;
	private $custom_plugin_menu;
	private $no_menu;

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
		global $menu, $submenu;

		parent::set_up();

		wp_set_current_user( self::$admin_id );

		$menu    = array();
		$submenu = array( 'options-general.php' => array() );

		// Copied from /wp-admin/menu.php.
		$menu[80]                           = array( __( 'Settings', 'default' ), 'manage_options', 'options-general.php', '', 'menu-top menu-icon-settings', 'menu-settings', 'dashicons-admin-settings' );
		$submenu['options-general.php'][10] = array( _x( 'General', 'settings screen', 'default' ), 'manage_options', 'options-general.php' );
		$submenu['options-general.php'][15] = array( __( 'Writing', 'default' ), 'manage_options', 'options-writing.php' );
		$submenu['options-general.php'][20] = array( __( 'Reading', 'default' ), 'manage_options', 'options-reading.php' );
		$submenu['options-general.php'][25] = array( __( 'Discussion', 'default' ), 'manage_options', 'options-discussion.php' );
		$submenu['options-general.php'][30] = array( __( 'Media', 'default' ), 'manage_options', 'options-media.php' );
		$submenu['options-general.php'][40] = array( __( 'Permalinks', 'default' ), 'manage_options', 'options-permalink.php' );
		$submenu['options-general.php'][45] = array( __( 'Privacy', 'default' ), 'manage_privacy_options', 'options-privacy.php' );

		add_menu_page(
			'Jetpack',
			'Jetpack',
			'manage_options',
			'jetpack',
			static function () {}
		);

		$this->built_in_menu      = new Admin_Menu( 'options-general.php' );
		$this->third_party_menu   = new Admin_Menu( 'jetpack' );
		$this->custom_plugin_menu = new Admin_Menu(
			'custom_plugin',
			array(
				'menu_title' => 'Custom Plugin',
				'icon_url'   => 'dashicons-admin-plugins',
				'position'   => 82,
			)
		);
		$this->no_menu            = new Admin_Menu( '' );
	}

	public function test_add_page_with_built_in_menu() {
		// As a prerequisite, assert that the menu item already exists.
		$this->assertNotFalse( $this->get_menu_item( 'options-general.php' ) );

		// Add the new admin page.
		$admin_page = new Generic_Admin_Page(
			array(
				'slug'       => 'custom_plugin_settings',
				'title'      => 'Custom Plugin Settings',
				'capability' => 'manage_options',
			)
		);
		$hook_suffix = $this->built_in_menu->add_page( $admin_page );
		$this->assertNotFalse( $hook_suffix );

		// Assert that the original menu was not overwritten.
		$menu_item = $this->get_menu_item( 'options-general.php' );
		$this->assertSame( __( 'Settings', 'default' ), $menu_item[0] );
		$this->assertSame( 'manage_options', $menu_item[1] );
		$this->assertSame( 'options-general.php', $menu_item[2] );
		$this->assertCount( 8, $this->get_submenu_items( 'options-general.php' ) ); // 7 items plus the new one.

		// Assert the admin page was added correctly.
		$submenu_item = $this->get_last_submenu_item( 'options-general.php' );
		$this->assertSame( 'Custom Plugin Settings', $submenu_item[0] );
		$this->assertSame( 'manage_options', $submenu_item[1] );
		$this->assertSame( 'custom_plugin_settings', $submenu_item[2] );

		$this->assertSame( 10, has_action( "load-{$hook_suffix}", array( $admin_page, 'load' ) ) );
		$this->assertSame( 10, has_action( $hook_suffix, array( $admin_page, 'render' ) ) );
	}

	public function test_add_page_with_third_party_menu() {
		// As a prerequisite, assert that the menu item already exists.
		$this->assertNotFalse( $this->get_menu_item( 'jetpack' ) );

		// Add the new admin page.
		$admin_page = new Generic_Admin_Page(
			array(
				'slug'       => 'custom_plugin_settings',
				'title'      => 'Custom Plugin Settings',
				'capability' => 'manage_options',
			)
		);
		$hook_suffix = $this->third_party_menu->add_page( $admin_page );
		$this->assertNotFalse( $hook_suffix );

		// Assert that the original menu was not overwritten.
		$menu_item = $this->get_menu_item( 'jetpack' );
		$this->assertSame( 'Jetpack', $menu_item[0] );
		$this->assertSame( 'manage_options', $menu_item[1] );
		$this->assertSame( 'jetpack', $menu_item[2] );
		$this->assertCount( 2, $this->get_submenu_items( 'jetpack' ) ); // 1 item plus the new one.

		// Assert the admin page was added correctly.
		$submenu_item = $this->get_last_submenu_item( 'jetpack' );
		$this->assertSame( 'Custom Plugin Settings', $submenu_item[0] );
		$this->assertSame( 'manage_options', $submenu_item[1] );
		$this->assertSame( 'custom_plugin_settings', $submenu_item[2] );

		$this->assertSame( 10, has_action( "load-{$hook_suffix}", array( $admin_page, 'load' ) ) );
		$this->assertSame( 10, has_action( $hook_suffix, array( $admin_page, 'render' ) ) );
	}

	public function test_add_page_with_custom_plugin_menu() {
		// As a prerequisite, assert that the menu item does not exist yet.
		$this->assertFalse( $this->get_menu_item( 'custom_plugin' ) );
		$this->assertFalse( $this->get_menu_item( 'custom_plugin_dashboard' ) ); // Checking for the page slug too.

		// Add the new admin page.
		$admin_page = new Generic_Admin_Page(
			array(
				'slug'       => 'custom_plugin_dashboard',
				'title'      => 'Custom Plugin Dashboard',
				'capability' => 'activate_plugins',
			)
		);
		$hook_suffix = $this->custom_plugin_menu->add_page( $admin_page );
		$this->assertNotFalse( $hook_suffix );

		// Assert that the menu was added with the slug of the page (as it's the first page), not the menu's own slug.
		$this->assertFalse( $this->get_menu_item( 'custom_plugin' ) );
		$menu_item = $this->get_menu_item( 'custom_plugin_dashboard' );
		$this->assertSame( 'Custom Plugin', $menu_item[0] );
		$this->assertSame( 'activate_plugins', $menu_item[1] );
		$this->assertSame( 'custom_plugin_dashboard', $menu_item[2] );
		$this->assertCount( 1, $this->get_submenu_items( 'custom_plugin_dashboard' ) ); // Just the new item.

		// Assert the admin page was added correctly.
		$submenu_item = $this->get_last_submenu_item( 'custom_plugin_dashboard' );
		$this->assertSame( 'Custom Plugin Dashboard', $submenu_item[0] );
		$this->assertSame( 'activate_plugins', $submenu_item[1] );
		$this->assertSame( 'custom_plugin_dashboard', $submenu_item[2] );

		$this->assertSame( 10, has_action( "load-{$hook_suffix}", array( $admin_page, 'load' ) ) );
		$this->assertSame( 10, has_action( $hook_suffix, array( $admin_page, 'render' ) ) );
	}

	public function test_add_page_without_any_menu() {
		// Add the new admin page.
		$admin_page = new Generic_Admin_Page(
			array(
				'slug'       => 'custom_plugin_dashboard',
				'title'      => 'Custom Plugin Dashboard',
				'capability' => 'activate_plugins',
			)
		);
		$hook_suffix = $this->no_menu->add_page( $admin_page );
		$this->assertNotFalse( $hook_suffix );

		// Assert that the no top-level menu was added.
		$this->assertFalse( $this->get_menu_item( 'custom_plugin' ) );
		$this->assertFalse( $this->get_menu_item( 'custom_plugin_dashboard' ) );

		// Assert the admin page was added correctly.
		$submenu_item = $this->get_last_submenu_item( '' );
		$this->assertSame( 'Custom Plugin Dashboard', $submenu_item[0] );
		$this->assertSame( 'activate_plugins', $submenu_item[1] );
		$this->assertSame( 'custom_plugin_dashboard', $submenu_item[2] );

		$this->assertSame( 10, has_action( "load-{$hook_suffix}", array( $admin_page, 'load' ) ) );
		$this->assertSame( 10, has_action( $hook_suffix, array( $admin_page, 'render' ) ) );
	}

	public function test_add_page_with_lacking_capability() {
		$admin_page = new Generic_Admin_Page(
			array(
				'slug'       => 'custom_plugin_settings',
				'title'      => 'Custom Plugin Settings',
				'capability' => 'nobody_can_do_this',
			)
		);
		$hook_suffix = $this->built_in_menu->add_page( $admin_page );
		$this->assertSame( '', $hook_suffix );
	}

	private function get_menu_item( string $menu_slug ) {
		global $menu;

		foreach ( $menu as $menu_item ) {
			if ( $menu_slug === $menu_item[2] ) {
				return $menu_item;
			}
		}

		return false;
	}

	private function get_submenu_items( string $menu_slug ) {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) ) {
			return false;
		}

		return $submenu[ $menu_slug ];
	}

	private function get_last_submenu_item( string $menu_slug ) {
		global $submenu;

		if ( ! isset( $submenu[ $menu_slug ] ) || count( $submenu[ $menu_slug ] ) < 1 ) {
			return false;
		}

		$keys = array_keys( $submenu[ $menu_slug ] );
		return $submenu[ $menu_slug ][ $keys[ count( $keys ) - 1 ] ];
	}
}
