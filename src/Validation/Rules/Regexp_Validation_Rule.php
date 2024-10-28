<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Regexp_Validation_Rule
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
 * Class for a validation rule that ensures values match a regular expression.
 *
 * @since 0.1.0
 */
class Regexp_Validation_Rule implements Validation_Rule, With_Type_Support {
	use Cast_Value_By_Type;
	use Type_Support;

	/**
	 * Regular expression to match.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $regexp;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $regexp Regular expression to match.
	 */
	public function __construct( string $regexp ) {
		$this->regexp = $regexp;
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
		if ( ! preg_match( $this->regexp, (string) $value ) ) {
			throw Validation_Exception::create(
				'invalid_pattern',
				sprintf(
					/* translators: 1: value, 2: regular expression */
					esc_html__( '%1$s does not match pattern %2$s.', 'default' ),
					esc_html( (string) $value ),
					esc_html( $this->regexp )
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
			// Cast the empty value to the same type as the given value, in order to not change the value type.
			return $this->cast_value_by_type( '', gettype( $value ) );
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
		return Types::TYPE_STRING | Types::TYPE_INTEGER | Types::TYPE_FLOAT;
	}
}
