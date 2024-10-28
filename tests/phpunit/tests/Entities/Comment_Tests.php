<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group entities
 */
class Comment_Tests extends Test_Case {

	private static $post_id;
	private static $comment_id;

	private $comment;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create(
			array(
				'post_name'   => 'post-with-a-comment',
				'post_status' => 'publish',
			)
		);

		self::$comment_id = $factory->comment->create(
			array(
				'comment_post_ID'  => self::$post_id,
				'comment_content'  => 'Hello!',
				'comment_approved' => '0',
			)
		);
	}

	public static function wpTearDownAfterClass() {
		wp_delete_comment( self::$comment_id, true );
		wp_delete_post( self::$post_id, true );
	}

	public function set_up() {
		parent::set_up();

		$this->comment = new Comment( get_comment( self::$comment_id ) );
	}

	public function test_get_id() {
		$this->assertSame( self::$comment_id, $this->comment->get_id() );
	}

	public function test_is_public() {
		$this->assertFalse( $this->comment->is_public() );

		// Update the comment status in the database, but also in the internal WP_Comment object.
		wp_set_comment_status( self::$comment_id, 'approve' );
		$wp_comment = $this->get_hidden_property_value( $this->comment, 'wp_obj' );
		$wp_comment->comment_approved = '1';

		$this->assertTrue( $this->comment->is_public() );
	}

	public function test_get_url() {
		$this->set_permalink_structure( '/%postname%/' );

		$this->assertSame(
			home_url( '/post-with-a-comment/' ) . sprintf( '#comment-%d', self::$comment_id ),
			$this->comment->get_url()
		);
	}

	public function test_get_edit_url() {
		$this->assertSame( '', $this->comment->get_edit_url() );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$this->assertSame(
			admin_url( sprintf( 'comment.php?action=editcomment&c=%d', self::$comment_id ) ),
			$this->comment->get_edit_url()
		);
	}

	/**
	 * @dataProvider data_get_field_value
	 */
	public function test_get_field_value( string $field, $expected_value ) {
		$this->assertSame( $expected_value, $this->comment->get_field_value( $field ) );
	}

	public function data_get_field_value() {
		return array(
			'invalid'        => array(
				'some_field',
				null,
			),
			'comment_content'      => array(
				'comment_content',
				'Hello!',
			),
			'comment_status'       => array(
				'comment_status',
				'unapproved',
			),
			'comment_approved'     => array(
				'comment_approved',
				'0',
			),
		);
	}
}
