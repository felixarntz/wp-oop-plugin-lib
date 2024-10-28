<?php
/**
 * Trait Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits\Sanitize_Headers
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits;

/**
 * Trait with a function to sanitize headers into an associative array of strings.
 *
 * @since 0.1.0
 */
trait Sanitize_Headers {

	/**
	 * Sanitizes the given headers associative array to make sure all values are strings.
	 *
	 * Multiple values for the same header will be concatenated with a comma.
	 *
	 * @since 0.1.0
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
