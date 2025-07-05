<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Block_Patterns\Block_Pattern_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Block_Patterns;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;
use WP_Block_Patterns_Registry;

/**
 * Class for a registry of WordPress block patterns.
 *
 * @since n.e.x.t
 */
class Block_Pattern_Registry implements Registry {

	/**
	 * Registers a block pattern with the given key and arguments.
	 *
	 * The "key" should be the block pattern name.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Block pattern name.
	 * @param array<string, mixed> $args Block pattern registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		return (bool) WP_Block_Patterns_Registry::get_instance()->register( $key, $args );
	}

	/**
	 * Checks whether a block pattern with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Block pattern name.
	 * @return bool True if the block pattern is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return WP_Block_Patterns_Registry::get_instance()->is_registered( $key );
	}

	/**
	 * Gets the registered block pattern for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Block pattern name.
	 * @return object|null The registered block pattern definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		$block_pattern_definition = WP_Block_Patterns_Registry::get_instance()->get_registered( $key );
		if ( ! $block_pattern_definition ) {
			return null;
		}
		return (object) $block_pattern_definition;
	}

	/**
	 * Gets all block patterns from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, object> Associative array of keys and their block pattern definitions, or empty array
	 *                                      if nothing is registered.
	 */
	public function get_all_registered(): array {
		return array_map(
			function ( $block_pattern_definition ) {
				return (object) $block_pattern_definition;
			},
			WP_Block_Patterns_Registry::get_instance()->get_all_registered()
		);
	}
}
