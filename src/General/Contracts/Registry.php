<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

/**
 * Interface for a registry of items.
 *
 * @since 0.1.0
 */
interface Registry {

	/**
	 * Registers an item with the given key and arguments.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $key  Item key.
	 * @param array<string, mixed> $args Item registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool;

	/**
	 * Checks whether an item with the given key is registered.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return bool True if the item is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool;

	/**
	 * Gets the registered item for the given key from the registry.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Item key.
	 * @return object|null The registered item definition, or `null` if not registered.
	 */
	public function get_registered( string $key );

	/**
	 * Gets all items from the registry.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Associative array of keys and their item definitions, or empty array if nothing is
	 *                              registered.
	 */
	public function get_all_registered(): array;
}
