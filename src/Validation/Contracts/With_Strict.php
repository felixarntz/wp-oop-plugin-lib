<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Strict
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

/**
 * Interface for a validation rule that supports an optional strict mode.
 *
 * @since 0.1.0
 */
interface With_Strict {

	/**
	 * Returns whether strict mode is enabled.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if strict mode is enabled, false otherwise.
	 */
	public function is_strict(): bool;

	/**
	 * Sets whether or not to enable strict mode.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $strict True to enable strict mode, false to disable it.
	 */
	public function set_strict( bool $strict ): void;
}
