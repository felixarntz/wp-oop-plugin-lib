<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Cache_Aware_Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Trash_Aware_Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Invalid_Entity_Data_Exception;

/**
 * Class for a repository of WordPress comments.
 *
 * @since n.e.x.t
 */
class Comment_Repository implements Entity_Repository, Cache_Aware_Entity_Repository, Trash_Aware_Entity_Repository {

	/**
	 * Checks whether a comment for the given ID exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Comment ID.
	 * @return bool True if the comment exists, false otherwise.
	 */
	public function exists( int $id ): bool {
		return get_comment( $id ) !== null;
	}

	/**
	 * Gets the comment for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Comment ID.
	 * @return Comment|null The comment, or `null` if no value exists.
	 */
	public function get( int $id ) {
		$comment = get_comment( $id );
		if ( ! $comment ) {
			return null;
		}
		return new Comment( $comment );
	}

	/**
	 * Updates the comment for a given ID in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int                  $id   Comment ID.
	 * @param array<string, mixed> $data New data to set for the comment. See {@see wp_update_comment()} for a list of
	 *                                   supported arguments.
	 * @return bool True on success, false on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when updating the comment fails and `WP_DEBUG` is enabled.
	 */
	public function update( int $id, array $data ): bool {
		$data['comment_ID'] = $id;

		$result = wp_update_comment( $data, true );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return false;
		}

		return true;
	}

	/**
	 * Adds a new comment to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $data Initial data to set for the comment. See {@see wp_insert_comment()} for a list
	 *                                   of supported arguments.
	 * @return int The comment ID, or `0` on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when adding the comment fails and `WP_DEBUG` is enabled.
	 */
	public function add( array $data ): int {
		return (int) wp_insert_comment( $data );
	}

	/**
	 * Deletes the comment for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Comment ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $id ): bool {
		return (bool) wp_delete_comment( $id, true );
	}

	/**
	 * Returns a comment query object for the given arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @return Comment_Query Query object.
	 */
	public function query( array $query_args ): Entity_Query {
		return new Comment_Query( $query_args );
	}

	/**
	 * Updates the entity caches for the given comment IDs that do not already exist in cache.
	 *
	 * Does not update any meta caches.
	 *
	 * @since n.e.x.t
	 *
	 * @param int[] $ids Comment IDs.
	 * @return bool True on success, or false on failure.
	 */
	public function prime_caches( array $ids ): bool {
		_prime_comment_caches( $ids, false );
		return true;
	}

	/**
	 * Moves the comment for a given ID to the trash.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Comment ID.
	 * @return bool True on success, false on failure.
	 */
	public function trash( int $id ): bool {
		return (bool) wp_trash_comment( $id );
	}

	/**
	 * Moves the comment for a given ID out of the trash.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Comment ID.
	 * @return bool True on success, false on failure.
	 */
	public function untrash( int $id ): bool {
		return (bool) wp_untrash_comment( $id );
	}

	/**
	 * Checks whether the comment for a given ID is in the trash.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Comment ID.
	 * @return bool True if the comment is in the trash, false otherwise.
	 */
	public function is_trashed( int $id ): bool {
		return wp_get_comment_status( $id ) === 'trash';
	}
}
