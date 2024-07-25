<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Multiple_Requests_Exception
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Response;

/**
 * Exception class for when one or more HTTP requests of multiple requests sent together fail.
 *
 * @since n.e.x.t
 */
class Multiple_Requests_Exception extends Request_Exception {

	/**
	 * The exceptions for the requests that failed.
	 *
	 * @since n.e.x.t
	 * @var array<string|int, Request_Exception>
	 */
	private $request_exceptions;

	/**
	 * The responses for the requests that succeeded.
	 *
	 * @since n.e.x.t
	 * @var array<string|int, Response>
	 */
	private $successful_responses;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string|int, Request_Exception> $request_exceptions   The exceptions for the requests that failed.
	 * @param array<string|int, Response>          $successful_responses Optional. The responses for the requests that
	 *                                                                   succeeded. Default empty array.
	 */
	public function __construct( array $request_exceptions, array $successful_responses = array() ) {
		if ( ! $successful_responses ) {
			$message = __( 'All requests failed.', 'wp-oop-plugin-lib' );
		} elseif ( count( $request_exceptions ) === 1 ) {
			$message = sprintf(
				/* translators: %d: overall number of requests */
				__( 'One out of %d requests failed.', 'wp-oop-plugin-lib' ),
				count( $request_exceptions ) + count( $successful_responses )
			);
		} else {
			$message = sprintf(
				/* translators: 1: number of failed requests, 2: overall number of requests */
				__( '%1$d out of %2$d requests failed.', 'wp-oop-plugin-lib' ),
				count( $request_exceptions ),
				count( $request_exceptions ) + count( $successful_responses )
			);
		}

		parent::__construct( esc_html( $message ) );
		$this->request_exceptions   = $request_exceptions;
		$this->successful_responses = $successful_responses;
	}

	/**
	 * Checks whether any of the requests succeeded.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if any of the requests succeeded, false otherwise.
	 */
	public function has_successful_responses(): bool {
		return ! empty( $this->successful_responses );
	}

	/**
	 * Checks whether a specific request failed.
	 *
	 * @since n.e.x.t
	 *
	 * @param string|int $key The key of the request.
	 * @return bool True if the request failed, false otherwise.
	 */
	public function has_failed( $key ): bool {
		return isset( $this->request_exceptions[ $key ] );
	}

	/**
	 * Checks whether a specific request succeeded.
	 *
	 * @since n.e.x.t
	 *
	 * @param string|int $key The key of the request.
	 * @return bool True if the request succeeded, false otherwise.
	 */
	public function has_succeeded( $key ): bool {
		return isset( $this->successful_responses[ $key ] );
	}

	/**
	 * Retrieves the exception for a specific request that failed.
	 *
	 * Before calling this method, you should check whether the request failed using the has_failed() method.
	 *
	 * @since n.e.x.t
	 *
	 * @param string|int $key The key of the request.
	 * @return Request_Exception The exception for the request that failed.
	 */
	public function get_exception( $key ): Request_Exception {
		return $this->request_exceptions[ $key ];
	}

	/**
	 * Retrieves the response for a specific request that succeeded.
	 *
	 * Before calling this method, you should check whether the request succeeded using the has_succeeded() method.
	 *
	 * @since n.e.x.t
	 *
	 * @param string|int $key The key of the request.
	 * @return Response The response for the request that succeeded.
	 */
	public function get_response( $key ): Response {
		return $this->successful_responses[ $key ];
	}

	/**
	 * Retrieves the exceptions for the requests that failed.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string|int, Request_Exception> The exceptions for the requests that failed.
	 */
	public function get_individual_exceptions(): array {
		return $this->request_exceptions;
	}

	/**
	 * Retrieves the responses for the requests that succeeded.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string|int, Response> The responses for the requests that succeeded.
	 */
	public function get_successful_responses(): array {
		return $this->successful_responses;
	}
}
