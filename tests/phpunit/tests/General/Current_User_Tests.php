<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Current_User
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Current_User;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group general
 */
class Current_User_Tests extends Test_Case {

	private static $user_ids;

	private $current_user;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$user_ids = array(
			'administrator' => $factory->user->create( array( 'role' => 'administrator' ) ),
			'author'        => $factory->user->create( array( 'role' => 'author' ) ),
			'subscriber'    => $factory->user->create( array( 'role' => 'subscriber' ) ),
		);
	}

	public static function wpTearDownAfterClass() {
		foreach ( self::$user_ids as $user_id ) {
			self::delete_user( $user_id );
		}
	}

	public function set_up() {
		parent::set_up();

		$this->current_user = new Current_User();
	}

	public function test_get_id() {
		$this->assertSame( 0, $this->current_user->get_id() );

		wp_set_current_user( self::$user_ids['administrator'] );
		$this->assertSame( self::$user_ids['administrator'], $this->current_user->get_id() );
	}

	public function test_set() {
		$this->current_user->set( self::$user_ids['author'] );
		$this->assertSame( self::$user_ids['author'], get_current_user_id() );
	}

	public function test_is_logged_in() {
		$this->assertFalse( $this->current_user->is_logged_in() );

		wp_set_current_user( self::$user_ids['administrator'] );
		$this->assertTrue( $this->current_user->is_logged_in() );
	}

	public function test_has_cap() {
		wp_set_current_user( self::$user_ids['administrator'] );
		$this->assertTrue( $this->current_user->has_cap( 'edit_posts' ) );
		$this->assertTrue( $this->current_user->has_cap( 'manage_options' ) );

		wp_set_current_user( self::$user_ids['author'] );
		$this->assertTrue( $this->current_user->has_cap( 'edit_posts' ) );
		$this->assertFalse( $this->current_user->has_cap( 'manage_options' ) );

		wp_set_current_user( self::$user_ids['subscriber'] );
		$this->assertFalse( $this->current_user->has_cap( 'edit_posts' ) );
		$this->assertFalse( $this->current_user->has_cap( 'manage_options' ) );
	}

	public function test_create_nonce() {
		$nonce = $this->current_user->create_nonce( 'my_action' );
		$this->assertSame( 1, wp_verify_nonce( $nonce, 'my_action' ) );
	}

	public function test_verify_nonce() {
		$nonce = wp_create_nonce( 'another_action' );
		$this->assertTrue( $this->current_user->verify_nonce( $nonce, 'another_action' ) );
		$this->assertFalse( $this->current_user->verify_nonce( $nonce, 'yet_another_action' ) );
	}

	/**
	 * @group ms-required
	 */
	public function test_is_super_admin_multisite() {
		$this->assertFalse( $this->current_user->is_super_admin() );

		wp_set_current_user( self::$user_ids['administrator'] );
		$this->assertFalse( $this->current_user->is_super_admin() );

		grant_super_admin( self::$user_ids['administrator'] );
		$this->assertTrue( $this->current_user->is_super_admin() );
	}

	/**
	 * @group ms-excluded
	 */
	public function test_is_super_admin_non_multisite() {
		$this->assertFalse( $this->current_user->is_super_admin() );

		wp_set_current_user( self::$user_ids['administrator'] );
		$this->assertTrue( $this->current_user->is_super_admin() );
	}
}
