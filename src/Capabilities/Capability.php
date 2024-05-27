<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Capability
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Key;

/**
 * Class representing a WordPress capability.
 *
 * @since n.e.x.t
 */
class Capability implements With_Key {

	/**
	 * Capability key.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $key;

	/**
	 * Required capabilities needed to grant this capability.
	 *
	 * If empty list, it means this is a base capability and must be granted directly on individual user roles.
	 *
	 * @since n.e.x.t
	 * @var string[]
	 */
	private $required_caps;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $key           Capability key.
	 * @param string[] $required_caps Optional. Required capabilities needed to grant this capability. An empty array
	 *                                means this is a base capability and must be granted directly on individual user
	 *                                roles. Default empty array.
	 */
	public function __construct( string $key, array $required_caps = array() ) {
		$this->key           = $key;
		$this->required_caps = $required_caps;
	}

	/**
	 * Sets the required base capabilities from the user's role(s) to grant this capability dynamically.
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
	 * @param string[] $required_caps Required capabilities needed to grant this capability. An empty array means this
	 *                                is a base capability and must be granted directly on individual user roles.
	 *                                Default empty array.
	 */
	public function set_required_caps( array $required_caps ): void {
		$this->required_caps = $required_caps;
	}

	/**
	 * Gets the required base capabilities from the user's role(s) to grant this capability.
	 *
	 * An empty array means the capability should be granted directly on individual user roles.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] Required capabilities needed to grant this capability, or empty array to indicate that this is
	 *                  a base capability.
	 */
	public function get_required_caps(): array {
		return $this->required_caps;
	}

	/**
	 * Gets the capability key / slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Capability key.
	 */
	public function get_key(): string {
		return $this->key;
	}
}
