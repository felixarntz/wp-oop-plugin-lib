<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Query
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use WP_User;
use WP_User_Query;

/**
 * Class for a query for WordPress users.
 *
 * @since 0.1.0
 */
class User_Query implements Entity_Query {

	/**
	 * The underlying WordPress user query object.
	 *
	 * @since 0.1.0
	 * @var WP_User_Query
	 */
	private $wp_obj;

	/**
	 * Query arguments.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $query_args;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $query_args Query arguments. See {@see WP_User_Query::prepare_query()} for a list of
	 *                                         supported arguments.
	 */
	public function __construct( array $query_args ) {
		$this->wp_obj     = new WP_User_Query();
		$this->query_args = $this->parse_defaults( $query_args );
	}

	/**
	 * Runs the query to get users.
	 *
	 * @since 0.1.0
	 *
	 * @return User[] List of user entities.
	 */
	public function get_entities(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'all';

		$this->wp_obj->prepare_query( $query_args );
		$this->wp_obj->query();
		return array_map(
			array( $this, 'wrap_user' ),
			$this->wp_obj->get_results()
		);
	}

	/**
	 * Runs the query to get user IDs.
	 *
	 * @since 0.1.0
	 *
	 * @return int[] List of user IDs.
	 */
	public function get_ids(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'id';

		$this->wp_obj->prepare_query( $query_args );
		$this->wp_obj->query();
		return array_map( 'absint', $this->wp_obj->get_results() );
	}

	/**
	 * Runs the query to get the user count.
	 *
	 * @since 0.1.0
	 *
	 * @return int User count.
	 */
	public function get_count(): int {
		$query_args                = $this->query_args;
		$query_args['fields']      = 'ids';
		$query_args['number']      = 10;
		$query_args['count_total'] = true;

		$this->wp_obj->prepare_query( $query_args );
		$this->wp_obj->query();
		return (int) $this->wp_obj->get_total();
	}

	/**
	 * Parses the given query arguments with better defaults.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @return array<string, mixed> Query arguments parsed with defaults.
	 */
	private function parse_defaults( array $query_args ) {
		return wp_parse_args(
			$query_args,
			array(
				'number'      => 100, // Better default than -1 (unlimited).
				'count_total' => false,
			)
		);
	}

	/**
	 * Wraps a WordPress user object into a corresponding entity instance.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_User $user WordPress user object.
	 * @return User User entity.
	 */
	private function wrap_user( WP_User $user ): User {
		return new User( $user );
	}
}
