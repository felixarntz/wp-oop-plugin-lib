<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Contracts\Dependency_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;

/**
 * Interface for a registry of dependencies which allow enqueuing.
 *
 * @since n.e.x.t
 */
interface Dependency_Registry extends Registry {

	/**
	 * Enqueues the dependency with the given key.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Dependency key.
	 */
	public function enqueue( string $key ): void;

	/**
	 * Dequeues the dependency with the given key.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Dependency key.
	 */
	public function dequeue( string $key ): void;

	/**
	 * Checks whether the dependency with the given key is enqueued.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Dependency key.
	 * @return bool True if the dependency is enqueued, false otherwise.
	 */
	public function is_enqueued( string $key ): bool;
}
