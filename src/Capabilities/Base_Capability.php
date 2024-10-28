<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Base_Capability
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

/**
 * Class representing a WordPress base capability.
 *
 * A base capability can be granted to a user role, or based on other base capabilities.
 *
 * @since 0.1.0
 */
class Base_Capability extends Abstract_Capability {

	/**
	 * Required base capabilities needed to grant this capability.
	 *
	 * If empty list, it means this base capability must be granted directly on individual user roles.
	 *
	 * @since 0.1.0
	 * @var string[]
	 */
	private $required_caps;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string   $key           Capability key.
	 * @param string[] $required_caps Optional. Required base capabilities needed to grant this capability. An empty
	 *                                array means this base capability must be granted directly on individual user
	 *                                roles. Default empty array.
	 */
	public function __construct( string $key, array $required_caps = array() ) {
		parent::__construct( $key );
		$this->required_caps = $required_caps;
	}

	/**
	 * Sets the required base capabilities from the user's role(s) to grant this base capability dynamically.
	 *
	 * While WordPress's built-in capabilities are stored in the database on their relevant roles, this approach is
	 * usually sub optimal for plugins and can lead to out of sync data. Instead, granting custom capabilities
	 * depending on one or more base capabilities controls custom capabilities programmatically.
	 *
	 * If the capability should instead be granted on a specific user role, an empty array can be provided to skip
	 * granting the capability based on any built-in capabilities.
	 *
	 * @since 0.1.0
	 *
	 * @param string[] $required_caps Required base capabilities needed to grant this capability. An empty array means
	 *                                this base capability must be granted directly on individual user roles. Default
	 *                                empty array.
	 */
	public function set_required_caps( array $required_caps ): void {
		$this->required_caps = $required_caps;
	}

	/**
	 * Gets the required base capabilities from the user's role(s) to grant this capability.
	 *
	 * An empty array means the base capability should be granted directly on individual user roles.
	 *
	 * @since 0.1.0
	 *
	 * @return string[] Required capabilities needed to grant this capability, or empty array to indicate that this
	 *                  base capability must be granted directly on individual user roles.
	 */
	public function get_required_caps(): array {
		return $this->required_caps;
	}
}
