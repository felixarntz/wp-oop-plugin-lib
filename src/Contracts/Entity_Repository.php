<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for a repository for entities.
 *
 * @since n.e.x.t
 */
interface Entity_Repository {

	/**
	 * Checks whether an entity for the given ID exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Entity ID.
	 * @return bool True if the entity exists, false otherwise.
	 */
	public function exists( int $id ): bool;

	/**
	 * Gets the entity for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Entity ID.
	 * @return Entity|null The entity, or `null` if no value exists.
	 */
	public function get( int $id );

	/**
	 * Updates the entity for a given ID in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int                  $id   Entity ID.
	 * @param array<string, mixed> $data New data to set for the entity.
	 * @return bool True on success, false on failure.
	 */
	public function update( int $id, array $data ): bool;

	/**
	 * Adds a new entity to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $data Initial data to set for the entity.
	 * @return int The entity ID, or `0` on failure.
	 */
	public function add( array $data ): int;

	/**
	 * Deletes the entity for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Entity ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $id ): bool;

	/**
	 * Returns an entity query object for the given arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @return Entity_Query Query object.
	 */
	public function query( array $query_args ): Entity_Query;
}
