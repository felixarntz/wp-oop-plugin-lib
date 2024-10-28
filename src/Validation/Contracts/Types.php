<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

/**
 * Interface acting as an enum for validation types.
 *
 * @since 0.1.0
 */
interface Types {

	const TYPE_BOOLEAN = 1;
	const TYPE_FLOAT   = 2;
	const TYPE_INTEGER = 4;
	const TYPE_STRING  = 8;
	const TYPE_ARRAY   = 16;
	const TYPE_OBJECT  = 32;

	// This is the same as all of the above with a bitwise OR operator (|).
	const TYPE_ANY = 63;
}
