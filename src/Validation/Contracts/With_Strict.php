<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Strict
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

/**
 * Interface for a validation rule that supports an optional strict mode.
 *
 * @since n.e.x.t
 */
interface With_Strict {

	/**
	 * Returns whether strict mode is enabled.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if strict mode is enabled, false otherwise.
	 */
	public function is_strict(): bool;

	/**
	 * Sets whether or not to enable strict mode.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict True to enable strict mode, false to disable it.
	 */
	public function set_strict( bool $strict ): void;
}
