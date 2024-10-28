<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Capabilities
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for a class that can check for capabilities.
 *
 * @since 0.1.0
 */
interface With_Capabilities {

	/**
	 * Checks whether the entity has the given capability.
	 *
	 * @since 0.1.0
	 *
	 * @param string $cap     Capability name.
	 * @param mixed  ...$args Optional further parameters, typically starting with an entity ID.
	 * @return bool True if the entity has the given capability false otherwise.
	 */
	public function has_cap( string $cap, ...$args ): bool;
}
