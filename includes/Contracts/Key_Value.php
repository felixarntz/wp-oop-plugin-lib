<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Key_Value
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for a key-value pair.
 *
 * @since n.e.x.t
 */
interface Key_Value {

	/**
	 * Checks whether the item has a value set.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value(): bool;

	/**
	 * Gets the value for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return mixed Value for the item.
	 */
	public function get_value();

	/**
	 * Updates the value for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $value New value to set for the item.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( $value ): bool;

	/**
	 * Deletes the data for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_value(): bool;

	/**
	 * Gets the key of the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Item key.
	 */
	public function get_key(): string;
}
