<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\REST_Namespace
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

/**
 * Class representing a WordPress REST API namespace.
 *
 * @since n.e.x.t
 */
class REST_Namespace {

	/**
	 * REST namespace, without leading or trailing slash.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $rest_namespace;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $rest_namespace REST namespace.
	 */
	public function __construct( string $rest_namespace ) {
		$this->rest_namespace = trim( $rest_namespace, '/' );
	}

	/**
	 * Returns the REST namespace as a string.
	 *
	 * @since n.e.x.t
	 *
	 * @return string REST namespace, without leading or trailing slash.
	 */
	public function __toString(): string {
		return $this->rest_namespace;
	}

	/**
	 * Gets the full route for a given REST base, prefixed with the REST namespace.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $rest_base REST route base.
	 * @return string Full REST route prefixed with the namespace.
	 */
	public function get_full_route( string $rest_base ): string {
		return '/' . $this->rest_namespace . '/' . trim( $rest_base, '/' );
	}

	/**
	 * Gets the route URL for a given REST base, including the REST namespace.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $rest_base REST route base.
	 * @return string Full REST route URL.
	 */
	public function get_route_url( string $rest_base ): string {
		return rest_url( $this->get_full_route( $rest_base ) );
	}
}
