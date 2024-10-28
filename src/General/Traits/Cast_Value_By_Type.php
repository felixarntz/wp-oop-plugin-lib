<?php
/**
 * Trait Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits;

/**
 * Trait with a function to cast a value by a type.
 *
 * @since 0.1.0
 */
trait Cast_Value_By_Type {

	/**
	 * Casts the given value into the given type identifier.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $value Value to cast.
	 * @param string $type  Type identifier. Supported values are 'bool', 'int', 'float', 'string', 'array', and
	 *                      'object'.
	 * @return mixed The cast value.
	 */
	protected function cast_value_by_type( $value, string $type ) {
		switch ( $type ) {
			case 'bool':
			case 'boolean':
				return (bool) $value;
			case 'int':
			case 'integer':
				return (int) $value;
			case 'double':
			case 'float':
				return (float) $value;
			case 'string':
				return (string) $value;
			case 'array':
			case 'object': // Objects are handled as associative arrays in WordPress.
				if ( ! $value ) {
					return array();
				}
				return (array) $value;
		}

		return $value;
	}
}
