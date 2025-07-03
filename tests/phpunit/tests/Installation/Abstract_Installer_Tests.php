<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Installation\Abstract_Installer
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Installation;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Runner;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Plugin_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Installer\Abstract_Installer_Implementation;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use WP_UnitTest_Factory;

/**
 * @group installation
 */
class Abstract_Installer_Tests extends Test_Case {

	private static $site_ids;

	private $installer;

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		// Setup for this test class is only needed on multisite.
		if ( ! is_multisite() ) {
			return;
		}

		self::$site_ids = $factory->blog->create_many( 3 );
	}

	public static function wpTearDownAfterClass() {
		// Setup for this test class is only needed on multisite.
		if ( ! is_multisite() ) {
			return;
		}

		foreach ( self::$site_ids as $site_id ) {
			wp_delete_site( $site_id );
		}
	}

	public function set_up() {
		parent::set_up();

		$option_repository = new Option_Repository();

		$this->installer = new Abstract_Installer_Implementation(
			new Plugin_Env(
				WP_PLUGIN_DIR . '/some-plugin/load.php',
				'1.0.0'
			),
			new Option(
				$option_repository,
				'sp_version',
				array(
					'type'     => 'string',
					'default'  => '',
					'autoload' => true,
				)
			),
			new Option(
				$option_repository,
				'sp_delete_data',
				array(
					'type'     => 'bool',
					'default'  => false,
					'autoload' => false,
				)
			)
		);
		$this->installer->set_network_runner(
			new Network_Runner( new Network_Env() )
		);
	}

	public function test_install_fresh() {
		$this->assertTrue( $this->installer->install() );
		$this->assertSame( 1, $this->installer->get_install_call_count() );
		$this->assertSame( 0, $this->installer->get_upgrade_call_count() );
		$this->assertSame( '1.0.0', get_option( 'sp_version' ) );
		if ( is_multisite() ) {
			$this->assertSame( '1.0.0', get_site_meta( get_current_blog_id(), 'sp_version', true ) );
		}
	}

	public function test_install_upgrade() {
		// Set the version to an older version, indicating the plugin is already installed.
		$this->set_installed_version( '0.9.0' );

		$this->assertTrue( $this->installer->install() );
		$this->assertSame( 0, $this->installer->get_install_call_count() );
		$this->assertSame( 1, $this->installer->get_upgrade_call_count() );
		$this->assertSame( '0.9.0', $this->installer->get_last_upgrade_call()['old_version'] );
		$this->assertSame( '1.0.0', get_option( 'sp_version' ) );
		if ( is_multisite() ) {
			$this->assertSame( '1.0.0', get_site_meta( get_current_blog_id(), 'sp_version', true ) );
		}
	}

	public function test_install_unnecessary() {
		// Set the version to the current, indicating the plugin is already installed.
		$this->set_installed_version( '1.0.0' );

		$this->assertTrue( $this->installer->install() );
		$this->assertSame( 0, $this->installer->get_install_call_count() );
		$this->assertSame( 0, $this->installer->get_upgrade_call_count() );
		$this->assertSame( '1.0.0', get_option( 'sp_version' ) );
		if ( is_multisite() ) {
			$this->assertSame( '1.0.0', get_site_meta( get_current_blog_id(), 'sp_version', true ) );
		}
	}

	public function test_is_installed() {
		$this->assertFalse( $this->installer->is_installed() );

		$this->set_installed_version( '1.0.0' );
		$this->assertTrue( $this->installer->is_installed() );
	}

	public function test_uninstall_unnecessary() {
		$this->assertTrue( $this->installer->uninstall() );
		$this->assertSame( 0, $this->installer->get_uninstall_call_count() );
	}

	/**
	 * @group ms-excluded
	 */
	public function test_uninstall_default() {
		// Set the version to the current, indicating the plugin is installed.
		$this->set_installed_version( '1.0.0' );

		// Since the delete data option is not set, the uninstall should not delete any data.
		$this->assertTrue( $this->installer->uninstall() );
		$this->assertSame( 0, $this->installer->get_uninstall_call_count() );
		$this->assertSame( '1.0.0', get_option( 'sp_version' ) );
	}

	/**
	 * @group ms-excluded
	 */
	public function test_uninstall_with_delete_data() {
		// Set the version to the current, indicating the plugin is installed.
		$this->set_installed_version( '1.0.0' );

		// Set the delete data option to true.
		update_option( 'sp_delete_data', true );

		$this->assertTrue( $this->installer->uninstall() );
		$this->assertSame( 1, $this->installer->get_uninstall_call_count() );
		$this->assertFalse( get_option( 'sp_version' ) );
		$this->assertFalse( get_option( 'sp_delete_data' ) );
	}

	/**
	 * @group ms-required
	 */
	public function test_uninstall_network_default() {
		// Set the version to the current for all sites, indicating the plugin is installed.
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->id );
			$this->set_installed_version( '1.0.0' );
			restore_current_blog();
		}

		$this->assertTrue( $this->installer->uninstall() );
		$this->assertSame( 0, $this->installer->get_uninstall_call_count() );

		// Ensure all 4 sites still have the version set (i.e. data was not removed).
		$this->assertCount( 4, get_sites( array( 'meta_key' => 'sp_version' ) ) );
	}

	/**
	 * @group ms-required
	 */
	public function test_uninstall_network_with_delete_data() {
		// Set the version to the current for all sites, indicating the plugin is installed, and opt in to delete data.
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->id );
			$this->set_installed_version( '1.0.0' );
			update_option( 'sp_delete_data', true );
			restore_current_blog();
		}

		$this->assertTrue( $this->installer->uninstall() );
		$this->assertSame( 4, $this->installer->get_uninstall_call_count() );

		// Ensure no sites have the version set (i.e. data was removed).
		$this->assertCount( 0, get_sites( array( 'meta_key' => 'sp_version' ) ) );
	}

	/**
	 * @group ms-required
	 */
	public function test_uninstall_network_mixed() {
		// Mark the plugin installed on the first two sites, and opt in to delete data only on the second site.
		switch_to_blog( self::$site_ids[0] );
		$this->set_installed_version( '1.0.0' );
		restore_current_blog();
		switch_to_blog( self::$site_ids[1] );
		$this->set_installed_version( '1.0.0' );
		update_option( 'sp_delete_data', true );
		restore_current_blog();

		$this->assertTrue( $this->installer->uninstall() );
		$this->assertSame( 1, $this->installer->get_uninstall_call_count() );
		$this->assertSame( self::$site_ids[1], $this->installer->get_last_uninstall_call()['site_id'] );

		// Ensure only the first site has the version still set (as it was not opted in to delete data).
		$this->assertSame(
			array( self::$site_ids[0] ),
			get_sites(
				array(
					'fields'   => 'ids',
					'meta_key' => 'sp_version',
				)
			)
		);
	}

	private function set_installed_version( $version ) {
		update_option( 'sp_version', $version );
		if ( is_multisite() ) {
			update_site_meta( get_current_blog_id(), 'sp_version', $version );
		}
	}
}
