<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Filters
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Hooks;

/**
 * Class that adds filters to dynamically grant base capabilities and meta capabilities.
 *
 * @since 0.1.0
 */
class Capability_Filters implements With_Hooks {

	/**
	 * Capability container.
	 *
	 * @since 0.1.0
	 * @var Capability_Container
	 */
	private $container;

	/**
	 * Map of `$cap => $required_caps` pairs, stored here to avoid recalculation on every filter call.
	 *
	 * @since n.e.xt
	 * @var array<string, string[]>|null
	 */
	private $required_base_caps_map;

	/**
	 * Map of `$cap => $map_callback` pairs, stored here to avoid recalculation on every filter call.
	 *
	 * @since n.e.xt
	 * @var array<string, callable>|null
	 */
	private $meta_map_callbacks_map;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Capability_Container $container Container with the capabilities that filters should be added for.
	 */
	public function __construct( Capability_Container $container ) {
		$this->container = $container;
	}

	/**
	 * Adds relevant WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function add_hooks(): void {
		add_filter( 'user_has_cap', array( $this, 'filter_user_has_cap' ) );
		add_filter( 'map_meta_cap', array( $this, 'filter_map_meta_cap' ), 10, 4 );
	}

	/**
	 * Gets the map of dynamic capabilities and which base capabilities they should map to.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, string[]> Map of `$cap => $required_caps` pairs.
	 */
	public function get_required_base_caps_map(): array {
		$keys = $this->container->get_keys();

		$caps_map = array();
		foreach ( $keys as $key ) {
			$capability = $this->container->get( $key );
			if ( ! $capability instanceof Base_Capability ) {
				continue;
			}

			$required_caps = $capability->get_required_caps();
			if ( ! $required_caps ) { // Skip capabilities that aren't dynamic.
				continue;
			}

			$caps_map[ $key ] = $required_caps;
		}
		return $caps_map;
	}

	/**
	 * Gets the map of meta capabilities and their map callbacks.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, callable> Map of `$cap => $map_callback` pairs.
	 */
	public function get_meta_map_callbacks_map(): array {
		$keys = $this->container->get_keys();

		$callbacks_map = array();
		foreach ( $keys as $key ) {
			$capability = $this->container->get( $key );
			if ( ! $capability instanceof Meta_Capability ) {
				continue;
			}

			$callbacks_map[ $key ] = $capability->get_map_callback();
		}
		return $callbacks_map;
	}

	/**
	 * Filters a user's capabilities, granting dynamic capabilities based on existing base capabilities.
	 *
	 * This should be used as a callback for the {@see 'user_has_cap'} filter.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, bool> $allcaps Array of key/value pairs where keys represent a capability name and boolean
	 *                                     values represent whether the user has that capability.
	 * @return array<string, bool> Filtered $allcaps, including dynamically granted custom capabilities.
	 */
	public function filter_user_has_cap( array $allcaps ): array {
		if ( null === $this->required_base_caps_map ) {
			$this->required_base_caps_map = $this->get_required_base_caps_map();
		}

		foreach ( $this->required_base_caps_map as $cap => $required_caps ) {
			$grant = true;
			foreach ( $required_caps as $required_cap ) {
				if ( ! isset( $allcaps[ $required_cap ] ) || ! $allcaps[ $required_cap ] ) {
					$grant = false;
					break;
				}
			}

			$allcaps[ $cap ] = $grant;
		}

		return $allcaps;
	}

	/**
	 * Filters the mapping of a meta capability to one or more base capabilities.
	 *
	 * This should be used as a callback for the {@see 'map_meta_cap'} filter.
	 *
	 * @since 0.1.0
	 *
	 * @param string[]   $caps    Primitive capabilities required of the user.
	 * @param string     $cap     Capability being checked.
	 * @param int|string $user_id User ID.
	 * @param mixed[]    $args    Additional arguments passed alongside the capability check.
	 * @return string[] Filtered $caps, potentially altered by the relevant map callback.
	 */
	public function filter_map_meta_cap( array $caps, string $cap, int|string $user_id, array $args ): array {
		if ( null === $this->meta_map_callbacks_map ) {
			$this->meta_map_callbacks_map = $this->get_meta_map_callbacks_map();
		}

		if ( ! isset( $this->meta_map_callbacks_map[ $cap ] ) ) {
			return $caps;
		}

		$map_callback  = $this->meta_map_callbacks_map[ $cap ];
		$required_caps = $map_callback( $user_id, ...$args );
		if ( ! is_array( $required_caps ) || ! $required_caps ) { // Prevent invalid return values.
			return $caps;
		}
		return $required_caps;
	}
}
