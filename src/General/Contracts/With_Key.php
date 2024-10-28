<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Key
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for an item that has a key (a string identifier unique for the kind of item).
 *
 * @since 0.1.0
 */
interface With_Key {

	/**
	 * Gets the key of the item.
	 *
	 * @since 0.1.0
	 *
	 * @return string Item key.
	 */
	public function get_key(): string;
}
