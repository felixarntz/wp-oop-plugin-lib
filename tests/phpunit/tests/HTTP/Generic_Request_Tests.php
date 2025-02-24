<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Request
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group http
 */
class Generic_Request_Tests extends Test_Case {

	/**
	 * @dataProvider data_get_url
	 */
	public function test_get_url( $url, $expected ) {
		$request = new Generic_Request( $url );
		$this->assertSame( $expected, $request->get_url() );
	}

	public static function data_get_url(): array {
		return array(
			'plain and simple'    => array(
				'https://wordpress.org',
				'https://wordpress.org',
			),
			'with trailing slash' => array(
				'https://wordpress.org/plugins/',
				'https://wordpress.org/plugins/',
			),
			'no trailing slash'   => array(
				'https://wordpress.org/plugins',
				'https://wordpress.org/plugins',
			),
		);
	}

	/**
	 * @dataProvider data_get_method
	 */
	public function test_get_method( $args, $expected, $doing_it_wrong = false ) {
		if ( $doing_it_wrong ) {
			$this->expectDoingItWrong( Generic_Request::class . '::__construct' );
		}
		$request = new Generic_Request( 'https://my-api.com/v1/entries', array(), $args );
		$this->assertSame( $expected, $request->get_method() );
	}

	public static function data_get_method(): array {
		return array(
			'default' => array(
				array(),
				Request::GET,
				false,
			),
			'GET'     => array(
				array( 'method' => Request::GET ),
				Request::GET,
				false,
			),
			'POST'    => array(
				array( 'method' => Request::POST ),
				Request::POST,
				false,
			),
			'PATCH'   => array(
				array( 'method' => Request::PATCH ),
				Request::PATCH,
				false,
			),
			'PUT'     => array(
				array( 'method' => Request::PUT ),
				Request::PUT,
				false,
			),
			'DELETE'  => array(
				array( 'method' => Request::DELETE ),
				Request::DELETE,
				false,
			),
			'invalid' => array(
				array( 'method' => 'invalid' ),
				Request::GET,
				true,
			),
		);
	}

	/**
	 * @dataProvider data_get_data_and_body
	 */
	public function test_get_data_and_body( $data, $args, $expected_data, $expected_body, $doing_it_wrong = false ) {
		if ( $doing_it_wrong ) {
			$this->expectDoingItWrong( Generic_Request::class . '::__construct' );
		}
		$request = new Generic_Request( 'https://my-api.com/v1/entries', $data, $args );
		$this->assertSame( $expected_data, $request->get_data() );
		$this->assertSame( $expected_body, $request->get_body() );
	}

	public static function data_get_data_and_body(): array {
		return array(
			'with data'    => array(
				array( 'key' => 'value' ),
				array(),
				array( 'key' => 'value' ),
				'',
				false,
			),
			'with body'    => array(
				array(),
				array( 'body' => 'key=value&enable=1' ),
				array(),
				'key=value&enable=1',
				false,
			),
			'empty data'   => array(
				array(),
				array(),
				array(),
				'',
				false,
			),
			'empty body'   => array(
				array(),
				array( 'body' => '' ),
				array(),
				'',
				false,
			),
			'invalid body' => array(
				array(),
				array( 'body' => array( 'key' => 'value' ) ),
				array(),
				'',
				true, // The body must be a string.
			),
			'with both'    => array(
				array( 'key' => 'value' ),
				array( 'body' => 'key=value&enable=1' ),
				array( 'key' => 'value' ),
				'',
				true, // Only data _or_ body must be provided.
			),
		);
	}

	/**
	 * @dataProvider data_get_headers
	 */
	public function test_get_headers( $args, $expected, $doing_it_wrong = false ) {
		if ( $doing_it_wrong ) {
			$this->expectDoingItWrong( Generic_Request::class . '::__construct' );
		}
		$request = new Generic_Request( 'https://my-api.com/v1/entries', array(), $args );
		$this->assertSame( $expected, $request->get_headers() );
	}

	public static function data_get_headers(): array {
		return array(
			'plain and simple'  => array(
				array(
					'headers' => array(
						'Content-Type'  => 'application/json',
						'Authorization' => 'Bearer 12345678',
					),
				),
				array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer 12345678',
				),
				false,
			),
			'empty'             => array(
				array( 'headers' => array() ),
				array(),
				false,
			),
			'none'              => array(
				array(),
				array(),
				false,
			),
			'with multi values' => array(
				array(
					'headers' => array(
						'Content-Type' => 'application/json',
						'X-Content-Id' => array( '1', '23', '42' ),
					),
				),
				array(
					'Content-Type' => 'application/json',
					'X-Content-Id' => '1, 23, 42',
				),
				false,
			),
			'invalid type'      => array(
				array( 'headers' => 'Content-Type: application/json' ),
				array(),
				true, // Headers must be an array.
			),
			'invalid array'     => array(
				array( 'headers' => array( 'Content-Type: application/json' ) ),
				array(),
				true, // Headers must be an associative array, not a numeric one.
			),
		);
	}

	/**
	 * @dataProvider data_get_options
	 */
	public function test_get_options( $args, $expected, $doing_it_wrong = false ) {
		if ( $doing_it_wrong ) {
			$this->expectDoingItWrong( Generic_Request::class . '::__construct' );
		}
		$request = new Generic_Request( 'https://my-api.com/v1/entries', array(), $args );
		$this->assertSame( $expected, $request->get_options() );
	}

	public static function data_get_options(): array {
		return array(
			'plain and simple' => array(
				array(
					'timeout'  => 25,
					'blocking' => false,
				),
				array(
					'timeout'  => 25,
					'blocking' => false,
				),
				false,
			),
			'empty'            => array(
				array(),
				array(),
				false,
			),
			'with method'      => array(
				array(
					'timeout'  => 25,
					'blocking' => false,
					'method'   => Request::POST,
				),
				array(
					'timeout'  => 25,
					'blocking' => false,
				),
				false,
			),
			'with headers'     => array(
				array(
					'timeout'  => 15,
					'blocking' => false,
					'headers'  => array( 'Content-Type' => 'application/json' ),
				),
				array(
					'timeout'  => 15,
					'blocking' => false,
				),
				false,
			),
			'with body'        => array(
				array(
					'timeout'  => 25,
					'blocking' => true,
					'body'     => 'key=value',
				),
				array(
					'timeout'  => 25,
					'blocking' => true,
				),
				false,
			),
		);
	}

	public function test_add_header() {
		$request = new Generic_Request(
			'https://my-api.com/v1/entries',
			array(),
			array(
				'headers' => array( 'Authorization' => 'Bearer 12345abcde' )
			)
		);
		$request->add_header( 'X-Cms-Info', 'WordPress' );
		$this->assertSame(
			array(
				'Authorization' => 'Bearer 12345abcde',
				'X-Cms-Info'    => 'WordPress',
			),
			$request->get_headers()
		);
	}

	public function test_add_data() {
		$request = new Generic_Request(
			'https://my-api.com/v1/entries',
			array( 'key' => 'value' )
		);
		$request->add_data( 'enabled', '1' );
		$this->assertSame(
			array(
				'key'     => 'value',
				'enabled' => '1',
			),
			$request->get_data()
		);
	}

	public function test_add_data_with_body() {
		$this->expectDoingItWrong( Generic_Request::class . '::add_data' );

		$request = new Generic_Request(
			'https://my-api.com/v1/entries',
			array(),
			array(
				'body' => 'key=value&q=OOP+Plugins',
			)
		);
		$request->add_data( 'enabled', '1' );
		$this->assertSame( array(), $request->get_data() );
	}
}
