<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts;

/**
 * Interface for an HTTP request to another URL.
 *
 * @since 0.1.0
 */
interface Request {

	const GET     = 'GET';
	const POST    = 'POST';
	const PUT     = 'PUT';
	const PATCH   = 'PATCH';
	const DELETE  = 'DELETE';
	const HEAD    = 'HEAD';
	const OPTIONS = 'OPTIONS';
	const TRACE   = 'TRACE';

	/**
	 * Retrieves the URL to which the request should be sent.
	 *
	 * @since 0.1.0
	 *
	 * @return string The request URL.
	 */
	public function get_url(): string;

	/**
	 * Retrieves the HTTP method to be used for the request.
	 *
	 * @since 0.1.0
	 *
	 * @return string The HTTP method for the request.
	 */
	public function get_method(): string;

	/**
	 * Retrieves the data to be sent with the request.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> The request data, or an empty array. If the request method is not GET or HEAD, in
	 *                              case of an empty array the request body should be used instead.
	 */
	public function get_data(): array;

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
	public function get_body(): string;

	/**
	 * Retrieves the headers to be sent with the request.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, string> The request headers.
	 */
	public function get_headers(): array;

	/**
	 * Retrieves the options to be used for sending the request.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> The request options.
	 */
	public function get_options(): array;

	/**
	 * Adds a header to the request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name  The header name.
	 * @param string $value The header value.
	 */
	public function add_header( string $name, string $value ): void;

	/**
	 * Adds data to the request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name  The name under which to send the data.
	 * @param mixed  $value The value to send.
	 */
	public function add_data( string $name, $value ): void;

	/**
	 * Adds an option to the request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $name  The option name.
	 * @param mixed  $value The option value.
	 */
	public function add_option( string $name, $value ): void;
}
