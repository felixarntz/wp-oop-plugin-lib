<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Request
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits\Sanitize_Headers;

/**
 * Class for a generic HTTP request to another URL.
 *
 * @since 0.1.0
 */
class Generic_Request implements Request {
	use Sanitize_Headers;

	/**
	 * The URL to which the request should be sent.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $url;

	/**
	 * The HTTP method to be used for the request.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $method;

	/**
	 * The data to be sent with the request.
	 *
	 * This is alternative to the body property, and should be used if the data will be sent as form data (e.g. for a
	 * POST request) or as query parameters (e.g. for a GET request).
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $data;

	/**
	 * The body to be sent with the request.
	 *
	 * This is alternative to the data property and should only be used if the data is not suitable for form data or
	 * query parameters.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $body;

	/**
	 * The headers to be sent with the request.
	 *
	 * @since 0.1.0
	 * @var array<string, string>
	 */
	private $headers;

	/**
	 * Additional options for the request.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $url  The URL to which the request should be sent.
	 * @param array<string, mixed> $data Optional. The data to be sent with the request. Default empty array.
	 * @param array<string, mixed> $args Optional. Additional options for the request. See {@see WP_Http::request()}
	 *                                   for possible options. Providing the 'body' key is only allowed if the data
	 *                                   parameter is empty, and only as a string. Default empty array.
	 */
	public function __construct( string $url, array $data = array(), array $args = array() ) {
		if ( $data && isset( $args['body'] ) && $args['body'] ) {
			_doing_it_wrong(
				__METHOD__,
				// phpcs:ignore Generic.Files.LineLength.TooLong
				esc_html__( 'Both a request data array and a request body were provided, but only one of them is allowed.', 'wp-oop-plugin-lib' ),
				''
			);
			unset( $args['body'] );
		}

		$args = $this->sanitize_args( $args, __METHOD__ );

		$this->url     = $url;
		$this->method  = $args['method'] ?? Request::GET;
		$this->data    = $data;
		$this->body    = $args['body'] ?? '';
		$this->headers = isset( $args['headers'] ) ? $this->sanitize_headers( $args['headers'] ) : array();

		unset( $args['method'], $args['body'], $args['headers'] );
		$this->options = $args;
	}

	/**
	 * Retrieves the URL to which the request should be sent.
	 *
	 * @since 0.1.0
	 *
	 * @return string The request URL.
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Retrieves the HTTP method to be used for the request.
	 *
	 * @since 0.1.0
	 *
	 * @return string The HTTP method for the request.
	 */
	public function get_method(): string {
		return $this->method;
	}

	/**
	 * Retrieves the data to be sent with the request.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @return array<string, string> The request headers.
	 */
	public function get_headers(): array {
		return $this->headers;
	}

	/**
	 * Retrieves the options to be used for sending the request.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> The request options.
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Adds a header to the request.
	 *
	 * @since 0.1.0
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
	 * This is only possible if no hard-coded request body was provided for the request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name  The name under which to send the data.
	 * @param mixed  $value The value to send.
	 */
	public function add_data( string $name, $value ): void {
		if ( $this->body ) {
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'Data cannot be added to a request that already has a body.', 'wp-oop-plugin-lib' ),
				''
			);
			return;
		}

		$this->data[ $name ] = $value;
	}

	/**
	 * Adds an option to the request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name  The option name.
	 * @param mixed  $value The option value.
	 */
	public function add_option( string $name, $value ): void {
		$this->options[ $name ] = $value;
	}

	/**
	 * Sanitizes the provided arguments.
	 *
	 * For any invalid arguments, PHP warnings may be triggered, and they will be stripped.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $args   Request arguments, including but not limited to options.
	 * @param string               $method PHP class method to reference in potential PHP warnings.
	 * @return array<string, mixed> The sanitized arguments.
	 */
	private function sanitize_args( array $args, string $method ): array {
		if ( isset( $args['method'] ) && ! $this->is_valid_method( $args['method'] ) ) {
			_doing_it_wrong(
				esc_html( $method ),
				esc_html(
					sprintf(
						/* translators: %s: invalid method string */
						__( 'The value %s is not a valid HTTP request method.', 'wp-oop-plugin-lib' ),
						(string) $args['method']
					)
				),
				''
			);
			unset( $args['method'] );
		}

		if ( isset( $args['body'] ) && ! is_string( $args['body'] ) ) {
			_doing_it_wrong(
				esc_html( $method ),
				esc_html__( 'The request body must be a string.', 'wp-oop-plugin-lib' ),
				''
			);
			unset( $args['body'] );
		}

		if (
			isset( $args['headers'] ) &&
			( ! is_array( $args['headers'] ) || ( $args['headers'] && wp_is_numeric_array( $args['headers'] ) ) )
		) {
			_doing_it_wrong(
				esc_html( $method ),
				esc_html__( 'The request headers must be an associative array.', 'wp-oop-plugin-lib' ),
				''
			);
			unset( $args['headers'] );
		}

		return $args;
	}

	/**
	 * Checks whether the given request method is valid.
	 *
	 * @since 0.1.0
	 *
	 * @param string $method The request method to check.
	 * @return bool True if the request method is valid, false otherwise.
	 */
	private function is_valid_method( string $method ): bool {
		return in_array(
			$method,
			array(
				Request::DELETE,
				Request::GET,
				Request::PATCH,
				Request::POST,
				Request::PUT,
			),
			true
		);
	}
}
