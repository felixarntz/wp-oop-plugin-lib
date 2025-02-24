<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Query
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class User_Query_Tests extends Test_Case {

	private static $users;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		// Create three users, the third one with a custom user name.
		self::$users   = $factory->user->create_many( 2, array( 'role' => 'contributor' ) );
		self::$users[] = $factory->user->create(
			array( 'user_login' => 'amazing_user' )
		);
	}

	public static function wpTearDownAfterClass() {
		foreach ( self::$users as $user_id ) {
			self::delete_user( $user_id );
		}
	}

	/**
	 * @dataProvider data_queries
	 */
	public function test_get_entities( array $query_args, array $expected_ids ) {
		$query_args   = $this->populate_with_ids( $query_args, array( 'users' => self::$users ) );
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'users' => self::$users ) );

		$query    = new User_Query( $query_args );
		$entities = $query->get_entities();
		$this->assertContainsOnlyInstancesOf( User::class, $entities );
		$this->assertSame(
			$expected_ids,
			array_map(
				function( $entity ) {
					return $entity->get_id();
				},
				$entities
			)
		);
	}

	/**
	 * @dataProvider data_queries
	 */
	public function test_get_ids( array $query_args, array $expected_ids ) {
		$query_args   = $this->populate_with_ids( $query_args, array( 'users' => self::$users ) );
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'users' => self::$users ) );

		$query = new User_Query( $query_args );
		$this->assertSame( $expected_ids, $query->get_ids() );
	}

	public static function data_queries() {
		return array(
			'query for user'           => array(
				array(
					'include' => array( 'users::0' ),
				),
				array( 'users::0' ),
			),
			'query with role' => array(
				array(
					'role'    => 'contributor',
					'orderby' => 'ID',
					'order'   => 'DESC',
				),
				array( 'users::1', 'users::0' ),
			),
			'query with user name' => array(
				array(
					'login' => 'amazing_user',
				),
				array( 'users::2' ),
			),
			'query with search'        => array(
				array(
					'search' => 'amazing*',
				),
				array( 'users::2' ),
			),
			'query with number limit'  => array(
				array(
					'include' => array( 'users::0', 'users::1' ),
					'number'  => 1,
					'orderby' => 'ID',
					'order'   => 'DESC',
				),
				array( 'users::1' ),
			),
		);
	}

	public function test_get_count() {
		$query = new User_Query(
			array(
				'role'   => 'contributor',
				'number' => 1,
			)
		);
		$this->assertSame( 2, $query->get_count() );
	}
}
