<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Request
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group http
 */
class JSON_Request_Tests extends Test_Case {

	public function test_constructor() {
		$request = new JSON_Request( 'https://my-api.com/v1/entries/3', array( 'key' => 'value' ) );
		$this->assertSame( Request::GET, $request->get_method() );
		$this->assertSame(
			array( 'Content-Type' => 'application/json' ),
			$request->get_headers()
		);
	}

	public function test_constructor_with_custom_header() {
		$request = new JSON_Request(
			'https://my-api.com/v1/entries/3',
			array( 'key' => 'value' ),
			array(
				'method'  => Request::POST,
				'headers' => array( 'X-Api-Key' => 'a1b2c3d4e5f6' ),
			)
		);
		$this->assertSame( Request::POST, $request->get_method() );
		$this->assertSame(
			array(
				'X-Api-Key'    => 'a1b2c3d4e5f6',
				'Content-Type' => 'application/json',
			),
			$request->get_headers()
		);
	}

	public function test_constructor_with_custom_body() {
		$request = new JSON_Request(
			'https://my-api.com/v1/entries/3',
			array(),
			array(
				'method' => Request::POST,
				'body'   => '{"key":"value"}',
			)
		);
		$this->assertSame( Request::POST, $request->get_method() );
		$this->assertSame( '{"key":"value"}', $request->get_body() );
	}

	public function test_constructor_with_invalid_body() {
		$this->expectDoingItWrong( JSON_Request::class . '::__construct' );

		$request = new JSON_Request(
			'https://my-api.com/v1/entries/3',
			array(),
			array(
				'method' => Request::POST,
				'body'   => 'This is some plain text.',
			)
		);
		$this->assertSame( Request::POST, $request->get_method() );
		$this->assertSame( '', $request->get_body() );
	}

	public function test_get_data() {
		$request = new JSON_Request( 'https://my-api.com/v1/entries/3', array( 'key' => 'value' ) );
		$this->assertSame( array(), $request->get_data() ); // Body is used for JSON requests.
	}

	public function test_get_body() {
		$request = new JSON_Request( 'https://my-api.com/v1/entries/3', array( 'key' => 'value' ) );
		$this->assertSame( '{"key":"value"}', $request->get_body() );
	}
}
