<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\With_Hooks
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for a class that includes WordPress hooks (actions and/or filters).
 *
 * @since n.e.x.t
 */
interface With_Hooks {

	/**
	 * Adds relevant WordPress hooks.
	 *
	 * @since n.e.x.t
	 */
	public function add_hooks(): void;
}
