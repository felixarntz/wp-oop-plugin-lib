<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Mutable_Input
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Read-only class for filtering mutable input, effectively for superglobal access.
 *
 * It is recommended to use the regular Input class for most input handling, while this class is useful for e.g. unit
 * tests where you want to mock input values.
 *
 * @since n.e.x.t
 */
class Mutable_Input extends Input {

	/**
	 * Gets a specific external variable by name and optionally filters it.
	 *
	 * @since n.e.x.t
	 *
	 * @link https://php.net/manual/en/function.filter-input.php
	 *
	 * @param int    $type          One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
	 * @param string $variable_name Name of a variable to get.
	 * @param int    $filter        Optional. The ID of the filter to apply. The manual page lists the available
	 *                              filters.
	 * @param mixed  $options       Optional. Associative array of options or bitwise disjunction of flags. If filter
	 *                              accepts options, flags can be provided in "flags" field of array.
	 * @return mixed Value of the requested variable on success, false if the filter fails, null if the $variable_name
	 *               variable is not set. If the flag FILTER_NULL_ON_FAILURE is used, it returns false if the variable
	 *               is not set and null if the filter fails.
	 */
	public function filter( $type, $variable_name, $filter = FILTER_DEFAULT, $options = 0 ) {
		switch ( $type ) {
			case INPUT_GET:
				// phpcs:ignore WordPress.Security.NonceVerification
				$superglobal = $_GET;
				break;
			case INPUT_POST:
				// phpcs:ignore WordPress.Security.NonceVerification
				$superglobal = $_POST;
				break;
			case INPUT_SERVER:
				$superglobal = $_SERVER;
				break;
			case INPUT_COOKIE:
				$superglobal = $_COOKIE;
				break;
			case INPUT_ENV:
				$superglobal = $_ENV;
				break;
			default:
				return null;
		}

		if ( ! isset( $superglobal[ $variable_name ] ) ) {
			return null;
		}

		return filter_var( $superglobal[ $variable_name ], $filter, $options );
	}
}
