<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Entity_Aware_Meta_Container
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use ArrayAccess;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container_Readonly;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\With_Entity_ID;

/**
 * Class for a meta container scoped to a specific entity.
 *
 * @since 0.1.0
 */
class Entity_Aware_Meta_Container implements Container_Readonly, ArrayAccess, With_Entity_ID {

	/**
	 * The original meta container.
	 *
	 * @since 0.1.0
	 * @var Meta_Container
	 */
	private $wrapped_container;

	/**
	 * ID of the entity to scope this instance to.
	 *
	 * @since 0.1.0
	 * @var int
	 */
	private $entity_id;

	/**
	 * Item instances already created.
	 *
	 * @since 0.1.0
	 * @var array<string, Entity_Aware_Meta_Key>
	 */
	private $instances = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Meta_Container $wrapped_container Underlying entity aware instance that this scoped instance
	 *                                          should inherit from.
	 * @param int            $entity_id         ID of the entity to scope this instance to.
	 */
	public function __construct( Meta_Container $wrapped_container, int $entity_id ) {
		$this->wrapped_container = $wrapped_container;
		$this->entity_id         = $entity_id;
	}

	/**
	 * Checks if a meta key for the given key exists in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Meta key.
	 * @return bool True if the meta key exists in the container, false otherwise.
	 */
	public function has( string $key ): bool {
		return $this->wrapped_container->has( $key );
	}

	/**
	 * Gets the meta key for the given key from the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Meta key.
	 * @return Entity_Aware_Meta_Key Meta key for the given key.
	 *
	 * @throws Not_Found_Exception Thrown when meta key with given key is not found.
	 */
	public function get( string $key ) {
		if ( ! $this->wrapped_container->has( $key ) ) {
			throw new Not_Found_Exception(
				esc_html(
					sprintf(
						/* translators: %s: meta key */
						__( 'Meta key with key %s was not found in container', 'wp-oop-plugin-lib' ),
						$key
					)
				)
			);
		}

		if ( ! isset( $this->instances[ $key ] ) ) {
			$this->instances[ $key ] = new Entity_Aware_Meta_Key(
				$this->wrapped_container->get( $key ),
				$this->entity_id
			);
		}

		return $this->instances[ $key ];
	}

	/**
	 * Gets all keys in the container.
	 *
	 * @since 0.1.0
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array {
		return $this->wrapped_container->get_keys();
	}

	/**
	 * Checks if a meta key for the given key exists in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key Meta key.
	 * @return bool True if the meta key exists in the container, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->has( $key );
	}

	/**
	 * Gets the meta key for the given key from the container.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key Meta key.
	 * @return mixed Meta key for the given key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets the given meta key under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key   Meta key.
	 * @param mixed $value Item creator closure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		// Does nothing as this class is read-only.
	}

	/**
	 * Unsets the meta key under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $key Meta key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		// Does nothing as this class is read-only.
	}

	/**
	 * Gets the entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @return int The entity ID.
	 */
	public function get_entity_id(): int {
		return $this->entity_id;
	}
}
