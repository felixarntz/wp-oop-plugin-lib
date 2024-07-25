<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Response
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts;

/**
 * Interface for an HTTP response from another URL.
 *
 * @since n.e.x.t
 */
interface Response {

	/**
	 * Retrieves the HTTP status code received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The 3-digit HTTP status code.
	 */
	public function get_status(): int;

	/**
	 * Retrieves the data received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> The response data, or an empty array if it could not automatically be decoded. In
	 *                              this case, the raw response body should be used.
	 */
	public function get_data(): array;

	/**
	 * Retrieves the body received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The raw response body. If response data could be automatically decoded, this should be empty, and
	 *                the response data should be used instead.
	 */
	public function get_body(): string;

	/**
	 * Retrieves the headers received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, string> The response headers.
	 */
	public function get_headers(): array;

	/**
	 * Retrieves a specific header received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $name The name of the header to retrieve.
	 * @return string The value of the header, or an empty string if the header was not found.
	 */
	public function get_header( string $name ): string;
}
