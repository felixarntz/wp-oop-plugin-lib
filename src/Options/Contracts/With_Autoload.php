<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts\With_Autoload
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
interface With_Autoload {

	/**
	 * Gets the autoload config for a given key in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Item key.
	 * @return bool Whether or not the item should be autoloaded.
	 */
	public function get_autoload( string $key ): bool;

	/**
	 * Sets the autoload config for a given key in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key      Item key.
	 * @param bool   $autoload Item autoload config.
	 */
	public function set_autoload( string $key, bool $autoload ): void;
}
