<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Registry;

/**
 * Class for a registry of WordPress options.
 *
 * @since n.e.x.t
 */
class Option_Registry implements Registry {

	/**
	 * Default option group to use.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $default_group;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $default_group Default option group to use.
	 */
	public function __construct( string $default_group ) {
		$this->default_group = $default_group;
	}

	/**
	 * Registers an option with the given key and arguments.
	 *
	 * If the arguments include a 'group' key, that value will be used as the option group as used by WordPress core.
	 * Otherwise, the default group will be used.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Option key.
	 * @param array<string, mixed> $args Option registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		// Use provided group, or default group otherwise.
		$group = $args['group'] ?? $this->default_group;

		register_setting( $group, $key, $args );
		return true;
	}

	/**
	 * Checks whether an option with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return bool True if the option is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		$registered = get_registered_settings();
		return isset( $registered[ $key ] );
	}

	/**
	 * Gets the registered option for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return object|null The registered option definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		$registered = get_registered_settings();
		return $registered[ $key ] ?? null;
	}

	/**
	 * Gets all options from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Associative array of keys and their option definitions, or empty array if nothing is registered.
	 */
	public function get_all_registered(): array {
		return get_registered_settings();
	}
}
