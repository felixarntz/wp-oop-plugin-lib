<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group meta
 */
class Meta_Key_Tests extends Test_Case {

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
		$this->repository = new Meta_Repository( 'post' );
	}

	public function test_has_value() {
		$meta_key = new Meta_Key(
			$this->repository,
			'test_meta',
			array(
				'type'        => 'string',
				'description' => 'Test meta key',
				'default'     => '',
			)
		);

		$this->assertFalse( $meta_key->has_value( self::$post_id ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertTrue( $meta_key->has_value( self::$post_id ) );
	}

	/**
	 * @dataProvider data_get_value
	 */
	public function test_get_value( $registration_args, $expected_default, $new_value, $expected_value ) {
		$meta_key = new Meta_Key(
			$this->repository,
			'test_meta',
			$registration_args
		);

		$this->assertSame( $expected_default, $meta_key->get_value( self::$post_id ) );

		if ( isset( $registration_args['single'] ) && ! $registration_args['single'] && is_array( $new_value ) ) {
			foreach ( $new_value as $single_value ) {
				add_post_meta( self::$post_id, 'test_meta', $single_value );
			}
		} else {
			update_post_meta( self::$post_id, 'test_meta', $new_value );
		}
		$this->assertSame( $expected_value, $meta_key->get_value( self::$post_id ) );
	}

	public function data_get_value() {
		return array(
			'no_type'          => array(
				array(
					'description' => 'Test meta key',
				),
				null,
				'some-value',
				'some-value',
			),
			'no_default'        => array(
				array(
					'type'        => 'string',
					'description' => 'Test meta key',
				),
				'',
				'some-value',
				'some-value',
			),
			'with_default'      => array(
				array(
					'type'        => 'string',
					'description' => 'Test meta key',
					'default'     => 'the default value',
				),
				'the default value',
				'some-value',
				'some-value',
			),
			'string'            => array(
				array(
					'type' => 'string',
				),
				'',
				123,
				'123',
			),
			'integer'           => array(
				array(
					'type' => 'integer',
				),
				0,
				'23',
				23,
			),
			'boolean'           => array(
				array(
					'type' => 'boolean',
				),
				false,
				'1',
				true,
			),
			'array'             => array(
				array(
					'type' => 'array',
				),
				array(),
				'some-value',
				array( 'some-value' ),
			),
			'single'             => array(
				array(
					'type'   => 'string',
					'single' => true,
				),
				'',
				'some-value',
				'some-value',
			),
			'not_single'         => array(
				array(
					'type'   => 'integer',
					'single' => false,
				),
				array(),
				'42',
				array( 42 ),
			),
			'not_single_default' => array(
				array(
					'type'   => 'integer',
					'single' => false,
					'default' => 23,
				),
				array( 23 ),
				'42',
				array( 42 ),
			),
			'not_single_bool'    => array(
				array(
					'type'   => 'boolean',
					'single' => false,
				),
				array(),
				false,
				array( false ),
			),
			'not_single_array'   => array(
				array(
					'type'   => 'integer',
					'single' => false,
				),
				array(),
				array( '42' ),
				array( 42 ),
			),
			'not_single_multi'   => array(
				array(
					'type'   => 'integer',
					'single' => false,
				),
				array(),
				array( '42', '23', 2, false ),
				array( 42, 23, 2, 0 ),
			),
		);
	}

	/**
	 * @dataProvider data_update_value
	 */
	public function test_update_value( $registration_args, $new_value, $expected_value ) {
		$meta_key = new Meta_Key(
			$this->repository,
			'test_meta',
			$registration_args
		);

		$this->assertTrue( $meta_key->update_value( self::$post_id, $new_value ) );
		if ( isset( $registration_args['single'] ) && ! $registration_args['single'] ) {
			$this->assertSame( $expected_value, get_post_meta( self::$post_id, 'test_meta' ) );
		} else {
			$this->assertSame( $expected_value, get_post_meta( self::$post_id, 'test_meta', true ) );
		}
	}

	public function data_update_value() {
		return array(
			'regular'         => array(
				array(
					'type' => 'string',
				),
				'some-value',
				'some-value',
			),
			'array'           => array(
				array(
					'type' => 'array',
				),
				array( 'some-value' ),
				array( 'some-value' ),
			),
			'single'          => array(
				array(
					'type'   => 'string',
					'single' => true,
				),
				'some-value',
				'some-value',
			),
			'not_single'      => array(
				array(
					'type'   => 'string',
					'single' => false,
				),
				'test-value',
				array( 'test-value' ),
			),
			'not_single_array' => array(
				array(
					'type'   => 'string',
					'single' => false,
				),
				array( 'test-value' ),
				array( 'test-value' ),
			),
			'not_single_multi' => array(
				array(
					'type'   => 'string',
					'single' => false,
				),
				array( 'test-value-1', 'test-value-2', 'test-value-3' ),
				array( 'test-value-1', 'test-value-2', 'test-value-3' ),
			),
		);
	}

	public function test_delete_value() {
		$meta_key = new Meta_Key(
			$this->repository,
			'test_meta',
			array( 'type' => 'string' )
		);

		$this->assertFalse( $meta_key->delete_value( self::$post_id ) );

		update_post_meta( self::$post_id, 'test_meta', 'some-value' );
		$this->assertTrue( $meta_key->delete_value( self::$post_id ) );
		$this->assertSame( '', get_post_meta( self::$post_id, 'test_meta', true ) );
	}

	public function test_get_key() {
		$meta_key = new Meta_Key(
			$this->repository,
			'some_meta_key',
			array( 'type' => 'string' )
		);
		$this->assertSame( 'some_meta_key', $meta_key->get_key() );
	}

	public function test_get_registration_args() {
		$registration_args = array(
			'type'        => 'integer',
			'description' => 'Some meta key',
			'default'     => 23,
		);
		$meta_key = new Meta_Key(
			$this->repository,
			'some_meta_key',
			$registration_args
		);
		$this->assertSame( $registration_args, $meta_key->get_registration_args() );
	}
}
