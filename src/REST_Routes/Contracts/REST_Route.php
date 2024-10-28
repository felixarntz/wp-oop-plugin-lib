<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Route
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;

/**
 * Interface for a REST route.
 *
 * @since 0.1.0
 */
interface REST_Route extends With_Registration_Args {

	/**
	 * Gets the route base.
	 *
	 * @since 0.1.0
	 *
	 * @return string Route base.
	 */
	public function get_base(): string;

	/**
	 * Gets the method specific route handler arguments.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Route handler arguments.
	 */
	public function get_handler_args(): array;

	/**
	 * Gets the global route arguments.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Global route arguments.
	 */
	public function get_global_args(): array;
}
