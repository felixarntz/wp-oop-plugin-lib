<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Input
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Read-only class for filtering input, effectively for superglobal access.
 *
 * @since n.e.x.t
 */
class Input {

	/**
	 * Map of input type to superglobal array.
	 *
	 * For use as fallback only.
	 *
	 * @since n.e.x.t
	 * @var array<int, array<string, mixed>>
	 */
	private $fallback_map;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 */
	public function __construct() {
		// Fallback map for environments where filter_input may not work with ENV or SERVER types.
		$this->fallback_map = array(
			INPUT_ENV    => $_ENV,
			INPUT_SERVER => $_SERVER, // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage
		);
	}

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
	 *               variable is not set. If the flag FILTER_NULL_ON_FAILURE is  used, it returns false if the variable
	 *               is not set and null if the filter fails.
	 */
	public function filter( $type, $variable_name, $filter = FILTER_DEFAULT, $options = 0 ) {
		/* @phpstan-ignore-next-line */
		$value = filter_input( $type, $variable_name, $filter, $options );

		/*
		 * Fallback for environments where filter_input may not work with specific types.
		 * This is only used for affected input types and if the value is not set.
		 */
		if (
			isset( $this->fallback_map[ $type ] )
			&& in_array( $value, array( null, false ), true )
			&& array_key_exists( $variable_name, $this->fallback_map[ $type ] )
		) {
			return filter_var( $this->fallback_map[ $type ][ $variable_name ], $filter, $options );
		}

		return $value;
	}
}
