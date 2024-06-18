<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Type_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;
use WP_Post_Type;

/**
 * Class for a registry of WordPress post types.
 *
 * @since n.e.x.t
 */
class Post_Type_Registry implements Registry {

	/**
	 * Registers a post type with the given key and arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Post type key.
	 * @param array<string, mixed> $args Post type registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		$post_type = register_post_type( $key, $args );
		if ( is_wp_error( $post_type ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Checks whether a post type with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Post type key.
	 * @return bool True if the post type is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return post_type_exists( $key );
	}

	/**
	 * Gets the registered post type for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Post type key.
	 * @return WP_Post_Type|null The registered post type definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		return get_post_type_object( $key );
	}

	/**
	 * Gets all post types from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, WP_Post_Type> Associative array of keys and their post type definitions, or empty array if
	 *                                     nothing is registered.
	 */
	public function get_all_registered(): array {
		return get_post_types( array(), 'objects' );
	}
}
