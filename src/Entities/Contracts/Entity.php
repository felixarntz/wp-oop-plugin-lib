<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts;

/**
 * Interface for an entity.
 *
 * @since 0.1.0
 */
interface Entity {

	/**
	 * Gets the entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @return int|null The entity ID.
	 */
	public function get_id(): ?int;

	/**
	 * Checks whether the entity is publicly accessible.
	 *
	 * @since 0.1.0
	 *
	 * @return bool|null True if the entity is public, false otherwise.
	 */
	public function is_public(): ?bool;

	/**
	 * Gets the entity's primary URL.
	 *
	 * @since 0.1.0
	 *
	 * @return string|null Primary entity URL, or empty string if none.
	 */
	public function get_url(): ?string;

	/**
	 * Gets the entity's edit URL, if the current user is able to edit it.
	 *
	 * @since 0.1.0
	 *
	 * @return string|null URL to edit the entity, or empty string if unable to edit.
	 */
	public function get_edit_url(): ?string;

	/**
	 * Gets the value for the given field of the entity.
	 *
	 * @since 0.1.0
	 *
	 * @param string $field The field identifier.
	 * @return mixed Value for the field, `null` if not set.
	 */
	public function get_field_value( string $field );

	/**
	 * Serializes entity to insert it data in save/update functions.
	 *
	 * @return string[]
	 */
	public function serialize(): array;
}
