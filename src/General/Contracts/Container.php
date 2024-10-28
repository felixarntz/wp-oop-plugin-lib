<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for a container with read and write access.
 *
 * @since 0.1.0
 */
interface Container extends Container_Readonly {

	/**
	 * Sets the given entry under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string   $key     Key.
	 * @param callable $creator Entry creator closure.
	 */
	public function set( string $key, callable $creator ): void;

	/**
	 * Unsets the entry under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Key.
	 */
	public function unset( string $key ): void;
}
