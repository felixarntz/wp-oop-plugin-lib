<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Invalid_Entity_Data_Exception;

/**
 * Class for a repository of WordPress users.
 *
 * @since n.e.x.t
 */
class User_Repository implements Entity_Repository {

	/**
	 * Checks whether a user for the given ID exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id User ID.
	 * @return bool True if the user exists, false otherwise.
	 */
	public function exists( int $id ): bool {
		return get_userdata( $id ) !== false;
	}

	/**
	 * Gets the user for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id User ID.
	 * @return User|null The user, or `null` if no value exists.
	 */
	public function get( int $id ) {
		$user = get_userdata( $id );
		if ( ! $user ) {
			return null;
		}
		return new User( $user );
	}

	/**
	 * Updates the user for a given ID in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int                  $id   User ID.
	 * @param array<string, mixed> $data New data to set for the user. See {@see wp_update_user()} for a list of
	 *                                   supported arguments.
	 * @return bool True on success, false on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when updating the user fails and `WP_DEBUG` is enabled.
	 */
	public function update( int $id, array $data ): bool {
		$data['ID'] = $id;

		$result = wp_update_user( $data );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return false;
		}

		return true;
	}

	/**
	 * Adds a new user to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $data Initial data to set for the user. See {@see wp_insert_user()} for a list of
	 *                                   supported arguments.
	 * @return int The user ID, or `0` on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when adding the user fails and `WP_DEBUG` is enabled.
	 */
	public function add( array $data ): int {
		$result = wp_insert_user( $data );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return 0;
		}

		return (int) $result;
	}

	/**
	 * Deletes the user for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id User ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $id ): bool {
		if ( is_multisite() ) {
			return (bool) wpmu_delete_user( $id );
		}
		return (bool) wp_delete_user( $id );
	}

	/**
	 * Returns a user query object for the given arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @return User_Query Query object.
	 */
	public function query( array $query_args ): Entity_Query {
		return new User_Query( $query_args );
	}
}
