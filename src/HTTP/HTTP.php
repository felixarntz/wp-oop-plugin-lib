<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\HTTP\HTTP
 *
 * @since 0.1.0
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
 * @since 0.1.0
 */
class HTTP {
	use Sanitize_Headers;

	/**
	 * Default options to use for all requests.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $default_options;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $default_options Optional. Default options to use for all requests. Default empty
	 *                                              array.
	 */
	public function __construct( array $default_options = array() ) {
		// Prior to WordPress 6.2, this class had a different name.
		if ( ! class_exists( CaseInsensitiveDictionary::class ) ) {
			class_alias( 'Requests_Utility_CaseInsensitiveDictionary', CaseInsensitiveDictionary::class );
		}

		// Remove potentially conflicting entries that are not actually options.
		unset( $default_options['method'], $default_options['headers'], $default_options['body'] );

		$this->default_options = $default_options;
	}

	/**
	 * Sends an HTTP request and returns the response.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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

		$requests_args = array();
		$responses     = array();
		foreach ( $requests as $key => $request ) {
			// Assemble the options with WordPress defaults included.
			$request_args = $this->build_request_args( $request );

			// Allow short-circuiting requests, just like in WP_Http::request().
			$pre_response = $this->run_wp_pre_http_request_filter( $request_args );
			if ( null !== $pre_response ) {
				$responses[ $key ] = $pre_response;
				continue;
			}

			// Prepare the options for usage with the Requests library.
			$request_args['options'] = $this->prepare_options_for_requests(
				$request_args['options'],
				$request_args['url'],
				$request_args['type']
			);

			$requests_args[ $key ] = $request_args;
		}

		// If all requests were handled by the response pre filter, we don't actually need to send any requests.
		if ( count( $requests_args ) === 0 ) {
			return $responses;
		}

		$successful = $responses; // Any pre-filter responses are by definition success responses.
		$failed     = array();

		// Similar to WP_Http::request(), avoid issues where mbstring.func_overload is enabled.
		mbstring_binary_safe_encoding();

		$responses = Requests::request_multiple( $requests_args );

		// See above.
		reset_mbstring_encoding();

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
	 * Assembles the request arguments for the given request, to pass to the Requests library.
	 *
	 * @since 0.1.0
	 *
	 * @param Request $request The request to send.
	 * @return array<string, mixed> Request arguments.
	 */
	private function build_request_args( Request $request ): array {
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
		$request_args['options'] = $this->merge_wp_default_options(
			$request_args['options'],
			$request_args['url'],
			$request_args['type']
		);
		return $request_args;
	}

	/**
	 * Runs the WordPress 'pre_http_request' filter to allow short-circuiting requests.
	 *
	 * When used in a multi request, the filter will be run for every request. For any request where it returns a value
	 * other than `false`, the request will not be actually sent and instead the data from the filter is used to create
	 * the response. If all requests within a multi request receive their response data in that way, no request is sent
	 * at all.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $request_args Request arguments.
	 * @return Response|null Response object based on the 'pre_http_request' filter data, or null if not filtered.
	 */
	private function run_wp_pre_http_request_filter( array $request_args ) {
		$parsed_args            = $request_args['options'];
		$parsed_args['method']  = $request_args['type'];
		$parsed_args['headers'] = $request_args['headers'] ?? array();
		$parsed_args['cookies'] = $request_args['options']['cookies'] ?? array();
		$parsed_args['body']    = $request_args['data'] ?? null;

		// Allow short-circuiting requests, just like in WP_Http::request().
		$pre = apply_filters( 'pre_http_request', false, $parsed_args, $request_args['url'] );
		if ( false !== $pre ) {
			return $this->create_response(
				$pre['response']['code'] ?? 200,
				$pre['body'] ?? '',
				$pre['headers'] ?? array()
			);
		}
		return null;
	}

	/**
	 * Creates a response object based on the response data.
	 *
	 * @see https://www.rfc-editor.org/rfc/rfc1341.html#page-7
	 *
	 * @since 0.1.0
	 *
	 * @param int                   $status  The HTTP status code received with the response.
	 * @param string                $body    The body received with the response.
	 * @param array<string, string> $headers The headers received with the response.
	 * @return Response The response object.
	 */
	private function create_response( int $status, string $body, array $headers ): Response {
		if (
			isset( $headers['content-type'] )
			&& in_array( 'application/json', array_map( 'trim', explode( ';', $headers['content-type'] ) ), true )
		) {
			return new JSON_Response( $status, $body, $headers );
		}

		return new Generic_Response( $status, $body, $headers );
	}

	/**
	 * Populates the given options array with defaults.
	 *
	 * WordPress's API only allows making a single request at a time, while the Requests library allows making multiple
	 * requests. However, the Requests library does not include the WordPress defaults for requests, such as the default
	 * timeout. This method ensures that they include these defaults.
	 *
	 * Most of the code in this method is similar to code in WP_Http::request() in WordPress core.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $options The options to prepare.
	 * @param string               $url     The request URL, only relevant as context for various filters.
	 * @param string               $method  The request method, relevant to determine some defaults.
	 * @return array<string, mixed> The prepared options, including WordPress defaults.
	 */
	private function merge_wp_default_options( array $options, string $url, string $method ): array {
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

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * Prepares the options for a request directly via the Requests library.
	 *
	 * WordPress's API only allows making a single request at a time, while the Requests library allows making multiple
	 * requests. However, the Requests library uses different argument names, so this method prepares the WordPress
	 * options for usage with the Requests library.
	 *
	 * Most of the code in this method is similar to code in WP_Http::request() in WordPress core.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $options The options to prepare.
	 * @param string               $url     The request URL, only relevant as context for various filters.
	 * @param string               $method  The request method, relevant to determine some defaults.
	 * @return array<string, mixed> The prepared options.
	 */
	private function prepare_options_for_requests( array $options, string $url, string $method ): array {
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
	 * @since 0.1.0
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
