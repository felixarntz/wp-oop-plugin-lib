<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Query
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use WP_Term_Query;
use WP_Term;

/**
 * Class for a query for WordPress terms.
 *
 * @since n.e.x.t
 */
class Term_Query implements Entity_Query {

	/**
	 * The underlying WordPress term query object.
	 *
	 * @since n.e.x.t
	 * @var WP_Term_Query
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
	 * @param array<string, mixed> $query_args Query arguments. See {@see WP_Term_Query::__construct()} for a list of
	 *                                         supported arguments.
	 */
	public function __construct( array $query_args ) {
		$this->wp_obj     = new WP_Term_Query();
		$this->query_args = $query_args;
	}

	/**
	 * Runs the query to get terms.
	 *
	 * @since n.e.x.t
	 * f
	 * @return Term[] List of term entities.
	 */
	public function get_entities(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'all';

		return array_map(
			array( $this, 'wrap_term' ),
			$this->wp_obj->query( $query_args )
		);
	}

	/**
	 * Runs the query to get term IDs.
	 *
	 * @since n.e.x.t
	 *
	 * @return int[] List of term IDs.
	 */
	public function get_ids(): array {
		$query_args           = $this->query_args;
		$query_args['fields'] = 'ids';

		return $this->wp_obj->query( $query_args );
	}

	/**
	 * Runs the query to get the term count.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Term count.
	 */
	public function get_count(): int {
		$query_args                           = $this->query_args;
		$query_args['count']                  = true;
		$query_args['update_term_meta_cache'] = false;
		$query_args['number']                 = 0;

		return (int) $this->wp_obj->query( $query_args );
	}

	/**
	 * Wraps a WordPress term object into a corresponding entity instance.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_Term $term WordPress term object.
	 * @return Term Term entity.
	 */
	private function wrap_term( WP_Term $term ): Term {
		return new Term( $term );
	}
}
