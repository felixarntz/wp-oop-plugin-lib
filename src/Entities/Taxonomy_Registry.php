<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Taxonomy_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Registry;
use WP_Taxonomy;

/**
 * Class for a registry of WordPress taxonomies.
 *
 * @since n.e.x.t
 */
class Taxonomy_Registry implements Registry {

	/**
	 * Registers a taxonomy with the given key and arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Taxonomy key.
	 * @param array<string, mixed> $args Taxonomy registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		if ( ! isset( $args['object_type'] ) ) {
			return false;
		}
		$object_type = $args['object_type'];
		unset( $args['object_type'] );

		$taxonomy = register_taxonomy( $key, $object_type, $args );
		if ( is_wp_error( $taxonomy ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Checks whether a taxonomy with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Taxonomy key.
	 * @return bool True if the taxonomy is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return taxonomy_exists( $key );
	}

	/**
	 * Gets the registered taxonomy for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Taxonomy key.
	 * @return WP_Taxonomy|null The registered taxonomy definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		$taxonomy = get_taxonomy( $key );
		if ( ! $taxonomy ) {
			return null;
		}
		return $taxonomy;
	}

	/**
	 * Gets all taxonomies from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, WP_Taxonomy> Associative array of keys and their taxonomy definitions, or empty array if nothing is registered.
	 */
	public function get_all_registered(): array {
		return get_taxonomies( array(), 'objects' );
	}
}
