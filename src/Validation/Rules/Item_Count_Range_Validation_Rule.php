<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Item_Count_Range_Validation_Rule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Non_Scalar_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;

/**
 * Class for a validation rule that ensures non-scalar values include a specific number of items.
 *
 * @since n.e.x.t
 */
class Item_Count_Range_Validation_Rule implements Non_Scalar_Validation_Rule {

	/**
	 * Minimum item count allowed.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $min_count;

	/**
	 * Maximum item count allowed.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $max_count;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $min_count Optional. Minimum count allowed. Default 0 (no limit).
	 * @param int $max_count Optional. Maximum count allowed. Default 0 (no limit).
	 */
	public function __construct( int $min_count = 0, int $max_count = 0 ) {
		$this->min_count = $min_count;
		$this->max_count = $max_count;
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
		$value = (array) $value;

		if ( count( $value ) < $this->min_count ) {
			throw Validation_Exception::create(
				'too_few_items',
				esc_html(
					sprintf(
						/* translators: 1: value, 2: minimum count */
						_n(
							'%1$s must contain at least %2$s item.',
							'%1$s must contain at least %2$s items.',
							$this->min_count,
							'default'
						),
						'array',
						$this->min_count
					)
				)
			);
		}

		if ( $this->max_count > 0 && count( $value ) > $this->max_count ) {
			throw Validation_Exception::create(
				'too_many_items',
				esc_html(
					sprintf(
						/* translators: 1: value, 2: maximum count */
						_n(
							'%1$s must contain at most %2$s item.',
							'%1$s must contain at most %2$s items.',
							$this->max_count,
							'default'
						),
						'array',
						$this->max_count
					)
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
		// Don't do anything to values that are entirely invalid.
		if ( ! is_array( $value ) ) {
			return $value;
		}

		// If there are too many items, strip the excess ones from the end.
		try {
			$this->validate( $value );
		} catch ( Validation_Exception $e ) {
			if ( $e->get_error_code() === 'too_many_items' ) {
				return array_slice( $value, 0, $this->max_count );
			}
		}

		return $value;
	}
}
