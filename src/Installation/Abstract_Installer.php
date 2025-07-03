<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Installation\Abstract_Installer
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Installation;

use Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Runner;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Plugin_Env;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Maybe_Throw;
use Felix_Arntz\WP_OOP_Plugin_Lib\Installation\Contracts\Installer;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option;

/**
 * Base class for a plugin installer (and uninstaller).
 *
 * @since 0.1.0
 */
abstract class Abstract_Installer implements Installer {
	use Maybe_Throw;

	/**
	 * The plugin environment.
	 *
	 * @since 0.1.0
	 * @var Plugin_Env
	 */
	private $plugin_env;

	/**
	 * Option to capture the installed version.
	 *
	 * @since 0.1.0
	 * @var Option
	 */
	private $version_option;

	/**
	 * Option to capture whether to delete data on uninstall.
	 *
	 * @since 0.1.0
	 * @var Option
	 */
	private $delete_data_option;

	/**
	 * Network runner instance.
	 *
	 * @since n.e.x.t
	 * @var Network_Runner
	 */
	private $network_runner;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
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
	 * Sets the network runner instance to use.
	 *
	 * This is optional. If no instance is set but it is needed, it will be instantiated.
	 *
	 * @since n.e.x.t
	 *
	 * @param Network_Runner $network_runner Network runner instance.
	 */
	final public function set_network_runner( Network_Runner $network_runner ): void {
		$this->network_runner = $network_runner;
	}

	/**
	 * Installs or upgrades data for the plugin as necessary.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	final public function install(): bool {
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
	 * @since 0.1.0
	 *
	 * @return bool True if the plugin data is installed, false otherwise.
	 */
	final public function is_installed(): bool {
		return (bool) $this->version_option->get_value();
	}

	/**
	 * Uninstalls data for the plugin as necessary.
	 *
	 * On a multisite network, this will attempt to uninstall the data for all relevant sites.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	final public function uninstall(): bool {
		// If not using multisite, simply uninstall the data for the single site.
		if ( ! is_multisite() ) {
			return $this->uninstall_single();
		}

		// Instantiate network runner if no instance was set yet.
		if ( ! $this->network_runner ) {
			$this->network_runner = new Network_Runner( new Network_Env() );
		}

		/*
		 * If using multisite, get all site IDs that have the plugin data installed.
		 * This uses site metadata where the plugin version is maintained for each site.
		 * A maximum of 20 sites is set in an attempt to avoid timeouts. Reliably uninstalling a plugin in a large
		 * WordPress multisite is unfortunately not possible.
		 */
		$callback = function () {
			if ( ! $this->uninstall_single() ) {
				return false;
			}

			/*
			 * Delete the site metadata only if uninstallation was actually performed.
			 * This is indicated by the version option no longer being present.
			 */
			if ( ! $this->version_option->get_value() ) {
				// TODO: Use a meta repository for this.
				delete_site_meta( get_current_blog_id(), $this->version_option->get_key() );
			}
			return true;
		};
		return $this->network_runner->run_for_sites(
			$callback,
			array(
				'number'   => 20,
				'meta_key' => $this->version_option->get_key(),
			)
		);
	}

	/**
	 * Installs or upgrades data for the plugin as necessary, for a single site.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @throws Exception Thrown when installing fails.
	 */
	abstract protected function install_data(): void;

	/**
	 * Upgrades data for the plugin based on an old version used.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @throws Exception Thrown when uninstalling fails.
	 */
	abstract protected function uninstall_data(): void;
}
