<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Enum_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Strict_Mode;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Type_Support;
use InvalidArgumentException;

/**
 * Class for a validation rule that ensures values match a value from a specific set of values.
 *
 * @since n.e.x.t
 */
class Enum_Validation_Rule implements Validation_Rule, With_Type_Support {
	use Cast_Value_By_Type;
	use Strict_Mode;
	use Type_Support;

	/**
	 * Allowed values.
	 *
	 * @since n.e.x.t
	 * @var mixed[]
	 */
	private $allowed_values;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed[] $allowed_values List of values to allow.
	 * @param bool    $strict         Optional. True to enable strict mode, false to disable it. Default false.
	 *
	 * @throws InvalidArgumentException Thrown when zero allowed values are provided.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function __construct( array $allowed_values, bool $strict = false ) {
		if ( ! $allowed_values ) {
			throw new InvalidArgumentException(
				esc_html__( 'At least one allowed value must be provided in enum validation rule.', 'wp-oop-plugin-lib' ) // phpcs:ignore Generic.Files.LineLength.TooLong
			);
		}
		$this->allowed_values = $allowed_values;
		$this->set_strict( $strict );
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
		if ( $this->is_strict() && $this->has_strict_match( $value ) ) {
			return;
		}

		if ( ! $this->is_strict() && $this->has_loose_match( $value ) ) {
			return;
		}

		if ( count( $this->allowed_values ) === 1 ) {
			throw Validation_Exception::create(
				'not_in_enum',
				wp_sprintf(
					/* translators: 1: value, 2: valid value */
					esc_html__( '%1$s is not %2$s.', 'default' ),
					esc_html( (string) $value ),
					esc_html( $this->allowed_values[0] )
				)
			);
		}

		throw Validation_Exception::create(
			'not_in_enum',
			wp_sprintf(
				/* translators: 1: value, 2: list of valid values */
				esc_html__( '%1$s is not one of %2$l.', 'default' ),
				esc_html( (string) $value ),
				array_map( 'esc_html', $this->allowed_values )
			)
		);
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
			$forced_value = $this->allowed_values[0];
			if ( ! is_numeric( $forced_value ) ) {
				return $forced_value;
			}

			// Cast the forced value to the same type as the given value, in order to not change the value type.
			return $this->cast_value_by_type( $forced_value, gettype( $value ) );
		}

		return $value;
	}

	/**
	 * Checks whether the given value has a match in the allowed values, using strict comparison.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $value Value to check.
	 * @return bool True if value has a match, false otherwise.
	 */
	private function has_strict_match( $value ): bool {
		foreach ( $this->allowed_values as $allowed_value ) {
			if ( $value === $allowed_value ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks whether the given value has a match in the allowed values, using loose comparison.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed $value Value to check.
	 * @return bool True if value has a match, false otherwise.
	 */
	private function has_loose_match( $value ): bool {
		foreach ( $this->allowed_values as $allowed_value ) {
			if ( rest_are_values_equal( $value, $allowed_value ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets the supported types for the validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
	 */
	protected function get_supported_types(): int {
		return Types::TYPE_STRING | Types::TYPE_INTEGER | Types::TYPE_FLOAT;
	}
}
