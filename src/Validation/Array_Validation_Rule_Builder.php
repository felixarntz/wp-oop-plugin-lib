<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Array_Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Array_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Item_Count_Range_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Items_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Unique_Items_Validation_Rule;

/**
 * Class for an array validation rule builder.
 *
 * @since n.e.x.t
 */
class Array_Validation_Rule_Builder extends Abstract_Validation_Rule_Builder {

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule[] $initial_rules Optional. Initial validation rules to use for the builder.
	 * @param bool              $strict        Optional. True to enable strict mode, false to disable it. Default
	 *                                         false.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function __construct( array $initial_rules = array(), bool $strict = false ) {
		array_unshift( $initial_rules, new Array_Validation_Rule( $strict ) );
		parent::__construct( $initial_rules );
	}

	/**
	 * Adds an array item validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $item_validation_rule Validation rule to validate all array items with.
	 * @return static Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function with_items( Validation_Rule $item_validation_rule ): static {
		return $this->with_rule( new Items_Validation_Rule( $item_validation_rule ) );
	}

	/**
	 * Adds an array item count range validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $min_count Optional. Minimum count allowed. Default 0 (no limit).
	 * @param int $max_count Optional. Maximum count allowed. Default 0 (no limit).
	 * @return static Builder instance for chaining.
	 */
	public function with_item_count_range( int $min_count = 0, int $max_count = 0 ): static {
		return $this->with_rule( new Item_Count_Range_Validation_Rule( $min_count, $max_count ) );
	}

	/**
	 * Adds a unique array items validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return static Builder instance for chaining.
	 */
	public function with_unique_items(): static {
		return $this->with_rule( new Unique_Items_Validation_Rule() );
	}

	/**
	 * Checks whether the given rule is allowed by the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $rule Rule to check.
	 * @return bool True if the rule is allowed, false otherwise.
	 */
	protected function is_allowed_rule( Validation_Rule $rule ): bool {
		// If no specific type support is specified, the rule supports all types.
		if ( ! $rule instanceof With_Type_Support ) {
			return true;
		}

		return $rule->supports_type( Types::TYPE_ARRAY );
	}
}
