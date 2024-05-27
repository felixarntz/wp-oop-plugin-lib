<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\WP_OOP_Plugin_Lib\Key_Value\Contracts\Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts\With_Autoload;

/**
 * Class for a repository of WordPress options.
 *
 * @since n.e.x.t
 */
class Option_Repository implements Key_Value_Repository, With_Autoload {

	/**
	 * Autoload config as $key => $autoload pairs.
	 *
	 * @since n.e.x.t
	 * @var array<string, bool>
	 */
	private $autoload_config = array();

	/**
	 * Checks whether a value for the given option exists in the database.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return bool True if a value for the option exists, false otherwise.
	 */
	public function exists( string $key ): bool {
		$value = get_option( $key, null );
		return null !== $value;
	}

	/**
	 * Gets the value for a given option from the database.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Optional. Value to return if no value exists for the option. Default null.
	 * @return mixed Value for the option, or the default if no value exists.
	 */
	public function get( string $key, $default = null ) {
		return get_option( $key, $default );
	}

	/**
	 * Updates the value for a given option in the database.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value New value to set for the option.
	 * @return bool True on success, false on failure.
	 */
	public function update( string $key, $value ): bool {
		// Consider autoload config if set.
		if ( isset( $this->autoload_config[ $key ] ) ) {
			return (bool) update_option( $key, $value, $this->autoload_config[ $key ] );
		}

		return (bool) update_option( $key, $value );
	}

	/**
	 * Deletes the data for a given option from the database.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return bool True on success, false on failure.
	 */
	public function delete( string $key ): bool {
		return (bool) delete_option( $key );
	}

	/**
	 * Gets the autoload config for a given option in the database.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Option key.
	 * @return bool Whether or not the item should be autoloaded.
	 */
	public function get_autoload( string $key ): bool {
		// The default value is true.
		return $this->autoload_config[ $key ] ?? true;
	}

	/**
	 * Sets the autoload config for a given option in the database.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key      Option key.
	 * @param bool   $autoload Option autoload config.
	 */
	public function set_autoload( string $key, bool $autoload ): void {
		$this->autoload_config[ $key ] = $autoload;
	}
}
