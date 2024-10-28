<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Version_Validation_Rule
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Type_Support;

/**
 * Class for a validation rule that ensures values are valid version numbers.
 *
 * @since 0.1.0
 */
class Version_Validation_Rule implements Validation_Rule, With_Type_Support {
	use Type_Support;

	/**
	 * Validates the given value.
	 *
	 * Validation will be strict and throw an exception for any unmet requirements.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Value to validate.
	 *
	 * @throws Validation_Exception Thrown when validation fails.
	 */
	public function validate( $value ): void {
		if ( ! preg_match( '/^(\d+\.\d+(?:\.\d+)?)(-[A-Za-z0-9.]+)?$/', (string) $value ) ) {
			throw Validation_Exception::create(
				'invalid_version',
				sprintf(
					/* translators: %s: value */
					esc_html__( '%s is not a valid version number.', 'wp-oop-plugin-lib' ),
					esc_html( (string) $value )
				)
			);
		}
	}

	/**
	 * Sanitizes the given value.
	 *
	 * This should be called before storing the value in the persistency layer (e.g. the database).
	 * If the value does not satisfy validation requirements, it will be sanitized to a value that does, e.g. a default.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $value ) {
		try {
			$this->validate( $value );
		} catch ( Validation_Exception $e ) {
			// If a valid string is contained within the overall string, extract it.
			if ( is_string( $value ) && preg_match( '/^(\d+\.\d+(?:\.\d+)?)(-[A-Za-z0-9.]+)?$/', $value, $matches ) ) {
				return $matches[0];
			}
			return '';
		}

		return sanitize_url( $value );
	}

	/**
	 * Gets the supported types for the validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
	 */
	protected function get_supported_types(): int {
		return Types::TYPE_STRING;
	}
}
