<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Response
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

/**
 * Class for a JSON HTTP response from another URL.
 *
 * @since n.e.x.t
 */
class JSON_Response extends Generic_Response {

	/**
	 * Data decoded from the JSON response body.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $data;

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
		$this->data = json_decode( $body, true );
		parent::__construct( $status, '', $headers );
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
		return $this->data;
	}
}
