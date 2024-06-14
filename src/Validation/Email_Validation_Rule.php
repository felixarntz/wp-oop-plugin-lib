<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Email_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Scalar_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;

/**
 * Class for a validation rule that ensures values are valid email addresses.
 *
 * @since n.e.x.t
 */
class Email_Validation_Rule implements Scalar_Validation_Rule {

	/**
	 * Validates the given value.
	 *
	 * Validation will be strict and throw an exception for any unmet requirements.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $value Value to validate.
	 *
	 * @throws Validation_Exception Thrown when validation fails.
	 */
	public function validate( $value ): void {
		if ( ! is_email( (string) $value ) ) {
			throw Validation_Exception::create(
				'invalid_email',
				sprintf(
					/* translators: %s: value */
					esc_html__( '%s is not a valid email address.', 'wp-oop-plugin-lib' ),
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
	 * @since n.e.x.t
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $value ) {
		try {
			$this->validate( $value );
		} catch ( Validation_Exception $e ) {
			return '';
		}

		return sanitize_email( $value );
	}
}
