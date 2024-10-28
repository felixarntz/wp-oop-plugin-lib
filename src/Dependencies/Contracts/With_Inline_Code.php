<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Contracts\With_Inline_Code
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Contracts;

/**
 * Interface for a dependency registry that allows adding inline code to the dependencies.
 *
 * @since 0.1.0
 */
interface With_Inline_Code {

	/**
	 * Adds inline code to the dependency with the given handle.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key  Dependency handle.
	 * @param string $code Code to inline after the dependency output.
	 * @return bool True on success, false on failure.
	 */
	public function add_inline_code( string $key, string $code ): bool;
}
