<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Key_Value_Repository
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value_Repository;

/**
 * Class for a repository that stores keys and values in an array.
 *
 * @since 0.1.0
 */
class Array_Key_Value_Repository implements Key_Value_Repository {

	/**
	 * The items in the repository.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $items = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $initial_items Optional. Initial keys and values. Default empty array.
	 */
	public function __construct( array $initial_items = array() ) {
		$this->items = $initial_items;
	}

	/**
	 * Checks whether a value for the given key exists in the repository.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return bool True if a value for the key exists, false otherwise.
	 */
	public function exists( string $key ): bool {
		return isset( $this->items[ $key ] );
	}

	/**
	 * Gets the value for a given key from the repository.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key     Item key.
	 * @param mixed  $default Optional. Value to return if no value exists for the key. Default null.
	 * @return mixed Value for the key, or the default if no value exists.
	 */
	public function get( string $key, $default = null ) {
		return isset( $this->items[ $key ] ) ? $this->items[ $key ] : $default;
	}

	/**
	 * Updates the value for a given key in the repository.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key   Item key.
	 * @param mixed  $value New value to set for the key.
	 * @return bool True on success, false on failure.
	 */
	public function update( string $key, $value ): bool {
		$this->items[ $key ] = $value;
		return true;
	}

	/**
	 * Deletes the data for a given key from the repository.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return bool True on success, false on failure.
	 */
	public function delete( string $key ): bool {
		if ( ! isset( $this->items[ $key ] ) ) {
			return false;
		}
		unset( $this->items[ $key ] );
		return true;
	}
}
