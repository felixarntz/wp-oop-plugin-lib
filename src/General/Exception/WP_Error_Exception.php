<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\WP_Error_Exception
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception;

use RuntimeException;
use WP_Error;

/**
 * Exception class equivalent to a WP_Error object.
 *
 * @since 0.1.0
 */
class WP_Error_Exception extends RuntimeException {

	/**
	 * Text based error code.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $error_code = '';

	/**
	 * Gets the text based error code.
	 *
	 * @since 0.1.0
	 *
	 * @return string Text based error code.
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
	 * Sets the text based error code.
	 *
	 * @since 0.1.0
	 *
	 * @param string $error_code Text based error code.
	 */
	public function set_error_code( string $error_code ): void {
		$this->error_code = $error_code;
	}

	/**
	 * Creates a new WP_Error exception based on error code, and error message.
	 *
	 * @since 0.1.0
	 *
	 * @param string $error_code Text based error code.
	 * @param string $message    Error message.
	 * @return WP_Error_Exception New exception instance.
	 */
	public static function create( string $error_code, string $message ): WP_Error_Exception {
		$instance = new self( $message );
		$instance->set_error_code( $error_code );
		return $instance;
	}

	/**
	 * Creates a new WP_Error exception from the given WP_Error object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Error $error WP_Error object.
	 * @return WP_Error_Exception New exception instance.
	 */
	public static function from_wp_error( WP_Error $error ): WP_Error_Exception {
		return self::create( $error->get_error_code(), $error->get_error_message() );
	}
}
