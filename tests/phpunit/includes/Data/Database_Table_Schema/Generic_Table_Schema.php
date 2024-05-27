<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema\Generic_Table_Schema
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema;

use Felix_Arntz\WP_OOP_Plugin_Lib\Database\Contracts\Database_Table_Schema;

class Generic_Table_Schema implements Database_Table_Schema {

	private $schema_arr;

	public function __construct( array $schema_arr ) {
		$this->schema_arr = $schema_arr;
	}

	public function get_schema_array(): array {
		return $this->schema_arr;
	}
}
