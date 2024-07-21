<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Exception\Invalid_Entity_Data_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Post_Repository_Tests extends Test_Case {

	private static $post_id;

	private $repository;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create();
	}

	public static function wpTearDownAfterClass() {
		wp_delete_post( self::$post_id, true );
	}

	public function set_up() {
		parent::set_up();

		$this->repository = new Post_Repository();
	}

	public function test_exists() {
		$this->assertFalse( $this->repository->exists( 9999 ) );

		$this->assertTrue( $this->repository->exists( self::$post_id ) );
	}

	public function test_get() {
		$this->assertNull( $this->repository->get( 9999 ) );

		$post = $this->repository->get( self::$post_id );
		$this->assertInstanceOf( Post::class, $post );
		$this->assertSame( self::$post_id, $post->get_id() );
	}

	public function test_update() {
		$this->assertTrue(
			$this->repository->update(
				self::$post_id,
				array(
					'post_title' => 'A new title',
				)
			)
		);
		$this->assertSame( 'A new title', get_post( self::$post_id )->post_title );
	}

	public function test_update_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertFalse(
			$this->repository->update(
				self::$post_id,
				array(
					'post_title'   => '',
					'post_content' => '',
					'post_excerpt' => '',
				)
			)
		);
	}

	public function test_add() {
		$new_id = $this->repository->add(
			array(
				'post_title' => 'Newly created post',
				'post_type'  => 'post',
			)
		);
		$this->assertNotSame( 0, $new_id );
		$this->assertSame( 'Newly created post', get_post( $new_id )->post_title );
	}

	public function test_add_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertSame(
			0,
			$this->repository->add(
				array(
					'post_title'   => '',
					'post_content' => '',
					'post_excerpt' => '',
				)
			)
		);
	}

	public function test_delete() {
		$this->assertTrue( $this->repository->delete( self::$post_id ) );
		$this->assertNull( get_post( self::$post_id ) );
	}

	public function test_query() {
		$query = $this->repository->query( array( 'post__in' => array( self::$post_id ) ) );
		$this->assertInstanceOf( Post_Query::class, $query );
		$this->assertSame( array( self::$post_id ), $query->get_ids() );
	}

	public function test_prime_caches() {
		wp_cache_delete( self::$post_id, 'posts' );
		$this->assertTrue( $this->repository->prime_caches( array( self::$post_id ) ) );
		$this->assertNotFalse( wp_cache_get( self::$post_id, 'posts' ) );
	}

	public function test_trash() {
		$this->assertTrue( $this->repository->trash( self::$post_id ) );
		$this->assertSame( 'trash', get_post_status( self::$post_id ) );
	}

	public function test_untrash() {
		wp_trash_post( self::$post_id );
		$this->assertTrue( $this->repository->untrash( self::$post_id ) );
		$this->assertSame( 'draft', get_post_status( self::$post_id ) );
	}

	public function test_is_trashed() {
		$this->assertFalse( $this->repository->is_trashed( self::$post_id ) );

		wp_trash_post( self::$post_id );
		$this->assertTrue( $this->repository->is_trashed( self::$post_id ) );
	}
}
