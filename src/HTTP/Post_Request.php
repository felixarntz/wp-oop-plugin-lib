<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Post_Request
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;

/**
 * Class for a HTTP POST request to another URL.
 *
 * @since 0.1.0
 */
class Post_Request extends Generic_Request {

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $url  The URL to which the request should be sent.
	 * @param array<string, mixed> $data Optional. The data to be sent with the request. Default empty array.
	 * @param array<string, mixed> $args Optional. Additional options for the request. See {@see WP_Http::request()}
	 *                                   for possible options. Providing the 'body' key is only allowed if the data
	 *                                   parameter is empty, and only as a string. Default empty array.
	 */
	public function __construct( string $url, array $data = array(), array $args = array() ) {
		$args['method'] = Request::POST;
		parent::__construct( $url, $data, $args );
	}
}
