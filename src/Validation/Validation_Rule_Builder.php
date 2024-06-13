<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use InvalidArgumentException;
use WP_Error;

/**
 * Class providing a convenience layer to easily compose an aggregate validation rule out of multiple rules.
 *
 * @since n.e.x.t
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class Validation_Rule_Builder {

	/**
	 * Validation rules set for this instance.
	 *
	 * @since n.e.x.t
	 * @var Validation_Rule[]
	 */
	private $rules = array();

	/**
	 * Denylist for certain validation rules, with class names as keys and a simple true as values.
	 *
	 * @since n.e.x.t
	 * @var array<string, bool>
	 */
	private $denylist = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule[] $initial_rules Optional. Initial validation rules to use for the builder.
	 */
	public function __construct( array $initial_rules = array() ) {
		array_walk(
			$initial_rules,
			array( $this, 'with_rule' )
		);
	}

	/**
	 * Adds a boolean validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_boolean( bool $strict = false ): self {
		return $this->with_rule( new Boolean_Validation_Rule( $strict ) );
	}

	/**
	 * Adds a float validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_float( bool $strict = false ): self {
		return $this->with_rule( new Float_Validation_Rule( $strict ) );
	}

	/**
	 * Adds an integer validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_integer( bool $strict = false ): self {
		return $this->with_rule( new Integer_Validation_Rule( $strict ) );
	}

	/**
	 * Adds a string validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $strict Optional. True to enable strict mode, false to disable it. Default false.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function require_string( bool $strict = false ): self {
		return $this->with_rule( new String_Validation_Rule( $strict ) );
	}

	/**
	 * Adds a date validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return self Builder instance for chaining.
	 */
	public function format_date(): self {
		return $this->with_rule( new Date_Validation_Rule() );
	}

	/**
	 * Adds a date-time validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return self Builder instance for chaining.
	 */
	public function format_datetime(): self {
		return $this->with_rule( new Datetime_Validation_Rule() );
	}

	/**
	 * Adds a email validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return self Builder instance for chaining.
	 */
	public function format_email(): self {
		return $this->with_rule( new Email_Validation_Rule() );
	}

	/**
	 * Adds a hex color validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return self Builder instance for chaining.
	 */
	public function format_hex_color(): self {
		return $this->with_rule( new Hex_Color_Validation_Rule() );
	}

	/**
	 * Adds a regular expression validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $regexp Regular expression to match.
	 * @return self Builder instance for chaining.
	 */
	public function format_regexp( string $regexp ): self {
		return $this->with_rule( new Regexp_Validation_Rule( $regexp ) );
	}

	/**
	 * Adds a URL validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return self Builder instance for chaining.
	 */
	public function format_url(): self {
		return $this->with_rule( new URL_Validation_Rule() );
	}

	/**
	 * Adds a date-time or date range validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $min_datetime Minimum date-time or date allowed.
	 * @param string $max_datetime Optional. Maximum date-time or date allowed. Default no limit.
	 * @return self Builder instance for chaining.
	 */
	public function with_datetime_range( string $min_datetime, string $max_datetime = null ): self {
		return $this->with_rule( new Datetime_Range_Validation_Rule( $min_datetime, $max_datetime ) );
	}

	/**
	 * Adds a numeric range validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param int|float $min Minimum value allowed.
	 * @param int|float $max Optional. Maximum value allowed. Default no limit.
	 * @return self Builder instance for chaining.
	 */
	public function with_numeric_range( $min, $max = null ): self {
		return $this->with_rule( new Numeric_Range_Validation_Rule( $min, $max ) );
	}

	/**
	 * Adds an enum validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @param mixed[] $allowed_values List of values to allow.
	 * @param bool    $strict         Optional. True to enable strict mode, false to disable it. Default false.
	 * @return self Builder instance for chaining.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function with_enum( array $allowed_values, bool $strict = false ): self {
		return $this->with_rule( new Enum_Validation_Rule( $allowed_values, $strict ) );
	}

	/**
	 * Adds the given rule to the rules for the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $rule Rule to add.
	 * @return self Builder instance for chaining.
	 *
	 * @throws InvalidArgumentException Thrown when a forbidden rule is passed.
	 */
	public function with_rule( Validation_Rule $rule ): self {
		if ( $this->is_rule_on_denylist( $rule ) ) {
			throw new InvalidArgumentException(
				sprintf(
					/* translators: %s: PHP class name */
					esc_html__( 'The validation rule with class %s cannot be added as it conflicts with another validation rule in the builder.', 'wp-oop-plugin-lib' ),
					esc_html( get_class( $rule ) )
				)
			);
		}
		$this->maybe_update_denylist( $rule );
		$this->rules[] = $rule;
		return $this;
	}

	/**
	 * Gets the aggregate validation rule consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return Validation_Rule Aggregate validation rule.
	 */
	public function get(): Validation_Rule {
		return new Aggregate_Validation_Rule( $this->rules );
	}

	/**
	 * Returns a WordPress option 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an option 'sanitize_callback'.
	 */
	public function get_option_sanitize_callback(): callable {
		$aggregate = $this->get();

		return function ( $value ) use ( $aggregate ) {
			return $aggregate->sanitize( $value );
		};
	}

	/**
	 * Returns a WordPress REST API 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an REST API 'sanitize_callback'.
	 */
	public function get_rest_sanitize_callback(): callable {
		// For now, this callback looks the same as for sanitizing an option.
		return $this->get_option_sanitize_callback();
	}

	/**
	 * Returns a WordPress REST API 'validate_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an REST API 'validate_callback'.
	 */
	public function get_rest_validate_callback(): callable {
		$aggregate = $this->get();

		return function ( $value, $request, $param ) use ( $aggregate ) {
			try {
				$aggregate->validate( $value );
			} catch ( Validation_Exception $e ) {
				return new WP_Error(
					$e->get_error_code(),
					sprintf(
						/* translators: 1: param name, 2: validation exception message */
						esc_html__( 'Validation for %1$s failed: %2$s', 'wp-oop-plugin-lib' ),
						esc_html( $param ),
						$e->getMessage()
					),
					array( 'param' => $param )
				);
			}
			return true;
		};
	}

	/**
	 * Checks whether the given rule is on the denylist.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $rule Rule to check denylist for.
	 * @return bool True if the rule is on the denylist, false otherwise.
	 */
	private function is_rule_on_denylist( Validation_Rule $rule ): bool {
		return isset( $this->denylist[ get_class( $rule ) ] );
	}

	/**
	 * Updates the denylist as needed, based on the given rule to be added to the builder's set of rules.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $rule Rule that is being added to the builder's set of rules.
	 */
	private function maybe_update_denylist( Validation_Rule $rule ): void {
		$rule_classname = get_class( $rule );
		switch ( $rule_classname ) {
			case String_Validation_Rule::class:
			case Date_Validation_Rule::class:
			case Datetime_Validation_Rule::class:
			case Email_Validation_Rule::class:
			case Hex_Color_Validation_Rule::class:
			case URL_Validation_Rule::class:
				$this->update_denylist(
					array(
						Boolean_Validation_Rule::class,
						Float_Validation_Rule::class,
						Integer_Validation_Rule::class,
					)
				);
				break;
			case Boolean_Validation_Rule::class:
			case Float_Validation_Rule::class:
			case Integer_Validation_Rule::class:
				$new_denylist_classnames = array(
					Boolean_Validation_Rule::class,
					Float_Validation_Rule::class,
					Integer_Validation_Rule::class,
					String_Validation_Rule::class,
					Date_Validation_Rule::class,
					Datetime_Validation_Rule::class,
					Email_Validation_Rule::class,
					Hex_Color_Validation_Rule::class,
					URL_Validation_Rule::class,
				);
				array_splice(
					$new_denylist_classnames,
					array_search( $rule_classname, $new_denylist_classnames, true ),
					1
				);
				$this->update_denylist( $new_denylist_classnames );
				break;
		}
	}

	/**
	 * Updates the denylist with the given rule class names.
	 *
	 * @since n.e.x.t
	 *
	 * @param string[] $rule_classnames Fully qualified class names of validation rules to add to the denylist.
	 */
	private function update_denylist( array $rule_classnames ): void {
		foreach ( $rule_classnames as $rule_classname ) {
			$this->denylist[ $rule_classname ] = true;
		}
	}
}
