<?php
/**
 * Tests for Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\HTTP
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Tests\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Response;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Multiple_Requests_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Request_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Generic_Response;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Get_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\HTTP;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Post_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\JSON_Response;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Post_Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case;
use InvalidArgumentException;
use WP_Error;

/**
 * @group http
 */
class HTTP_Tests extends Test_Case {

	/**
	 * @dataProvider data_options
	 */
	public function test_request_options( array $options, array $default_options, array $expected ) {
		$http = new HTTP( $default_options );

		$request = new Generic_Request( 'https://example.com/', array(), $options );

		$parsed_args = array();
		add_filter(
			'pre_http_request',
			static function ( $response, $args ) use ( &$parsed_args ) {
				$parsed_args = $args;
				return array(
					'headers'  => array(),
					'response' => array(
						'code'    => 200,
						'message' => 'OK',
					),
					'body'     => '',
					'cookies'  => array(),
					'filename' => null,
				);
			},
			10,
			2
		);

		$http->request( $request );

		$relevant_args = array_intersect_key( $parsed_args, $expected );
		$this->assertSameSetsWithIndex( $expected, $relevant_args );
	}

	/**
	 * @dataProvider data_options
	 */
	public function test_request_multiple_options( array $options, array $default_options, array $expected ) {
		$http = new HTTP( $default_options );

		$request = new Generic_Request( 'https://example.com/', array(), $options );

		$parsed_args = array();
		add_filter(
			'pre_http_request',
			static function ( $response, $args ) use ( &$parsed_args ) {
				$parsed_args = $args;
				return array(
					'headers'  => array(),
					'response' => array(
						'code'    => 200,
						'message' => 'OK',
					),
					'body'     => '',
					'cookies'  => array(),
					'filename' => null,
				);
			},
			10,
			2
		);

		$http->request_multiple( array( $request ) );

		$relevant_args = array_intersect_key( $parsed_args, $expected );
		$this->assertSameSetsWithIndex( $expected, $relevant_args );
	}

	public function data_options() {
		return array(
			'defaults'              => array(
				array(),
				array(),
				array(
					'timeout'             => 5,
					'redirection'         => 5,
					'user-agent'          => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
					'reject_unsafe_urls'  => false,
					'blocking'            => true,
					'sslverify'           => true,
					'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
					'stream'              => false,
					'filename'            => null,
					'limit_response_size' => null,
				),
			),
			'simple options'        => array(
				array( 'timeout' => 25 ),
				array(),
				array(
					'timeout'             => 25,
					'redirection'         => 5,
					'user-agent'          => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
					'reject_unsafe_urls'  => false,
					'blocking'            => true,
					'sslverify'           => true,
					'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
					'stream'              => false,
					'filename'            => null,
					'limit_response_size' => null,
				),
			),
			'default options'       => array(
				array(),
				array( 'timeout' => 15 ),
				array(
					'timeout'             => 15,
					'redirection'         => 5,
					'user-agent'          => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
					'reject_unsafe_urls'  => false,
					'blocking'            => true,
					'sslverify'           => true,
					'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
					'stream'              => false,
					'filename'            => null,
					'limit_response_size' => null,
				),
			),
			'options with defaults' => array(
				array(
					'timeout'     => 20,
					'redirection' => 0,
				),
				array(
					'timeout'    => 15,
					'user-agent' => 'MyPlugin',
				),
				array(
					'timeout'             => 20,
					'redirection'         => 0,
					'user-agent'          => 'MyPlugin',
					'reject_unsafe_urls'  => false,
					'blocking'            => true,
					'sslverify'           => true,
					'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
					'stream'              => false,
					'filename'            => null,
					'limit_response_size' => null,
				),
			),
		);
	}

	/**
	 * @dataProvider data_request
	 */
	public function test_request( Request $request, array $mock_response_data, $expected ) {
		$http = new HTTP();

		if ( $expected instanceof Request_Exception ) {
			$this->expectException( Request_Exception::class );
			$this->expectExceptionMessage( $expected->getMessage() );
		} elseif ( ! $expected instanceof Response ) {
			throw new InvalidArgumentException( 'The expected value must either be of type Response or Request_Exception.' );
		}

		add_filter(
			'pre_http_request',
			static function ( $response ) use ( $mock_response_data ) {
				if ( isset( $mock_response_data['error'] ) ) {
					return new WP_Error( $mock_response_data['error']['code'], $mock_response_data['error']['message'] );
				}

				if ( isset( $mock_response_data['response'] ) ) {
					if ( ! is_array( $mock_response_data['response'] ) ) {
						$mock_response_data['response'] = array( 'code' => (int) $mock_response_data['response'] );
					}
					if ( ! isset( $mock_response_data['response']['message'] ) ) {
						$mock_response_data['response']['message'] = get_status_header_desc( $mock_response_data['response']['code'] );
					}
				}

				return array_merge(
					array(
						'headers'  => array(),
						'response' => array(
							'code'    => 200,
							'message' => 'OK',
						),
						'body'     => '',
						'cookies'  => array(),
						'filename' => null,
					),
					$mock_response_data
				);
			}
		);

		$response = $http->request( $request );
		$this->assertSame( $expected->get_status(), $response->get_status(), 'Unexpected response status.' );
		$this->assertSame( $expected->get_data(), $response->get_data(), 'Unexpected response data.' );
		$this->assertSame( $expected->get_body(), $response->get_body(), 'Unexpected response body.' );
		$this->assertSame( $expected->get_headers(), $response->get_headers(), 'Unexpected response headers.' );
	}

	/**
	 * @dataProvider data_request
	 */
	public function test_request_multiple( Request $request, array $mock_response_data, $expected ) {
		$http = new HTTP();

		if ( $expected instanceof Request_Exception ) {
			$this->expectException( Multiple_Requests_Exception::class );
			$this->expectExceptionMessage( 'All requests failed.' );
		} elseif ( ! $expected instanceof Response ) {
			throw new InvalidArgumentException( 'The expected value must either be of type Response or Request_Exception.' );
		}

		add_filter(
			'pre_http_request',
			static function ( $response ) use ( $mock_response_data ) {
				if ( isset( $mock_response_data['error'] ) ) {
					return new WP_Error( $mock_response_data['error']['code'], $mock_response_data['error']['message'] );
				}

				if ( isset( $mock_response_data['response'] ) ) {
					if ( ! is_array( $mock_response_data['response'] ) ) {
						$mock_response_data['response'] = array( 'code' => (int) $mock_response_data['response'] );
					}
					if ( ! isset( $mock_response_data['response']['message'] ) ) {
						$mock_response_data['response']['message'] = get_status_header_desc( $mock_response_data['response']['code'] );
					}
				}

				return array_merge(
					array(
						'headers'  => array(),
						'response' => array(
							'code'    => 200,
							'message' => 'OK',
						),
						'body'     => '',
						'cookies'  => array(),
						'filename' => null,
					),
					$mock_response_data
				);
			}
		);

		$responses = $http->request_multiple( array( 'my-request' => $request ) );
		$this->assertCount( 1, $responses );
		$this->assertArrayHasKey( 'my-request', $responses );
		$this->assertSame( $expected->get_status(), $responses['my-request']->get_status(), 'Unexpected response status.' );
		$this->assertSame( $expected->get_data(), $responses['my-request']->get_data(), 'Unexpected response data.' );
		$this->assertSame( $expected->get_body(), $responses['my-request']->get_body(), 'Unexpected response body.' );
		$this->assertSame( $expected->get_headers(), $responses['my-request']->get_headers(), 'Unexpected response headers.' );
	}

	public function data_request(): array {
		return array(
			'regular GET'   => array(
				new Get_Request( 'https://example.com/' ),
				array(
					'headers'  => array(
						'content-type' => 'text/html',
					),
					'response' => 200,
					'body'     => '<html><head><title>Example</title></head><body><p>Hello, world!</p></body></html>',
				),
				new Generic_Response(
					200,
					'<html><head><title>Example</title></head><body><p>Hello, world!</p></body></html>',
					array(
						'content-type' => 'text/html',
					)
				),
			),
			'regular POST'  => array(
				new Post_Request( 'https://example.com/', array( 'enable' => '1' ) ),
				array(
					'headers'  => array(
						'content-type' => 'text/html',
					),
					'response' => 200,
					'body'     => '<html><head><title>Success!</title></head><body><p>Success!</p></body></html>',
				),
				new Generic_Response(
					200,
					'<html><head><title>Success!</title></head><body><p>Success!</p></body></html>',
					array(
						'content-type' => 'text/html',
					)
				),
			),
			'JSON GET'      => array(
				new Get_Request( 'https://example.com/api/items' ),
				array(
					'headers'  => array(
						'content-type' => 'application/json',
					),
					'response' => 200,
					'body'     => '[{"id":1,"slug":"item-1"},{"id":2,"slug":"item-2"}]',
				),
				new JSON_Response(
					200,
					'[{"id":1,"slug":"item-1"},{"id":2,"slug":"item-2"}]',
					array(
						'content-type' => 'application/json',
					)
				),
			),
			'JSON POST'     => array(
				new JSON_Post_Request( 'https://example.com/api/items', array( 'slug' => 'new-item' ) ),
				array(
					'headers'  => array(
						'content-type' => 'application/json',
					),
					'response' => 200,
					'body'     => '{"id":3,"slug":"new-item"}',
				),
				new JSON_Response(
					200,
					'{"id":3,"slug":"new-item"}',
					array(
						'content-type' => 'application/json',
					)
				),
			),
			'error status'  => array(
				new Get_Request( 'https://example.com/api/items' ),
				array(
					'headers'  => array(
						'content-type' => 'application/json',
					),
					'response' => 403,
					'body'     => '{"error":"Access denied"}',
				),
				new JSON_Response(
					403,
					'{"error":"Access denied"}',
					array(
						'content-type' => 'application/json',
					)
				),
			),
			'request error' => array(
				new Get_Request( 'https://example.com/api/items' ),
				array(
					'error' => array(
						'code'    => 'request_failed',
						'message' => 'Request failed. Are you offline?',
					),
				),
				new Request_Exception( 'Request failed. Are you offline?' ),
			),
		);
	}
}
