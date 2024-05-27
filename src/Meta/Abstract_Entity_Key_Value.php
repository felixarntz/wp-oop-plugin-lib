<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Abstract_Entity_Key_Value
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\Entity_Key_Value;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\Entity_Key_Value_Repository;

/**
 * Base class representing an key-value pair that is connected to an entity.
 *
 * Should typically not be used directly, but rather through a more specific class extending it.
 *
 * @since n.e.x.t
 */
class Abstract_Entity_Key_Value implements Entity_Key_Value {

	/**
	 * Repository used for the item.
	 *
	 * @since n.e.x.t
	 * @var Entity_Key_Value_Repository
	 */
	protected $repository;

	/**
	 * Item key.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	protected $key;

	/**
	 * Item default value.
	 *
	 * @since n.e.x.t
	 * @var mixed
	 */
	protected $default_value;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Entity_Key_Value_Repository $repository    Repository used for the item.
	 * @param string                      $key           Item key.
	 * @param mixed                       $default_value Optional. Default value for the item if not set in the
	 *                                                   repository. If null, it will be ignored. Default null.
	 */
	public function __construct( Entity_Key_Value_Repository $repository, string $key, $default_value = null ) {
		$this->repository    = $repository;
		$this->key           = $key;
		$this->default_value = $default_value;
	}

	/**
	 * Checks whether the item has a value set in the given entity.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $entity_id Entity ID.
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value( int $entity_id ): bool {
		return $this->repository->exists( $entity_id, $this->key );
	}

	/**
	 * Gets the value for the item in the given entity.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $entity_id Entity ID.
	 * @return mixed Value for the item.
	 */
	public function get_value( int $entity_id ) {
		// Pass default value if set.
		if ( isset( $this->default_value ) ) {
			return $this->repository->get( $entity_id, $this->key, $this->default_value );
		}

		return $this->repository->get( $entity_id, $this->key );
	}

	/**
	 * Updates the value for the item in the given entity.
	 *
	 * @since n.e.x.t
	 *
	 * @param int   $entity_id Entity ID.
	 * @param mixed $value     New value to set for the item.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( int $entity_id, $value ): bool {
		return $this->repository->update( $entity_id, $this->key, $value );
	}

	/**
	 * Deletes the data for the item in the given entity.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $entity_id Entity ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete_value( int $entity_id ): bool {
		return $this->repository->delete( $entity_id, $this->key );
	}

	/**
	 * Gets the key of the item.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Item key.
	 */
	public function get_key(): string {
		return $this->key;
	}
}
