<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request_Handler
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Multiple_Requests_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Request_Exception;
use InvalidArgumentException;

/**
 * Interface for an HTTP request to another URL.
 *
 * @since n.e.x.t
 */
interface Request_Handler {

	/**
	 * Sends an HTTP request and returns the response.
	 *
	 * @since n.e.x.t
	 *
	 * @param Request $request The request to send.
	 * @return Response The response received.
	 *
	 * @throws Request_Exception Thrown if the request fails.
	 */
	public function request( Request $request ): Response;

	/**
	 * Sends multiple HTTP requests and returns the responses.
	 *
	 * The returned responses are in the same order / use the same keys as the requests.
	 *
	 * If any of the requests fail, a Multiple_Requests_Exception will be thrown. The exception will contain the
	 * responses of the requests that succeeded, and the exceptions of the requests that failed.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string|int, Request> $requests The requests to send.
	 * @return array<string|int, Response> The responses received.
	 *
	 * @throws Multiple_Requests_Exception Thrown if one or more requests fail. If any requests succeeded, their
	 *                                     responses will be included in the exception.
	 * @throws InvalidArgumentException    Thrown if an invalid request is provided.
	 */
	public function request_multiple( array $requests ): array;
}
