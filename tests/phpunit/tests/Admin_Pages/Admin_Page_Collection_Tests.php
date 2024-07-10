<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Page_Collection
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\Admin_Pages;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Page_Collection;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page\Generic_Admin_Page;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

class Admin_Page_Collection_Tests extends Test_Case {

	public function test_getIterator() {
		$admin_pages = array(
			new Generic_Admin_Page(
				array(
					'slug'       => 'test_page_1',
					'title'      => 'Test Page 1',
					'capability' => 'edit_posts',
				)
			),
			new Generic_Admin_Page(
				array(
					'slug'       => 'test_page_2',
					'title'      => 'Test Page 2',
					'capability' => 'manage_options',
				)
			),
		);
		$collection = new Admin_Page_Collection( $admin_pages );

		// Assert that an array iterator is returned.
		$this->assertInstanceOf( ArrayIterator::class, $collection->getIterator() );

		// Actually test the iterator.
		foreach ( $collection as $index => $admin_page ) {
			$this->assertSame( $admin_pages[ $index ], $admin_page );
		}
	}

	public function test_count() {
		$collection = new Admin_Page_Collection(
			array(
				new Generic_Admin_Page(
					array(
						'slug'       => 'test_page_1',
						'title'      => 'Test Page 1',
						'capability' => 'edit_posts',
					)
				),
				new Generic_Admin_Page(
					array(
						'slug'       => 'test_page_2',
						'title'      => 'Test Page 2',
						'capability' => 'manage_options',
					)
				),
				new Generic_Admin_Page(
					array(
						'slug'       => 'test_page_3',
						'title'      => 'Test Page 3',
						'capability' => 'manage_options',
					)
				),
			)
		);

		$this->assertSame( 3, $collection->count() );
		$this->assertCount( 3, $collection );
	}
}
