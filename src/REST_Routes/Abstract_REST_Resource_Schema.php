<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Abstract_REST_Resource_Schema
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\REST_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Resource_Schema;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Base class representing a WordPress REST API resource schema.
 *
 * @since n.e.x.t
 */
abstract class Abstract_REST_Resource_Schema implements REST_Resource_Schema {

	/**
	 * Namespace to use for all routes.
	 *
	 * @since n.e.x.t
	 * @var REST_Namespace
	 */
	private $route_namespace;

	/**
	 * Internal resource schema definition.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $schema;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param REST_Namespace $route_namespace Namespace to use for all routes.
	 */
	public function __construct( REST_Namespace $route_namespace ) {
		$this->route_namespace = $route_namespace;
		$this->schema          = $this->schema();
	}

	/**
	 * Prepares the given resource for a response, based on the request fields.
	 *
	 * @since n.e.x.t
	 *
	 * @param Entity          $entity  The entity to prepare.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response REST response with the resource data.
	 */
	final public function prepare_resource( Entity $entity, WP_REST_Request $request ): WP_REST_Response {
		$fields = $this->get_fields_to_include( $request );

		$response = rest_ensure_response(
			$this->prepare_resource_fields( $entity, $fields )
		);

		if ( $this->is_field_included( '_links', $fields ) || $this->is_field_included( '_embedded', $fields ) ) {
			$response->add_links(
				$this->prepare_resource_links( $entity )
			);
		}

		return $response;
	}

	/**
	 * Prepares the resources from the given query for a response, based on the request fields.
	 *
	 * @since n.e.x.t
	 *
	 * @param Entity_Query    $query   The entity query to prepare.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response REST response with the resource data.
	 *
	 * @throws REST_Exception Thrown when a REST error occurs.
	 */
	final public function prepare_resources_for_query( Entity_Query $query, WP_REST_Request $request ): WP_REST_Response {
		$resources = array();
		foreach ( $query->get_entities() as $entity ) {
			$entity_response = $this->prepare_resource( $entity, $request );
			$resources[]     = rest_get_server()->response_to_data( $entity_response, false );
		}

		$page     = isset( $request['page'] ) ? $request['page'] : 1;
		$per_page = isset( $request['per_page'] ) ? $request['per_page'] : 10;

		$total_entities = $query->get_count();
		$max_pages      = (int) ceil( $total_entities / $per_page );
		if ( $page > $max_pages && $total_entities > 0 ) {
			throw REST_Exception::create(
				'rest_invalid_page_number',
				esc_html__( 'The page number requested is larger than the number of pages available.', 'wp-oop-plugin-lib' ),
				400
			);
		}

		$response = rest_ensure_response( $resources );
		$response->header( 'X-WP-Total', (string) $total_entities );
		$response->header( 'X-WP-TotalPages', (string) $max_pages );

		$base_url = add_query_arg(
			urlencode_deep( $request->get_query_params() ),
			$this->get_collection_route_url()
		);

		if ( $page > 1 ) {
			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base_url );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base_url );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Gets the public schema definition for the resource.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Public resource schema definition.
	 */
	final public function get_public_schema(): array {
		$schema = $this->schema;

		if ( isset( $schema['properties'] ) ) {
			foreach ( $schema['properties'] as &$property ) {
				unset( $property['arg_options'] );
			}
		}

		return $schema;
	}

	/**
	 * Retrieves the arguments definition based on the resource schema.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $method Optional. HTTP method of the request. Default WP_REST_Server::CREATABLE.
	 * @return array<string, mixed> Arguments definition.
	 */
	final public function get_schema_args( string $method = WP_REST_Server::CREATABLE ): array {
		if ( WP_REST_Server::READABLE === $method ) {
			return array( 'context' => $this->get_context_arg() );
		}

		$args = rest_get_endpoint_args_for_schema( $this->schema, $method );

		// If this is an update request, remove arguments which are immutable.
		if ( WP_REST_Server::EDITABLE === $method && isset( $this->schema['properties'] ) ) {
			foreach ( $this->schema['properties'] as $field_id => $params ) {
				if ( ! empty( $params['immutable'] ) ) {
					unset( $args[ $field_id ] );
				}
			}
		}

		return $args;
	}

	/**
	 * Gets an array of fields to be included on the response.
	 *
	 * Included fields are based on item schema and `_fields=` request argument.
	 *
	 * This is mostly a copy of {@see WP_REST_Controller::get_fields_for_response()}.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return string[] Fields to be included in the response.
	 */
	final protected function get_fields_to_include( WP_REST_Request $request ): array {
		$properties = isset( $this->schema['properties'] ) ? $this->schema['properties'] : array();

		// Exclude fields that specify a different context than the request context.
		$context = $request['context'];
		if ( $context ) {
			foreach ( $properties as $name => $options ) {
				if ( ! empty( $options['context'] ) && ! in_array( $context, $options['context'], true ) ) {
					unset( $properties[ $name ] );
				}
			}
		}

		$fields = array_keys( $properties );

		/*
		 * '_links' and '_embedded' are not typically part of the item schema,
		 * but they can be specified in '_fields', so they are added here as a
		 * convenience for checking with rest_is_field_included().
		 */
		$fields[] = '_links';
		if ( $request->has_param( '_embed' ) ) {
			$fields[] = '_embedded';
		}

		if ( ! isset( $request['_fields'] ) ) {
			return $fields;
		}
		$requested_fields = wp_parse_list( $request['_fields'] );
		if ( 0 === count( $requested_fields ) ) {
			return $fields;
		}
		// Trim off outside whitespace from the comma delimited list.
		$requested_fields = array_map( 'trim', $requested_fields );
		// Always persist 'id', because it can be needed for add_additional_fields_to_object().
		if ( in_array( 'id', $fields, true ) ) {
			$requested_fields[] = 'id';
		}
		// Return the list of all requested fields which appear in the schema.
		return array_reduce(
			$requested_fields,
			static function ( $response_fields, $field ) use ( $fields ) {
				if ( in_array( $field, $fields, true ) ) {
					$response_fields[] = $field;
					return $response_fields;
				}

				// Check for nested fields if $field is not a direct match.
				$nested_fields = explode( '.', $field );

				/*
				 * A nested field is included so long as its top-level property
				 * is present in the schema.
				 */
				if ( in_array( $nested_fields[0], $fields, true ) ) {
					$response_fields[] = $field;
				}
				return $response_fields;
			},
			array()
		);
	}

	/**
	 * Retrieves the magical context param.
	 *
	 * This is mostly a copy of {@see WP_REST_Controller::get_context_param()}.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Context parameter details.
	 */
	final protected function get_context_arg(): array {
		$param_details = array(
			'description'       => __( 'Scope under which the request is made; determines fields present in response.', 'default' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		if ( ! isset( $this->schema['properties'] ) ) {
			return $param_details;
		}

		$contexts = array();

		foreach ( $this->schema['properties'] as $attributes ) {
			if ( isset( $attributes['context'] ) ) {
				$contexts = array_merge( $contexts, $attributes['context'] );
			}
		}

		if ( $contexts ) {
			$param_details['enum'] = array_unique( $contexts );
			rsort( $param_details['enum'] );

			if ( in_array( 'view', $param_details['enum'], true ) ) {
				$param_details['default'] = 'view';
			}
		}

		return $param_details;
	}

	/**
	 * Determines whether the provided field should be included in the response.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $field  A field to test for inclusion in the response body.
	 * @param string[] $fields An array of string fields supported by the endpoint.
	 * @return bool Whether to include the field or not.
	 */
	final protected function is_field_included( string $field, array $fields ): bool {
		return rest_is_field_included( $field, $fields );
	}

	/**
	 * Gets the route URL for a given REST base, including the REST namespace.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $rest_base REST route base.
	 * @return string Full REST route URL.
	 */
	final protected function get_route_url( string $rest_base ): string {
		return $this->route_namespace->get_route_url( $rest_base );
	}

	/**
	 * Prepares the given resource for inclusion in a response, based on the given fields.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed    $entity The entity to prepare.
	 * @param string[] $fields Fields to be included in the response.
	 * @return array<string, mixed> Associative array of resource data.
	 */
	abstract protected function prepare_resource_fields( $entity, array $fields ): array;

	/**
	 * Prepares links for the given resource.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $entity The entity to prepare links for.
	 * @return array<string, array<string, mixed>> Links for the given resource.
	 */
	abstract protected function prepare_resource_links( $entity ): array;

	/**
	 * Returns the full URL to the resource's collection route.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Full collection route URL.
	 */
	abstract protected function get_collection_route_url(): string;

	/**
	 * Returns the internal resource schema definition.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Internal resource schema definition.
	 */
	abstract protected function schema(): array;
}
