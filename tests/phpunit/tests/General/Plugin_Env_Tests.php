<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Plugin_Env
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Plugin_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Plugin_Env_Tests extends Test_Case {

	private $plugin_env;

	public function set_up() {
		parent::set_up();

		$this->plugin_env = new Plugin_Env(
			WP_PLUGIN_DIR . '/my-plugin/load.php',
			'1.2.3'
		);
	}

	public function test_main_file() {
		$this->assertSame( WP_PLUGIN_DIR . '/my-plugin/load.php', $this->plugin_env->main_file() );
	}

	public function test_version() {
		$this->assertSame( '1.2.3', $this->plugin_env->version() );
	}

	public function test_basename() {
		$this->assertSame( 'my-plugin/load.php', $this->plugin_env->basename() );
	}

	/**
	 * @dataProvider data_path
	 */
	public function test_path( $path, $expected ) {
		$this->assertSame( $expected, $this->plugin_env->path( $path ) );
	}

	public static function data_path() {
		return array(
			'file'      => array(
				'a-file.php',
				WP_PLUGIN_DIR . '/my-plugin/a-file.php',
			),
			'directory' => array(
				'assets/js/index.min.js',
				WP_PLUGIN_DIR . '/my-plugin/assets/js/index.min.js',
			),
			'empty'     => array(
				'',
				WP_PLUGIN_DIR . '/my-plugin/',
			),
			'slash'     => array(
				'/',
				WP_PLUGIN_DIR . '/my-plugin/',
			),
		);
	}

	/**
	 * @dataProvider data_url
	 */
	public function test_url( $url, $expected ) {
		$this->assertSame( $expected, $this->plugin_env->url( $url ) );
	}

	public static function data_url() {
		return array(
			'file'      => array(
				'a-file.php',
				WP_PLUGIN_URL . '/my-plugin/a-file.php',
			),
			'directory' => array(
				'assets/js/index.min.js',
				WP_PLUGIN_URL . '/my-plugin/assets/js/index.min.js',
			),
			'empty'     => array(
				'',
				WP_PLUGIN_URL . '/my-plugin/',
			),
			'slash'     => array(
				'/',
				WP_PLUGIN_URL . '/my-plugin/',
			),
		);
	}
}
