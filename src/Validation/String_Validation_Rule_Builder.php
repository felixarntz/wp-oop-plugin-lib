<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\String_Validation_Rule_Builder
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Types;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule_Builder;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\With_Type_Support;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Date_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Datetime_Range_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Datetime_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Email_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Enum_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Hex_Color_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Regexp_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\String_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\URL_Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Version_Validation_Rule;

/**
 * Class for a string validation rule builder.
 *
 * @since 0.1.0
 */
class String_Validation_Rule_Builder extends Abstract_Validation_Rule_Builder {

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Validation_Rule[] $initial_rules Optional. Initial validation rules to use for the builder.
	 * @param bool              $strict        Optional. True to enable strict mode, false to disable it. Default
	 *                                         false.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function __construct( array $initial_rules = array(), bool $strict = false ) {
		array_unshift( $initial_rules, new String_Validation_Rule( $strict ) );
		parent::__construct( $initial_rules );
	}

	/**
	 * Adds a date validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_date(): Validation_Rule_Builder {
		return $this->with_rule( new Date_Validation_Rule() );
	}

	/**
	 * Adds a date-time validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_datetime(): Validation_Rule_Builder {
		return $this->with_rule( new Datetime_Validation_Rule() );
	}

	/**
	 * Adds a email validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_email(): Validation_Rule_Builder {
		return $this->with_rule( new Email_Validation_Rule() );
	}

	/**
	 * Adds a version validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_version(): Validation_Rule_Builder {
		return $this->with_rule( new Version_Validation_Rule() );
	}

	/**
	 * Adds a hex color validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_hex_color(): Validation_Rule_Builder {
		return $this->with_rule( new Hex_Color_Validation_Rule() );
	}

	/**
	 * Adds a regular expression validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @param string $regexp Regular expression to match.
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_regexp( string $regexp ): Validation_Rule_Builder {
		return $this->with_rule( new Regexp_Validation_Rule( $regexp ) );
	}

	/**
	 * Adds a URL validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function format_url(): Validation_Rule_Builder {
		return $this->with_rule( new URL_Validation_Rule() );
	}

	/**
	 * Adds a date-time or date range validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @param string  $min_datetime Minimum date-time or date allowed.
	 * @param ?string $max_datetime Optional. Maximum date-time or date allowed. Default no limit.
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 */
	public function with_datetime_range( string $min_datetime, ?string $max_datetime = null ): Validation_Rule_Builder {
		return $this->with_rule( new Datetime_Range_Validation_Rule( $min_datetime, $max_datetime ) );
	}

	/**
	 * Adds an enum validation rule.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed[] $allowed_values List of values to allow.
	 * @param bool    $strict         Optional. True to enable strict mode, false to disable it. Default false.
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function with_enum( array $allowed_values, bool $strict = false ): Validation_Rule_Builder {
		return $this->with_rule( new Enum_Validation_Rule( $allowed_values, $strict ) );
	}

	/**
	 * Checks whether the given rule is allowed by the builder.
	 *
	 * @since 0.1.0
	 *
	 * @param Validation_Rule $rule Rule to check.
	 * @return bool True if the rule is allowed, false otherwise.
	 */
	protected function is_allowed_rule( Validation_Rule $rule ): bool {
		// If no specific type support is specified, the rule supports all types.
		if ( ! $rule instanceof With_Type_Support ) {
			return true;
		}

		return $rule->supports_type( Types::TYPE_STRING );
	}
}
