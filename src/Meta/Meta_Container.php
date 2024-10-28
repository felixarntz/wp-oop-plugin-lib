<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Container
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use ArrayAccess;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Exception\Not_Found_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\Entity_Key_Value_Repository;

/**
 * Class for a meta container.
 *
 * @since 0.1.0
 */
class Meta_Container implements Container, ArrayAccess {

	/**
	 * Meta keys stored in the container.
	 *
	 * @since 0.1.0
	 * @var array<string, callable>
	 */
	private $meta_keys = array();

	/**
	 * Meta key instances already created.
	 *
	 * @since 0.1.0
	 * @var array<string, Meta_Key>
	 */
	private $instances = array();

	/**
	 * Checks if a meta key for the given key exists in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Meta key.
	 * @return bool True if the meta key exists in the container, false otherwise.
	 */
	public function has( string $key ): bool {
		return isset( $this->meta_keys[ $key ] );
	}

	/**
	 * Gets the meta key for the given key from the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Meta key.
	 * @return Meta_Key Meta key for the given key.
	 *
	 * @throws Not_Found_Exception    Thrown when meta key with given key is not found.
	 * @throws Invalid_Type_Exception Thrown when meta key with given key has invalid type.
	 */
	public function get( string $key ) {
		if ( ! isset( $this->meta_keys[ $key ] ) ) {
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
			$instance = $this->meta_keys[ $key ]( $this );
			if ( ! $instance instanceof Meta_Key ) {
				throw new Invalid_Type_Exception(
					esc_html(
						sprintf(
							/* translators: %s: meta key */
							__( 'Meta key with key %s is not of type Meta_Key', 'wp-oop-plugin-lib' ),
							$key
						)
					)
				);
			}
			$this->instances[ $key ] = $instance;
		}

		return $this->instances[ $key ];
	}

	/**
	 * Sets the given meta key under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string   $key     Meta key.
	 * @param callable $creator Meta key creator closure.
	 */
	public function set( string $key, callable $creator ): void {
		$this->meta_keys[ $key ] = $creator;
		unset( $this->instances[ $key ] );
	}

	/**
	 * Sets a meta key using the given repository and arguments under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string                      $key               Meta key.
	 * @param Entity_Key_Value_Repository $repository        Repository used for the meta key.
	 * @param array<string, mixed>        $registration_args Optional. Meta key registration arguments. Default empty
	 *                                                       array.
	 */
	public function set_by_args( string $key, Entity_Key_Value_Repository $repository, array $registration_args = array() ): void { // phpcs:ignore Generic.Files.LineLength.TooLong
		$this->set(
			$key,
			function () use ( $repository, $key, $registration_args ) {
				return new Meta_Key( $repository, $key, $registration_args );
			}
		);
	}

	/**
	 * Unsets the meta key under the given key in the container.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Meta key.
	 */
	public function unset( string $key ): void {
		unset( $this->meta_keys[ $key ], $this->instances[ $key ] );
	}

	/**
	 * Gets all keys in the container.
	 *
	 * @since 0.1.0
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array {
		return array_keys( $this->meta_keys );
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
	 * @return Meta_Key Meta key for the given key.
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
	 * @param mixed $value Meta key creator closure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
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
		$this->unset( $key );
	}

	/**
	 * Creates an instance similar to this container, but scoped to the given entity.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return Entity_Aware_Meta_Container New container scoped to the object.
	 */
	public function create_entity_aware( int $entity_id ): Entity_Aware_Meta_Container {
		return new Entity_Aware_Meta_Container( $this, $entity_id );
	}
}
