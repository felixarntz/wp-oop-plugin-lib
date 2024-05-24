<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\With_Capabilities
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for a class that can check for capabilities.
 *
 * @since n.e.x.t
 */
interface With_Capabilities {

	/**
	 * Checks whether the entity has the given capability.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $cap     Capability name.
	 * @param mixed  ...$args Optional further parameters, typically starting with an entity ID.
	 * @return bool True if the entity has the given capability false otherwise.
	 */
	public function has_cap( string $cap, ...$args ): bool;
}
