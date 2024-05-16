<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Database;

use Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table_Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Database_Table_Schema\ID_Slug_Content_Table_Schema;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Custom_Table_Container_Tests extends Test_Case {

	private $container;

	public function set_up() {
		parent::set_up();
		$this->container = new Custom_Table_Container();
		$this->container->set(
			'myplugin_items',
			static function () {
				return new Custom_Table( 'myplugin_items', new ID_Slug_Content_Table_Schema() );
			}
		);
		$this->container->set(
			'invalid_type_table',
			static function () {
				return new \stdClass();
			}
		);
	}

	public function tear_down() {
		parent::tear_down();

		foreach ( $this->container->get_keys() as $key ) {
			$this->container->unset( $key );
		}
	}

	/**
	 * @dataProvider data_has
	 */
	public function test_has( $table, $expected ) {
		if ( $expected ) {
			$this->assertTrue( $this->container->has( $table ) );
		} else {
			$this->assertFalse( $this->container->has( $table ) );
		}
	}

	public function data_has() {
		return array(
			'non existent table'                 => array( 'non_existent_table', false ),
			'existing table using invalid class' => array( 'invalid_type_table', true ),
			'existing valid table'               => array( 'myplugin_items', true ),
		);
	}

	/**
	 * @dataProvider data_get
	 */
	public function test_get( $table, $expected_exception = null ) {
		if ( $expected_exception ) {
			$this->expectException( $expected_exception );
		}
		$result = $this->container->get( $table );
		$this->assertInstanceOf( Custom_Table::class, $result );
		$this->assertSame( $table, $result->get_key() );
	}

	public function data_get() {
		return array(
			'non existent table'                 => array( 'non_existent_table', Not_Found_Exception::class ),
			'existing table using invalid class' => array( 'invalid_type_table', Invalid_Type_Exception::class ),
			'existing valid table'               => array( 'myplugin_items', null ),
		);
	}
}
