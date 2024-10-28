<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Script_Registry
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Dependencies;

use Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Script_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group dependencies
 */
class Script_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();

		$this->registry = new Script_Registry();
	}

	public function tear_down() {
		parent::tear_down();

		unset( $GLOBALS['wp_scripts'] );
	}

	/**
	 * @dataProvider data_register
	 */
	public function test_register( string $key, array $args, array $expected_data = array() ) {
		$this->assertTrue( $this->registry->register( $key, $args ) );

		$this->assertTrue( wp_script_is( $key, 'registered' ) );

		foreach ( $expected_data as $field => $value ) {
			if ( in_array( $field, array( 'src', 'deps', 'ver', 'args' ), true ) ) {
				$this->assertSame( $value, wp_scripts()->registered[ $key ]->$field );
			} elseif ( 'strategy' === $field && version_compare( $GLOBALS['wp_version'], '6.3', '<' ) ) {
				// Strategy is only supported in WordPress 6.3 and later.
				$this->assertFalse( wp_scripts()->get_data( $key, $field ) );
			} else {
				$this->assertSame( $value, wp_scripts()->get_data( $key, $field ) );
			}
		}
	}

	public function data_register() {
		return array(
			'basic script'           => array(
				'basic_script',
				array(
					'src' => 'basic-script.js',
					'ver' => '1.0.0',
				),
				array(
					'src'   => 'basic-script.js',
					'ver'   => '1.0.0',
					'group' => false,
				),
			),
			'alias script'           => array(
				'alias_script',
				array(
					'ver' => '1.0',
				),
				array(
					'src'   => false,
					'ver'   => '1.0',
					'group' => false,
				),
			),
			'script with deps'       => array(
				'script_with_deps',
				array(
					'src'  => 'script-with-deps.js',
					'deps' => array( 'jquery', 'wp-util' ),
				),
				array(
					'src'   => 'script-with-deps.js',
					'deps'  => array( 'jquery', 'wp-util' ),
					'ver'   => false,
					'group' => false,
				),
			),
			'script without version' => array(
				'script_without_version',
				array(
					'src' => 'script-without-version.js',
					'ver' => null,
				),
				array(
					'src'   => 'script-without-version.js',
					'ver'   => null,
					'group' => false,
				),
			),
			'script with alt args'   => array(
				'script_with_alt_args',
				array(
					'src'          => 'script-with-alt-args.js',
					'dependencies' => array( 'wp-dom-ready' ),
					'version'      => '2.0',
				),
				array(
					'src'   => 'script-with-alt-args.js',
					'deps'  => array( 'wp-dom-ready' ),
					'ver'   => '2.0',
					'group' => false,
				),
			),
			'script with strategy'   => array(
				'script_with_strategy',
				array(
					'src'       => 'script-with-strategy.js',
					'in_footer' => false,
					'strategy'  => 'async',
				),
				array(
					'src'      => 'script-with-strategy.js',
					'group'    => false,
					'strategy' => 'async',
				),
			),
			'script in footer'       => array(
				'script_in_footer',
				array(
					'src'       => 'script-in-footer.js',
					'in_footer' => true,
				),
				array(
					'src'      => 'script-in-footer.js',
					'group'    => 1,
				),
			),
			'script with manifest'   => array(
				'script_with_manifest',
				array(
					'src'      => 'script-with-manifest.js',
					'manifest' => dirname( __DIR__, 2 ) . '/includes/fixtures/dependency-manifest.php',
				),
				array(
					'src'  => 'script-with-manifest.js',
					'deps' => array( 'wp-element', 'wp-i18n' ),
					'ver'  => '1.2.3',
				),
			),
		);
	}

	public function test_is_registered() {
		$this->assertFalse( $this->registry->is_registered( 'test_handle' ) );

		wp_register_script( 'test_handle', 'my-test-script.min.js' );

		$this->assertTrue( $this->registry->is_registered( 'test_handle' ) );
	}

	public function test_get_registered() {
		$this->assertNull( $this->registry->get_registered( 'test_handle' ) );

		wp_register_script( 'test_handle', 'my-test-script.min.js' );

		$script = $this->registry->get_registered( 'test_handle' );
		$this->assertInstanceOf( '_WP_Dependency', $script );
		$this->assertSame( 'test_handle', $script->handle );
		$this->assertSame( 'my-test-script.min.js', $script->src );
	}

	public function test_get_all_registered() {
		$this->assertSame( wp_scripts()->registered, $this->registry->get_all_registered() );
	}

	public function test_enqueue() {
		wp_register_script( 'test_script', 'test-script.js' );
		$this->assertFalse( wp_script_is( 'test_script', 'enqueued' ) );

		$this->registry->enqueue( 'test_script' );
		$this->assertTrue( wp_script_is( 'test_script', 'enqueued' ) );
	}

	public function test_dequeue() {
		wp_enqueue_script( 'test_script', 'test-script.js' );
		$this->assertTrue( wp_script_is( 'test_script', 'enqueued' ) );

		$this->registry->dequeue( 'test_script' );
		$this->assertFalse( wp_script_is( 'test_script', 'enqueued' ) );
	}

	public function test_is_enqueued() {
		$this->assertFalse( $this->registry->is_enqueued( 'test_script' ) );

		wp_enqueue_script( 'test_script', 'test-script.js' );

		$this->assertTrue( $this->registry->is_enqueued( 'test_script' ) );
	}

	public function test_add_inline_code() {
		$js = 'console.log( "Hello, world!" );';

		wp_enqueue_script( 'test_script', 'test-script.js' );
		$this->registry->add_inline_code( 'test_script', $js );

		if ( ! method_exists( wp_scripts(), 'get_inline_script_data' ) ) {
			$this->assertSame( array( $js ), array_values( array_filter( wp_scripts()->get_data( 'test_script', 'after' ) ) ) );
		} else {
			$this->assertSame( $js, wp_scripts()->get_inline_script_data( 'test_script' ) );
		}
	}
}
