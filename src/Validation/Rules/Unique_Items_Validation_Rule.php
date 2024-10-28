<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Unique_Items_Validation_Rule
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
 * Class for a validation rule that ensures arrays contain only unique items.
 *
 * @since 0.1.0
 */
class Unique_Items_Validation_Rule implements Validation_Rule, With_Type_Support {
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
		if ( ! rest_validate_array_contains_unique_items( (array) $value ) ) {
			throw Validation_Exception::create(
				'duplicate_items',
				sprintf(
					/* translators: %s: value */
					esc_html__( '%s has duplicate items.', 'default' ),
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
		// Don't do anything to values that are entirely invalid.
		if ( ! is_array( $value ) ) {
			return $value;
		}

		// Include only unique items.
		$unique_items = array();
		foreach ( $value as $item ) {
			$stabilized = rest_stabilize_value( $item );

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			$key = serialize( $stabilized );

			if ( isset( $unique_items[ $key ] ) ) {
				continue;
			}

			$unique_items[ $key ] = $item;
		}
		return array_values( $unique_items );
	}

	/**
	 * Gets the supported types for the validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
	 */
	protected function get_supported_types(): int {
		return Types::TYPE_ARRAY;
	}
}
