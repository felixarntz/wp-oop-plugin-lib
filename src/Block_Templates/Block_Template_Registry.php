<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Block_Templates\Block_Template_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Block_Templates;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Registry;
use WP_Block_Template;
use WP_Block_Templates_Registry;

/**
 * Class for a registry of WordPress block templates.
 *
 * @since n.e.x.t
 */
class Block_Template_Registry implements Registry {

	/**
	 * Registers a block template with the given key and arguments.
	 *
	 * The "key" should be the block template name.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Block template name, including namespace.
	 * @param array<string, mixed> $args Block template registration arguments.
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		if ( ! $this->support_check( __METHOD__ ) ) {
			return false;
		}

		return (bool) WP_Block_Templates_Registry::get_instance()->register( $key, $args );
	}

	/**
	 * Checks whether a block template with the given key is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Block template name, including namespace.
	 * @return bool True if the block template is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		if ( ! $this->support_check( __METHOD__ ) ) {
			return false;
		}

		return WP_Block_Templates_Registry::get_instance()->is_registered( $key );
	}

	/**
	 * Gets the registered block template for the given key from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Block template name, including namespace.
	 * @return WP_Block_Template|null The registered block template definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		if ( ! $this->support_check( __METHOD__ ) ) {
			return null;
		}

		return WP_Block_Templates_Registry::get_instance()->get_registered( $key );
	}

	/**
	 * Gets all block templates from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, WP_Block_Template> Associative array of keys and their block template definitions, or
	 *                                          empty array if nothing is registered.
	 */
	public function get_all_registered(): array {
		if ( ! $this->support_check( __METHOD__ ) ) {
			return array();
		}

		return WP_Block_Templates_Registry::get_instance()->get_all_registered();
	}

	/**
	 * Utility to check whether block template registration is supported by the current WordPress version.
	 *
	 * It also triggers a PHP notice if the functionality is not supported.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $method The method that was called. Used to trigger a PHP notice as applicable.
	 * @return bool True whether the functionality is supported, false otherwise.
	 */
	private function support_check( string $method ): bool {
		if ( ! class_exists( WP_Block_Templates_Registry::class ) ) {
			_doing_it_wrong(
				// The $method parameter is safe to use as it is always __METHOD__, called internally by this class.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$method,
				esc_html__( 'Block template registration is not supported by the current WordPress version. Block template registration functionality was added in WordPress 6.7.', 'wp-oop-plugin-lib' ), // phpcs:ignore Generic.Files.LineLength.TooLong
				''
			);
			return false;
		}
		return true;
	}
}
