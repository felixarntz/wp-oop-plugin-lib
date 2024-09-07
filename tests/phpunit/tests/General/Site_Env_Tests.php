<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Site_Env
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Site_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Site_Env_Tests extends Test_Case {

	private $site_env;

	public function set_up() {
		parent::set_up();

		$this->site_env = new Site_Env();
	}

	public function test_id() {
		$this->assertSame( 1, $this->site_env->id() );
	}

	/**
	 * @dataProvider data_info
	 */
	public function test_info( $field, $expected ) {
		$this->assertSame( $expected, $this->site_env->info( $field ) );
	}

	public function data_info(): array {
		return array(
			'name' => array( 'name', WP_TESTS_TITLE ),
			'url'  => array( 'url', 'http://' . WP_TESTS_DOMAIN ),
		);
	}

	/**
	 * @dataProvider data_url
	 */
	public function test_url( $url, $expected ) {
		$this->assertSame( $expected, $this->site_env->url( $url ) );
	}

	public function data_url() {
		return array(
			'custom page' => array(
				'custom-page/',
				'http://' . WP_TESTS_DOMAIN . '/custom-page/',
			),
			'empty'       => array(
				'',
				'http://' . WP_TESTS_DOMAIN,
			),
			'slash'       => array(
				'/',
				'http://' . WP_TESTS_DOMAIN . '/',
			),
		);
	}

	/**
	 * @dataProvider data_wp_url
	 */
	public function test_wp_url( $url, $expected ) {
		$this->assertSame( $expected, $this->site_env->wp_url( $url ) );
	}

	public function data_wp_url() {
		return array(
			'wp-includes' => array(
				'wp-includes/post.php',
				'http://' . WP_TESTS_DOMAIN . '/wp-includes/post.php',
			),
			'empty'       => array(
				'',
				'http://' . WP_TESTS_DOMAIN,
			),
			'slash'       => array(
				'/',
				'http://' . WP_TESTS_DOMAIN . '/',
			),
		);
	}

	/**
	 * @dataProvider data_admin_url
	 */
	public function test_admin_url( $url, $expected ) {
		$this->assertSame( $expected, $this->site_env->admin_url( $url ) );
	}

	public function data_admin_url() {
		return array(
			'plugins.php' => array(
				'plugins.php',
				'http://' . WP_TESTS_DOMAIN . '/wp-admin/plugins.php',
			),
			'empty'       => array(
				'',
				'http://' . WP_TESTS_DOMAIN . '/wp-admin/',
			),
			'slash'       => array(
				'/',
				'http://' . WP_TESTS_DOMAIN . '/wp-admin/',
			),
		);
	}

	public function test_get_active_plugins() {
		$plugin_list = array(
			'gutenberg/gutenberg.php',
			'performance-lab/load.php',
			'plugin-check/plugin.php',
		);
		add_filter( 'pre_option_active_plugins', $this->get_return_value_callback( $plugin_list ) );

		$this->assertSame( $plugin_list, $this->site_env->get_active_plugins() );
	}

	/**
	 * @group ms-required
	 */
	public function test_get_active_plugins_exclude_network_active() {
		$plugin_list = array(
			'gutenberg/gutenberg.php',
			'performance-lab/load.php',
			'plugin-check/plugin.php',
		);
		add_filter( 'pre_option_active_plugins', $this->get_return_value_callback( $plugin_list ) );

		$network_plugins = array( 'performance-lab/load.php' => true );
		add_filter( 'pre_site_option_active_sitewide_plugins', $this->get_return_value_callback( $network_plugins ) );

		$this->assertSame(
			array(
				'gutenberg/gutenberg.php',
				'plugin-check/plugin.php',
			),
			$this->site_env->get_active_plugins()
		);
	}

	public function test_get_active_themes() {
		add_filter( 'template', $this->get_return_value_callback( 'twentytwentyfive' ) );
		add_filter( 'stylesheet', $this->get_return_value_callback( 'twentytwentyfive' ) );
		$this->assertSame( array( 'twentytwentyfive' ), $this->site_env->get_active_themes() );
	}

	public function test_get_active_themes_with_child_theme() {
		add_filter( 'template', $this->get_return_value_callback( 'twentytwentyfive' ) );
		add_filter( 'stylesheet', $this->get_return_value_callback( 'twentytwentyfive-child' ) );

		$this->assertSame( array( 'twentytwentyfive-child', 'twentytwentyfive' ), $this->site_env->get_active_themes() );
	}
}
