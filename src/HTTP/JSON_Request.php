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
		// If the body is provided directly, it must be JSON-encoded data.
		if ( isset( $args['body'] ) && is_string( $args['body'] ) && $args['body'] ) {
			json_decode( $args['body'], true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				_doing_it_wrong(
					__METHOD__,
					// phpcs:ignore Generic.Files.LineLength.TooLong
					esc_html__( 'When providing the JSON request body directly, it must be a valid JSON string.', 'wp-oop-plugin-lib' ),
					''
				);
				unset( $args['body'] );
			}
		}

		// Ensure the Content-Type header is set to application/json, unless otherwise specified.
		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array( 'Content-Type' => 'application/json' );
		} elseif ( ! isset( $args['headers']['Content-Type'] ) && ! isset( $args['headers']['content-type'] ) ) {
			$args['headers']['Content-Type'] = 'application/json';
		}

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
		$body = parent::get_body();
		if ( $body ) {
			return $body;
		}
		$data = parent::get_data();
		if ( ! $data ) {
			return '';
		}
		return wp_json_encode( $data );
	}
}
