<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container_Readonly
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;

/**
 * Interface for a container with readonly access.
 *
 * @since 0.1.0
 */
interface Container_Readonly {

	/**
	 * Checks if an entry for the given key exists in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Key.
	 * @return bool True if the entry exists in the container, false otherwise.
	 */
	public function has( string $key ): bool;

	/**
	 * Gets the entry for the given key from the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Key.
	 * @return mixed Entry for the given key.
	 *
	 * @throws Not_Found_Exception Thrown when entry with given key is not found.
	 */
	public function get( string $key );

	/**
	 * Gets all keys in the container.
	 *
	 * @since 0.1.0
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array;
}
