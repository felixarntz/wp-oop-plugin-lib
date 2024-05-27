<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Route
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;

/**
 * Interface for a REST route.
 *
 * @since n.e.x.t
 */
interface REST_Route extends With_Registration_Args {

	/**
	 * Gets the route base.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Route base.
	 */
	public function get_base(): string;

	/**
	 * Gets the method specific route handler arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Route handler arguments.
	 */
	public function get_handler_args(): array;

	/**
	 * Gets the global route arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Global route arguments.
	 */
	public function get_global_args(): array;
}
