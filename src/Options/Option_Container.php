<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use ArrayAccess;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;

/**
 * Class for an option container.
 *
 * @since n.e.x.t
 */
class Option_Container implements Container, ArrayAccess {

	/**
	 * Options stored in the container.
	 *
	 * @since n.e.x.t
	 * @var array<string, callable>
	 */
	private $options = array();

	/**
	 * Option instances already created.
	 *
	 * @since n.e.x.t
	 * @var array<string, Option>
	 */
	private $instances = array();

	/**
	 * Checks if an option for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return bool True if the option exists in the container, false otherwise.
	 */
	public function has( string $key ): bool {
		return isset( $this->options[ $key ] );
	}

	/**
	 * Gets the option for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return Option Option for the given key.
	 *
	 * @throws Not_Found_Exception    Thrown when option with given key is not found.
	 * @throws Invalid_Type_Exception Thrown when option with given key has invalid type.
	 */
	public function get( string $key ) {
		if ( ! isset( $this->options[ $key ] ) ) {
			throw new Not_Found_Exception(
				esc_html(
					sprintf(
						/* translators: %s: option key */
						__( 'Option with key %s was not found in container', 'wp-oop-plugin-lib' ),
						$key
					)
				)
			);
		}

		if ( ! isset( $this->instances[ $key ] ) ) {
			$instance = $this->options[ $key ]( $this );
			if ( ! $instance instanceof Option ) {
				throw new Invalid_Type_Exception(
					esc_html(
						sprintf(
							/* translators: %s: option key */
							__( 'Option with key %s is not of type Option', 'wp-oop-plugin-lib' ),
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
	 * Sets the given option under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $key     Option key.
	 * @param callable $creator Option creator closure.
	 */
	public function set( string $key, callable $creator ): void {
		$this->options[ $key ] = $creator;
		unset( $this->instances[ $key ] );
	}

	/**
	 * Sets an option using the given repository and arguments under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key               Option key.
	 * @param Key_Value_Repository $repository        Repository used for the option.
	 * @param array<string, mixed> $registration_args Optional. Option registration arguments. Default empty array.
	 */
	public function set_by_args( string $key, Key_Value_Repository $repository, array $registration_args = array() ): void {
		$this->set(
			$key,
			function () use ( $repository, $key, $registration_args ) {
				return new Option( $repository, $key, $registration_args );
			}
		);
	}

	/**
	 * Unsets the option under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 */
	public function unset( string $key ): void {
		unset( $this->options[ $key ], $this->instances[ $key ] );
	}

	/**
	 * Gets all keys in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array {
		return array_keys( $this->options );
	}

	/**
	 * Checks if an option for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Option key.
	 * @return bool True if the option exists in the container, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->has( $key );
	}

	/**
	 * Gets the option for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Option key.
	 * @return Option Option for the given key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets the given option under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key   Option key.
	 * @param mixed $value Option creator closure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Unsets the option under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Option key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		$this->unset( $key );
	}
}
