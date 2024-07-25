<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Request
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

/**
 * Class for an HTTP request that sends JSON to another URL.
 *
 * @since n.e.x.t
 */
class JSON_Request extends Generic_Request {

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $url  The URL to which the request should be sent.
	 * @param array<string, mixed> $data Optional. The data to be sent with the request. Default empty array.
	 * @param array<string, mixed> $args Optional. Additional options for the request. See {@see WP_Http::request()}
	 *                                   for possible options. Providing the 'body' key is only allowed if the data
	 *                                   parameter is empty, and only as a string. Default empty array.
	 */
	public function __construct( string $url, array $data = array(), array $args = array() ) {
		// Do not allow passing a body manually, as it will be the JSON-encoded data.
		unset( $args['body'] );

		parent::__construct( $url, $data, $args );
	}

	/**
	 * Retrieves the data to be sent with the request.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> The request data, or an empty array. If the request method is not GET or HEAD, in
	 *                              case of an empty array the request body should be used instead.
	 */
	public function get_data(): array {
		// The data should be sent as JSON, so the body should be used instead.
		return array();
	}

	/**
	 * Retrieves the body to be sent with the request.
	 *
	 * A request may have either data or a body, but not both.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The request body, or an empty string. Only relevant if the request method is not GET or HEAD. In
	 *                case of an empty string, the request data should be used instead.
	 */
	public function get_body(): string {
		return wp_json_encode( parent::get_data() );
	}
}
