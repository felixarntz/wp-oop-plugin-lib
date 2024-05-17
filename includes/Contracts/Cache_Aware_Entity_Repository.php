<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Cache_Aware_Entity_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for an entity repository that supports a cache.
 *
 * @since n.e.x.t
 */
interface Cache_Aware_Entity_Repository {

	/**
	 * Updates the entity caches for the given IDs.
	 *
	 * @since n.e.x.t
	 *
	 * @param int[] $ids Entity IDs.
	 * @return bool True on success, or false on failure.
	 */
	public function update_caches( array $ids ): bool;
}
