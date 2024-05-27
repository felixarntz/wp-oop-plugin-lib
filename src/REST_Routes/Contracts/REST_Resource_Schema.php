<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Resource_Schema
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Interface for a REST resource schema.
 *
 * @since n.e.x.t
 */
interface REST_Resource_Schema {

	/**
	 * Prepares the given resource for a response, based on the request fields.
	 *
	 * @since n.e.x.t
	 *
	 * @param Entity          $entity  The entity to prepare.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response REST response with the resource data.
	 */
	public function prepare_resource( Entity $entity, WP_REST_Request $request ): WP_REST_Response;

	/**
	 * Prepares the resources from the given query for a response, based on the request fields.
	 *
	 * @since n.e.x.t
	 *
	 * @param Entity_Query    $query   The entity query to prepare.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response REST response with the resource data.
	 */
	public function prepare_resources_for_query( Entity_Query $query, WP_REST_Request $request ): WP_REST_Response;

	/**
	 * Gets the public schema definition for the resource.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Public resource schema definition.
	 */
	public function get_public_schema(): array;

	/**
	 * Retrieves the arguments definition based on the resource schema.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $method Optional. HTTP method of the request. Default WP_REST_Server::CREATABLE.
	 * @return array<string, mixed> Arguments definition.
	 */
	public function get_schema_args( string $method = WP_REST_Server::CREATABLE ): array;
}
