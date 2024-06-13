<?php
/**
 * Trait Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Strict_Mode
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits;

/**
 * Trait that implements the With_Strict interface, making strict mode available for a validation rule.
 *
 * @since n.e.x.t
 */
trait Strict_Mode {

	/**
	 * Whether or not strict mode is enabled.
	 *
	 * @since n.e.x.t
	 * @var bool
	 */
	private $strict_mode = false;

	/**
	 * Returns whether strict mode is enabled.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if strict mode is enabled, false otherwise.
	 */
	public function is_strict(): bool {
		return $this->strict_mode;
	}

	/**
	 * Sets whether or not to enable strict mode.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict True to enable strict mode, false to disable it.
	 */
	public function set_strict( bool $strict ): void {
		$this->strict_mode = $strict;
	}
}
