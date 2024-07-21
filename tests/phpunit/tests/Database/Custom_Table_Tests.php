<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Database;

use Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table;
use Felix_Arntz\WP_OOP_Plugin_Lib\Database\Exception\Database_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema\Generic_Table_Schema;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema\ID_Slug_Content_Table_Schema;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group database
 */
class Custom_Table_Tests extends Test_Case {

	private $new_wpdb_tables = array();

	public function tear_down() {
		global $wpdb;

		parent::tear_down();

		$suppress = $wpdb->suppress_errors();

		foreach ( $this->new_wpdb_tables as $key ) {
			$table_name = $wpdb->$key;
			$wpdb->query( "DROP TABLE $table_name" );
			unset( $wpdb->$key );
		}
		$this->new_wpdb_tables = array();

		$wpdb->suppress_errors( $suppress );
	}

	public function test_get_key() {
		$table = new Custom_Table( 'my_table', new ID_Slug_Content_Table_Schema() );
		$this->assertSame( 'my_table', $table->get_key() );
	}

	public function test_exists() {
		$table = new Custom_Table( 'myplugin_entities', new ID_Slug_Content_Table_Schema() );

		// Test without table registration.
		$this->assertFalse( $table->exists() );

		// Test with table registration (but without creating the table).
		$this->register_table_in_wpdb( $table->get_key() );
		$this->assertFalse( $table->exists() );
	}

	public function test_create_without_registration() {
		$table = new Custom_Table( 'myplugin_entities', new ID_Slug_Content_Table_Schema() );

		$this->expectException( Database_Exception::class );
		$this->expectExceptionMessage( 'Database table myplugin_entities not registered in wpdb' );
		$table->create();
	}

	public function test_create_with_empty_schema() {
		$table = new Custom_Table( 'myplugin_entities', new Generic_Table_Schema( array() ) );

		$this->expectException( Database_Exception::class );
		$this->expectExceptionMessage( 'Database table myplugin_entities must not have empty schema' );
		$this->register_table_in_wpdb( $table->get_key() );
		$table->create();
	}

	public function test_create() {
		global $wpdb;

		$table = new Custom_Table( 'myplugin_entities', new ID_Slug_Content_Table_Schema() );

		$this->register_table_in_wpdb( $table->get_key() );
		$table->create();

		$this->assertTrue( $table->exists() );

		// Run a random query against the table, just to make sure there is no error.
		$this->assertSame(
			array(),
			$wpdb->get_results( "SELECT * FROM $wpdb->myplugin_entities WHERE slug = 'something' LIMIT 2" )
		);
	}

	public function test_drop_without_registration() {
		$table = new Custom_Table( 'myplugin_data', new ID_Slug_Content_Table_Schema() );

		$this->expectException( Database_Exception::class );
		$this->expectExceptionMessage( 'Database table myplugin_data not registered in wpdb' );
		$table->drop();
	}

	public function test_drop() {
		$table = new Custom_Table( 'myplugin_data', new ID_Slug_Content_Table_Schema() );

		$this->register_table_in_wpdb( $table->get_key() );
		$table->create();
		$this->assertTrue( $table->exists() );

		$table->drop();
		$this->assertFalse( $table->exists() );
	}

	private function register_table_in_wpdb( string $key ) {
		global $wpdb;

		$wpdb->$key = $wpdb->prefix . $key;

		$this->new_wpdb_tables[] = $key;
	}
}
