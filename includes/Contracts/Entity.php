<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Contracts;

/**
 * Interface for an entity.
 *
 * @since n.e.x.t
 */
interface Entity {

	/**
	 * Gets the entity ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The entity ID.
	 */
	public function get_id(): int;

	/**
	 * Checks whether the entity is publicly accessible.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the entity is public, false otherwise.
	 */
	public function is_public(): bool;

	/**
	 * Gets the entity's primary URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Primary entity URL, or empty string if none.
	 */
	public function get_url(): string;

	/**
	 * Gets the entity's edit URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string URL to edit the entity, or empty string if none.
	 */
	public function get_edit_url(): string;

	/**
	 * Gets the value for the given field of the entity.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $field The field identifier.
	 * @return mixed Value for the field, `null` if not set.
	 */
	public function get_field_value( string $field );
}
