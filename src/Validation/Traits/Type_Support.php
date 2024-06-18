<?php
/**
 * Trait Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits\Type_Support
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Validation\Traits;

/**
 * Trait that implements the With_Type_Support interface, limiting a validation rule to support specific input types.
 *
 * @since n.e.x.t
 */
trait Type_Support {

	/**
	 * Value types supported by the validation rule.
	 *
	 * This is any of the type constants from the Types interface, combined with a bitwise OR.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $supported_types = 0;

	/**
	 * Checks whether the validation rule supports values of the given type.
	 *
	 * This method is mostly for internal use, e.g. to ensure that builders don't allow rules that are useless for them.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $type One of the type constants from the Types interface.
	 * @return bool True if the given type is supported, false otherwise.
	 */
	final public function supports_type( int $type ): bool {
		if ( null === $this->supported_types ) {
			$this->supported_types = $this->get_supported_types();
		}

		return (bool) ( $this->supported_types & $type );
	}

	/**
	 * Gets the supported types for the validation rule.
	 *
	 * @since n.e.x.t
	 *
	 * @return int One or more of the type constants from the Types interface, combined with a bitwise OR.
	 */
	abstract protected function get_supported_types(): int;
}
