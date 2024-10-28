<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\REST_Route_Registry
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;

/**
 * Class for a registry of WordPress REST routes within a given namespace.
 *
 * @since 0.1.0
 */
class REST_Route_Registry implements Registry {

	/**
	 * Namespace to use for all routes.
	 *
	 * @since 0.1.0
	 * @var REST_Namespace
	 */
	private $route_namespace;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param REST_Namespace $route_namespace Namespace to use for all routes.
	 */
	public function __construct( REST_Namespace $route_namespace ) {
		$this->route_namespace = $route_namespace;
	}

	/**
	 * Registers a REST route with the given key and arguments.
	 *
	 * The "key" should be the REST route's REST base, which will be prefixed with the route namespace.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $key  REST route base.
	 * @param array<string, mixed> $args REST route registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		return register_rest_route( "{$this->route_namespace}", $key, $args );
	}

	/**
	 * Checks whether a REST route with the given key is registered.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key REST route base.
	 * @return bool True if the REST route is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		$full_route = $this->route_namespace->get_full_route( $key );

		$routes = rest_get_server()->get_routes( "{$this->route_namespace}" );
		return isset( $routes[ $full_route ] );
	}

	/**
	 * Gets the registered REST route for the given key from the registry.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key REST route base.
	 * @return object|null The registered REST route definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		$full_route = $this->route_namespace->get_full_route( $key );

		$routes = rest_get_server()->get_routes( "{$this->route_namespace}" );
		if ( ! isset( $routes[ $full_route ] ) ) {
			return null;
		}
		return (object) $routes[ $full_route ];
	}

	/**
	 * Gets all REST routes in the namespace from the registry.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, object> Associative array of keys and their REST route definitions, or empty array if
	 *                               nothing is registered.
	 */
	public function get_all_registered(): array {
		return array_map(
			function ( $route_definition ) {
				return (object) $route_definition;
			},
			rest_get_server()->get_routes( "{$this->route_namespace}" )
		);
	}
}
