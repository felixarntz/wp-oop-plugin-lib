<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Exception\REST_Exception
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Exception;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\WP_Error_Exception;
use WP_Error;

/**
 * Exception class for when a REST route error occurs.
 *
 * @since 0.1.0
 */
class REST_Exception extends WP_Error_Exception {

	/**
	 * HTTP response code.
	 *
	 * @since 0.1.0
	 * @var int
	 */
	private $response_code = 500;

	/**
	 * Gets the HTTP response code.
	 *
	 * @since 0.1.0
	 *
	 * @return int HTTP response code.
	 */
	public function get_response_code(): int {
		return $this->response_code;
	}

	/**
	 * Sets the HTTP response code.
	 *
	 * @since 0.1.0
	 *
	 * @param int $response_code HTTP response code.
	 */
	public function set_response_code( int $response_code ): void {
		if ( $response_code < 400 ) { // Only error response code >= 400 are allowed.
			return;
		}
		$this->response_code = $response_code;
	}

	/**
	 * Creates a new REST exception based on error code, error message, and a HTTP Response code.
	 *
	 * @since 0.1.0
	 *
	 * @param string $error_code    REST error code.
	 * @param string $message       Error message.
	 * @param int    $response_code Optional. HTTP response code. Default 500.
	 * @return REST_Exception New exception instance.
	 */
	public static function create( string $error_code, string $message, int $response_code = 500 ): REST_Exception {
		$instance = new self( $message );
		$instance->set_error_code( $error_code );
		$instance->set_response_code( $response_code );
		return $instance;
	}

	/**
	 * Creates a new REST exception from the given WP_Error object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Error $error WP_Error object.
	 * @return REST_Exception New exception instance.
	 */
	public static function from_wp_error( WP_Error $error ): REST_Exception {
		$error_data    = $error->get_error_data();
		$response_code = $error_data['status'] ?? 500;
		return self::create( $error->get_error_code(), $error->get_error_message(), $response_code );
	}
}
