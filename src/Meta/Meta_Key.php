<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Meta;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Traits\Cast_Value_By_Type;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\Entity_Key_Value_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Contracts\With_Single;

/**
 * Class representing a WordPress meta key.
 *
 * @since 0.1.0
 */
class Meta_Key extends Abstract_Entity_Key_Value implements With_Registration_Args {
	use Cast_Value_By_Type;

	/**
	 * Meta key registration arguments.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	protected $registration_args;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Entity_Key_Value_Repository $repository        Repository used for the meta key.
	 * @param string                      $key               Meta key.
	 * @param array<string, mixed>        $registration_args Optional. Meta key registration arguments. Default empty
	 *                                                       array.
	 */
	public function __construct( Entity_Key_Value_Repository $repository, string $key, array $registration_args = array() ) { // phpcs:ignore Generic.Files.LineLength.TooLong
		// Extract default value from registration arguments if passed.
		$default = null;
		if ( isset( $registration_args['default'] ) ) {
			$default = $registration_args['default'];
		}

		// Set 'single' value from registration arguments if passed.
		if ( $repository instanceof With_Single && isset( $registration_args['single'] ) ) {
			$repository->set_single( $key, (bool) $registration_args['single'] );
		}

		parent::__construct( $repository, $key, $default );

		$this->registration_args = $registration_args;
	}

	/**
	 * Checks whether the meta key has a value set for the given entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return bool True if a value is set, false otherwise.
	 */
	public function has_value( int $entity_id ): bool {
		return parent::has_value( $entity_id );
	}

	/**
	 * Gets the value for the meta key for the given entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return mixed Value for the meta key.
	 */
	public function get_value( int $entity_id ) {
		$value = parent::get_value( $entity_id );

		/*
		 * By default, meta keys are assumed to have a single value. This may be overwritten via meta registration by
		 * setting the 'single' argument to `false`. In this case the value is always an array, and the individual
		 * items should be type-casted as needed.
		 */
		if ( isset( $this->registration_args['single'] ) && ! $this->registration_args['single'] ) {
			$value = (array) $value;
			if ( isset( $this->registration_args['type'] ) ) {
				$value = array_map(
					function ( $item ) {
						return $this->cast_value_by_type( $item, $this->registration_args['type'] );
					},
					$value
				);
			}
			return $value;
		}

		// Otherwise, this is a single value that should be type-casted as needed.
		if ( isset( $this->registration_args['type'] ) ) {
			return $this->cast_value_by_type( $value, $this->registration_args['type'] );
		}

		return $value;
	}

	/**
	 * Updates the value for the meta key for the given entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @param int   $entity_id Entity ID.
	 * @param mixed $value     New value to set for the meta key.
	 * @return bool True on success, false on failure.
	 */
	public function update_value( int $entity_id, $value ): bool {
		return parent::update_value( $entity_id, $value );
	}

	/**
	 * Deletes the data for the meta key for the given entity ID.
	 *
	 * @since 0.1.0
	 *
	 * @param int $entity_id Entity ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete_value( int $entity_id ): bool {
		return parent::delete_value( $entity_id );
	}

	/**
	 * Gets the meta key.
	 *
	 * @since 0.1.0
	 *
	 * @return string Meta key.
	 */
	public function get_key(): string {
		return parent::get_key();
	}

	/**
	 * Gets the registration arguments for the meta key.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Meta key registration arguments.
	 */
	public function get_registration_args(): array {
		return $this->registration_args;
	}
}
