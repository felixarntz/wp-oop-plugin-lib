<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\WP_Error_Exception;
use WP_Error;

/**
 * Exception class for when validation of a value fails.
 *
 * @since n.e.x.t
 */
class Validation_Exception extends WP_Error_Exception {

	/**
	 * Creates a new validation exception based on error code, and error message.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $error_code Text based error code.
	 * @param string $message    Error message.
	 * @return Validation_Exception New exception instance.
	 */
	public static function create( string $error_code, string $message ): Validation_Exception {
		$instance = new self( $message );
		$instance->set_error_code( $error_code );
		return $instance;
	}

	/**
	 * Creates a new validation exception from the given WP_Error object.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_Error $error WP_Error object.
	 * @return Validation_Exception New exception instance.
	 */
	public static function from_wp_error( WP_Error $error ): Validation_Exception {
		return self::create( $error->get_error_code(), $error->get_error_message() );
	}
}
