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
	 * @var array<string, mixed>|null
	 */
	private $data;

	/**
	 * Retrieves the data received with the response.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> The response data, or an empty array if it could not automatically be decoded. In
	 *                              this case, the raw response body should be used.
	 */
	public function get_data(): array {
		if ( ! isset( $this->data ) ) {
			$this->data = json_decode( $this->get_body(), true );
			if ( null === $this->data ) {
				$this->data = array();
			}
		}
		return $this->data;
	}
}
