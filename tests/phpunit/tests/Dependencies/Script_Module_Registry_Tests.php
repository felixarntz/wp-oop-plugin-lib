<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Script_Module_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Dependencies;

use Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Script_Module_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Script_Module_Registry_Tests extends Test_Case {

	private $registry;

	public function set_up() {
		parent::set_up();

		$this->registry = new Script_Module_Registry();
	}

	public function tear_down() {
		parent::tear_down();

		unset( $GLOBALS['wp_script_modules'] );
	}

	/**
	 * @dataProvider data_register
	 */
	public function test_register( string $key, array $args, array $expected_data = array() ) {
		// Script modules are only supported in WordPress 6.5 and later.
		if ( version_compare( $GLOBALS['wp_version'], '6.5', '<' ) ) {
			$this->expectDoingItWrong( Script_Module_Registry::class . '::register' );
			$this->assertFalse( $this->registry->register( $key, $args ) );
			return;
		}

		$this->assertTrue( $this->registry->register( $key, $args ) );

		$registered = $this->get_hidden_property_value( wp_script_modules(), 'registered' );
		$this->assertTrue( isset( $registered[ $key ] ) );

		foreach ( $expected_data as $field => $value ) {
			$this->assertSame( $value, $registered[ $key ][ $field ] ?? null );
		}
	}

	public function data_register() {
		return array(
			'basic script module'           => array(
				'basic_script',
				array(
					'src' => 'basic-script-module.js',
					'ver' => '1.0.0',
				),
				array(
					'src'     => 'basic-script-module.js',
					'version' => '1.0.0',
				),
			),
			'script module with deps'       => array(
				'script_module_with_deps',
				array(
					'src'  => 'script-module-with-deps.js',
					'deps' => array( 'wp-interactivity' ),
				),
				array(
					'src'          => 'script-module-with-deps.js',
					'dependencies' => array(
						array(
							'id'     => 'wp-interactivity',
							'import' => 'static',
						),
					),
					'version'      => false,
				),
			),
			'script module without version' => array(
				'script_module_without_version',
				array(
					'src' => 'script-module-without-version.js',
					'ver' => null,
				),
				array(
					'src'     => 'script-module-without-version.js',
					'version' => null,
				),
			),
			'script module with alt args'   => array(
				'script_module_with_alt_args',
				array(
					'src'          => 'script-module-with-alt-args.js',
					'dependencies' => array( 'wp-interactivity' ),
					'version'      => '2.0',
				),
				array(
					'src'          => 'script-module-with-alt-args.js',
					'dependencies' => array(
						array(
							'id'     => 'wp-interactivity',
							'import' => 'static',
						),
					),
					'version'      => '2.0',
				),
			),
			'script module with manifest'   => array(
				'script_module_with_manifest',
				array(
					'src'      => 'script-module-with-manifest.js',
					'manifest' => dirname( __DIR__, 2 ) . '/includes/fixtures/dependency-manifest.php',
				),
				array(
					'src'          => 'script-module-with-manifest.js',
					'dependencies' => array(
						array(
							'id'     => 'wp-element',
							'import' => 'static',
						),
						array(
							'id'     => 'wp-i18n',
							'import' => 'static',
						),
					),
					'version'      => '1.2.3',
				),
			),
		);
	}

	public function test_is_registered() {
		// Checking if a script module is registered is not supported.
		$this->expectDoingItWrong( Script_Module_Registry::class . '::is_registered' );
		$this->assertFalse( $this->registry->is_registered( 'test_handle' ) );
	}

	public function test_get_registered() {
		// Getting a script module is not supported.
		$this->expectDoingItWrong( Script_Module_Registry::class . '::get_registered' );
		$this->assertNull( $this->registry->get_registered( 'test_handle' ) );
	}

	public function test_get_all_registered() {
		// Getting script modules is not supported.
		$this->expectDoingItWrong( Script_Module_Registry::class . '::get_all_registered' );
		$this->assertSame( array(), $this->registry->get_all_registered() );
	}

	public function test_enqueue() {
		// Script modules are only supported in WordPress 6.5 and later.
		if ( version_compare( $GLOBALS['wp_version'], '6.5', '<' ) ) {
			$this->expectDoingItWrong( Script_Module_Registry::class . '::enqueue' );
			$this->assertFalse( $this->registry->enqueue( 'test_script_module' ) );
			return;
		}

		wp_register_script_module( 'test_script_module', 'test-script-module.js' );

		$registered = $this->get_hidden_property_value( wp_script_modules(), 'registered' );
		$this->assertFalse( $registered['test_script_module']['enqueue'] );

		$this->registry->enqueue( 'test_script_module' );

		$registered = $this->get_hidden_property_value( wp_script_modules(), 'registered' );
		$this->assertTrue( $registered['test_script_module']['enqueue'] );
	}

	public function test_dequeue() {
		// Script modules are only supported in WordPress 6.5 and later.
		if ( version_compare( $GLOBALS['wp_version'], '6.5', '<' ) ) {
			$this->expectDoingItWrong( Script_Module_Registry::class . '::dequeue' );
			$this->assertFalse( $this->registry->dequeue( 'test_script_module' ) );
			return;
		}

		wp_enqueue_script_module( 'test_script_module', 'test-script-module.js' );

		$registered = $this->get_hidden_property_value( wp_script_modules(), 'registered' );
		$this->assertTrue( $registered['test_script_module']['enqueue'] );

		$this->registry->dequeue( 'test_script_module' );

		$registered = $this->get_hidden_property_value( wp_script_modules(), 'registered' );
		$this->assertFalse( $registered['test_script_module']['enqueue'] );
	}

	public function test_is_enqueued() {
		// Checking if a script module is enqueued is not supported.
		$this->expectDoingItWrong( Script_Module_Registry::class . '::is_enqueued' );
		$this->assertFalse( $this->registry->is_enqueued( 'test_script_module' ) );
	}
}
