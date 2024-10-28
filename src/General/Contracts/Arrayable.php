<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Arrayable
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for something that can return its array representation.
 *
 * @since 0.1.0
 */
interface Arrayable {

	/**
	 * Returns the array representation.
	 *
	 * @since 0.1.0
	 *
	 * @return mixed[] Array representation.
	 */
	public function to_array(): array;
}
