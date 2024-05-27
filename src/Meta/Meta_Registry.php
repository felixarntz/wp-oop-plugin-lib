<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;

/**
 * Class for a registry of WordPress metadata.
 *
 * @since n.e.x.t
 */
class Meta_Registry implements Registry {

	/**
	 * Object type.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	protected $object_type;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $object_type Object type.
	 */
	public function __construct( string $object_type ) {
		$this->object_type = $object_type;
	}

	/**
	 * Registers a metadata item with the given key and arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Meta key.
	 * @param array<string, mixed> $args Meta key registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		return register_meta( $this->object_type, $key, $args );
	}

	/**
	 * Checks whether a metadata item with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Meta key.
	 * @return bool True if the metadata item is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		return registered_meta_key_exists( $this->object_type, $key );
	}

	/**
	 * Gets the registered metadata item for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Meta key.
	 * @return object|null The registered metadata definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		$registered = get_registered_meta_keys( $this->object_type );
		if ( ! isset( $registered[ $key ] ) ) {
			return null;
		}
		return (object) $registered[ $key ];
	}

	/**
	 * Gets all metadata items from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Associative array of keys and their metadata definitions, or empty array if nothing is registered.
	 */
	public function get_all_registered(): array {
		return get_registered_meta_keys( $this->object_type );
	}
}
