<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\With_Entity_ID
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts;

/**
 * Interface for a class that is aware of a specific entity ID.
 *
 * @since 0.1.0
 */
interface With_Entity_ID {

	/**
	 * Gets the entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @return int The entity ID.
	 */
	public function get_entity_id(): int;
}
