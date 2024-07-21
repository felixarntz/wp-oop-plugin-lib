<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

class User_Tests extends Test_Case {

	private static $user_id;

	private $user;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$user_id = $factory->user->create(
			array(
				'user_login' => 'amazing_user',
				'user_email' => 'amazing_user@example.org',
				'role'       => 'administrator',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		if ( is_multisite() ) {
			wpmu_delete_user( self::$user_id );
		} else {
			wp_delete_user( self::$user_id );
		}
	}

	public function set_up() {
		parent::set_up();

		$this->user = new User( get_userdata( self::$user_id ) );
	}

	public function test_get_id() {
		$this->assertSame( self::$user_id, $this->user->get_id() );
	}

	public function test_is_public() {
		$this->assertTrue( $this->user->is_public() );
	}

	public function test_get_url() {
		$this->set_permalink_structure( '/%postname%/' );

		$this->assertSame(
			home_url( '/author/amazing_user/' ),
			$this->user->get_url()
		);
	}

	public function test_get_edit_url() {
		$this->assertSame( '', $this->user->get_edit_url() );

		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		if ( is_multisite() ) {
			grant_super_admin( $admin_id );
		}
		wp_set_current_user( $admin_id );
		$this->assertSame(
			admin_url( sprintf( 'user-edit.php?user_id=%d', self::$user_id ) ),
			$this->user->get_edit_url()
		);
	}

	/**
	 * @dataProvider data_get_field_value
	 */
	public function test_get_field_value( string $field, $expected_value ) {
		$this->assertSame( $expected_value, $this->user->get_field_value( $field ) );
	}

	public function data_get_field_value() {
		return array(
			'invalid'        => array(
				'some_field',
				null,
			),
			'user_login'     => array(
				'user_login',
				'amazing_user',
			),
			'user_nicename'      => array(
				'user_nicename',
				'amazing_user',
			),
			'user_email'      => array(
				'user_email',
				'amazing_user@example.org',
			),
		);
	}

	public function test_has_cap() {
		$this->assertTrue( $this->user->has_cap( 'manage_options' ) );
		$this->assertFalse( $this->user->has_cap( 'perform_wizardry' ) );
	}
}
