<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;

/**
 * Class for controlling how to grant a specific set of capabilities.
 *
 * @since n.e.x.t
 */
class Capability_Controller {

	/**
	 * Capability container.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
	 *
	 * @param Capability_Container $container Container with the capabilities that this controller instance should be
	 *                                        able to control.
	 */
	public function __construct( Capability_Container $container ) {
		$this->container = $container;
	}

	/**
	 * Grants the given capability dynamically depending on required base capabilities from the user's role(s).
	 *
	 * While WordPress's built-in capabilities are stored in the database on their relevant roles, this approach is
	 * usually sub optimal for plugins and can lead to out of sync data. Instead, granting custom capabilities
	 * depending on one or more base capabilities controls custom capabilities programmatically.
	 *
	 * If the capability should instead be granted on a specific user role, an empty array can be provided to skip
	 * granting the capability based on any built-in capabilities.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $cap           Base capability to grant. Must be part of the plugin capabilities in this
	 *                                controller.
	 * @param string[] $required_caps Required capabilities needed to grant this capability. An empty array means this
	 *                                is a base capability and must be granted directly on individual user roles.
	 *                                Default empty array.
	 *
	 * @throws Not_Found_Exception Thrown when the capability is not found in the container or is not a base capability.
	 */
	public function grant_cap_for_base_caps( string $cap, array $required_caps ): void {
		$capability = $this->container->get( $cap );
		if ( ! $capability instanceof Base_Capability ) {
			throw new Not_Found_Exception(
				sprintf(
					/* translators: %s: capability key */
					esc_html__( 'Capability %s must be a base capability to grant it based on other capabilities.', 'wp-oop-plugin-lib' ), // phpcs:ignore Generic.Files.LineLength.TooLong
					esc_html( $cap )
				)
			);
		}

		$capability->set_required_caps( $required_caps );

		// Reset the required base caps map to recalculate it on the next filter call.
		$this->required_base_caps_map = null;
	}

	/**
	 * Grants the given meta capability dynamically depending on a callback function.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $cap          Meta capability to grant. Must be part of the plugin capabilities in this
	 *                               controller.
	 * @param callable $map_callback Callback function to determine the required base capabilities needed to grant this
	 *                               meta capability. The function receives the user ID and any additional parameters
	 *                               passed alongside the capability check and must return an array.
	 *
	 * @throws Not_Found_Exception Thrown when the capability is not found in the container or is not a meta capability.
	 */
	public function set_meta_map_callback( string $cap, callable $map_callback ): void {
		$capability = $this->container->get( $cap );
		if ( ! $capability instanceof Meta_Capability ) {
			throw new Not_Found_Exception(
				sprintf(
					/* translators: %s: capability key */
					esc_html__( 'Capability %s must be a meta capability to set a map callback.', 'wp-oop-plugin-lib' ), // phpcs:ignore Generic.Files.LineLength.TooLong
					esc_html( $cap )
				)
			);
		}

		$capability->set_map_callback( $map_callback );

		// Reset the meta map callbacks map to recalculate it on the next filter call.
		$this->meta_map_callbacks_map = null;
	}

	/**
	 * Gets the map of dynamic capabilities and which base capabilities they should map to.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
	 * @since n.e.x.t
	 *
	 * @param string[] $caps    Primitive capabilities required of the user.
	 * @param string   $cap     Capability being checked.
	 * @param int      $user_id User ID.
	 * @param mixed[]  $args    Additional arguments passed alongside the capability check.
	 * @return string[] Filtered $caps, potentially altered by the relevant map callback.
	 */
	public function filter_map_meta_cap( array $caps, string $cap, int $user_id, array $args ): array {
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
