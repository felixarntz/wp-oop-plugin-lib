<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Boolean_Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Boolean_Validation_Rule;

/**
 * Class for a boolean validation rule builder.
 *
 * @since n.e.x.t
 */
class Boolean_Validation_Rule_Builder extends Abstract_Validation_Rule_Builder {

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
		array_unshift( $initial_rules, new Boolean_Validation_Rule( $strict ) );
		parent::__construct( $initial_rules );
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

		return $rule->supports_type( Types::TYPE_BOOLEAN );
	}
}
