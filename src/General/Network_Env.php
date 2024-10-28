<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Env
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Read-only class containing utilities for the network environment.
 *
 * @since 0.1.0
 */
class Network_Env {

	/**
	 * Checks whether this WordPress installation is a multisite installation.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if a multisite installation, false otherwise.
	 */
	public function is_multisite(): bool {
		return is_multisite();
	}

	/**
	 * Returns the network ID.
	 *
	 * @since 0.1.0
	 *
	 * @return int The network ID.
	 */
	public function id(): int {
		return get_current_network_id();
	}

	/**
	 * Returns the network URL, i.e. relative to the home page.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The site URL.
	 */
	public function url( string $relative_path = '/' ): string {
		$url = network_home_url( $relative_path );

		/*
		 * In Multisite, network_home_url() returns a URL with a trailing slash, even if the path is empty.
		 * This is inconsistent with home_url(), so we fix that here.
		 */
		if ( '' === $relative_path ) {
			$url = untrailingslashit( $url );
		}

		return $url;
	}

	/**
	 * Returns the network's WordPress URL, in which WordPress core is installed.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The WordPress URL.
	 */
	public function wp_url( string $relative_path = '/' ): string {
		$url = network_site_url( $relative_path );

		/*
		 * In Multisite, network_site_url() returns a URL with a trailing slash, even if the path is empty.
		 * This is inconsistent with site_url(), so we fix that here.
		 */
		if ( '' === $relative_path ) {
			$url = untrailingslashit( $url );
		}

		return $url;
	}

	/**
	 * Returns the network admin URL (typically the 'wp-admin/network' directory within the WordPress URL).
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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
