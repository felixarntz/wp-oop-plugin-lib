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
 * This is useful for allowing to customize how capabilities are granted, e.g. by triggering a WordPress action hook
 * and passing an instance of the class to it.
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
	}
}
