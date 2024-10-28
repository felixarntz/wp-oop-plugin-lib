<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts;

/**
 * Interface for a query for entities.
 *
 * @since 0.1.0
 */
interface Entity_Query {

	/**
	 * Runs the query to get entities.
	 *
	 * @since 0.1.0
	 *
	 * @return Entity[] List of entities.
	 */
	public function get_entities(): array;

	/**
	 * Runs the query to get entity IDs.
	 *
	 * @since 0.1.0
	 *
	 * @return int[] List of entity IDs.
	 */
	public function get_ids(): array;

	/**
	 * Runs the query to get the entity count.
	 *
	 * @since 0.1.0
	 *
	 * @return int Entity count.
	 */
	public function get_count(): int;
}
