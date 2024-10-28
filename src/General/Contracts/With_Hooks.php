<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Hooks
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for a class that includes WordPress hooks (actions and/or filters).
 *
 * @since 0.1.0
 */
interface With_Hooks {

	/**
	 * Adds relevant WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function add_hooks(): void;
}
