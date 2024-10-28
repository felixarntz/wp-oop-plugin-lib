<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Taxonomy_Registry
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Taxonomy_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_Taxonomy;

/**
 * @group entities
 */
class Taxonomy_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();

		$this->registry = new Taxonomy_Registry();
	}

	public function test_register() {
		$this->registry->register(
			'test_tax',
			array(
				'object_type' => 'post',
				'label'       => 'Test Taxonomy',
				'public'      => true,
			)
		);

		$taxonomy_exists = taxonomy_exists( 'test_tax' );

		// Cleanup.
		unregister_taxonomy( 'test_tax' );

		$this->assertTrue( $taxonomy_exists );
	}

	public function test_is_registered() {
		$this->assertTrue( $this->registry->is_registered( 'post_tag' ) );
		$this->assertFalse( $this->registry->is_registered( 'test_tax' ) );
	}

	public function test_get_registered() {
		$taxonomy = $this->registry->get_registered( 'category' );
		$this->assertInstanceOf( WP_Taxonomy::class, $taxonomy );
		$this->assertSame( 'category', $taxonomy->name );
	}

	public function test_get_all_registered() {
		$this->assertSame(
			get_taxonomies( array(), 'objects' ),
			$this->registry->get_all_registered()
		);
	}
}
