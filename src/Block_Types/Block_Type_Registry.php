<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Block_Types\Block_Type_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Block_Types;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;
use WP_Block_Type;
use WP_Block_Type_Registry;

/**
 * Class for a registry of WordPress block types.
 *
 * @since n.e.x.t
 */
class Block_Type_Registry implements Registry {

	/**
	 * Registers a block type with the given key and arguments.
	 *
	 * The "key" should be the block type name.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Block type name.
	 * @param array<string, mixed> $args Block type registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		return (bool) WP_Block_Type_Registry::get_instance()->register( $key, $args );
	}

	/**
	 * Checks whether a block type with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Block type name.
	 * @return bool True if the block type is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return WP_Block_Type_Registry::get_instance()->is_registered( $key );
	}

	/**
	 * Gets the registered block type for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Block type name.
	 * @return WP_Block_Type|null The registered block type definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		return WP_Block_Type_Registry::get_instance()->get_registered( $key );
	}

	/**
	 * Gets all block types from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, WP_Block_Type> Associative array of keys and their block type definitions, or empty array
	 *                                      if nothing is registered.
	 */
	public function get_all_registered(): array {
		return WP_Block_Type_Registry::get_instance()->get_all_registered();
	}
}
