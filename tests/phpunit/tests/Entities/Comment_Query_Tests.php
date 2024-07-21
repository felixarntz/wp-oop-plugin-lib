<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Query
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Comment_Query_Tests extends Test_Case {

	private static $comments;
	private static $posts;
	private static $terms;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$posts = $factory->post->create_many( 2 );

		// Create three comments, two for post 1 and one for post 2.
		self::$comments   = $factory->comment->create_many( 2, array( 'comment_post_ID' => self::$posts[0] ) );
		self::$comments[] = $factory->comment->create(
			array(
				'comment_post_ID' => self::$posts[1],
				'comment_content' => 'This is an amazing comment.',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		foreach ( self::$comments as $comment_id ) {
			wp_delete_comment( $comment_id, true );
		}

		foreach ( self::$posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * @dataProvider data_queries
	 */
	public function test_get_entities( array $query_args, array $expected_ids ) {
		$query_args   = $this->populate_with_ids(
			$query_args,
			array(
				'comments' => self::$comments,
				'posts'    => self::$posts,
			)
		);
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'comments' => self::$comments ) );

		$query    = new Comment_Query( $query_args );
		$entities = $query->get_entities();
		$this->assertContainsOnlyInstancesOf( Comment::class, $entities );
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
		$query_args   = $this->populate_with_ids(
			$query_args,
			array(
				'comments' => self::$comments,
				'posts'    => self::$posts,
			)
		);
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'comments' => self::$comments ) );

		$query = new Comment_Query( $query_args );
		$this->assertSame( $expected_ids, $query->get_ids() );
	}

	public function data_queries() {
		return array(
			'query for comment'        => array(
				array(
					'comment__in'  => array( 'comments::0' ),
				),
				array( 'comments::0' ),
			),
			'query for post comments'  => array(
				array(
					'post_id' => 'posts::0',
					'orderby' => 'comment_ID',
					'order'   => 'DESC',
				),
				array( 'comments::1', 'comments::0' ),
			),
			'query with invalid post'  => array(
				array(
					'post_id' => 9999,
				),
				array(),
			),
			'query with search'        => array(
				array(
					'search' => 'amazing',
				),
				array( 'comments::2' ),
			),
			'query with number limit'  => array(
				array(
					'comment__in' => array( 'comments::0', 'comments::2' ),
					'number'      => 1,
					'orderby'     => 'comment_ID',
					'order'       => 'DESC',
				),
				array( 'comments::2' ),
			),
		);
	}

	public function test_get_count() {
		$query = new Comment_Query(
			array(
				'post_id' => self::$posts[0],
				'number'  => 1,
			)
		);
		$this->assertSame( 2, $query->get_count() );
	}
}
