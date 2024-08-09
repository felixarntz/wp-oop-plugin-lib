<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Exception\Invalid_Entity_Data_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class User_Repository_Tests extends Test_Case {

	private static $user_id;

	private $repository;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$user_id = $factory->user->create();
	}

	public static function wpTearDownAfterClass() {
		self::delete_user( self::$user_id );
	}

	public function set_up() {
		parent::set_up();

		$this->repository = new User_Repository();
	}

	public function test_exists() {
		$this->assertFalse( $this->repository->exists( 9999 ) );

		$this->assertTrue( $this->repository->exists( self::$user_id ) );
	}

	public function test_get() {
		$this->assertNull( $this->repository->get( 9999 ) );

		$user = $this->repository->get( self::$user_id );
		$this->assertInstanceOf( User::class, $user );
		$this->assertSame( self::$user_id, $user->get_id() );
	}

	public function test_update() {
		$this->assertTrue(
			$this->repository->update(
				self::$user_id,
				array(
					'user_nicename' => 'amazing_user',
				)
			)
		);
		$this->assertSame( 'amazing_user', get_userdata( self::$user_id )->user_nicename );
	}

	public function test_update_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertFalse(
			$this->repository->update(
				self::$user_id,
				// The user_nicename field is limited to 50 characters.
				array( 'user_nicename' => str_repeat( 'ab', 30 ) )
			)
		);
	}

	public function test_add() {
		$new_id = $this->repository->add(
			array(
				'user_login' => 'the_author',
				'user_email' => 'the_author@example.org',
				'user_pass'  => 'password',
				'role'       => 'author',
			)
		);
		$this->assertNotSame( 0, $new_id );
		$this->assertSame( 'the_author@example.org', get_userdata( $new_id )->user_email );
	}

	public function test_add_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertSame(
			0,
			$this->repository->add(
				array(
					'user_login' => '',
				)
			)
		);
	}

	public function test_delete() {
		$this->assertTrue( $this->repository->delete( self::$user_id ) );
		$this->assertFalse( get_userdata( self::$user_id ) );
	}

	public function test_query() {
		$query = $this->repository->query( array( 'include' => array( self::$user_id ) ) );
		$this->assertInstanceOf( User_Query::class, $query );
		$this->assertSame( array( self::$user_id ), $query->get_ids() );
	}
}
