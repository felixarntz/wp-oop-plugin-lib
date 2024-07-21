<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Type_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Type_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_Post_Type;

class Post_Type_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();

		$this->registry = new Post_Type_Registry();
	}

	public function test_register() {
		$this->registry->register(
			'test_pt',
			array(
				'label'  => 'Test Entries',
				'public' => true,
			)
		);

		$post_type_exists = post_type_exists( 'test_pt' );

		// Cleanup.
		unregister_post_type( 'test_pt' );

		$this->assertTrue( $post_type_exists );
	}

	public function test_is_registered() {
		$this->assertTrue( $this->registry->is_registered( 'page' ) );
		$this->assertFalse( $this->registry->is_registered( 'test_pt' ) );
	}

	public function test_get_registered() {
		$post_type = $this->registry->get_registered( 'attachment' );
		$this->assertInstanceOf( WP_Post_Type::class, $post_type );
		$this->assertSame( 'attachment', $post_type->name );
	}

	public function test_get_all_registered() {
		$this->assertSame(
			get_post_types( array(), 'objects' ),
			$this->registry->get_all_registered()
		);
	}
}
