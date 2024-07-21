<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Exception\Invalid_Entity_Data_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Term_Repository_Tests extends Test_Case {

	private static $term_id;

	private $repository;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$term_id = $factory->term->create( array( 'taxonomy' => 'category' ));
	}

	public static function wpTearDownAfterClass() {
		wp_delete_term( self::$term_id, 'category' );
	}

	public function set_up() {
		parent::set_up();

		$this->repository = new Term_Repository();
	}

	public function test_exists() {
		$this->assertFalse( $this->repository->exists( 9999 ) );

		$this->assertTrue( $this->repository->exists( self::$term_id ) );
	}

	public function test_get() {
		$this->assertNull( $this->repository->get( 9999 ) );

		$term = $this->repository->get( self::$term_id );
		$this->assertInstanceOf( Term::class, $term );
		$this->assertSame( self::$term_id, $term->get_id() );
	}

	public function test_update() {
		$this->assertTrue(
			$this->repository->update(
				self::$term_id,
				array( 'name' => 'A new name' )
			)
		);
		$this->assertSame( 'A new name', get_term( self::$term_id )->name );
	}

	public function test_update_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertFalse(
			$this->repository->update(
				self::$term_id,
				array(
					'name' => '',
				)
			)
		);
	}

	public function test_add() {
		$new_id = $this->repository->add(
			array(
				'name'     => 'Newly created term',
				'taxonomy' => 'category',
			)
		);
		$this->assertNotSame( 0, $new_id );
		$this->assertSame( 'Newly created term', get_term( $new_id )->name );
	}

	public function test_add_with_error() {
		if ( WP_DEBUG ) {
			$this->expectException( Invalid_Entity_Data_Exception::class );
		}
		$this->assertSame(
			0,
			$this->repository->add(
				array(
					'name'     => '',
					'taxonomy' => 'category',
				)
			)
		);
	}

	public function test_delete() {
		$this->assertTrue( $this->repository->delete( self::$term_id ) );
		$this->assertNull( get_term( self::$term_id ) );
	}

	public function test_query() {
		$query = $this->repository->query(
			array(
				'include'    => array( self::$term_id ),
				'hide_empty' => false,
			)
		);
		$this->assertInstanceOf( Term_Query::class, $query );
		$this->assertSame( array( self::$term_id ), $query->get_ids() );
	}

	public function test_prime_caches() {
		wp_cache_delete( self::$term_id, 'terms' );
		$this->assertTrue( $this->repository->prime_caches( array( self::$term_id ) ) );
		$this->assertNotFalse( wp_cache_get( self::$term_id, 'terms' ) );
	}
}
