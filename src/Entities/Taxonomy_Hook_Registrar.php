<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Taxonomy_Hook_Registrar
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;

/**
 * Class that adds the relevant hook to register WordPress taxonomies.
 *
 * @since 0.1.0
 */
class Taxonomy_Hook_Registrar implements Hook_Registrar {

	/**
	 * WordPress taxonomy registry instance.
	 *
	 * @since 0.1.0
	 * @var Taxonomy_Registry|null
	 */
	private $registry;

	/**
	 * Constructor.
	 *
	 * @param Taxonomy_Registry|null $registry Optional. WordPress taxonomy registry instance. By default, a new
	 *                                         instance will be created.
	 */
	public function __construct( Taxonomy_Registry $registry = null ) {
		$this->registry = $registry;
	}

	/**
	 * Adds a callback that registers the taxonomies to the relevant hook.
	 *
	 * The callback receives a registry instance as the sole parameter, allowing to call the
	 * {@see Taxonomy_Registry::register()} method.
	 *
	 * @since 0.1.0
	 *
	 * @param callable $register_callback Callback to register the taxonomies.
	 */
	public function add_register_callback( callable $register_callback ): void {
		add_action(
			'init',
			function () use ( $register_callback ) {
				if ( ! $this->registry ) {
					$this->registry = new Taxonomy_Registry();
				}
				$register_callback( $this->registry );
			}
		);
	}
}
