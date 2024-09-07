<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Response
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Response;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group http
 */
class Generic_Response_Tests extends Test_Case {

	/**
	 * @dataProvider data_get_status
	 */
	public function test_get_status( $status_code, $expected ) {
		$response = new Generic_Response( $status_code, '', array() );
		$this->assertSame( $expected, $response->get_status() );
	}

	public function data_get_status(): array {
		return array(
			'plain and simple' => array(
				200,
				200,
			),
			'access error'     => array(
				400,
				400,
			),
			'internal error'   => array(
				500,
				500,
			),
		);
	}

	/**
	 * @dataProvider data_get_data_and_body
	 */
	public function test_get_data_and_body( $body, $expected_data, $expected_body ) {
		$response = new Generic_Response( 200, $body, array() );
		$this->assertSame( $expected_data, $response->get_data() );
		$this->assertSame( $expected_body, $response->get_body() );
	}

	public function data_get_data_and_body(): array {
		return array(
			'plain and simple' => array(
				'key=value',
				array(),
				'key=value',
			),
			'empty'            => array(
				'',
				array(),
				'',
			),
		);
	}

	/**
	 * @dataProvider data_get_headers
	 */
	public function test_get_headers( $headers, $expected ) {
		$response = new Generic_Response( 200, '', $headers );
		$this->assertSame( $expected, $response->get_headers() );
	}

	public function data_get_headers(): array {
		return array(
			'plain and simple'  => array(
				array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer 12345678',
				),
				array(
					'content-type'  => 'application/json',
					'authorization' => 'Bearer 12345678',
				),
			),
			'empty'             => array(
				array(),
				array(),
			),
			'with multi values' => array(
				array(
					'Content-Type' => 'application/json',
					'X-Content-Id' => '1, 23, 42',
				),
				array(
					'content-type' => 'application/json',
					'x-content-id' => '1, 23, 42',
				),
			),
			'with mixed case'   => array(
				array(
					'Content-Type' => 'application/json',
					'content-type' => 'application/json',
				),
				array(
					'content-type' => 'application/json',
				),
			),
		);
	}

	/**
	 * @dataProvider data_get_header
	 */
	public function test_get_header( $headers, $expected ) {
		$response = new Generic_Response( 200, '', $headers );
		foreach ( $expected as $header_name => $expected_value ) {
			$this->assertSame( $expected_value, $response->get_header( $header_name ) );
		}
	}

	public function data_get_header(): array {
		return array(
			'plain and simple'  => array(
				array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer 12345678',
				),
				array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer 12345678',
				),
			),
			'empty'             => array(
				array(),
				array(
					'Content-Type' => '',
				),
			),
			'with multi values' => array(
				array(
					'Content-Type' => 'application/json',
					'X-Content-Id' => '1, 23, 42',
				),
				array(
					'Content-Type' => 'application/json',
					'X-Content-Id' => '1, 23, 42',
				),
			),
			'with mixed case'   => array(
				array(
					'Content-Type' => 'application/json',
				),
				array(
					'Content-Type' => 'application/json',
					'content-type' => 'application/json',
				),
			),
		);
	}
}
