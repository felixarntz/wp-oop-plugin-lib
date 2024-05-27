<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\Entity_Key_Value_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts;

/**
 * Interface for a repository for key-value pairs that are connected to an entity.
 *
 * @since n.e.x.t
 */
interface Entity_Key_Value_Repository {

	/**
	 * Checks whether a value for the given entity and key exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int    $entity_id Entity ID.
	 * @param string $key       Item key.
	 * @return bool True if a value for the key exists, false otherwise.
	 */
	public function exists( int $entity_id, string $key ): bool;

	/**
	 * Gets the value for a given entity and key from the repository.
	 *
	 * Always returns a single value.
	 *
	 * @since n.e.x.t
	 *
	 * @param int    $entity_id Entity ID.
	 * @param string $key       Item key.
	 * @param mixed  $default   Optional. Value to return if no value exists for the key. Default null.
	 * @return mixed Value for the key, or the default if no value exists.
	 */
	public function get( int $entity_id, string $key, $default = null );

	/**
	 * Updates the value for a given entity and key in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int    $entity_id Entity ID.
	 * @param string $key       Item key.
	 * @param mixed  $value     New value to set for the key.
	 * @return bool True on success, false on failure.
	 */
	public function update( int $entity_id, string $key, $value ): bool;

	/**
	 * Deletes the data for a given entity and key from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int    $entity_id Entity ID.
	 * @param string $key       Item key.
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $entity_id, string $key ): bool;
}
