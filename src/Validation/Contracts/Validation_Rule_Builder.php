<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule_Builder
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

use InvalidArgumentException;

/**
 * Interface for a validation rule builder.
 *
 * Validation rule builders provide a convenience layer to compose a single validation rule out of multiple rules.
 *
 * @since 0.1.0
 */
interface Validation_Rule_Builder {

	/**
	 * Adds the given rule to the rules for the builder.
	 *
	 * @since 0.1.0
	 *
	 * @param Validation_Rule $rule Rule to add.
	 * @return Validation_Rule_Builder Builder instance for chaining.
	 *
	 * @throws InvalidArgumentException Thrown when a forbidden rule is passed.
	 */
	public function with_rule( Validation_Rule $rule ): Validation_Rule_Builder;

	/**
	 * Gets the combined validation rule consisting of all rules present in the builder.
	 *
	 * @since 0.1.0
	 *
	 * @return Validation_Rule Combined validation rule.
	 */
	public function get(): Validation_Rule;

	/**
	 * Returns a WordPress option 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since 0.1.0
	 *
	 * @return callable Callback function to register as an option 'sanitize_callback'.
	 */
	public function get_option_sanitize_callback(): callable;

	/**
	 * Returns a WordPress REST API 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since 0.1.0
	 *
	 * @return callable Callback function to register as an REST API 'sanitize_callback'.
	 */
	public function get_rest_sanitize_callback(): callable;

	/**
	 * Returns a WordPress REST API 'validate_callback' consisting of all rules present in the builder.
	 *
	 * @since 0.1.0
	 *
	 * @return callable Callback function to register as an REST API 'validate_callback'.
	 */
	public function get_rest_validate_callback(): callable;
}
