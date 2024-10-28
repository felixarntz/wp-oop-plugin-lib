<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Numeric_Range_Validation_Rule
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Type_Support;

/**
 * Class for a validation rule that ensures values fall within a numeric range.
 *
 * @since 0.1.0
 */
class Numeric_Range_Validation_Rule implements Validation_Rule, With_Type_Support {
	use Cast_Value_By_Type;
	use Type_Support;

	/**
	 * Minimum value allowed.
	 *
	 * @since 0.1.0
	 * @var int|float
	 */
	private $min;

	/**
	 * Maximum value allowed.
	 *
	 * @since 0.1.0
	 * @var int|float
	 */
	private $max;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param int|float $min Minimum value allowed.
	 * @param int|float $max Optional. Maximum value allowed. Default no limit.
	 */
	public function __construct( $min, $max = null ) {
		$this->min = is_int( $min ) ? $min : (float) $min;
		if ( null !== $max ) {
			$this->max = is_int( $max ) ? $max : (float) $max;
		}
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
		if ( $value < $this->min ) {
			throw Validation_Exception::create(
				'out_of_bounds_min',
				sprintf(
					/* translators: 1: value, 2: minimum number */
					esc_html__( '%1$s must be greater than or equal to %2$d', 'default' ),
					esc_html( (string) $value ),
					// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
					$this->min
				)
			);
		}

		if ( null !== $this->max && $value > $this->max ) {
			throw Validation_Exception::create(
				'out_of_bounds_max',
				sprintf(
					/* translators: 1: value, 2: maximum number */
					esc_html__( '%1$s must be less than or equal to %2$d', 'default' ),
					esc_html( (string) $value ),
					// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
					$this->max
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
			$forced_value = $e->get_error_code() === 'out_of_bounds_max' ? $this->max : $this->min;

			// Cast the forced value to the same type as the given value, in order to not change the value type.
			return $this->cast_value_by_type( $forced_value, gettype( $value ) );
		}

		return $value;
	}

	/**
	 * Gets the supported types for the validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
	 */
	protected function get_supported_types(): int {
		return Types::TYPE_INTEGER | Types::TYPE_FLOAT;
	}
}
