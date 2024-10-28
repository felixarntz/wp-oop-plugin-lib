<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Meta_Capability
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

/**
 * Class representing a WordPress meta capability.
 *
 * A meta capability is a capability that is mapped to one or more base capabilities based on dynamic logic.
 *
 * @since 0.1.0
 */
class Meta_Capability extends Abstract_Capability {

	/**
	 * Callback function to determine the required base capabilities needed to grant this meta capability.
	 *
	 * @since 0.1.0
	 * @var callable
	 */
	private $map_callback;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string   $key          Capability key.
	 * @param callable $map_callback Optional. Callback function to determine the required base capabilities needed to
	 *                               grant this meta capability. function receives the user ID and any additional
	 *                               parameters passed alongside the capability check and must return an array. Default
	 *                               null.
	 */
	public function __construct( string $key, callable $map_callback = null ) {
		parent::__construct( $key );
		$this->map_callback = $map_callback ?? function () {
			return array();
		};
	}

	/**
	 * Sets the callback function to determine the required base capabilities needed to grant this meta capability.
	 *
	 * @since 0.1.0
	 *
	 * @param callable $map_callback Callback function to determine the required base capabilities needed to grant this
	 *                               meta capability. The function receives the user ID and any additional parameters
	 *                               passed alongside the capability check and must return an array.
	 */
	public function set_map_callback( callable $map_callback ): void {
		$this->map_callback = $map_callback;
	}

	/**
	 * Gets the callback function to determine the required base capabilities needed to grant this meta capability.
	 *
	 * @since 0.1.0
	 *
	 * @return callable Callback function to determine the required base capabilities needed to grant this meta
	 *                  capability. The function receives the user ID and any additional parameters passed alongside
	 *                  the capability check and must return an array.
	 */
	public function get_map_callback(): callable {
		return $this->map_callback;
	}
}
