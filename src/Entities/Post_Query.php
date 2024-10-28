<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Query
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use WP_Post;
use WP_Query;

/**
 * Class for a query for WordPress posts.
 *
 * @since 0.1.0
 */
class Post_Query implements Entity_Query {

	/**
	 * The underlying WordPress post query object.
	 *
	 * @since 0.1.0
	 * @var WP_Query
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
	 * @since 0.1.0
	 *
	 * @return Post[] List of post entities.
	 */
	public function get_entities(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = '';

		return array_map(
			array( $this, 'wrap_post' ),
			$this->wp_obj->query( $query_args )
		);
	}

	/**
	 * Runs the query to get post IDs.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * @since 0.1.0
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

	/**
	 * Wraps a WordPress post object into a corresponding entity instance.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post WordPress post object.
	 * @return Post Post entity.
	 */
	private function wrap_post( WP_Post $post ): Post {
		return new Post( $post );
	}
}
