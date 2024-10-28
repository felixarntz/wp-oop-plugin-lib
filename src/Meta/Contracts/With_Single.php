<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\With_Single
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts;

/**
 * Interface for a key-value pair repository with support for differentiating between entries with a single value vs
 * with multiple values.
 *
 * @since 0.1.0
 */
interface With_Single {

	/**
	 * Gets the 'single' config for a given key in the repository.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return bool Whether or not the item should be singleed.
	 */
	public function get_single( string $key ): bool;

	/**
	 * Sets the 'single' config for a given key in the repository.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key      Item key.
	 * @param bool   $single Item single config.
	 */
	public function set_single( string $key, bool $single ): void;
}
