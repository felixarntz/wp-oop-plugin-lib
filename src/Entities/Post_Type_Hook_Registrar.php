<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Type_Hook_Registrar
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;

/**
 * Class that adds the relevant hook to register WordPress post types.
 *
 * @since n.e.x.t
 */
class Post_Type_Hook_Registrar implements Hook_Registrar {

	/**
	 * WordPress post type registry instance.
	 *
	 * @since n.e.x.t
	 * @var Post_Type_Registry|null
	 */
	private $registry;

	/**
	 * Constructor.
	 *
	 * @param Post_Type_Registry|null $registry Optional. WordPress post type registry instance. By default, a new
	 *                                          instance will be created.
	 */
	public function __construct( Post_Type_Registry $registry = null ) {
		$this->registry = $registry;
	}

	/**
	 * Adds a callback that registers the post types to the relevant hook.
	 *
	 * The callback receives a registry instance as the sole parameter, allowing to call the
	 * {@see Post_Type_Registry::register()} method.
	 *
	 * @since n.e.x.t
	 *
	 * @param callable $register_callback Callback to register the post types.
	 */
	public function add_register_callback( callable $register_callback ): void {
		add_action(
			'init',
			function () use ( $register_callback ) {
				if ( ! $this->registry ) {
					$this->registry = new Post_Type_Registry();
				}
				$register_callback( $this->registry );
			}
		);
	}
}
