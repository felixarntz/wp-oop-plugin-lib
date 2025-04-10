<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\General\Generic_Key_Value
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value_Repository;

/**
 * Class representing a generic key-value pair.
 *
 * Should typically not be used directly, but rather through a more specific class extending it.
 *
 * @since 0.1.0
 */
class Generic_Key_Value implements Key_Value {

	/**
	 * Repository used for the item.
	 *
	 * @since 0.1.0
	 * @var Key_Value_Repository
	 */
	protected $repository;

	/**
	 * Item key.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $key;

	/**
	 * Item default value.
	 *
	 * @since 0.1.0
	 * @var mixed
	 */
	protected $default_value;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Key_Value_Repository $repository    Repository used for the item.
	 * @param string               $key           Item key.
	 * @param mixed                $default_value Optional. Default value for the item if not set in the repository.
	 *                                            If null, it will be ignored. Default null.
	 */
	public function __construct( Key_Value_Repository $repository, string $key, $default_value = null ) {
		$this->repository    = $repository;
		$this->key           = $key;
		$this->default_value = $default_value;
	}

	/**
	 * Checks whether the item has a value set.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value(): bool {
		return $this->repository->exists( $this->key );
	}

	/**
	 * Gets the value for the item.
	 *
	 * @since 0.1.0
	 *
	 * @return mixed Value for the item.
	 */
	public function get_value() {
		// Pass default value if set.
		if ( isset( $this->default_value ) ) {
			return $this->repository->get( $this->key, $this->default_value );
		}

		return $this->repository->get( $this->key );
	}

	/**
	 * Updates the value for the item.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value New value to set for the item.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( $value ): bool {
		return $this->repository->update( $this->key, $value );
	}

	/**
	 * Deletes the data for the item.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_value(): bool {
		return $this->repository->delete( $this->key );
	}

	/**
	 * Gets the key of the item.
	 *
	 * @since 0.1.0
	 *
	 * @return string Item key.
	 */
	public function get_key(): string {
		return $this->key;
	}
}
