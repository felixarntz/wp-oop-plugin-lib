<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\With_Key
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for an item that has a key (a string identifier unique for the kind of item).
 *
 * @since n.e.x.t
 */
interface With_Key {

	/**
	 * Gets the key of the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Item key.
	 */
	public function get_key(): string;
}
