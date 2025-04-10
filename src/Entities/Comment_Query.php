<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Query
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use WP_Comment;
use WP_Comment_Query;

/**
 * Class for a query for WordPress comments.
 *
 * @since 0.1.0
 */
class Comment_Query implements Entity_Query {

	/**
	 * The underlying WordPress comment query object.
	 *
	 * @since 0.1.0
	 * @var WP_Comment_Query
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
	 * @param array<string, mixed> $query_args Query arguments. See {@see WP_Comment_Query::__construct()} for a list
	 *                                         of supported arguments.
	 */
	public function __construct( array $query_args ) {
		$this->wp_obj     = new WP_Comment_Query();
		$this->query_args = $query_args;
	}

	/**
	 * Runs the query to get comments.
	 *
	 * @since 0.1.0
	 * f
	 * @return Comment[] List of comment entities.
	 */
	public function get_entities(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'all';

		return array_map(
			array( $this, 'wrap_comment' ),
			$this->wp_obj->query( $query_args )
		);
	}

	/**
	 * Runs the query to get comment IDs.
	 *
	 * @since 0.1.0
	 *
	 * @return int[] List of comment IDs.
	 */
	public function get_ids(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'ids';

		return $this->wp_obj->query( $query_args );
	}

	/**
	 * Runs the query to get the comment count.
	 *
	 * @since 0.1.0
	 *
	 * @return int Comment count.
	 */
	public function get_count(): int {
		$query_args                              = $this->query_args;
		$query_args['count']                     = true;
		$query_args['update_comment_meta_cache'] = false;
		$query_args['number']                    = 0;

		return (int) $this->wp_obj->query( $query_args );
	}

	/**
	 * Wraps a WordPress comment object into a corresponding entity instance.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Comment $comment WordPress comment object.
	 * @return Comment Comment entity.
	 */
	private function wrap_comment( WP_Comment $comment ): Comment {
		return new Comment( $comment );
	}
}
