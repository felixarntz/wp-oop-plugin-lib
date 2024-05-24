<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability_Controller
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

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
	 * @param string   $cap           Capability to grant. Must be part of the plugin capabilities in this controller.
	 * @param string[] $required_caps Required capabilities needed to grant this capability. An empty array means this
	 *                                is a base capability and must be granted directly on individual user roles.
	 *                                Default empty array.
	 */
	public function grant_cap_for_base_caps( string $cap, array $required_caps ): void {
		$this->container->get( $cap )->set_required_caps( $required_caps );
	}

	/**
	 * Gets all capabilities in this controller.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of capabilities in this controller.
	 */
	public function get_controlled_caps(): array {
		return $this->container->get_keys();
	}

	/**
	 * Gets the map of dynamic capabilities and which base capabilities they should map to.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, string[]> Map of `$cap => $required_caps` pairs.
	 */
	public function get_required_caps_map(): array {
		$keys = $this->container->get_keys();

		$caps_map = array();
		foreach ( $keys as $key ) {
			$cap = $this->container->get( $key );

			$required_caps = $cap->get_required_caps();
			if ( ! $required_caps ) { // Skip capabilities that aren't dynamic.
				continue;
			}

			$caps_map[ $key ] = $required_caps;
		}
		return $caps_map;
	}
}
