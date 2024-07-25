<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Abstract_Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation;

use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule_Builder;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Exception\Validation_Exception;
use Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Rules\Aggregate_Validation_Rule;
use InvalidArgumentException;
use WP_Error;

/**
 * Base class for a validation rule builder.
 *
 * Validation rule builders provide a convenience layer to compose a single validation rule out of multiple rules.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Validation_Rule_Builder implements Validation_Rule_Builder {

	/**
	 * Validation rules set for this instance.
	 *
	 * @since n.e.x.t
	 * @var Validation_Rule[]
	 */
	private $rules = array();

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
	 * Adds the given rule to the rules for the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $rule Rule to add.
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 *
	 * @throws InvalidArgumentException Thrown when a forbidden rule is passed.
	 */
	final public function with_rule( Validation_Rule $rule ): Validation_Rule_Builder {
		if ( ! $this->is_allowed_rule( $rule ) ) {
			throw new InvalidArgumentException(
				sprintf(
					/* translators: 1: rule PHP class name, 2: builder PHP class name */
					esc_html__( 'The validation rule with class %1$s is not allowed by the builder class %2$s.', 'wp-oop-plugin-lib' ), // phpcs:ignore Generic.Files.LineLength.TooLong
					esc_html( get_class( $rule ) ),
					esc_html( get_class( $this ) )
				)
			);
		}
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
	final public function get(): Validation_Rule {
		return new Aggregate_Validation_Rule( $this->rules );
	}

	/**
	 * Returns a WordPress option 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an option 'sanitize_callback'.
	 */
	final public function get_option_sanitize_callback(): callable {
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
	final public function get_rest_sanitize_callback(): callable {
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
	final public function get_rest_validate_callback(): callable {
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
	 * Returns the array of validation rules in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return Validation_Rule[] Validation rules for the builder instance.
	 */
	final protected function get_rules_array(): array {
		return $this->rules;
	}

	/**
	 * Checks whether the given rule is allowed by the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @param Validation_Rule $rule Rule to check.
	 * @return bool True if the rule is allowed, false otherwise.
	 */
	abstract protected function is_allowed_rule( Validation_Rule $rule ): bool;
}
