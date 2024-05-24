<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Database\Custom_Table
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Database;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Database_Table_Schema;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Database_Exception;

/**
 * Class representing a custom database table.
 *
 * @since n.e.x.t
 */
class Custom_Table {

	/**
	 * The database table key.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $key;

	/**
	 * The database table schema.
	 *
	 * @since n.e.x.t
	 * @var Database_Table_Schema
	 */
	private $schema;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string                $key    Database table key.
	 * @param Database_Table_Schema $schema Database table schema.
	 */
	public function __construct( string $key, Database_Table_Schema $schema ) {
		$this->key    = $key;
		$this->schema = $schema;
	}

	/**
	 * Gets the database table key.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Database table key.
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Checks whether the database table exists in the database.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the database table exists, false otherwise.
	 */
	public function exists(): bool {
		global $wpdb;

		if ( ! isset( $wpdb->{$this->key} ) ) {
			return false;
		}

		$table_name = $wpdb->{$this->key};

		$suppress = $wpdb->suppress_errors();
		$result   = $wpdb->query( "DESCRIBE $table_name" ); // phpcs:ignore WordPress.DB.PreparedSQL
		$wpdb->suppress_errors( $suppress );

		return (bool) $result;
	}

	/**
	 * Creates the database table in the database.
	 *
	 * @since n.e.x.t
	 *
	 * @throws Database_Exception Thrown when database table creation fails.
	 */
	public function create(): void {
		global $wpdb;

		if ( ! isset( $wpdb->{$this->key} ) ) {
			throw new Database_Exception(
				esc_html(
					sprintf(
						/* translators: %s: database table key */
						__( 'Database table %s not registered in wpdb', 'wp-oop-plugin-lib' ),
						$this->key
					)
				)
			);
		}

		$schema_arr = $this->schema->get_schema_array();

		if ( ! $schema_arr ) {
			throw new Database_Exception(
				esc_html(
					sprintf(
						/* translators: %s: database table key */
						__( 'Database table %s must not have empty schema', 'wp-oop-plugin-lib' ),
						$this->key
					)
				)
			);
		}

		$table_name = $wpdb->{$this->key};

		$query = "CREATE TABLE $table_name (\n\t" . implode( ",\n\t", $schema_arr ) . "\n) {$wpdb->get_charset_collate()};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $query );

		if ( ! $this->exists() ) {
			throw new Database_Exception(
				esc_html(
					sprintf(
						/* translators: %s: database table key */
						__( 'Database table %s creation failed', 'wp-oop-plugin-lib' ),
						$this->key
					)
				)
			);
		}
	}

	/**
	 * Drops/deletes the database table from the database.
	 *
	 * @since n.e.x.t
	 *
	 * @throws Database_Exception Thrown when database table deletion fails.
	 */
	public function drop(): void {
		global $wpdb;

		if ( ! isset( $wpdb->{$this->key} ) ) {
			throw new Database_Exception(
				esc_html(
					sprintf(
						/* translators: %s: database table key */
						__( 'Database table %s not registered in wpdb', 'wp-oop-plugin-lib' ),
						$this->key
					)
				)
			);
		}

		$table_name = $wpdb->{$this->key};

		$wpdb->query( "DROP TABLE $table_name" ); // phpcs:ignore WordPress.DB.PreparedSQL

		if ( $this->exists() ) {
			throw new Database_Exception(
				esc_html(
					sprintf(
						/* translators: %s: database table key */
						__( 'Database table %s deletion failed', 'wp-oop-plugin-lib' ),
						$this->key
					)
				)
			);
		}
	}
}
