<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Query
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Term_Query_Tests extends Test_Case {

	private static $terms;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		register_taxonomy( 'wptests_tax', 'post' );

		// Create two terms of the custom taxonomy and a third term that is a category with custom name.
		self::$terms = $factory->term->create_many(
			2,
			array(
				'taxonomy' => 'wptests_tax',
			)
		);
		self::$terms[] = $factory->term->create(
			array(
				'taxonomy' => 'category',
				'name'     => 'crazy',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		wp_delete_term( self::$terms[0], 'wptests_tax' );
		wp_delete_term( self::$terms[1], 'wptests_tax' );
		wp_delete_term( self::$terms[2], 'category' );

		unregister_taxonomy( 'wptests_tax' );
	}

	/**
	 * @dataProvider data_queries
	 */
	public function test_get_entities( array $query_args, array $expected_ids ) {
		$query_args   = $this->populate_with_ids(
			$query_args,
			array( 'terms' => self::$terms )
		);
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'terms' => self::$terms ) );

		$query    = new Term_Query( $query_args );
		$entities = $query->get_entities();
		$this->assertContainsOnlyInstancesOf( Term::class, $entities );
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
			array( 'terms' => self::$terms )
		);
		$expected_ids = $this->populate_with_ids( $expected_ids, array( 'terms' => self::$terms ) );

		$query = new Term_Query( $query_args );
		$this->assertSame( $expected_ids, $query->get_ids() );
	}

	public function data_queries() {
		return array(
			'query for term'           => array(
				array(
					'taxonomy'   => 'wptests_tax',
					'include'    => array( 'terms::0' ),
					'hide_empty' => false,
				),
				array( 'terms::0' ),
			),
			'query with taxonomy'      => array(
				array(
					'taxonomy'   => 'wptests_tax',
					'orderby'    => 'term_id',
					'order'      => 'ASC',
					'hide_empty' => false,
				),
				array( 'terms::0', 'terms::1' ),
			),
			'query with name'          => array(
				array(
					'taxonomy'   => 'category',
					'name'       => 'crazy',
					'hide_empty' => false,
				),
				array( 'terms::2' ),
			),
			'query with search'        => array(
				array(
					'search'     => 'crazy',
					'hide_empty' => false,
				),
				array( 'terms::2' ),
			),
			'query with number limit'  => array(
				array(
					'include'    => array( 'terms::0', 'terms::1' ),
					'number'     => 1,
					'orderby'    => 'term_id',
					'order'      => 'DESC',
					'hide_empty' => false,
				),
				array( 'terms::1' ),
			),
		);
	}

	public function test_get_count() {
		$query = new Term_Query(
			array(
				'taxonomy'   => 'wptests_tax',
				'number'     => 1,
				'hide_empty' => false,
			)
		);
		$this->assertSame( 2, $query->get_count() );
	}
}
