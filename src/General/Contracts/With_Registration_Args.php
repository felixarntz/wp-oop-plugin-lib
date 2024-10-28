<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for an item with registration arguments.
 *
 * @since 0.1.0
 */
interface With_Registration_Args {

	/**
	 * Gets the registration arguments for the item.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Item registration arguments.
	 */
	public function get_registration_args(): array;
}
