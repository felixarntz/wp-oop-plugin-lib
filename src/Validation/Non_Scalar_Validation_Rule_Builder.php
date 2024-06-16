<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Non_Scalar_Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Non_Scalar_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Aggregate_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Array_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Item_Count_Range_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Unique_Items_Validation_Rule;
use InvalidArgumentException;

/**
 * Class for a non-scalar validation rule builder.
 *
 * Validation rule builders provide a convenience layer to compose a single validation rule out of multiple non-scalar rules.
 *
 * @since n.e.x.t
 */
final class Non_Scalar_Validation_Rule_Builder extends Abstract_Validation_Rule_Builder {

	/**
	 * Validation rules set for this instance.
	 *
	 * @since n.e.x.t
	 * @var Non_Scalar_Validation_Rule[]
	 */
	private $rules = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Non_Scalar_Validation_Rule[] $initial_rules Optional. Initial validation rules to use for the builder.
	 */
	public function __construct( array $initial_rules = array() ) {
		array_walk(
			$initial_rules,
			array( $this, 'with_rule' )
		);
	}

	/**
	 * Adds an array validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_array( bool $strict = false ): self {
		return $this->with_rule( new Array_Validation_Rule( null, $strict ) );
	}

	/**
	 * Adds an array item validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $item_validation_rule Validation rule to validate all array items with.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function with_items( Validation_Rule $item_validation_rule ): self {
		return $this->with_rule( new Array_Validation_Rule( $item_validation_rule, false ) );
	}

	/**
	 * Adds an array item count range validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $min_count Optional. Minimum count allowed. Default 0 (no limit).
	 * @param int $max_count Optional. Maximum count allowed. Default 0 (no limit).
	 * @return self Builder instance for chaining.
	 */
	public function with_item_count_range( int $min_count = 0, int $max_count = 0 ): self {
		return $this->with_rule( new Item_Count_Range_Validation_Rule( $min_count, $max_count ) );
	}

	/**
	 * Adds a unique array items validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return self Builder instance for chaining.
	 */
	public function with_unique_items(): self {
		return $this->with_rule( new Unique_Items_Validation_Rule() );
	}

	/**
	 * Adds the given rule to the rules for the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @param Non_Scalar_Validation_Rule $rule Rule to add.
	 * @return self Builder instance for chaining.
	 *
	 * @throws InvalidArgumentException Thrown when a forbidden rule is passed.
	 */
	public function with_rule( Non_Scalar_Validation_Rule $rule ): self {
		$this->rules[] = $rule;
		return $this;
	}

	/**
	 * Gets the combined validation rule consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return Validation_Rule Combined validation rule.
	 */
	public function get(): Validation_Rule {
		return new Aggregate_Validation_Rule( $this->rules );
	}
}
