<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Shortcode_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;

/**
 * Class for a registry of WordPress shortcodes.
 *
 * @since n.e.x.t
 */
class Shortcode_Registry implements Registry {

	/**
	 * Registers a shortcode with the given key and arguments.
	 *
	 * The "key" should be the shortcode's tag.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Shortcode tag.
	 * @param array<string, mixed> $args Shortcode registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		if ( '' === $key ) {
			return false;
		}
		if ( ! isset( $args['callback'] ) || ! is_callable( $args['callback'] ) ) {
			return false;
		}
		add_shortcode( $key, $args['callback'] );
		return true;
	}

	/**
	 * Checks whether a shortcode with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Shortcode tag.
	 * @return bool True if the shortcode is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return shortcode_exists( $key );
	}

	/**
	 * Gets the registered shortcode for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @global array $shortcode_tags WordPress shortcode tags global.
	 *
	 * @param string $key Shortcode tag.
	 * @return object|null The registered shortcode definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $key ] ) ) {
			return null;
		}

		return (object) array(
			'tag'      => $key,
			'callback' => $shortcode_tags[ $key ],
		);
	}

	/**
	 * Gets all shortcodes from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @global array $shortcode_tags WordPress shortcode tags global.
	 *
	 * @return array<string, object> Associative array of keys and their shortcode definitions, or empty array if
	 *                               nothing is registered.
	 */
	public function get_all_registered(): array {
		global $shortcode_tags;

		$data = array();
		foreach ( $shortcode_tags as $tag => $callback ) {
			$data[ $tag ] = (object) array(
				'tag'      => $tag,
				'callback' => $callback,
			);
		}
		return $data;
	}
}
