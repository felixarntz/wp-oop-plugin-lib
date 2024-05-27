<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\User
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\With_Capabilities;
use WP_User;

/**
 * Class representing a WordPress user.
 *
 * @since n.e.x.t
 */
class User implements Entity, With_Capabilities {

	/**
	 * The underlying WordPress user object.
	 *
	 * @since n.e.x.t
	 * @var WP_User
	 */
	private $wp_obj;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_User $user The underlying WordPress user object.
	 */
	public function __construct( WP_User $user ) {
		$this->wp_obj = $user;
	}

	/**
	 * Gets the user ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The user ID.
	 */
	public function get_id(): int {
		return (int) $this->wp_obj->ID;
	}

	/**
	 * Checks whether the user is publicly accessible.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the user is public, false otherwise.
	 */
	public function is_public(): bool {
		return true;
	}

	/**
	 * Gets the user's primary URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string URL to the user's posts, or empty string if none.
	 */
	public function get_url(): string {
		return (string) get_author_posts_url( $this->wp_obj->ID, $this->wp_obj->user_nicename );
	}

	/**
	 * Gets the user's edit URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string URL to edit the user, or empty string if none.
	 */
	public function get_edit_url(): string {
		return (string) get_edit_user_link( $this->wp_obj->ID );
	}

	/**
	 * Gets the value for the given field of the user.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $field The field identifier.
	 * @return mixed Value for the field, `null` if not set.
	 */
	public function get_field_value( string $field ) {
		if ( isset( $this->wp_obj->$field ) ) {
			return $this->wp_obj->$field;
		}

		return null;
	}

	/**
	 * Checks whether the user has the given capability.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $cap     Capability name.
	 * @param mixed  ...$args Optional further parameters, typically starting with an entity ID.
	 * @return bool True if the user has the given capability false otherwise.
	 */
	public function has_cap( string $cap, ...$args ): bool {
		return $this->wp_obj->has_cap( $cap, ...$args );
	}
}
