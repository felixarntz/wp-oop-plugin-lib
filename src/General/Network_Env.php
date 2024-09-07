<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Env
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Read-only class containing utilities for the network environment.
 *
 * @since n.e.x.t
 */
class Network_Env {

	/**
	 * Checks whether this WordPress installation is a multisite installation.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if a multisite installation, false otherwise.
	 */
	public function is_multisite(): bool {
		return is_multisite();
	}

	/**
	 * Returns the network ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The network ID.
	 */
	public function id(): int {
		return get_current_network_id();
	}

	/**
	 * Returns the network URL, i.e. relative to the home page.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The site URL.
	 */
	public function url( string $relative_path = '/' ): string {
		return network_home_url( $relative_path );
	}

	/**
	 * Returns the network's WordPress URL, in which WordPress core is installed.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The WordPress URL.
	 */
	public function wp_url( string $relative_path = '/' ): string {
		return network_site_url( $relative_path );
	}

	/**
	 * Returns the network admin URL (typically the 'wp-admin/network' directory within the WordPress URL).
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The admin URL.
	 */
	public function admin_url( string $relative_path = '/' ): string {
		return network_admin_url( $relative_path );
	}

	/**
	 * Returns the active plugins for the network.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of plugin basenames, relative to the plugins directory.
	 */
	public function get_active_plugins(): array {
		if ( ! $this->is_multisite() ) {
			return array();
		}

		$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
		$active_plugins = array_keys( $active_plugins );
		sort( $active_plugins );
		return $active_plugins;
	}
}
