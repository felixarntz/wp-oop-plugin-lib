<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Abstract_Dependency_Registry
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies;

use Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Contracts\Dependency_Registry;

/**
 * Base class for a registry of dependencies, including support for providing manifest files.
 *
 * @since 0.1.0
 */
abstract class Abstract_Dependency_Registry implements Dependency_Registry {

	/**
	 * Parses the given dependency arguments with relevant defaults.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $args Dependency registration arguments.
	 * @return array<string, mixed> Parsed dependency registration arguments.
	 */
	final protected function parse_args( array $args ): array {
		if ( isset( $args['manifest'] ) ) {
			$args = $this->parse_manifest_args( $args['manifest'], $args );
			unset( $args['manifest'] );
		}

		// Support more verbose 'dependencies' and 'version' keys as an alternative.
		if ( isset( $args['dependencies'] ) && ! isset( $args['deps'] ) ) {
			$args['deps'] = $args['dependencies'];
			unset( $args['dependencies'] );
		}
		if ( isset( $args['version'] ) && ! isset( $args['ver'] ) ) {
			$args['ver'] = $args['version'];
			unset( $args['version'] );
		}

		return wp_parse_args(
			$args,
			array_merge(
				$this->get_additional_args_defaults(),
				array(
					'src'  => false,
					'deps' => array(),
				)
			)
		);
	}

	/**
	 * Returns defaults to parse dependency arguments with.
	 *
	 * The keys 'src' and 'deps' do not need to be included as they are universal defaults for any dependency type.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Dependency registration defaults.
	 */
	abstract protected function get_additional_args_defaults(): array;

	/**
	 * Parses the given dependency arguments with arguments returned by the given manifest PHP file.
	 *
	 * @since 0.1.0
	 *
	 * @param string               $manifest Full path of a PHP file which returns arguments for the dependency.
	 * @param array<string, mixed> $args     Dependency registration arguments.
	 * @return array<string, mixed> Parsed dependency registration arguments.
	 */
	private function parse_manifest_args( string $manifest, array $args ): array {
		$manifest_args = require $manifest;

		if ( ! is_array( $manifest_args ) ) {
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'Dependency manifest PHP file must return an array.', 'wp-oop-plugin-lib' ),
				''
			);
			return $args;
		}

		return wp_parse_args( $args, $manifest_args );
	}
}
