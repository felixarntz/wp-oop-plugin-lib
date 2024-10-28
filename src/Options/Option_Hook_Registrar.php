<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Hook_Registrar
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;

/**
 * Class that adds the relevant hook to register WordPress options.
 *
 * @since 0.1.0
 */
class Option_Hook_Registrar implements Hook_Registrar {

	/**
	 * WordPress option registry instance.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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
