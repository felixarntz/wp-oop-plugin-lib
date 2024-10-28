<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Abstract_Capability
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Capabilities\Contracts\Capability;

/**
 * Base class representing a WordPress capability.
 *
 * @since 0.1.0
 */
abstract class Abstract_Capability implements Capability {

	/**
	 * Capability key.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $key;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Capability key.
	 */
	public function __construct( string $key ) {
		$this->key = $key;
	}

	/**
	 * Gets the capability key / slug.
	 *
	 * @since 0.1.0
	 *
	 * @return string Capability key.
	 */
	public function get_key(): string {
		return $this->key;
	}
}
