<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Put_Request
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Put_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group http
 */
class JSON_Put_Request_Tests extends Test_Case {

	public function test_constructor() {
		$request = new JSON_Put_Request( 'https://my-api.com/v1/entries/3', array( 'key' => 'value' ) );
		$this->assertSame( Request::PUT, $request->get_method() );
		$this->assertSame(
			array( 'Content-Type' => 'application/json' ),
			$request->get_headers()
		);
	}
}