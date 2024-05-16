<?php
/**
 * Trait Felix_Arntz\WP_OOP_Plugin_Lib\Traits\Maybe_Throw
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Traits;

use Exception;

/**
 * Trait with a function to cast a value by a type.
 *
 * @since n.e.x.t
 */
trait Maybe_Throw {

	/**
	 * Calls a callback function that may throw exceptions, and catches exceptions unless `WP_DEBUG` is enabled.
	 *
	 * @since n.e.x.t
	 *
	 * @param callable $callback      Callback function.
	 * @param mixed[]  $callback_args Parameters to pass to the callback function.
	 * @return bool True on success, or false if an exception was caught.
	 *
	 * @throws Exception Thrown based on the underlying callback, and only if `WP_DEBUG` is enabled.
	 */
	protected function maybe_throw( callable $callback, array $callback_args ): bool {
		if ( WP_DEBUG ) {
			call_user_func_array( $callback, $callback_args );
			return true;
		}

		try {
			call_user_func_array( $callback, $callback_args );
		} catch ( Exception $e ) {
			return false;
		}
		return true;
	}

	/**
	 * Calls a callback function that may return a `WP_Error`, and throws an exception for them only if `WP_DEBUG` is enabled.
	 *
	 * @since n.e.x.t
	 *
	 * @param callable $callback      Callback function.
	 * @param mixed[]  $callback_args Parameters to pass to the callback function.
	 * @return bool True on success, or false if a `WP_Error` was returned.
	 *
	 * @throws Exception Thrown based on the underlying callback, and only if `WP_DEBUG` is enabled.
	 */
	protected function maybe_throw_wp_error( callable $callback, array $callback_args ): bool {
		$result = call_user_func_array( $callback, $callback_args );
		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Exception( esc_html( $result->get_error_message() ) );
			}
			return false;
		}
		return true;
	}
}
