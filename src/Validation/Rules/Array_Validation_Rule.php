<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Array_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Non_Scalar_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Strict;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Strict_Mode;

/**
 * Class for a validation rule that ensures array values.
 *
 * This class should only be used for the more traditional concept of an array, i.e. an indexed or numerical array.
 * It does not validate associative arrays. Please use Associative_Array_Validation_Rule for that.
 *
 * @since n.e.x.t
 */
class Array_Validation_Rule implements Non_Scalar_Validation_Rule, With_Strict {
	use Strict_Mode;

	/**
	 * Validation rule to validate all items with, or null.
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
	 * @param Validation_Rule|null $item_validation_rule Optional. Validation rule to validate all array items with.
	 *                                                   Default null (items can be anything).
	 * @param bool                 $strict               Optional. True to enable strict mode, false to disable it.
	 *                                                   Default false.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function __construct( Validation_Rule $item_validation_rule = null, bool $strict = false ) {
		$this->item_validation_rule = $item_validation_rule;
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
		if ( wp_is_numeric_array( $value ) ) {
			$this->validate_items( $value );
			return;
		}

		if ( $this->is_strict() || ! rest_is_array( $value ) ) {
			throw Validation_Exception::create(
				'invalid_array',
				sprintf(
					/* translators: 1: value, 2: type name */
					esc_html__( '%1$s is not of type %2$s.', 'default' ),
					esc_html( (string) $value ),
					'array'
				)
			);
		}

		$this->validate_items( rest_sanitize_array( $value ) );
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
		$items = rest_sanitize_array( $value );
		if ( ! $this->item_validation_rule ) {
			return $items;
		}

		foreach ( $items as $index => $item ) {
			$items[ $index ] = $this->item_validation_rule->sanitize( $item );
		}
		return $items;
	}

	/**
	 * Validates the individual array items.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed[] $items Array items.
	 */
	private function validate_items( array $items ): void {
		if ( ! $this->item_validation_rule ) {
			return;
		}

		foreach ( $items as $item ) {
			$this->item_validation_rule->validate( $item );
		}
	}
}
