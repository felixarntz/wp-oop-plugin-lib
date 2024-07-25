<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Response
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Response;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * Class for a generic HTTP response from another URL.
 *
 * @since n.e.x.t
 */
class Generic_Response implements Response {

	/**
	 * The HTTP status code received with the response.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $status;

	/**
	 * The body received with the response.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $body;

	/**
	 * The headers received with the response.
	 *
	 * @since n.e.x.t
	 * @var CaseInsensitiveDictionary
	 */
	private $headers;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param int                   $status  The HTTP status code received with the response.
	 * @param string                $body    The body received with the response.
	 * @param array<string, string> $headers The headers received with the response.
	 */
	public function __construct( int $status, string $body, array $headers ) {
		$this->status  = $status;
		$this->body    = $body;
		$this->headers = new CaseInsensitiveDictionary( $headers );
	}

	/**
	 * Retrieves the HTTP status code received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The 3-digit HTTP status code.
	 */
	public function get_status(): int {
		return $this->status;
	}

	/**
	 * Retrieves the data received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> The response data, or an empty array if it could not automatically be decoded. In
	 *                              this case, the raw response body should be used.
	 */
	public function get_data(): array {
		// This is a generic response, so we don't know how to decode the body.
		return array();
	}

	/**
	 * Retrieves the body received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The raw response body. If response data could be automatically decoded, this should be empty, and
	 *                the response data should be used instead.
	 */
	public function get_body(): string {
		return $this->body;
	}

	/**
	 * Retrieves the headers received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, string> The response headers.
	 */
	public function get_headers(): array {
		return $this->headers->getAll();
	}

	/**
	 * Retrieves a specific header received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $name The name of the header to retrieve.
	 * @return string The value of the header, or an empty string if the header was not found.
	 */
	public function get_header( string $name ): string {
		if ( ! isset( $this->headers[ $name ] ) ) {
			return '';
		}
		return $this->headers[ $name ];
	}
}
