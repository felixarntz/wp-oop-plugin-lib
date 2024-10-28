<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Response
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Response;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * Class for a generic HTTP response from another URL.
 *
 * @since 0.1.0
 */
class Generic_Response implements Response {

	/**
	 * The HTTP status code received with the response.
	 *
	 * @since 0.1.0
	 * @var int
	 */
	private $status;

	/**
	 * The body received with the response.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $body;

	/**
	 * The headers received with the response.
	 *
	 * @since 0.1.0
	 * @var CaseInsensitiveDictionary
	 */
	private $headers;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param int                   $status  The HTTP status code received with the response.
	 * @param string                $body    The body received with the response.
	 * @param array<string, string> $headers The headers received with the response.
	 */
	public function __construct( int $status, string $body, array $headers ) {
		// Prior to WordPress 6.2, this class had a different name.
		if ( ! class_exists( CaseInsensitiveDictionary::class ) ) {
			class_alias( 'Requests_Utility_CaseInsensitiveDictionary', CaseInsensitiveDictionary::class );
		}

		$this->status  = $status;
		$this->body    = $body;
		$this->headers = new CaseInsensitiveDictionary( $headers );
	}

	/**
	 * Retrieves the HTTP status code received with the response.
	 *
	 * @since 0.1.0
	 *
	 * @return int The 3-digit HTTP status code.
	 */
	public function get_status(): int {
		return $this->status;
	}

	/**
	 * Retrieves the data received with the response.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @return array<string, string> The response headers.
	 */
	public function get_headers(): array {
		return $this->headers->getAll();
	}

	/**
	 * Retrieves a specific header received with the response.
	 *
	 * @since 0.1.0
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
