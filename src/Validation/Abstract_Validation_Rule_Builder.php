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
	 * Gets the combined validation rule consisting of all rules present in the builder.
	 *
	 * @since n.e.x.t
	 *
	 * @return Validation_Rule Combined validation rule.
	 */
	abstract public function get(): Validation_Rule;

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
}
