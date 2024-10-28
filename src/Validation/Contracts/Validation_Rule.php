<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;

/**
 * Interface for a validation rule.
 *
 * @since 0.1.0
 */
interface Validation_Rule {

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
	public function validate( $value ): void;

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
	public function sanitize( $value );
}
