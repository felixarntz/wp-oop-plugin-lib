<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Query
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Query;
use WP_User_Query;
use WP_User;

/**
 * Class for a query for WordPress users.
 *
 * @since n.e.x.t
 */
class User_Query implements Entity_Query {

	/**
	 * The underlying WordPress user query object.
	 *
	 * @since n.e.x.t
	 * @var WP_User_Query
	 */
	private $wp_obj;

	/**
	 * Query arguments.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $query_args;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
	 * @since n.e.x.t
	 *
	 * @return int[] List of user IDs.
	 */
	public function get_ids(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'ids';

		$this->wp_obj->prepare_query( $query_args );
		$this->wp_obj->query();
		return $this->wp_obj->get_results();
	}

	/**
	 * Runs the query to get the user count.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
	 * @since n.e.x.t
	 *
	 * @param WP_User $user WordPress user object.
	 * @return User User entity.
	 */
	private function wrap_user( WP_User $user ): User {
		return new User( $user );
	}
}
