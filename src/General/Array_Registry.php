<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Registry
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

use ArrayAccess;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Arrayable;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;

/**
 * Class for a registry that registers arguments on an associative array.
 *
 * This can be used to provide a consistent registration API around data structures which in WordPress can only be
 * added via filters.
 *
 * @since 0.1.0
 */
class Array_Registry implements Registry, Arrayable, ArrayAccess {

	/**
	 * The registered items.
	 *
	 * @since 0.1.0
	 * @var array<string, object>
	 */
	private $items = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, array<string, mixed>> $initial_items Optional. Initial array of registered items. Default
	 *                                                           empty array.
	 */
	public function __construct( array $initial_items = array() ) {
		$this->items = array_map(
			static function ( $item ) {
				return (object) $item;
			},
			$initial_items
		);
	}

	/**
	 * Registers an item with the given key and arguments.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $key  Item key.
	 * @param array<string, mixed> $args Item registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		$this->items[ $key ] = (object) $args;
		return true;
	}

	/**
	 * Checks whether an item with the given key is registered.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return bool True if the item is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return isset( $this->items[ $key ] );
	}

	/**
	 * Gets the registered item for the given key from the registry.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return object|null The registered item definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		if ( ! isset( $this->items[ $key ] ) ) {
			return null;
		}
		return $this->items[ $key ];
	}

	/**
	 * Gets all items from the registry.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, object> Associative array of keys and their item definitions, or empty array if nothing is
	 *                              registered. This is effectively the array representation of the registry.
	 */
	public function get_all_registered(): array {
		return $this->items;
	}

	/**
	 * Returns the array representation of the registry.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Array representation.
	 */
	public function to_array(): array {
		return $this->get_all_registered();
	}

	/**
	 * Checks if an item for the given key is registered.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key Item key.
	 * @return bool True if the item is registered, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->is_registered( $key );
	}

	/**
	 * Gets the item for the given key from the container.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key Item key.
	 * @return object|null The registered item definition, or `null` if not registered.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get_registered( $key );
	}

	/**
	 * Registers an item with the given key and arguments.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $key  Item key.
	 * @param array<string, mixed> $args Item registration arguments.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $args ) {
		$this->register( $key, $args );
	}

	/**
	 * Magic unset method. Does nothing at this time, as , registries do not allow unregistering items.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key Item key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		// Empty method body.
	}
}
