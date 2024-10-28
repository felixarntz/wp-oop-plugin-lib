<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Installation\Contracts\Installer
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Installation\Contracts;

/**
 * Interface for a plugin installer (and uninstaller).
 *
 * @since 0.1.0
 */
interface Installer {

	/**
	 * Installs or upgrades data for the plugin as necessary.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function install(): bool;

	/**
	 * Checks whether data for the plugin data is installed.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if the plugin data is installed, false otherwise.
	 */
	public function is_installed(): bool;

	/**
	 * Uninstalls data for the plugin as necessary.
	 *
	 * On a multisite network, this will attempt to uninstall the data for all relevant sites.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function uninstall(): bool;
}
