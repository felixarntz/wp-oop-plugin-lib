<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Exception\Invalid_Entity_Data_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Comment_Repository_Tests extends Test_Case {

	private static $comment_id;

	private $repository;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$comment_id = $factory->comment->create();
	}

	public static function wpTearDownAfterClass() {
		wp_delete_comment( self::$comment_id, true );
	}

	public function set_up() {
		parent::set_up();

		$this->repository = new Comment_Repository();
	}

	public function test_exists() {
		$this->assertFalse( $this->repository->exists( 9999 ) );

		$this->assertTrue( $this->repository->exists( self::$comment_id ) );
	}

	public function test_get() {
		$this->assertNull( $this->repository->get( 9999 ) );

		$comment = $this->repository->get( self::$comment_id );
		$this->assertInstanceOf( Comment::class, $comment );
		$this->assertSame( self::$comment_id, $comment->get_id() );
	}

	public function test_update() {
		$this->assertTrue(
			$this->repository->update(
				self::$comment_id,
				array(
					'comment_content' => 'This is the new content.',
				)
			)
		);
		$this->assertSame( 'This is the new content.', get_comment( self::$comment_id )->comment_content );
	}

	public function test_update_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertFalse(
			$this->repository->update(
				self::$comment_id,
				array(
					'comment_post_ID' => 9999,
				)
			)
		);
	}

	public function test_add() {
		$new_id = $this->repository->add(
			array(
				'comment_content' => 'So insightful!',
			)
		);
		$this->assertNotSame( 0, $new_id );
		$this->assertSame( 'So insightful!', get_comment( $new_id )->comment_content );
	}

	public function test_add_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertSame(
			0,
			$this->repository->add(
				array(
					'comment_post_ID' => 9999,
				)
			)
		);
	}

	public function test_delete() {
		$this->assertTrue( $this->repository->delete( self::$comment_id ) );
		$this->assertNull( get_comment( self::$comment_id ) );
	}

	public function test_query() {
		$query = $this->repository->query( array( 'comment__in' => array( self::$comment_id ) ) );
		$this->assertInstanceOf( Comment_Query::class, $query );
		$this->assertSame( array( self::$comment_id ), $query->get_ids() );
	}

	public function test_prime_caches() {
		wp_cache_delete( self::$comment_id, 'comment' );
		$this->assertTrue( $this->repository->prime_caches( array( self::$comment_id ) ) );
		$this->assertNotFalse( wp_cache_get( self::$comment_id, 'comment' ) );
	}

	public function test_trash() {
		$this->assertTrue( $this->repository->trash( self::$comment_id ) );
		$this->assertSame( 'trash', wp_get_comment_status( self::$comment_id ) );
	}

	public function test_untrash() {
		wp_trash_comment( self::$comment_id );
		$this->assertTrue( $this->repository->untrash( self::$comment_id ) );
		$this->assertSame( 'approved', wp_get_comment_status( self::$comment_id ) );
	}

	public function test_is_trashed() {
		$this->assertFalse( $this->repository->is_trashed( self::$comment_id ) );

		wp_trash_comment( self::$comment_id );
		$this->assertTrue( $this->repository->is_trashed( self::$comment_id ) );
	}
}
