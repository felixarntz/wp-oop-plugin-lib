<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

use Closure;

/**
 * Interface for a container with read and write access.
 *
 * @since n.e.x.t
 */
interface Container extends Container_Readonly {

	/**
	 * Sets the given entry under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $key     Key.
	 * @param callable $creator Entry creator closure.
	 */
	public function set( string $key, callable $creator ): void;

	/**
	 * Unsets the entry under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Key.
	 */
	public function unset( string $key ): void;
}
