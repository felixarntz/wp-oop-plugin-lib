<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Abstract_REST_Route
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\REST_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Route;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Base class representing a WordPress REST API route.
 *
 * @since n.e.x.t
 */
abstract class Abstract_REST_Route implements REST_Route {

	/**
	 * Route base.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $base;

	/**
	 * Route methods, as a comma-separated string.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $methods;

	/**
	 * Route arguments.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $args;

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
	 */
	public function __construct() {
		$this->base        = $this->base();
		$this->methods     = (string) $this->methods();
		$this->args        = $this->args();
		$this->global_args = $this->global_args();
	}

	/**
	 * Gets the route base.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Route base.
	 */
	final public function get_base(): string {
		return $this->base;
	}

	/**
	 * Gets the registration arguments for the route.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Route registration arguments.
	 */
	final public function get_registration_args(): array {
		return array_merge(
			$this->global_args,
			array( $this->get_handler_args() )
		);
	}

	/**
	 * Gets the method specific route handler arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Route handler arguments.
	 */
	final public function get_handler_args(): array {
		return array(
			'methods'             => $this->methods,
			'callback'            => function ( WP_REST_Request $request ) {
				$response = null;
				try {
					$response = $this->handle_request( $request );
				} catch ( REST_Exception $e ) {
					return $this->rest_exception_to_wp_error( $e );
				}
				return $response;
			},
			'permission_callback' => function ( WP_REST_Request $request ) {
				try {
					$this->check_permissions( $request );
				} catch ( REST_Exception $e ) {
					return $this->rest_exception_to_wp_error( $e );
				}
				return true;
			},
			'args'                => $this->args,
		);
	}

	/**
	 * Gets the global route arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Global route arguments.
	 */
	final public function get_global_args(): array {
		return $this->global_args;
	}

	/**
	 * Returns the query arguments 'number' and 'offset' from the given request's pagination arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_REST_Request $request WordPress REST request object, including parameters.
	 * @return array<string, mixed> Associative array with 'number' and 'offset' keys.
	 */
	final protected function request_pagination_to_query_args( WP_REST_Request $request ): array {
		$page     = isset( $request['page'] ) ? $request['page'] : 1;
		$per_page = isset( $request['per_page'] ) ? $request['per_page'] : 10;

		return array(
			'number' => $per_page,
			'offset' => ( $page - 1 ) * $per_page,
		);
	}

	/**
	 * Retrieves the pagination arguments definition for a list request.
	 *
	 * This is a partial copy of {@see WP_REST_Controller::get_collection_params()}.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Arguments definition.
	 */
	final protected function get_pagination_args(): array {
		return array(
			'page'     => array(
				'description'       => __( 'Current page of the results.', 'default' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'default' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Returns the route base.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Route base.
	 */
	abstract protected function base(): string;

	/**
	 * Returns the route methods, as a comma-separated string.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Route methods, as a comma-separated string.
	 */
	abstract protected function methods(): string;

	/**
	 * Checks the required permissions for the given request and throws an exception if they aren't met.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_REST_Request $request WordPress REST request object, including parameters.
	 *
	 * @throws REST_Exception Thrown when the permissions aren't met, or when a REST error occurs.
	 */
	abstract protected function check_permissions( WP_REST_Request $request ): void;

	/**
	 * Handles the given request and returns a response.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_REST_Request $request WordPress REST request object, including parameters.
	 * @return WP_REST_Response WordPress REST response object.
	 *
	 * @throws REST_Exception Thrown when a REST error occurs.
	 */
	abstract protected function handle_request( WP_REST_Request $request ): WP_REST_Response;

	/**
	 * Returns the route specific arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Route arguments.
	 */
	abstract protected function args(): array;

	/**
	 * Returns the global route arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Global route arguments.
	 */
	abstract protected function global_args(): array;

	/**
	 * Returns a new WP_Error object for the given REST exception.
	 *
	 * @since n.e.x.t
	 *
	 * @param REST_Exception $e REST exception.
	 * @return WP_Error WordPress error object for the REST exception.
	 */
	private function rest_exception_to_wp_error( REST_Exception $e ) {
		return new WP_Error(
			$e->get_error_code(),
			$e->getMessage(),
			array( 'status' => $e->get_response_code() )
		);
	}
}
