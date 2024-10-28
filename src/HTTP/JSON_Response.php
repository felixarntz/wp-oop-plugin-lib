<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Response
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

/**
 * Class for a JSON HTTP response from another URL.
 *
 * @since 0.1.0
 */
class JSON_Response extends Generic_Response {

	/**
	 * Data decoded from the JSON response body.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>|null
	 */
	private $data;

	/**
	 * Retrieves the data received with the response.
	 *
	 * @since 0.1.0
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
