<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Style_Registry
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Dependencies;

use Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Style_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group dependencies
 */
class Style_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();

		$this->registry = new Style_Registry();
	}

	public function tear_down() {
		parent::tear_down();

		unset( $GLOBALS['wp_styles'] );
	}

	/**
	 * @dataProvider data_register
	 */
	public function test_register( string $key, array $args, array $expected_data = array() ) {
		$this->assertTrue( $this->registry->register( $key, $args ) );

		$this->assertTrue( wp_style_is( $key, 'registered' ) );

		foreach ( $expected_data as $field => $value ) {
			if ( in_array( $field, array( 'src', 'deps', 'ver', 'args' ), true ) ) {
				$this->assertSame( $value, wp_styles()->registered[ $key ]->$field );
			} else {
				$this->assertSame( $value, wp_styles()->get_data( $key, $field ) );
			}
		}
	}

	public function data_register() {
		return array(
			'basic style'           => array(
				'basic_style',
				array(
					'src' => 'basic-style.css',
					'ver' => '1.0.0',
				),
				array(
					'src'  => 'basic-style.css',
					'ver'  => '1.0.0',
					'args' => 'all',
				),
			),
			'alias style'           => array(
				'alias_style',
				array(
					'ver' => '1.0',
				),
				array(
					'src'  => false,
					'ver'  => '1.0',
					'args' => 'all',
				),
			),
			'style with deps'       => array(
				'style_with_deps',
				array(
					'src'  => 'style-with-deps.css',
					'deps' => array( 'forms' ),
				),
				array(
					'src'   => 'style-with-deps.css',
					'deps'  => array( 'forms' ),
					'ver'   => false,
					'args' => 'all',
				),
			),
			'style without version' => array(
				'style_without_version',
				array(
					'src' => 'style-without-version.css',
					'ver' => null,
				),
				array(
					'src'   => 'style-without-version.css',
					'ver'   => null,
					'args' => 'all',
				),
			),
			'style with alt args'   => array(
				'style_with_alt_args',
				array(
					'src'          => 'style-with-alt-args.css',
					'dependencies' => array( 'buttons' ),
					'version'      => '2.0',
				),
				array(
					'src'   => 'style-with-alt-args.css',
					'deps'  => array( 'buttons' ),
					'ver'   => '2.0',
					'args' => 'all',
				),
			),
			'style with media'   => array(
				'style_with_media',
				array(
					'src'   => 'style-with-media.css',
					'media' => 'print',
				),
				array(
					'src'  => 'style-with-media.css',
					'args' => 'print',
				),
			),
			'style with path'       => array(
				'style_with_path',
				array(
					'src'  => 'style-with-path.css',
					'path' => dirname( __DIR__, 2 ) . '/includes/fixtures/style-with-path.css',
				),
				array(
					'src'  => 'style-with-path.css',
					'path' => dirname( __DIR__, 2 ) . '/includes/fixtures/style-with-path.css',
					'rtl'  => false,
				),
			),
			'style with RTL'       => array(
				'style_with_rtl',
				array(
					'src'  => 'style-with-rtl.css',
					'path' => dirname( __DIR__, 2 ) . '/includes/fixtures/style-with-rtl.css',
				),
				array(
					'src'  => 'style-with-rtl.css',
					'path' => dirname( __DIR__, 2 ) . '/includes/fixtures/style-with-rtl.css',
					'rtl'  => 'replace',
				),
			),
			'style with manifest'   => array(
				'style_with_manifest',
				array(
					'src'      => 'style-with-manifest.css',
					'manifest' => dirname( __DIR__, 2 ) . '/includes/fixtures/dependency-manifest.php',
				),
				array(
					'src'  => 'style-with-manifest.css',
					'deps' => array( 'wp-element', 'wp-i18n' ),
					'ver'  => '1.2.3',
				),
			),
		);
	}

	public function test_is_registered() {
		$this->assertFalse( $this->registry->is_registered( 'test_handle' ) );

		wp_register_style( 'test_handle', 'my-test-style.min.css' );

		$this->assertTrue( $this->registry->is_registered( 'test_handle' ) );
	}

	public function test_get_registered() {
		$this->assertNull( $this->registry->get_registered( 'test_handle' ) );

		wp_register_style( 'test_handle', 'my-test-style.min.css' );

		$style = $this->registry->get_registered( 'test_handle' );
		$this->assertInstanceOf( '_WP_Dependency', $style );
		$this->assertSame( 'test_handle', $style->handle );
		$this->assertSame( 'my-test-style.min.css', $style->src );
	}

	public function test_get_all_registered() {
		$this->assertSame( wp_styles()->registered, $this->registry->get_all_registered() );
	}

	public function test_enqueue() {
		wp_register_style( 'test_style', 'test-style.css' );
		$this->assertFalse( wp_style_is( 'test_style', 'enqueued' ) );

		$this->registry->enqueue( 'test_style' );
		$this->assertTrue( wp_style_is( 'test_style', 'enqueued' ) );
	}

	public function test_dequeue() {
		wp_enqueue_style( 'test_style', 'test-style.css' );
		$this->assertTrue( wp_style_is( 'test_style', 'enqueued' ) );

		$this->registry->dequeue( 'test_style' );
		$this->assertFalse( wp_style_is( 'test_style', 'enqueued' ) );
	}

	public function test_is_enqueued() {
		$this->assertFalse( $this->registry->is_enqueued( 'test_style' ) );

		wp_enqueue_style( 'test_style', 'test-style.css' );

		$this->assertTrue( $this->registry->is_enqueued( 'test_style' ) );
	}

	public function test_add_inline_code() {
		$css = '.test { text-align: right; }';

		wp_enqueue_style( 'test_style', 'test-style.css' );
		$this->registry->add_inline_code( 'test_style', $css );

		$this->assertSame( array( $css ), wp_styles()->get_data( 'test_style', 'after' ) );
	}
}
