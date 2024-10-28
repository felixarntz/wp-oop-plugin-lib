<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\Entity_Key_Value
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts;

/**
 * Interface for a key-value pair that is connected to an entity.
 *
 * @since 0.1.0
 */
interface Entity_Key_Value {

	/**
	 * Checks whether the item has a value set in the given entity.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value( int $entity_id ): bool;

	/**
	 * Gets the value for the item in the given entity.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return mixed Value for the item.
	 */
	public function get_value( int $entity_id );

	/**
	 * Updates the value for the item in the given entity.
	 *
	 * @since 0.1.0
	 *
	 * @param int   $entity_id Entity ID.
	 * @param mixed $value     New value to set for the item.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( int $entity_id, $value ): bool;

	/**
	 * Deletes the data for the item in the given entity.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete_value( int $entity_id ): bool;

	/**
	 * Gets the key of the item.
	 *
	 * @since 0.1.0
	 *
	 * @return string Item key.
	 */
	public function get_key(): string;
}
