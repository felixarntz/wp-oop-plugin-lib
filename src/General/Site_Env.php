<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Site_Env
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Read-only class containing utilities for the site environment.
 *
 * @since n.e.x.t
 */
class Site_Env {

	/**
	 * Returns information about the site.
	 *
	 * @since n.e.x.t
	 * @see get_bloginfo()
	 *
	 * @param string $field The site field to retrieve.
	 * @return string The site field value.
	 */
	public function get_info( string $field ): string {
		return (string) get_bloginfo( $field );
	}

	/**
	 * Returns the site URL, relative to the home page.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The site URL.
	 */
	public function url( string $relative_path = '/' ): string {
		return home_url( $relative_path );
	}

	/**
	 * Returns the WordPress URL, in which WordPress core is installed.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The WordPress URL.
	 */
	public function wp_url( string $relative_path = '/' ): string {
		return site_url( $relative_path );
	}

	/**
	 * Returns the content URL (typically the 'wp-content' directory within the WordPress URL).
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The content URL.
	 */
	public function content_url( string $relative_path = '/' ): string {
		return content_url( $relative_path );
	}

	/**
	 * Returns the admin URL (typically the 'wp-admin' directory within the WordPress URL).
	 *
	 * @since n.e.x.t
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string The admin URL.
	 */
	public function admin_url( string $relative_path = '/' ): string {
		return admin_url( $relative_path );
	}

	/**
	 * Returns the active plugins.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of plugin basenames, relative to the plugins directory.
	 */
	public function get_active_plugins(): array {
		return wp_get_active_and_valid_plugins();
	}

	/**
	 * Returns the active themes.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of theme directories, relative to the themes directory.
	 */
	public function get_active_themes(): array {
		$themes = array();
		if ( is_child_theme() ) {
			$themes[] = get_stylesheet();
		}
		$themes[] = get_template();
		return $themes;
	}
}
