<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Response
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Response;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;

/**
 * @group http
 */
class JSON_Response_Tests extends Test_Case {

	/**
	 * @dataProvider data_get_data
	 */
	public function test_get_data( $body, $expected_data ) {
		$response = new JSON_Response( 200, $body, array() );
		$this->assertSame( $expected_data, $response->get_data() );
	}

	public static function data_get_data(): array {
		return array(
			'plain and simple' => array(
				'{"key":"value"}',
				array( 'key' => 'value' ),
			),
			'with boolean'     => array(
				'{"key":"value", "enabled": true}',
				array(
					'key'     => 'value',
					'enabled' => true,
				),
			),
			'empty'            => array(
				'',
				array(),
			),
			'invalid JSON'     => array(
				' not JSON :(',
				array(),
			),
		);
	}
}
