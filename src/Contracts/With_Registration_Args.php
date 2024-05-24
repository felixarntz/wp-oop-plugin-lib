<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\With_Registration_Args
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for an item with registration arguments.
 *
 * @since n.e.x.t
 */
interface With_Registration_Args {

	/**
	 * Gets the registration arguments for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Item registration arguments.
	 */
	public function get_registration_args(): array;
}
