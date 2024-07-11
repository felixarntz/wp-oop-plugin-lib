<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

use ArrayAccess;
use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Contracts\Capability;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;

/**
 * Class for a capability container.
 *
 * @since n.e.x.t
 */
class Capability_Container implements Container, ArrayAccess {

	/**
	 * Capabilities stored in the container.
	 *
	 * @since n.e.x.t
	 * @var array<string, callable>
	 */
	private $capabilities = array();

	/**
	 * Capability instances already created.
	 *
	 * @since n.e.x.t
	 * @var array<string, Capability>
	 */
	private $instances = array();

	/**
	 * Checks if a capability for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Capability key.
	 * @return bool True if the capability exists in the container, false otherwise.
	 */
	public function has( string $key ): bool {
		return isset( $this->capabilities[ $key ] );
	}

	/**
	 * Gets the capability for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Capability key.
	 * @return Capability Capability for the given key.
	 *
	 * @throws Not_Found_Exception    Thrown when option with given key is not found.
	 * @throws Invalid_Type_Exception Thrown when option with given key has invalid type.
	 */
	public function get( string $key ) {
		if ( ! isset( $this->capabilities[ $key ] ) ) {
			throw new Not_Found_Exception(
				esc_html(
					sprintf(
						/* translators: %s: capability key */
						__( 'Capability with key %s was not found in container', 'wp-oop-plugin-lib' ),
						$key
					)
				)
			);
		}

		if ( ! isset( $this->instances[ $key ] ) ) {
			$instance = $this->capabilities[ $key ]( $this );
			if ( ! $instance instanceof Capability ) {
				throw new Invalid_Type_Exception(
					esc_html(
						sprintf(
							/* translators: %s: capability key */
							__( 'Capability with key %s is not of type Capability', 'wp-oop-plugin-lib' ),
							$key
						)
					)
				);
			}
			$this->instances[ $key ] = $instance;
		}

		return $this->instances[ $key ];
	}

	/**
	 * Sets the given capability under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $key     Capability key.
	 * @param callable $creator Capability creator closure.
	 */
	public function set( string $key, callable $creator ): void {
		$this->capabilities[ $key ] = $creator;
		unset( $this->instances[ $key ] );
	}

	/**
	 * Sets a capability using the given required capabilities under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string            $key           Capability key.
	 * @param string[]|callable $required_caps Array with required base capabilities if this is a base capability,
	 *                                         or callback function to dynamically determine the required base
	 *                                         capabilities if this is a meta capability.
	 */
	public function set_by_args( string $key, $required_caps ): void {
		$this->set(
			$key,
			function () use ( $key, $required_caps ) {
				if ( is_callable( $required_caps ) ) {
					return new Meta_Capability( $key, $required_caps );
				}
				return new Base_Capability( $key, $required_caps );
			}
		);
	}

	/**
	 * Unsets the capability under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Capability key.
	 */
	public function unset( string $key ): void {
		unset( $this->capabilities[ $key ], $this->instances[ $key ] );
	}

	/**
	 * Gets all keys in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array {
		return array_keys( $this->capabilities );
	}

	/**
	 * Checks if a capability for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Capability key.
	 * @return bool True if the capability exists in the container, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->has( $key );
	}

	/**
	 * Gets the capability for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Capability key.
	 * @return Capability Capability for the given key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets the given capability under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key   Capability key.
	 * @param mixed $value Capability creator closure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Unsets the capability under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Capability key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		$this->unset( $key );
	}
}
