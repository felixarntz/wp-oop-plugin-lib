<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Env
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group general
 */
class Network_Env_Tests extends Test_Case {

	private $network_env;

	public function set_up() {
		parent::set_up();

		$this->network_env = new Network_Env();
	}

	public function test_is_multisite() {
		if ( is_multisite() ) {
			$this->assertTrue( $this->network_env->is_multisite() );
		} else {
			$this->assertFalse( $this->network_env->is_multisite() );
		}
	}

	public function test_id() {
		$this->assertSame( 1, $this->network_env->id() );
	}

	/**
	 * @dataProvider data_url
	 */
	public function test_url( $url, $expected ) {
		// Note: This works on single site too, it'll just return the site's URL instead.
		$this->assertSame( $expected, $this->network_env->url( $url ) );
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
		// Note: This works on single site too, it'll just return the site's WordPress URL instead.
		$this->assertSame( $expected, $this->network_env->wp_url( $url ) );
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
	 * @group ms-required
	 * @dataProvider data_admin_url
	 */
	public function test_admin_url( $url, $expected ) {
		$this->assertSame( $expected, $this->network_env->admin_url( $url ) );
	}

	public function data_admin_url() {
		return array(
			'plugins.php' => array(
				'plugins.php',
				'http://' . WP_TESTS_DOMAIN . '/wp-admin/network/plugins.php',
			),
			'empty'       => array(
				'',
				'http://' . WP_TESTS_DOMAIN . '/wp-admin/network/',
			),
			'slash'       => array(
				'/',
				'http://' . WP_TESTS_DOMAIN . '/wp-admin/network/',
			),
		);
	}

	/**
	 * @group ms-excluded
	 * @dataProvider data_admin_url_non_multisite
	 */
	public function test_admin_url_non_multisite( $url, $expected ) {
		$this->assertSame( $expected, $this->network_env->admin_url( $url ) );
	}

	public function data_admin_url_non_multisite() {
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

	/**
	 * @group ms-required
	 */
	public function test_get_active_plugins() {
		$plugin_list = array(
			'gutenberg/gutenberg.php'  => true,
			'performance-lab/load.php' => true,
			'plugin-check/plugin.php'  => true,
		);
		add_filter( 'pre_site_option_active_sitewide_plugins', $this->get_return_value_callback( $plugin_list ) );

		$this->assertSame( array_keys( $plugin_list ), $this->network_env->get_active_plugins() );
	}

	/**
	 * @group ms-excluded
	 */
	public function test_get_active_plugins_non_multisite() {
		$plugin_list = array(
			'gutenberg/gutenberg.php'  => true,
			'performance-lab/load.php' => true,
			'plugin-check/plugin.php'  => true,
		);
		add_filter( 'pre_site_option_active_sitewide_plugins', $this->get_return_value_callback( $plugin_list ) );

		// Despite the above filter, this should return an empty array because it's not a multisite.
		$this->assertSame( array(), $this->network_env->get_active_plugins() );
	}
}
