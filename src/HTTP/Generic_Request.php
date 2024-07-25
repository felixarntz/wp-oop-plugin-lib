<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Request
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits\Sanitize_Headers;
use RuntimeException;

/**
 * Class for a generic HTTP request to another URL.
 *
 * @since n.e.x.t
 */
class Generic_Request implements Request {
	use Sanitize_Headers;

	/**
	 * The URL to which the request should be sent.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $url;

	/**
	 * The HTTP method to be used for the request.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $method;

	/**
	 * The data to be sent with the request.
	 *
	 * This is alternative to the body property, and should be used if the data will be sent as form data (e.g. for a
	 * POST request) or as query parameters (e.g. for a GET request).
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $data;

	/**
	 * The body to be sent with the request.
	 *
	 * This is alternative to the data property and should only be used if the data is not suitable for form data or
	 * query parameters.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $body;

	/**
	 * The headers to be sent with the request.
	 *
	 * @since n.e.x.t
	 * @var array<string, string>
	 */
	private $headers;

	/**
	 * Additional options for the request.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $options;

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
		$this->url     = $url;
		$this->method  = $args['method'] ?? Request::GET;
		$this->data    = $data;
		$this->body    = ! $data && isset( $args['body'] ) && is_string( $args['body'] ) ? $args['body'] : '';
		$this->headers = isset( $args['headers'] ) ? $this->sanitize_headers( $args['headers'] ) : array();

		unset( $args['method'], $args['body'], $args['headers'] );
		$this->options = $args;
	}

	/**
	 * Retrieves the URL to which the request should be sent.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The request URL.
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Retrieves the HTTP method to be used for the request.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The HTTP method for the request.
	 */
	public function get_method(): string {
		return $this->method;
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
		return $this->data;
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
		return $this->body;
	}

	/**
	 * Retrieves the headers to be sent with the request.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, string> The request headers.
	 */
	public function get_headers(): array {
		return $this->headers;
	}

	/**
	 * Retrieves the options to be used for sending the request.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> The request options.
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Adds a header to the request.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $name  The header name.
	 * @param string $value The header value.
	 */
	public function add_header( string $name, string $value ): void {
		$this->headers[ $name ] = $value;
	}

	/**
	 * Adds data to the request.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $name  The name under which to send the data.
	 * @param mixed  $value The value to send.
	 *
	 * @throws RuntimeException Thrown if data is added to a request that already has a body.
	 */
	public function add_data( string $name, $value ): void {
		if ( $this->body ) {
			throw new RuntimeException(
				esc_html__( 'Data cannot be added to a request that already has a body.', 'wp-oop-plugin-lib' )
			);
		}

		$this->data[ $name ] = $value;
	}
}
