<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Base\Abstract_Installer
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Base;

use Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Installer;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option;
use Felix_Arntz\WP_OOP_Plugin_Lib\Plugin_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\Traits\Maybe_Throw;

/**
 * Base class for a plugin installer (and uninstaller).
 *
 * @since n.e.x.t
 */
abstract class Abstract_Installer implements Installer {
	use Maybe_Throw;

	/**
	 * The plugin environment.
	 *
	 * @since n.e.x.t
	 * @var Plugin_Env
	 */
	private $plugin_env;

	/**
	 * Option to capture the installed version.
	 *
	 * @since n.e.x.t
	 * @var Option
	 */
	private $version_option;

	/**
	 * Option to capture whether to delete data on uninstall.
	 *
	 * @since n.e.x.t
	 * @var Option
	 */
	private $delete_data_option;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Plugin_Env $plugin_env         The plugin environment.
	 * @param Option     $version_option     Option to capture the installed version.
	 * @param Option     $delete_data_option Option to capture whether to delete data on uninstall.
	 */
	public function __construct( Plugin_Env $plugin_env, Option $version_option, Option $delete_data_option ) {
		$this->plugin_env         = $plugin_env;
		$this->version_option     = $version_option;
		$this->delete_data_option = $delete_data_option;
	}

	/**
	 * Installs or upgrades data for the plugin as necessary.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	public function install(): bool {
		$success = $this->install_single();
		if ( ! $success ) {
			return false;
		}

		$current_version = $this->plugin_env->version();

		// If nothing was installed (database already has current version), bail early.
		if ( $this->version_option->get_value() === $current_version ) {
			return true;
		}

		// Refresh current version in the database.
		$this->version_option->update_value( $current_version );

		/*
		 * If using multisite, also refresh this value in site metadata.
		 * This allows to efficiently detect which sites have the plugin data installed which allows to reliably
		 * uninstall the plugin on multisite networks as well.
		 */
		if ( is_multisite() ) {
			// TODO: Use a meta repository for this.
			update_site_meta( get_current_blog_id(), $this->version_option->get_key(), $current_version );
		}

		return true;
	}

	/**
	 * Checks whether data for the plugin data is installed.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the plugin data is installed, false otherwise.
	 */
	public function is_installed(): bool {
		return (bool) $this->version_option->get_value();
	}

	/**
	 * Uninstalls data for the plugin as necessary.
	 *
	 * On a multisite network, this will attempt to uninstall the data for all relevant sites.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	public function uninstall(): bool {
		// If not using multisite, simply uninstall the data for the single site.
		if ( ! is_multisite() ) {
			return $this->uninstall_single();
		}

		/*
		 * If using multisite, get all site IDs that have the plugin data installed.
		 * This uses site metadata where the plugin version is maintained for each site.
		 * A maximum of 20 sites is set in an attempt to avoid timeouts. Reliably uninstalling a plugin in a large
		 * WordPress multisite is unfortunately not possible.
		 */
		$site_ids = get_sites(
			array(
				'fields'   => 'ids',
				'number'   => 20,
				'meta_key' => $this->version_option->get_key(),
			)
		);

		// Iterate through the site and uninstall the data for each one.
		$success_ids = array();
		foreach ( $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			if ( $this->uninstall_single() ) {
				// TODO: Use a meta repository for this.
				delete_site_meta( $site_id, $this->version_option->get_key() );
				$success_ids[] = $site_id;
			}
			restore_current_blog();
		}

		return count( $site_ids ) === count( $success_ids );
	}

	/**
	 * Installs or upgrades data for the plugin as necessary, for a single site.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	private function install_single(): bool {
		$installed_version = (string) $this->version_option->get_value();

		// If already installed, either do nothing or upgrade as necessary.
		if ( $installed_version ) {
			if ( version_compare( $installed_version, $this->plugin_env->version(), '>=' ) ) {
				return true; // Nothing to install or upgrade.
			}

			return $this->maybe_throw( array( $this, 'upgrade_data' ), array( $installed_version ) );
		}

		return $this->maybe_throw( array( $this, 'install_data' ), array() );
	}

	/**
	 * Uninstalls data for the plugin as necessary, for a single site.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	private function uninstall_single(): bool {
		$installed_version  = $this->version_option->get_value();
		$should_delete_data = $this->delete_data_option->get_value();
		if ( ! $installed_version || ! $should_delete_data ) {
			return true; // No data to delete.
		}

		$success = $this->maybe_throw( array( $this, 'uninstall_data' ), array() );
		if ( ! $success ) {
			return false;
		}
		$this->version_option->delete_value();
		$this->delete_data_option->delete_value();
		return true;
	}

	/**
	 * Installs the full data for the plugin.
	 *
	 * @since n.e.x.t
	 *
	 * @throws Exception Thrown when installing fails.
	 */
	abstract protected function install_data(): void;

	/**
	 * Upgrades data for the plugin based on an old version used.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $old_version Old version number that is currently installed on the site.
	 *
	 * @throws Exception Thrown when upgrading fails.
	 */
	abstract protected function upgrade_data( string $old_version ): void;

	/**
	 * Uninstalls the full data for the plugin.
	 *
	 * If this method is called, the administrator has explicitly opted in to deleting all plugin data.
	 *
	 * @since n.e.x.t
	 *
	 * @throws Exception Thrown when uninstalling fails.
	 */
	abstract protected function uninstall_data(): void;
}
