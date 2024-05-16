<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Query
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Query;
use WP_Query;

/**
 * Class for a query for WordPress posts.
 *
 * @since n.e.x.t
 */
class Post_Query implements Entity_Query {

	/**
	 * The underlying WordPress post query object.
	 *
	 * @since n.e.x.t
	 * @var WP_Query
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
	 * @param array<string, mixed> $query_args Query arguments. See {@see WP_Query::parse_query()} for a list of
	 *                                         supported arguments.
	 */
	public function __construct( array $query_args ) {
		$this->wp_obj     = new WP_Query();
		$this->query_args = $this->parse_defaults( $query_args );
	}

	/**
	 * Runs the query to get posts.
	 *
	 * @since n.e.x.t
	 *
	 * @return \WP_Post[] List of posts.
	 */
	public function get_entities(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = '';

		return $this->wp_obj->query( $query_args );
	}

	/**
	 * Runs the query to get post IDs.
	 *
	 * @since n.e.x.t
	 *
	 * @return int[] List of post IDs.
	 */
	public function get_ids(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'ids';

		return $this->wp_obj->query( $query_args );
	}

	/**
	 * Runs the query to get the post count.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Post count.
	 */
	public function get_count(): int {
		$query_args                           = $this->query_args;
		$query_args['fields']                 = 'ids';
		$query_args['update_post_meta_cache'] = false;
		$query_args['update_post_term_cache'] = false;
		$query_args['posts_per_page']         = 10;
		$query_args['no_found_rows']          = false;

		$this->wp_obj->query( $query_args );
		return (int) $this->wp_obj->found_posts;
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
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);
	}
}
