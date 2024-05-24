<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_ID_Aware
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for a class that is aware of a specific entity ID.
 *
 * @since n.e.x.t
 */
interface Entity_ID_Aware {

	/**
	 * Gets the entity ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The entity ID.
	 */
	public function get_entity_id(): int;
}
