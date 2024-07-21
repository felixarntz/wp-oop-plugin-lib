<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

class Term_Tests extends Test_Case {

	private static $term_id;

	private $term;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$term_id = $factory->term->create(
			array(
				'taxonomy' => 'category',
				'name'     => 'Yet Another Category',
				'slug'     => 'yet-another-category',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		wp_delete_term( self::$term_id, 'category' );
	}

	public function set_up() {
		parent::set_up();

		$this->term = new Term( get_term( self::$term_id ) );
	}

	public function test_get_id() {
		$this->assertSame( self::$term_id, $this->term->get_id() );
	}

	public function test_is_public() {
		$this->assertTrue( $this->term->is_public() );
	}

	public function test_get_url() {
		$this->set_permalink_structure( '/%postname%/' );

		// Ensure the 'category' permastruct exists (which would normally happen during taxonomy registration).
		$tax          = get_taxonomy( 'category' );
		$tax->rewrite = array(
			'hierarchical' => true,
			'slug'         => get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category',
			'with_front'   => ! get_option( 'category_base' ) || $GLOBALS['wp_rewrite']->using_index_permalinks(),
			'ep_mask'      => EP_CATEGORIES,
		);
		$tax->add_rewrite_rules();
		flush_rewrite_rules();

		$this->assertSame(
			home_url( '/category/yet-another-category/' ),
			$this->term->get_url()
		);
	}

	public function test_get_edit_url() {
		$this->assertSame( '', $this->term->get_edit_url() );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$this->assertSame(
			admin_url( sprintf( 'term.php?taxonomy=category&tag_ID=%d&post_type=post', self::$term_id ) ),
			$this->term->get_edit_url()
		);
	}

	/**
	 * @dataProvider data_get_field_value
	 */
	public function test_get_field_value( string $field, $expected_value ) {
		$this->assertSame( $expected_value, $this->term->get_field_value( $field ) );
	}

	public function data_get_field_value() {
		return array(
			'invalid'        => array(
				'some_field',
				null,
			),
			'name'           => array(
				'name',
				'Yet Another Category',
			),
			'slug'           => array(
				'slug',
				'yet-another-category',
			),
			'taxonomy'       => array(
				'taxonomy',
				'category',
			),
		);
	}
}
