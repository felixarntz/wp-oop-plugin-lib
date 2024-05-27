<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Options\Option
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Options;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Base\Abstract_Key_Value;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\With_Registration_Args;
use Felix_Arntz\WP_OOP_Plugin_Lib\Options\Contracts\With_Autoload;
use Felix_Arntz\WP_OOP_Plugin_Lib\Traits\Cast_Value_By_Type;

/**
 * Class representing a WordPress option.
 *
 * @since n.e.x.t
 */
class Option extends Abstract_Key_Value implements With_Registration_Args {
	use Cast_Value_By_Type;

	/**
	 * Option registration arguments.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	protected $registration_args;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Key_Value_Repository $repository        Repository used for the option.
	 * @param string               $key               Option key.
	 * @param array<string, mixed> $registration_args Optional. Option registration arguments. Default empty array.
	 */
	public function __construct( Key_Value_Repository $repository, string $key, array $registration_args = array() ) {
		// Extract default value from registration arguments if passed.
		$default = null;
		if ( isset( $registration_args['default'] ) ) {
			$default = $registration_args['default'];
		}

		// Set autoload value from registration arguments if passed.
		if ( $repository instanceof With_Autoload && isset( $registration_args['autoload'] ) ) {
			$repository->set_autoload( $key, (bool) $registration_args['autoload'] );
		}

		// Unset autoload value in registration arguments, since it is not used by WordPress.
		unset( $registration_args['autoload'] );

		parent::__construct( $repository, $key, $default );

		$this->registration_args = $registration_args;
	}

	/**
	 * Checks whether the option has a value set.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value(): bool {
		return parent::has_value();
	}

	/**
	 * Gets the value for the option.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
	 * @since n.e.x.t
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_value(): bool {
		return parent::delete_value();
	}

	/**
	 * Gets the key of the option.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Option key.
	 */
	public function get_key(): string {
		return parent::get_key();
	}

	/**
	 * Gets the registration arguments for the option.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Option registration arguments.
	 */
	public function get_registration_args(): array {
		return $this->registration_args;
	}
}
