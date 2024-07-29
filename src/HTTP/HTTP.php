<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\HTTP
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\HTTP;

use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Request;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Contracts\Response;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Multiple_Requests_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Exception\Request_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\Traits\Sanitize_Headers;
use InvalidArgumentException;
use WP_HTTP_Proxy;
use WpOrg\Requests\Proxy\Http as Requests_HTTP_Proxy;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

/**
 * Class for sending HTTP requests and processing responses.
 *
 * @since n.e.x.t
 */
class HTTP {
	use Sanitize_Headers;

	/**
	 * Default options to use for all requests.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $default_options;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $default_options Optional. Default options to use for all requests. Default empty
	 *                                              array.
	 */
	public function __construct( array $default_options = array() ) {
		// Remove potentially conflicting entries that are not actually options.
		unset( $default_options['method'], $default_options['headers'], $default_options['body'] );

		$this->default_options = $default_options;
	}

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
	public function request( Request $request ): Response {
		$headers = $request->get_headers();
		$data    = $request->get_data();
		if ( ! $data ) {
			$data = $request->get_body();
		}

		$args           = wp_parse_args( $request->get_options(), $this->default_options );
		$args['method'] = $request->get_method();
		if ( $headers ) {
			$args['headers'] = $headers;
		}
		if ( $data ) {
			$args['body'] = $data;
		}

		$response = wp_remote_request( $request->get_url(), $args );
		if ( is_wp_error( $response ) ) {
			throw new Request_Exception( esc_html( $response->get_error_message() ) );
		}

		$status  = (int) wp_remote_retrieve_response_code( $response );
		$body    = wp_remote_retrieve_body( $response );
		$headers = wp_remote_retrieve_headers( $response );
		if ( $headers instanceof CaseInsensitiveDictionary ) {
			$headers = $headers->getAll();
		}
		$headers = $this->sanitize_headers( $headers );

		return $this->create_response( $status, $body, $headers );
	}

	/**
	 * Sends multiple HTTP requests and returns the responses.
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
	public function request_multiple( array $requests ): array {
		// Ensure all values are Request objects.
		foreach ( $requests as $request ) {
			if ( ! $request instanceof Request ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid request provided.', 'wp-oop-plugin-lib' )
				);
			}
		}

		// Prepare arguments for the Requests::request_multiple() method.
		$requests_args = array();
		foreach ( $requests as $key => $request ) {
			$headers = $request->get_headers();
			$data    = $request->get_data();
			if ( ! $data ) {
				$data = $request->get_body();
			}

			$request_args = array(
				'url'     => $request->get_url(),
				'type'    => $request->get_method(),
				'options' => wp_parse_args( $request->get_options(), $this->default_options ),
			);
			if ( $headers ) {
				$request_args['headers'] = $headers;
			}
			if ( $data ) {
				$request_args['data'] = $data;
			}

			// Include the defaults from WP_Http::request(), since the Requests library does not include them.
			$request_args['options'] = $this->prepare_options(
				$request_args['options'],
				$request_args['url'],
				$request_args['type']
			);

			$requests_args[ $key ] = $request_args;
		}

		// Similar to WP_Http::request(), avoid issues where mbstring.func_overload is enabled.
		mbstring_binary_safe_encoding();

		$responses = Requests::request_multiple( $requests_args );

		// See above.
		reset_mbstring_encoding();

		$successful = array();
		$failed     = array();
		foreach ( $responses as $key => $response ) {
			if ( $response instanceof \WpOrg\Requests\Exception ) {
				$failed[ $key ] = new Request_Exception( $response->getMessage() );
				continue;
			}

			$status  = (int) $response->status_code;
			$body    = $response->body;
			$headers = $this->sanitize_headers( $response->headers->getAll() );

			$successful[ $key ] = $this->create_response( $status, $body, $headers );
		}

		/*
		 * If any requests failed, throw a bulk exception.
		 * The successful responses will be included in the exception, so they can still be used if needed.
		 */
		if ( $failed ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new Multiple_Requests_Exception( $failed, $successful );
		}

		// If this is reached, all requests succeeded.
		return $successful;
	}

	/**
	 * Creates a response object based on the response data.
	 *
	 * @see https://www.rfc-editor.org/rfc/rfc1341.html#page-7
	 *
	 * @since n.e.x.t
	 *
	 * @param int                   $status  The HTTP status code received with the response.
	 * @param string                $body    The body received with the response.
	 * @param array<string, string> $headers The headers received with the response.
	 * @return Response The response object.
	 */
	private function create_response( int $status, string $body, array $headers ): Response {
		if (
			isset( $headers['content-type'] )
				&& in_array( 'application/json', array_map( 'trim', explode( ';', $headers['content-type'] ) ) )
		) {
			return new JSON_Response( $status, $body, $headers );
		}

		return new Generic_Response( $status, $body, $headers );
	}

	/**
	 * Prepares the options for a request directly via the Requests library, including WordPress defaults.
	 *
	 * WordPress's API only allows making a single request at a time, while the Requests library allows making multiple
	 * requests. However, the Requests library does not include the WordPress defaults for requests, such as the default
	 * timeout. This method prepares the options for a request to include these defaults.
	 *
	 * Most of the code in this method is similar to code in WP_Http::request() in WordPress core.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $options The options to prepare.
	 * @param string               $url     The request URL, only relevant as context for various filters.
	 * @param string               $method  The request method, relevant to determine some defaults.
	 * @return array<string, mixed> The prepared options, including WordPress defaults.
	 */
	private function prepare_options( array $options, string $url, string $method ): array {
		$wp_user_agent = 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' );

		$defaults = array(
			'timeout'             => apply_filters( 'http_request_timeout', 5, $url ),
			'redirection'         => apply_filters( 'http_request_redirection_count', 5, $url ),
			'user-agent'          => apply_filters( 'http_headers_useragent', $wp_user_agent, $url ),
			'sslverify'           => true,
			'sslcertificates'     => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
			'stream'              => false,
			'filename'            => null,
			'limit_response_size' => null,
		);

		if ( 'HEAD' === $method ) {
			$defaults['redirection'] = 0;
		}

		if ( isset( $options['stream'] ) && $options['stream'] ) {
			$defaults['filename'] = get_temp_dir() . basename( $url );
		}

		$options = wp_parse_args( $options, $defaults );

		// Migrate WordPress options to Requests options.
		$options = $this->migrate_wp_options_to_requests_options( $options );

		// Enforce additional behavior similar to WordPress core.
		if ( $options['filename'] ) {
			$options['blocking'] = true;
		}
		if ( 'HEAD' !== $method && 'GET' !== $method ) {
			$options['data_format'] = 'body';
		}
		$options['verify'] = apply_filters( 'https_ssl_verify', $options['verify'], $url );

		// Add proxy settings if necessary, similar to WordPress core.
		$proxy = new WP_HTTP_Proxy();
		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
			$options['proxy'] = new Requests_HTTP_Proxy( $proxy->host() . ':' . $proxy->port() );

			if ( $proxy->use_authentication() ) {
				$options['proxy']->use_authentication = true;
				$options['proxy']->user               = $proxy->username();
				$options['proxy']->pass               = $proxy->password();
			}
		}

		return $options;
	}

	/**
	 * Migrates WordPress options to Requests options.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $options The options to migrate.
	 * @return array<string, mixed> The migrated options.
	 */
	private function migrate_wp_options_to_requests_options( array $options ): array {
		if ( isset( $options['limit_response_size'] ) ) {
			$options['max_bytes'] = $options['limit_response_size'];
		}
		if ( ! $options['redirection'] ) {
			$options['follow_redirects'] = false;
		} else {
			$options['redirects'] = $options['redirection'];
		}
		if ( ! $options['sslverify'] ) {
			$options['verify']     = false;
			$options['verifyname'] = false;
		} else {
			$options['verify'] = $options['sslcertificates'];
		}
		$options['useragent'] = $options['user-agent'];
		unset(
			$options['limit_response_size'],
			$options['redirection'],
			$options['sslverify'],
			$options['sslcertificates'],
			$options['stream'], // This is irrelevant as the 'filename' presence alone handles it.
			$options['user-agent']
		);

		return $options;
	}
}
