<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Database\Contracts\Database_Table_Schema
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Database\Contracts;

/**
 * Interface for a custom database table schema class.
 *
 * @since 0.1.0
 */
interface Database_Table_Schema {

	/**
	 * Gets the database table schema.
	 *
	 * @since 0.1.0
	 *
	 * @return string[] Database table schema, as an array of field directives.
	 */
	public function get_schema_array(): array;
}
