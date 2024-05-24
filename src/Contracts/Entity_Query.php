<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Query
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for a query for entities.
 *
 * @since n.e.x.t
 */
interface Entity_Query {

	/**
	 * Runs the query to get entities.
	 *
	 * @since n.e.x.t
	 *
	 * @return Entity[] List of entities.
	 */
	public function get_entities(): array;

	/**
	 * Runs the query to get entity IDs.
	 *
	 * @since n.e.x.t
	 *
	 * @return int[] List of entity IDs.
	 */
	public function get_ids(): array;

	/**
	 * Runs the query to get the entity count.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Entity count.
	 */
	public function get_count(): int;
}
