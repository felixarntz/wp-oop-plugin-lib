<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Query
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Post_Query_Tests extends Test_Case {

	private static $posts;
	private static $terms;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		register_taxonomy( 'wptests_tax', 'post' );

		self::$terms = $factory->term->create_many(
			2,
			array(
				'taxonomy' => 'wptests_tax',
			)
		);

		// Create three posts, assign terms to the first two.
		self::$posts = $factory->post->create_many( 3 );
		wp_set_object_terms( self::$posts[0], array( self::$terms[0] ), 'wptests_tax' );
		wp_set_object_terms( self::$posts[1], array( self::$terms[1] ), 'wptests_tax' );

		// Set a custom title for the third post.
		wp_update_post(
			array(
				'ID'         => self::$posts[2],
				'post_title' => 'Amazing post',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		foreach ( self::$posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}

		foreach ( self::$terms as $term_id ) {
			wp_delete_term( $term_id, 'wptests_tax' );
		}

		unregister_taxonomy( 'wptests_tax' );
	}

	/**
	 * @dataProvider data_queries
	 */
	public function test_get_entities( array $query_args, array $expected_ids ) {
		$query_args   = $this->populate_with_ids(
			$query_args,
			array(
				'posts' => self::$posts,
				'terms' => self::$terms,
			)
		);
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'posts' => self::$posts ) );

		$query    = new Post_Query( $query_args );
		$entities = $query->get_entities();
		$this->assertContainsOnlyInstancesOf( Post::class, $entities );
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
				'posts' => self::$posts,
				'terms' => self::$terms,
			)
		);
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'posts' => self::$posts ) );

		$query = new Post_Query( $query_args );
		$this->assertSame( $expected_ids, $query->get_ids() );
	}

	public function data_queries() {
		return array(
			'query for post'           => array(
				array(
					'post_type' => 'post',
					'post__in'  => array( 'posts::0' ),
				),
				array( 'posts::0' ),
			),
			'query with taxonomy term' => array(
				array(
					'post_type' => 'post',
					'tax_query' => array(
						array(
							'taxonomy' => 'wptests_tax',
							'field'    => 'term_id',
							'terms'    => array( 'terms::1' ),
						),
					),
				),
				array( 'posts::1' ),
			),
			'query with invalid term'  => array(
				array(
					'post_type' => 'post',
					'tax_query' => array(
						array(
							'taxonomy' => 'wptests_tax',
							'field'    => 'term_id',
							'terms'    => array( 9999 ),
						),
					),
				),
				array(),
			),
			'query with search'        => array(
				array(
					's' => 'amazing',
				),
				array( 'posts::2' ),
			),
			'query with number limit'  => array(
				array(
					'post_type'      => 'post',
					'post__in'       => array( 'posts::0', 'posts::1' ),
					'posts_per_page' => 1,
					'orderby'        => 'ID',
					'order'          => 'DESC',
				),
				array( 'posts::1' ),
			),
		);
	}

	public function test_get_count() {
		$query = new Post_Query(
			array(
				'post_type'      => 'post',
				'tax_query'      => array(
					array(
						'taxonomy' => 'wptests_tax',
						'field'    => 'term_id',
						'terms'    => array( self::$terms[0], self::$terms[1] ),
					),
				),
				'posts_per_page' => 1,
			)
		);
		$this->assertSame( 2, $query->get_count() );
	}
}
