<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Cache_Aware_Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Invalid_Entity_Data_Exception;
use WP_Term;

/**
 * Class for a repository of WordPress terms.
 *
 * @since n.e.x.t
 */
class Term_Repository implements Entity_Repository, Cache_Aware_Entity_Repository {

	/**
	 * Checks whether a term for the given ID exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Term ID.
	 * @return bool True if the term exists, false otherwise.
	 */
	public function exists( int $id ): bool {
		return (bool) term_exists( $id );
	}

	/**
	 * Gets the term for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Term ID.
	 * @return Term|null The term, or `null` if no value exists.
	 */
	public function get( int $id ) {
		$term = get_term( $id );
		if ( ! $term || is_wp_error( $term ) ) {
			return null;
		}
		return new Term( $term );
	}

	/**
	 * Updates the term for a given ID in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int                  $id   Term ID.
	 * @param array<string, mixed> $data New data to set for the term. See {@see wp_update_term()} for a list of
	 *                                   supported arguments.
	 * @return bool True on success, false on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when updating the term fails and `WP_DEBUG` is enabled.
	 */
	public function update( int $id, array $data ): bool {
		// Use original taxonomy if not explicitly provided.
		if ( ! isset( $data['taxonomy'] ) ) {
			$term = get_term( $id );
			if ( ! $term || is_wp_error( $term ) ) {
				if ( WP_DEBUG ) {
					throw new Invalid_Entity_Data_Exception( esc_html__( 'Missing taxonomy.', 'wp-oop-plugin-lib' ) );
				}
				return false;
			}
			$data['taxonomy'] = $term->taxonomy;
		}

		$taxonomy = $data['taxonomy'];
		unset( $data['taxonomy'] );

		$result = wp_update_term( $id, $taxonomy, $data );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return false;
		}

		return true;
	}

	/**
	 * Adds a new term to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $data Initial data to set for the term. Keys 'name' and 'taxonomy' are required. See
	 *                                   {@see wp_insert_term()} for a list of supported arguments.
	 * @return int The term ID, or `0` on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when adding the term fails and `WP_DEBUG` is enabled.
	 */
	public function add( array $data ): int {
		if ( ! isset( $data['name'] ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html__( 'Missing name.', 'wp-oop-plugin-lib' ) );
			}
			return 0;
		}

		if ( ! isset( $data['taxonomy'] ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html__( 'Missing taxonomy.', 'wp-oop-plugin-lib' ) );
			}
			return 0;
		}

		$name     = $data['name'];
		$taxonomy = $data['taxonomy'];
		unset( $data['name'], $data['taxonomy'] );

		$result = wp_insert_term( $name, $taxonomy, $data );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return 0;
		}

		return (int) $result['term_id'];
	}

	/**
	 * Deletes the term for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Term ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $id ): bool {
		$term = get_term( $id );
		if ( ! $term || is_wp_error( $term ) ) {
			return false;
		}

		$result = wp_delete_term( $id, $term->taxonomy );
		if ( is_wp_error( $result ) ) {
			return false;
		}
		return (bool) $result;
	}

	/**
	 * Returns a term query object for the given arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @return Term_Query Query object.
	 */
	public function query( array $query_args ): Entity_Query {
		return new Term_Query( $query_args );
	}

	/**
	 * Updates the entity caches for the given term IDs.
	 *
	 * @since n.e.x.t
	 *
	 * @param int[] $ids Term IDs.
	 * @return bool True on success, or false on failure.
	 */
	public function update_caches( array $ids ): bool {
		_prime_term_caches( $ids );
		return true;
	}
}
