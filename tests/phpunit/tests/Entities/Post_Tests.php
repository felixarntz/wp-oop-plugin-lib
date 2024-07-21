<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

class Post_Tests extends Test_Case {

	private static $post_id;

	private $post;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create(
			array(
				'post_title'  => 'A special post',
				'post_name'   => 'a-special-post',
				'post_type'   => 'post',
				'post_status' => 'draft',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		wp_delete_post( self::$post_id, true );
	}

	public function set_up() {
		parent::set_up();

		$this->post = new Post( get_post( self::$post_id ) );
	}

	public function test_get_id() {
		$this->assertSame( self::$post_id, $this->post->get_id() );
	}

	public function test_is_public() {
		$this->assertFalse( $this->post->is_public() );

		// Update the post status in the database, but also in the internal WP_Post object.
		wp_update_post(
			array(
				'ID'          => self::$post_id,
				'post_status' => 'publish',
			)
		);
		$wp_post = $this->get_hidden_property_value( $this->post, 'wp_obj' );
		$wp_post->post_status = 'publish';

		$this->assertTrue( $this->post->is_public() );
	}

	public function test_get_url() {
		$this->set_permalink_structure( '/%postname%/' );

		// Due to wp_force_plain_post_permalink() being called in get_permalink(), we need to publish the post.
		wp_update_post(
			array(
				'ID'          => self::$post_id,
				'post_status' => 'publish',
			)
		);
		$wp_post = $this->get_hidden_property_value( $this->post, 'wp_obj' );
		$wp_post->post_status = 'publish';

		$this->assertSame(
			home_url( '/a-special-post/' ),
			$this->post->get_url()
		);
	}

	public function test_get_edit_url() {
		$this->assertSame( '', $this->post->get_edit_url() );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$this->assertSame(
			admin_url( sprintf( 'post.php?post=%d&action=edit', self::$post_id ) ),
			$this->post->get_edit_url()
		);
	}

	/**
	 * @dataProvider data_get_field_value
	 */
	public function test_get_field_value( string $field, $expected_value ) {
		$this->assertSame( $expected_value, $this->post->get_field_value( $field ) );
	}

	public function data_get_field_value() {
		return array(
			'invalid'        => array(
				'some_field',
				null,
			),
			'post_title'     => array(
				'post_title',
				'A special post',
			),
			'post_name'      => array(
				'post_name',
				'a-special-post',
			),
			'post_type'      => array(
				'post_type',
				'post',
			),
			'post_status'    => array(
				'post_status',
				'draft',
			),
			'comment_status' => array(
				'comment_status',
				'open',
			),
			'ping_status'    => array(
				'ping_status',
				'open',
			),
		);
	}
}
