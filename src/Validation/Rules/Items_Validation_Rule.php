<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Items_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Type_Support;

/**
 * Class for a validation rule that validates the individual items of an array.
 *
 * @since n.e.x.t
 */
class Items_Validation_Rule implements Validation_Rule, With_Type_Support, With_Type_Support {
	use Type_Support;

	/**
	 * Validation rule to validate all items with.
	 *
	 * @since n.e.x.t
	 * @var Validation_Rule
	 */
	private $item_validation_rule;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $item_validation_rule Validation rule to validate all array items with.
	 */
	public function __construct( Validation_Rule $item_validation_rule ) {
		$this->item_validation_rule = $item_validation_rule;
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
		if ( ! is_array( $value ) ) {
			return;
		}

		foreach ( $value as $item ) {
			$this->item_validation_rule->validate( $item );
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
		if ( ! is_array( $value ) ) {
			return;
		}

		foreach ( $value as $index => $item ) {
			$value[ $index ] = $this->item_validation_rule->sanitize( $item );
		}
		return $value;
	}

	/**
	 * Gets the supported types for the validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
	 */
	protected function get_supported_types(): int {
		return Types::TYPE_ARRAY;
	}
}
