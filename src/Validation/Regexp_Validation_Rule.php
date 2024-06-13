<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Regexp_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;

/**
 * Class for a validation rule that ensures values match a regular expression.
 *
 * @since n.e.x.t
 */
class Regexp_Validation_Rule implements Validation_Rule {
	use Cast_Value_By_Type;

	/**
	 * Regular expression to match.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $regexp;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
	 * @since n.e.x.t
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
}
