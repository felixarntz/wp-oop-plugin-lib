<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\General_Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;

/**
 * Class for a general validation rule builder.
 *
 * This class is recommended for usage as a general-purpose validation rule builder that allows building any kind of
 * validation rule. It exposes the different type validation rule builders via methods.
 *
 * @since n.e.x.t
 */
class General_Validation_Rule_Builder extends Abstract_Validation_Rule_Builder {

	/**
	 * Adds a boolean validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return Boolean_Validation_Rule_Builder Boolean validation rule builder instance, for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_boolean( bool $strict = false ): Boolean_Validation_Rule_Builder {
		return new Boolean_Validation_Rule_Builder( $this->get_rules_array(), $strict );
	}

	/**
	 * Adds a float validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return Float_Validation_Rule_Builder Float validation rule builder instance, for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_float( bool $strict = false ): Float_Validation_Rule_Builder {
		return new Float_Validation_Rule_Builder( $this->get_rules_array(), $strict );
	}

	/**
	 * Adds an integer validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return Integer_Validation_Rule_Builder Integer validation rule builder instance, for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_integer( bool $strict = false ): Integer_Validation_Rule_Builder {
		return new Integer_Validation_Rule_Builder( $this->get_rules_array(), $strict );
	}

	/**
	 * Adds a string validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return String_Validation_Rule_Builder String validation rule builder instance, for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_string( bool $strict = false ): String_Validation_Rule_Builder {
		return new String_Validation_Rule_Builder( $this->get_rules_array(), $strict );
	}

	/**
	 * Adds an array validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return Array_Validation_Rule_Builder Array validation rule builder instance, for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_array( bool $strict = false ): Array_Validation_Rule_Builder {
		return new Array_Validation_Rule_Builder( $this->get_rules_array(), $strict );
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
		// This builder allows any kind of rules.
		return true;
	}
}
