<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Datetime_Range_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Scalar_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;

/**
 * Class for a validation rule that ensures values fall within a date-time or date range.
 *
 * @since n.e.x.t
 */
class Datetime_Range_Validation_Rule implements Scalar_Validation_Rule {
	use Cast_Value_By_Type;

	/**
	 * Minimum date-time or date allowed.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $min_datetime;

	/**
	 * Maximum date-time or date allowed.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $max_datetime;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $min_datetime Minimum date-time or date allowed.
	 * @param string $max_datetime Optional. Maximum date-time or date allowed. Default no limit.
	 */
	public function __construct( $min_datetime, $max_datetime = null ) {
		$this->min_datetime = (string) $min_datetime;
		if ( null !== $max_datetime ) {
			$this->max_datetime = (string) $max_datetime;
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
		$timestamp = is_numeric( $value ) ? $value : strtotime( (string) $value );

		if ( $timestamp < strtotime( $this->min_datetime ) ) {
			throw Validation_Exception::create(
				'out_of_bounds_min',
				sprintf(
					/* translators: 1: value, 2: minimum date */
					esc_html__( '%1$s must be at or before %2$s', 'wp-oop-plugin-lib' ),
					esc_html( (string) $value ),
					esc_html( $this->min_datetime )
				)
			);
		}

		if ( null !== $this->max_datetime && $timestamp > strtotime( $this->max_datetime ) ) {
			throw Validation_Exception::create(
				'out_of_bounds_max',
				sprintf(
					/* translators: 1: value, 2: maximum date */
					esc_html__( '%1$s must be before or at %2$s', 'wp-oop-plugin-lib' ),
					esc_html( (string) $value ),
					esc_html( $this->max_datetime )
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
			$forced_value = $e->get_error_code() === 'out_of_bounds_max' ? $this->max_datetime : $this->min_datetime;

			// In case the given value is a string, ensure that the forced value matches the format.
			if ( is_string( $value ) && ! is_numeric( $value ) ) {
				if ( ! $value ) { // No way to detect the intended format if the given value is empty.
					return '';
				}
				if ( $this->is_date_string( $value ) && ! $this->is_date_string( $forced_value ) && preg_match( '/^\d{4}-\d{2}-\d{2}/', $forced_value, $matches ) ) {
					return $matches[0];
				}
				if ( ! $this->is_date_string( $value ) && $this->is_date_string( $forced_value ) ) {
					return "{$forced_value} 00:00:00";
				}
				return $forced_value;
			}

			// Cast the forced value to the same type as the given value, in order to not change the value type.
			$forced_value = strtotime( $forced_value );
			return $this->cast_value_by_type( $forced_value, gettype( $value ) );
		}

		return $value;
	}

	/**
	 * Checks whether the given string is a date string (as opposed to a date-time string).
	 *
	 * @since n.e.x.t
	 *
	 * @param string $value Date or date-time string.
	 * @return bool True if a date string, false otherwise.
	 */
	private function is_date_string( string $value ): bool {
		$date_check = new Date_Validation_Rule();
		try {
			$date_check->validate( $value );
		} catch ( Validation_Exception $e ) {
			return false;
		}
		return true;
	}
}
