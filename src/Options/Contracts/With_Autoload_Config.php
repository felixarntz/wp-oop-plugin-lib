<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts\With_Autoload_Config
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts;

/**
 * Interface for a key-value pair repository with autoload support.
 *
 * @since n.e.x.t
 */
interface With_Autoload_Config {

	/**
	 * Gets the autoload config for a given key in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Item key.
	 * @return bool|null Whether or not the item should be autoloaded, or null if not specified.
	 */
	public function get_autoload_config( string $key );

	/**
	 * Sets the autoload config for a given key in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key      Item key.
	 * @param bool   $autoload Item autoload config.
	 */
	public function set_autoload_config( string $key, bool $autoload ): void;
}
