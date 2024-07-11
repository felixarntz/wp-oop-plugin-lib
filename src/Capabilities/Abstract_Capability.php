<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Abstract_Capability
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Contracts\Capability;

/**
 * Base class representing a WordPress capability.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Capability implements Capability {

	/**
	 * Capability key.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $key;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Capability key.
	 */
	public function __construct( string $key ) {
		$this->key = $key;
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
