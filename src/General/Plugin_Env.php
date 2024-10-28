<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Plugin_Env
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Read-only class containing utilities for the plugin environment.
 *
 * @since 0.1.0
 */
class Plugin_Env {

	/**
	 * Absolute path of the plugin main file.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $main_file;

	/**
	 * Current plugin version number.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $main_file Absolute path to the plugin main file.
	 * @param string $version   Current plugin version number.
	 */
	public function __construct( string $main_file, string $version ) {
		$this->main_file = $main_file;
		$this->version   = $version;
	}

	/**
	 * Returns the absolute path to the plugin main file.
	 *
	 * @since 0.1.0
	 *
	 * @return string Absolute path to the plugin main file.
	 */
	public function main_file(): string {
		return $this->main_file;
	}

	/**
	 * Returns the current plugin version number.
	 *
	 * @since 0.1.0
	 *
	 * @return string Current plugin version number.
	 */
	public function version(): string {
		return $this->version;
	}

	/**
	 * Returns the plugin basename.
	 *
	 * @since 0.1.0
	 *
	 * @return string Plugin basename.
	 */
	public function basename(): string {
		return plugin_basename( $this->main_file );
	}

	/**
	 * Returns the absolute path for a relative path to the plugin directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string Absolute path.
	 */
	public function path( string $relative_path = '/' ): string {
		return plugin_dir_path( $this->main_file ) . ltrim( $relative_path, '/' );
	}

	/**
	 * Returns the full URL for a path relative to the plugin directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path Optional. Relative path. Default '/'.
	 * @return string Full URL.
	 */
	public function url( string $relative_path = '/' ): string {
		return plugin_dir_url( $this->main_file ) . ltrim( $relative_path, '/' );
	}
}
