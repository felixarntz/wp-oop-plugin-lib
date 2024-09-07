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
	 * Returns the site ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The site ID.
	 */
	public function id(): int {
		return get_current_blog_id();
	}

	/**
	 * Returns information about the site.
	 *
	 * @since n.e.x.t
	 * @see get_bloginfo()
	 *
	 * @param string $field The site field to retrieve.
	 * @return string The site field value.
	 */
	public function info( string $field ): string {
		return (string) get_bloginfo( $field );
	}

	/**
	 * Returns the site URL, i.e. relative to the home page.
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
	 * Returns the site's WordPress URL, in which WordPress core is installed.
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
	 * Returns the site admin URL (typically the 'wp-admin' directory within the WordPress URL).
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
	 * Returns the active plugins for the site.
	 *
	 * Does not include network-activated plugins (relevant for multisite installations).
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of plugin basenames, relative to the plugins directory.
	 */
	public function get_active_plugins(): array {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		$network_env = new Network_Env();
		if ( ! $network_env->is_multisite() ) {
			return $active_plugins;
		}
		return array_values( array_diff( $active_plugins, $network_env->get_active_plugins() ) );
	}

	/**
	 * Returns the active themes for the site.
	 *
	 * This is either just the active theme, or the active theme and the child theme if a child theme is active.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of theme directories, relative to the themes directory.
	 */
	public function get_active_themes(): array {
		$parent_theme = get_template();
		$child_theme  = get_stylesheet();

		$themes = array();
		if ( $child_theme !== $parent_theme ) {
			$themes[] = $child_theme;
		}
		$themes[] = $parent_theme;
		return $themes;
	}
}
