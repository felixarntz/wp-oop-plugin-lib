<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Key_Value\Contracts\Key_Value_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Key_Value\Contracts;

/**
 * Interface for a repository for key-value pairs.
 *
 * @since n.e.x.t
 */
interface Key_Value_Repository {

	/**
	 * Checks whether a value for the given key exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Item key.
	 * @return bool True if a value for the key exists, false otherwise.
	 */
	public function exists( string $key ): bool;

	/**
	 * Gets the value for a given key from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key     Item key.
	 * @param mixed  $default Optional. Value to return if no value exists for the key. Default null.
	 * @return mixed Value for the key, or the default if no value exists.
	 */
	public function get( string $key, $default = null );

	/**
	 * Updates the value for a given key in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key   Item key.
	 * @param mixed  $value New value to set for the key.
	 * @return bool True on success, false on failure.
	 */
	public function update( string $key, $value ): bool;

	/**
	 * Deletes the data for a given key from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Item key.
	 * @return bool True on success, false on failure.
	 */
	public function delete( string $key ): bool;
}
