<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema\ID_Slug_Content_Table_Schema
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Database_Table_Schema;

class ID_Slug_Content_Table_Schema implements Database_Table_Schema {

	public function get_schema_array(): array {
		return array(
			'id bigint(20) unsigned NOT NULL auto_increment',
			'slug text NOT NULL',
			'content longtext NOT NULL',
			'PRIMARY KEY  (id)',
			'UNIQUE KEY slug (slug)',
		);
	}
}
