<?php
/**
 * Trait Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits\Sanitize_Headers
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits;

/**
 * Trait with a function to sanitize headers into an associative array of strings.
 *
 * @since n.e.x.t
 */
trait Sanitize_Headers {

	/**
	 * Sanitizes the given headers associative array to make sure all values are strings.
	 *
	 * Multiple values for the same header will be concatenated with a comma.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, string|array<string>> $headers The headers to sanitize.
	 * @return array<string, string> The sanitized headers.
	 */
	protected function sanitize_headers( array $headers ): array {
		$sanitized_headers = array();
		foreach ( $headers as $name => $value ) {
			if ( is_array( $value ) ) {
				$sanitized_headers[ $name ] = implode( ', ', $value );
			} else {
				$sanitized_headers[ $name ] = $value;
			}
		}
		return $sanitized_headers;
	}
}
