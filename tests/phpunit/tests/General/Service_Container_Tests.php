<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Service_Container
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Menu;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Plugin_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Service_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Service_Container_Tests extends Test_Case {

	private $container;

	public function set_up() {
		parent::set_up();
		$this->container = new Service_Container();

		$this->container->set(
			'main_file',
			static function () {
				return WP_PLUGIN_DIR . '/some-plugin/load.php';
			}
		);
		$this->container->set(
			'version',
			static function () {
				return '1.0.0';
			}
		);
		$this->container->set(
			'plugin_env',
			static function ( $cont ) {
				return new Plugin_Env( $cont['main_file'], $cont['version'] );
			}
		);

		$this->container->set(
			'option_repository',
			static function () {
				return new Option_Repository();
			}
		);
		$this->container->set(
			'test_option',
			static function ( $cont ) {
				return new Option(
					$cont['option_repository'],
					'test_option',
					array(
						'type'     => 'string',
						'default'  => 'test-value',
						'autoload' => false,
					)
				);
			}
		);

		// This service is accessing another service that does not exist.
		$this->container->set(
			'with_missing_dependency',
			static function ( $cont ) {
				return new Admin_Menu( $cont['admin_menu_slug'] );
			}
		);
	}

	/**
	 * @dataProvider data_has
	 */
	public function test_has( $service_key, $expected ) {
		if ( $expected ) {
			$this->assertTrue( $this->container->has( $service_key ) );
		} else {
			$this->assertFalse( $this->container->has( $service_key ) );
		}
	}

	public function data_has() {
		return array(
			'main_file'               => array(
				'main_file',
				true,
			),
			'version'                 => array(
				'version',
				true,
			),
			'plugin_env'              => array(
				'plugin_env',
				true,
			),
			'option_repository'       => array(
				'option_repository',
				true,
			),
			'test_option'             => array(
				'test_option',
				true,
			),
			'with_missing_dependency' => array(
				'with_missing_dependency',
				true,
			),
			'missing'                 => array(
				'missing',
				false,
			),
		);
	}

	public function test_get_simple() {
		$this->assertSame( WP_PLUGIN_DIR . '/some-plugin/load.php', $this->container->get( 'main_file' ) );
		$this->assertSame( '1.0.0', $this->container->get( 'version' ) );
	}

	public function test_get_with_dependency() {
		$plugin_env = $this->container->get( 'plugin_env' );
		$this->assertInstanceOf( Plugin_Env::class, $plugin_env );
		$this->assertSame( WP_PLUGIN_DIR . '/some-plugin/load.php', $plugin_env->main_file() );
		$this->assertSame( '1.0.0', $plugin_env->version() );
	}

	public function test_get_with_dependency_instance() {
		$test_option = $this->container->get( 'test_option' );
		$this->assertInstanceOf( Option::class, $test_option );
		$option_repository = $this->get_hidden_property_value( $test_option, 'repository' );
		$this->assertSame( $this->container->get( 'option_repository' ), $option_repository );
	}

	public function test_get_with_missing() {
		$this->expectException( Not_Found_Exception::class );
		$this->expectExceptionMessage( 'Service with key missing was not found in container' );
		$this->container->get( 'missing' );
	}

	public function test_get_with_missing_dependency() {
		$this->expectException( Not_Found_Exception::class );
		$this->expectExceptionMessage( 'Service with key admin_menu_slug was not found in container' );
		$this->container->get( 'with_missing_dependency' );
	}

	public function test_set_simple() {
		$this->assertFalse( $this->container->has( 'admin_menu' ) );
		$this->container->set(
			'admin_menu',
			static function () {
				return new Admin_Menu( 'options-general.php' );
			}
		);
		$this->assertTrue( $this->container->has( 'admin_menu' ) );
		$this->assertInstanceOf( Admin_Menu::class, $this->container->get( 'admin_menu' ) );
	}

	public function test_set_with_override() {
		$this->assertTrue( $this->container->has( 'test_option' ) );
		$this->assertSame( 'test-value', $this->container->get( 'test_option' )->get_value() );
		$this->container->set(
			'test_option',
			static function ( $cont ) {
				return new Option(
					$cont['option_repository'],
					'test_option',
					array(
						'type'     => 'integer',
						'default'  => 23,
						'autoload' => false,
					)
				);
			}
		);
		$this->assertSame( 23, $this->container->get( 'test_option' )->get_value() );
	}

	public function test_unset() {
		// Resolve the service prior to its removal.
		$this->container->get( 'option_repository' );

		// Remove it.
		$this->container->unset( 'option_repository' );
		$this->assertFalse( $this->container->has( 'option_repository' ) );

		// Ensure the already resolved instance was wiped as intended.
		$this->expectException( Not_Found_Exception::class );
		$this->container->get( 'option_repository' );
	}

	public function test_get_keys() {
		$this->assertSame(
			array(
				'main_file',
				'version',
				'plugin_env',
				'option_repository',
				'test_option',
				'with_missing_dependency',
			),
			$this->container->get_keys()
		);
	}

	public function test_listen_simple() {
		$called = 0;

		$this->container->listen(
			'plugin_env',
			function() use ( &$called ) {
				$called++;
			}
		);

		$this->assertSame( 0, $called );

		// Resolve the service.
		$this->container->get( 'plugin_env' );
		$this->assertSame( 1, $called );

		// Getting the service again should not resolve, i.e. not rerun the listeners.
		$this->container->get( 'plugin_env' );
		$this->assertSame( 1, $called );

		// Overriding the service should instantly lead to resolving, i.e. rerunning the listener.
		$this->container->set(
			'plugin_env',
			static function () {
				return new Plugin_env( WP_PLUGIN_DIR . '/plugin/plugin.php', '2.5.0' );
			}
		);
		$this->assertSame( 2, $called );
	}

	public function test_listen_with_override_dependency() {
		// Resolve the version and plugin_env services.
		$this->assertSame( '1.0.0', $this->container->get( 'plugin_env' )->version() );

		// Add a listener that creates a new Plugin_Env service if the version changes.
		$new_version = null;
		$this->container->listen(
			'version',
			function ( $version, $cont ) use ( &$new_version ) {
				$cont->set(
					'plugin_env',
					static function ( $cont ) {
						return new Plugin_Env( $cont['main_file'], $cont['version'] );
					}
				);
			}
		);

		// Override the version service.
		$this->container->set(
			'version',
			static function () {
				return '1.1.0';
			}
		);

		// Ensure the plugin_env service was properly recreated based on the listener.
		$this->assertSame( '1.1.0', $this->container->get( 'plugin_env' )->version() );
	}

	public function test_offsetExists() {
		$this->assertSame( $this->container->has( 'plugin_env' ), isset( $this->container['plugin_env'] ) );
	}

	public function test_offsetGet() {
		$this->assertSame( $this->container->get( 'plugin_env' ), $this->container['plugin_env'] );
	}

	public function test_offsetSet() {
		$this->assertFalse( $this->container->has( 'admin_menu' ) );
		$this->container['admin_menu'] = static function () {
			return new Admin_Menu( 'options-general.php' );
		};
		$this->assertTrue( $this->container->has( 'admin_menu' ) );
	}

	public function test_offsetUnset() {
		unset( $this->container['option_repository'] );
		$this->assertFalse( $this->container->has( 'option_repository' ) );
	}
}
