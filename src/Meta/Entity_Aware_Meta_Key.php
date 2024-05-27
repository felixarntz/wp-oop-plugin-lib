<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Key
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\Key_Value\Contracts\Key_Value;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\With_Entity_ID;

/**
 * Wrapper representing a WordPress meta key scoped to a specific entity.
 *
 * @since n.e.x.t
 */
class Entity_Aware_Meta_Key implements With_Entity_ID, Key_Value {

	/**
	 * Underlying, general entity aware instance.
	 *
	 * @since n.e.x.t
	 * @var Meta_Key
	 */
	private $wrapped_meta;

	/**
	 * ID of the entity to scope this instance to.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $entity_id;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Meta_Key $wrapped_meta Underlying entity aware instance that this scoped instance
	 *                               should inherit from.
	 * @param int      $entity_id    ID of the entity to scope this instance to.
	 */
	public function __construct( Meta_Key $wrapped_meta, int $entity_id ) {
		$this->wrapped_meta = $wrapped_meta;
		$this->entity_id    = $entity_id;
	}

	/**
	 * Checks whether the item has a value set.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value(): bool {
		return $this->wrapped_meta->has_value( $this->entity_id );
	}

	/**
	 * Gets the value for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return mixed Value for the item.
	 */
	public function get_value() {
		return $this->wrapped_meta->get_value( $this->entity_id );
	}

	/**
	 * Updates the value for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $value New value to set for the item.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( $value ): bool {
		return $this->wrapped_meta->update_value( $this->entity_id, $value );
	}

	/**
	 * Deletes the data for the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_value(): bool {
		return $this->wrapped_meta->delete_value( $this->entity_id );
	}

	/**
	 * Gets the key of the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Option key.
	 */
	public function get_key(): string {
		return $this->wrapped_meta->get_key();
	}

	/**
	 * Gets the entity ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The entity ID.
	 */
	public function get_entity_id(): int {
		return $this->entity_id;
	}
}
