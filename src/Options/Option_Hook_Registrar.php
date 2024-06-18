<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Hook_Registrar
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;

/**
 * Class that adds the relevant hook to register WordPress options.
 *
 * @since n.e.x.t
 */
class Option_Hook_Registrar implements Hook_Registrar {

	/**
	 * WordPress option registry instance.
	 *
	 * @since n.e.x.t
	 * @var Option_Registry
	 */
	private $registry;

	/**
	 * Constructor.
	 *
	 * @param Option_Registry $registry WordPress option registry instance.
	 */
	public function __construct( Option_Registry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Adds a callback that registers the options to the relevant hook.
	 *
	 * The callback receives a registry instance as the sole parameter, allowing to call the
	 * {@see Option_Registry::register()} method.
	 *
	 * @since n.e.x.t
	 *
	 * @param callable $register_callback Callback to register the options.
	 */
	public function add_register_callback( callable $register_callback ): void {
		add_action(
			'init',
			function () use ( $register_callback ) {
				$register_callback( $this->registry );
			}
		);
	}
}
