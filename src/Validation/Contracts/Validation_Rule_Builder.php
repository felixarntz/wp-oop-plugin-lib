<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts\Validation_Rule_Builder
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Contracts;

/**
 * Interface for a validation rule builder.
 *
 * Validation rule builders provide a convenience layer to compose a single validation rule out of multiple rules.
 *
 * @since n.e.x.t
 */
interface Validation_Rule_Builder {

	/**
	 * Gets the combined validation rule consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return Validation_Rule Combined validation rule.
	 */
	public function get(): Validation_Rule;

	/**
	 * Returns a WordPress option 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an option 'sanitize_callback'.
	 */
	public function get_option_sanitize_callback(): callable;

	/**
	 * Returns a WordPress REST API 'sanitize_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an REST API 'sanitize_callback'.
	 */
	public function get_rest_sanitize_callback(): callable;

	/**
	 * Returns a WordPress REST API 'validate_callback' consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Callback function to register as an REST API 'validate_callback'.
	 */
	public function get_rest_validate_callback(): callable;
}
