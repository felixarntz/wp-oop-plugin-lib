<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Service_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

use ArrayAccess;
use Closure;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;

/**
 * Class for a service container.
 *
 * @since n.e.x.t
 */
class Service_Container implements Container, ArrayAccess {

	/**
	 * Services stored in the container.
	 *
	 * @since n.e.x.t
	 * @var array<string, callable>
	 */
	private $services = array();

	/**
	 * Service instances already created.
	 *
	 * @since n.e.x.t
	 * @var array<string, object>
	 */
	private $instances = array();

	/**
	 * Listener callbacks.
	 *
	 * These callbacks are attached to a specific service and called whenever that service is resolved.
	 *
	 * @since n.e.x.t
	 * @var array<string, callable[]>
	 */
	private $listener_callbacks = array();

	/**
	 * Checks if a service for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return bool True if the service exists in the container, false otherwise.
	 */
	public function has( string $key ): bool {
		return $this->is_bound( $key );
	}

	/**
	 * Gets the service for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return object Service for the given key.
	 *
	 * @throws Not_Found_Exception Thrown when service with given key is not found.
	 */
	public function get( string $key ) {
		if ( ! isset( $this->services[ $key ] ) ) {
			throw new Not_Found_Exception(
				esc_html(
					sprintf(
						/* translators: %s: service key */
						__( 'Service with key %s was not found in container', 'wp-oop-plugin-lib' ),
						$key
					)
				)
			);
		}

		return $this->resolve( $key );
	}

	/**
	 * Sets the given service under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $key     Service key.
	 * @param callable $creator Service creator closure.
	 */
	public function set( string $key, callable $creator ): void {
		$this->bind( $key, $creator );
	}

	/**
	 * Unsets the service under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 */
	public function unset( string $key ): void {
		unset( $this->services[ $key ], $this->instances[ $key ] );
	}

	/**
	 * Gets all keys in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array {
		return array_keys( $this->services );
	}

	/**
	 * Adds a listener callback for a service in the container.
	 *
	 * The callback will be called whenever that service is resolved, whether it is for the initial resolve, or after a
	 * subsequent change. The callback will receive the latest service instance and a reference to the container as
	 * parameters.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $key      Service key.
	 * @param Closure $callback Listener callback.
	 */
	public function listen( string $key, Closure $callback ): void {
		if ( ! isset( $this->listener_callbacks[ $key ] ) ) {
			$this->listener_callbacks[ $key ] = array();
		}

		$this->listener_callbacks[ $key ][] = $callback;
	}

	/**
	 * Checks if a service for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return bool True if the service exists in the container, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->has( $key );
	}

	/**
	 * Gets the service for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return mixed Service for the given key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets the given service under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key   Service key.
	 * @param mixed  $value Service creator closure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Unsets the service under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		$this->unset( $key );
	}

	/**
	 * Binds the given service creator closure under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $key             Service key.
	 * @param Closure $service_creator Service creator closure.
	 */
	private function bind( string $key, Closure $service_creator ): void {
		$this->services[ $key ] = $service_creator;

		// If the service was already resolved, update it.
		if ( $this->is_resolved( $key ) ) {
			$this->drop_stale_instances( $key );

			// If there are listeners attached, resolve immediately so that dependents do not get stale.
			// Otherwise, it only needs to be resolved once explicitly retrieved.
			if ( isset( $this->listener_callbacks[ $key ] ) ) {
				$this->resolve( $key );
			}
		}
	}

	/**
	 * Resolves the service for the given key and returns the resolved instance.
	 *
	 * If the service was already resolved, it will simply return the existing instance.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return object Service resolved for the given key.
	 */
	private function resolve( string $key ): object {
		if ( isset( $this->instances[ $key ] ) ) {
			return $this->instances[ $key ];
		}

		$this->instances[ $key ] = $this->services[ $key ]( $this );
		$this->fire_listener_callbacks( $key );

		return $this->instances[ $key ];
	}

	/**
	 * Checks whether the service under the given key has been bound.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return bool True if the service has been bound, false otherwise.
	 */
	private function is_bound( string $key ): bool {
		return isset( $this->services[ $key ] ) || isset( $this->instances[ $key ] );
	}

	/**
	 * Checks whether the service under the given key has already been resolved.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 * @return bool True if the service has been resolved, false otherwise.
	 */
	private function is_resolved( string $key ): bool {
		return isset( $this->instances[ $key ] );
	}

	/**
	 * Drops the instances under the given key.
	 *
	 * This method can be used to force the {@see Service_Container::resolve()} method to re-resolve.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 */
	private function drop_stale_instances( string $key ): void {
		unset( $this->instances[ $key ] );
	}

	/**
	 * Fires all listener callbacks under the given key, if any are set.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Service key.
	 */
	private function fire_listener_callbacks( string $key ): void {
		if ( ! isset( $this->listener_callbacks[ $key ] ) ) {
			return;
		}

		foreach ( $this->listener_callbacks[ $key ] as $callback ) {
			$callback( $this->instances[ $key ], $this );
		}
	}
}
