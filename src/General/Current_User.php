<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Current_User
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Capabilities;
use WP_User;

/**
 * Class representing the current user.
 *
 * @since n.e.x.t
 */
class Current_User implements With_Capabilities {

	/**
	 * Gets the current user ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The current user ID, or `0` if no user is signed in.
	 */
	public function get_id(): int {
		return get_current_user_id();
	}

	/**
	 * Gets the current user entity.
	 *
	 * @since n.e.x.t
	 *
	 * @return WP_User The current user object. Will be a stub if no user is signed in.
	 */
	public function get(): WP_User {
		return wp_get_current_user();
	}

	/**
	 * Sets the current user to the one with the given ID.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id User ID.
	 */
	public function set( int $id ): void {
		wp_set_current_user( $id );
	}

	/**
	 * Checks whether current user is logged in.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the user is logged in, false otherwise.
	 */
	public function is_logged_in(): bool {
		return is_user_logged_in();
	}

	/**
	 * Checks whether the current user has the given capability.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $cap     Capability name.
	 * @param mixed  ...$args Optional further parameters, typically starting with an entity ID.
	 * @return bool True if the user has the given capability false otherwise.
	 */
	public function has_cap( string $cap, ...$args ): bool {
		return wp_get_current_user()->has_cap( $cap, ...$args );
	}

	/**
	 * Creates a cryptographic token tied to the given action and the current user session.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $action Action to add context to the nonce.
	 * @return string The token.
	 */
	public function create_nonce( string $action ): string {
		return wp_create_nonce( $action );
	}

	/**
	 * Verifies that the given security nonce is correct for the given action and the current user session.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $nonce  Nonce value to verify.
	 * @param string $action Action context for the nonce.
	 * @return bool True if the nonce is valid, false otherwise.
	 */
	public function verify_nonce( string $nonce, string $action ): bool {
		return wp_verify_nonce( $nonce, $action );
	}
}
