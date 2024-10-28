<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for a key-value pair.
 *
 * @since 0.1.0
 */
interface Key_Value extends With_Key {

	/**
	 * Checks whether the item has a value set.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value(): bool;

	/**
	 * Gets the value for the item.
	 *
	 * @since 0.1.0
	 *
	 * @return mixed Value for the item.
	 */
	public function get_value();

	/**
	 * Updates the value for the item.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value New value to set for the item.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( $value ): bool;

	/**
	 * Deletes the data for the item.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_value(): bool;
}
