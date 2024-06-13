<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Aggregate_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use InvalidArgumentException;

/**
 * Class for a validation rule that consists of several underlying validation rules.
 *
 * @since n.e.x.t
 */
class Aggregate_Validation_Rule implements Validation_Rule {

	/**
	 * Underlying validation rules.
	 *
	 * @since n.e.x.t
	 * @var Validation_Rule[]
	 */
	private $rules = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule[] $rules Underlying validation rules.
	 *
	 * @throws InvalidArgumentException Thrown when the given rules are invalid.
	 */
	public function __construct( array $rules ) {
		foreach ( $rules as $rule ) {
			if ( ! $rule instanceof Validation_Rule ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid validation rule provided in aggregate validation rule.', 'wp-oop-plugin-lib' )
				);
			}
			$this->rules[] = $rule;
		}
	}

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
		foreach ( $this->rules as $rule ) {
			$rule->validate( $value );
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
		foreach ( $this->rules as $rule ) {
			$value = $rule->sanitize( $value );
		}
		return $value;
	}
}
