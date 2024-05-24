<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Aggregate_REST_Route
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

use InvalidArgumentException;
use Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Route;

/**
 * Class representing a WordPress REST API route supporting multiple route handlers.
 *
 * @since n.e.x.t
 */
class Aggregate_REST_Route implements REST_Route {

	/**
	 * Route base.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $base;

	/**
	 * Route handlers.
	 *
	 * @since n.e.x.t
	 * @var REST_Route[]
	 */
	private $route_handlers;

	/**
	 * Global route arguments.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $global_args = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param REST_Route[] $route_handlers One or more {@see REST_Route} handler objects.
	 *
	 * @throws InvalidArgumentException Thrown when the given route handlers are invalid.
	 */
	public function __construct( array $route_handlers ) {
		if ( ! $route_handlers ) {
			throw new InvalidArgumentException(
				esc_html__( 'No route handlers provided for REST route.', 'wp-oop-plugin-lib' )
			);
		}
		foreach ( $route_handlers as $route_handler ) {
			if ( ! $route_handler instanceof REST_Route ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid route handler provided for REST route.', 'wp-oop-plugin-lib' )
				);
			}

			$new_base = $route_handler->get_base();
			if ( $this->base && $new_base !== $this->base ) {
				throw new InvalidArgumentException(
					esc_html(
						sprintf(
							/* translators: 1: REST base, 2: another REST base */
							__( 'Inconsistent REST bases %1$s and %2$s in route handlers provided for REST route.', 'wp-oop-plugin-lib' ),
							$this->base,
							$new_base
						)
					)
				);
			}

			$this->base             = $new_base;
			$this->route_handlers[] = $route_handler;
			$this->global_args      = array_merge( $this->global_args, $route_handler->get_global_args() );
		}
	}

	/**
	 * Gets the route base.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Route base.
	 */
	public function get_base(): string {
		return $this->base;
	}

	/**
	 * Gets the registration arguments for the route.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Route registration arguments.
	 */
	public function get_registration_args(): array {
		return array_merge(
			$this->global_args,
			array_map(
				static function ( $route ) {
					return $route->get_handler_args();
				},
				$this->route_handlers
			)
		);
	}

	/**
	 * Gets the method specific route handler arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Route handler arguments.
	 */
	public function get_handler_args(): array {
		// Return an empty array as an aggregate route itself does not handle any requests, only the inner routes do.
		return array();
	}

	/**
	 * Gets the global route arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Global route arguments.
	 */
	public function get_global_args(): array {
		return $this->global_args;
	}
}
