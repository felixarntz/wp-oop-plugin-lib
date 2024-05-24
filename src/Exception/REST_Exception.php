<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Exception\REST_Exception
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Exception;

use RuntimeException;

/**
 * Exception class for when a REST route error occurs.
 *
 * @since n.e.x.t
 */
class REST_Exception extends RuntimeException {

	/**
	 * REST error code.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $error_code = '';

	/**
	 * HTTP response code.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $response_code = 500;

	/**
	 * Gets the REST error code.
	 *
	 * @since n.e.x.t
	 *
	 * @return string REST error code.
	 */
	public function get_error_code(): string {
		if ( ! $this->error_code ) {
			// Fall back to using error message as error code.
			return preg_replace(
				'/[^a-z0-9_]+/',
				'',
				str_replace( array( ' ', '-' ), '_', strtolower( $this->getMessage() ) )
			);
		}

		return $this->error_code;
	}

	/**
	 * Gets the HTTP response code.
	 *
	 * @since n.e.x.t
	 *
	 * @return int HTTP response code.
	 */
	public function get_response_code(): int {
		return $this->response_code;
	}

	/**
	 * Sets the REST error code.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $error_code REST error code.
	 */
	public function set_error_code( string $error_code ): void {
		$this->error_code = $error_code;
	}

	/**
	 * Sets the HTTP response code.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
}
