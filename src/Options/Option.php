<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Generic_Key_Value;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts\With_Autoload_Config;

/**
 * Class representing a WordPress option.
 *
 * @since 0.1.0
 */
class Option extends Generic_Key_Value implements With_Registration_Args {
	use Cast_Value_By_Type;

	/**
	 * Option registration arguments.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	protected $registration_args;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Key_Value_Repository $repository        Repository used for the option.
	 * @param string               $key               Option key.
	 * @param array<string, mixed> $registration_args Optional. Option registration arguments. Default empty array.
	 */
	public function __construct( Key_Value_Repository $repository, string $key, array $registration_args = array() ) {
		// Extract default value from registration arguments if passed.
		$default = $registration_args['default'] ?? null;

		// Set autoload value from registration arguments if passed.
		if ( $repository instanceof With_Autoload_Config && isset( $registration_args['autoload'] ) ) {
			$repository->set_autoload_config( $key, (bool) $registration_args['autoload'] );
		}

		// Unset autoload value in registration arguments, since it is not used by WordPress.
		unset( $registration_args['autoload'] );

		parent::__construct( $repository, $key, $default );

		$this->registration_args = $registration_args;
	}

	/**
	 * Checks whether the option has a value set.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value(): bool {
		return parent::has_value();
	}

	/**
	 * Gets the value for the option.
	 *
	 * @since 0.1.0
	 *
	 * @return mixed Value for the option.
	 */
	public function get_value() {
		$value = parent::get_value();

		if ( isset( $this->registration_args['type'] ) ) {
			return $this->cast_value_by_type( $value, $this->registration_args['type'] );
		}

		return $value;
	}

	/**
	 * Updates the value for the option.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value New value to set for the option.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( $value ): bool {
		return parent::update_value( $value );
	}

	/**
	 * Deletes the data for the option.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_value(): bool {
		return parent::delete_value();
	}

	/**
	 * Gets the key of the option.
	 *
	 * @since 0.1.0
	 *
	 * @return string Option key.
	 */
	public function get_key(): string {
		return parent::get_key();
	}

	/**
	 * Gets the registration arguments for the option.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Option registration arguments.
	 */
	public function get_registration_args(): array {
		return $this->registration_args;
	}
}
