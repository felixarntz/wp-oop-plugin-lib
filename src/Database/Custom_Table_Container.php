<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table_Container
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Database;

use ArrayAccess;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Container;
use Felix_Arntz\WP_OOP_Plugin_Lib\Database\Contracts\Database_Table_Schema;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Invalid_Type_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Not_Found_Exception;

/**
 * Class for a custom database table container.
 *
 * @since n.e.x.t
 */
class Custom_Table_Container implements Container, ArrayAccess {

	/**
	 * Custom tables stored in the container.
	 *
	 * @since n.e.x.t
	 * @var array<string, callable>
	 */
	private $tables = array();

	/**
	 * Custom table instances already created.
	 *
	 * @since n.e.x.t
	 * @var array<string, Custom_Table>
	 */
	private $instances = array();

	/**
	 * Checks if a custom database table for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Custom table key.
	 * @return bool True if the custom database table exists in the container, false otherwise.
	 */
	public function has( string $key ): bool {
		return isset( $this->tables[ $key ] );
	}

	/**
	 * Gets the custom database table for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Custom table key.
	 * @return Custom_Table Custom table for the given key.
	 *
	 * @throws Not_Found_Exception    Thrown when database table with given key is not found.
	 * @throws Invalid_Type_Exception Thrown when database table with given key has invalid type.
	 */
	public function get( string $key ) {
		if ( ! isset( $this->tables[ $key ] ) ) {
			throw new Not_Found_Exception(
				esc_html(
					sprintf(
						/* translators: %s: database table key */
						__( 'Custom table with key %s was not found in container', 'wp-oop-plugin-lib' ),
						$key
					)
				)
			);
		}

		if ( ! isset( $this->instances[ $key ] ) ) {
			$instance = $this->tables[ $key ]( $this );
			if ( ! $instance instanceof Custom_Table ) {
				throw new Invalid_Type_Exception(
					esc_html(
						sprintf(
							/* translators: %s: database table key */
							__( 'Custom table with key %s is not of type Custom_Table', 'wp-oop-plugin-lib' ),
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
	 * Sets the given custom database table under the given key in the container.
	 *
	 * This method will also register the table key in the `$wpdb` global database object.
	 *
	 * @since n.e.x.t
	 *
	 * @param string   $key     Custom table key.
	 * @param callable $creator Custom table creator closure.
	 */
	public function set( string $key, callable $creator ): void {
		global $wpdb;

		if ( ! in_array( $key, $wpdb->tables, true ) ) {
			$wpdb->tables[] = $key;
		}
		$wpdb->$key = $wpdb->prefix . $key;

		$this->tables[ $key ] = $creator;
		unset( $this->instances[ $key ] );
	}

	/**
	 * Sets a custom database table using the given repository and arguments under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param string                $key    Database table key.
	 * @param Database_Table_Schema $schema Database table schema.
	 */
	public function set_by_args( string $key, Database_Table_Schema $schema ): void {
		$this->set(
			$key,
			function () use ( $key, $schema ) {
				return new Custom_Table( $key, $schema );
			}
		);
	}

	/**
	 * Unsets the custom database table under the given key in the container.
	 *
	 * This method will also unregister the table key in the `$wpdb` global database object.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Custom table key.
	 */
	public function unset( string $key ): void {
		global $wpdb;

		$index = array_search( $key, $wpdb->tables, true );
		if ( false !== $index ) {
			array_splice( $wpdb->tables, $index, 1 );
		}
		unset( $wpdb->$key );

		unset( $this->tables[ $key ], $this->instances[ $key ] );
	}

	/**
	 * Gets all keys in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @return string[] List of keys.
	 */
	public function get_keys(): array {
		return array_keys( $this->tables );
	}

	/**
	 * Checks if a custom database table for the given key exists in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Custom table key.
	 * @return bool True if the custom database table exists in the container, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $key ) {
		return $this->has( $key );
	}

	/**
	 * Gets the custom database table for the given key from the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Custom table key.
	 * @return Custom_Table Custom table for the given key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Sets the given database table under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key   Custom table key.
	 * @param mixed $value Custom table creator closure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Unsets the custom database table under the given key in the container.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $key Custom table key.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $key ) {
		$this->unset( $key );
	}
}
