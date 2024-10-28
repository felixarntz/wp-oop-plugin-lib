<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Hook_Registrar
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;

/**
 * Class that adds the relevant hook to register WordPress metadata.
 *
 * @since 0.1.0
 */
class Meta_Hook_Registrar implements Hook_Registrar {

	/**
	 * WordPress metadata registry instance.
	 *
	 * @since 0.1.0
	 * @var Meta_Registry
	 */
	private $registry;

	/**
	 * Constructor.
	 *
	 * @param Meta_Registry $registry WordPress metadata registry instance.
	 */
	public function __construct( Meta_Registry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Adds a callback that registers the metadata to the relevant hook.
	 *
	 * The callback receives a registry instance as the sole parameter, allowing to call the
	 * {@see Meta_Registry::register()} method.
	 *
	 * @since 0.1.0
	 *
	 * @param callable $register_callback Callback to register the metadata.
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
