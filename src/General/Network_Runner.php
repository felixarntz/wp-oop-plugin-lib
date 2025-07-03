<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Network_Runner
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

/**
 * Class for running logic across the network.
 *
 * @since n.e.x.t
 */
class Network_Runner {

	/**
	 * Network environment.
	 *
	 * @since n.e.x.t
	 * @var Network_Env
	 */
	private $network_env;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Network_Env $network_env Network environment.
	 */
	public function __construct( Network_Env $network_env ) {
		$this->network_env = $network_env;
	}

	/**
	 * Runs a given callback for all sites in the network or for sites matching certain criteria.
	 *
	 * If not in a Multisite environment, the callback will simply be invoked once for the current site.
	 *
	 * @since n.e.x.t
	 *
	 * @param callable             $callback        Callback function to run for each site. It must be parameter-less
	 *                                              and return a boolean for whether it completed successfully or not.
	 * @param array<string, mixed> $site_query_args Optional. Additional arguments for querying sites.
	 * @return bool True if the callback successfully ran for all relevant sites, false otherwise.
	 */
	public function run_for_sites( callable $callback, array $site_query_args = array() ): bool {
		if ( ! $this->network_env->is_multisite() ) {
			return (bool) call_user_func( $callback );
		}

		$site_query_args = wp_parse_args(
			$site_query_args,
			array( 'number' => 100 )
		);

		$site_query_args['fields'] = 'ids';

		$site_ids = get_sites( $site_query_args );

		// Iterate through the site and store for which ones the callback ran successfully.
		$success_ids = array();
		foreach ( $site_ids as $site_id ) {
			switch_to_blog( $site_id );
			if ( call_user_func( $callback ) ) {
				$success_ids[] = $site_id;
			}
			restore_current_blog();
		}

		return count( $site_ids ) === count( $success_ids );
	}
}
