<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\String_Validation_Rule
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Strict;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Strict_Mode;

/**
 * Class for a validation rule that ensures string values.
 *
 * @since 0.1.0
 */
class String_Validation_Rule implements Validation_Rule, With_Strict {
	use Strict_Mode;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function __construct( bool $strict = false ) {
		$this->set_strict( $strict );
	}

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
		if ( is_string( $value ) ) {
			return;
		}

		if ( $this->is_strict() || ! is_scalar( $value ) ) {
			throw Validation_Exception::create(
				'invalid_string',
				sprintf(
					/* translators: 1: value, 2: type name */
					esc_html__( '%1$s is not of type %2$s.', 'default' ),
					esc_html( (string) $value ),
					'string'
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
		return (string) $value;
	}
}
