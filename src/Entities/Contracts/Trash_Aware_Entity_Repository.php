<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Trash_Aware_Entity_Repository
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts;

/**
 * Interface for an entity repository that supports a trash.
 *
 * @since 0.1.0
 */
interface Trash_Aware_Entity_Repository {

	/**
	 * Moves the entity for a given ID to the trash.
	 *
	 * @since 0.1.0
	 *
	 * @param int $id Entity ID.
	 * @return bool True on success, false on failure.
	 */
	public function trash( int $id ): bool;

	/**
	 * Moves the entity for a given ID out of the trash.
	 *
	 * @since 0.1.0
	 *
	 * @param int $id Entity ID.
	 * @return bool True on success, false on failure.
	 */
	public function untrash( int $id ): bool;

	/**
	 * Checks whether the entity for a given ID is in the trash.
	 *
	 * @since 0.1.0
	 *
	 * @param int $id Entity ID.
	 * @return bool True if the entity is in the trash, false otherwise.
	 */
	public function is_trashed( int $id ): bool;
}
