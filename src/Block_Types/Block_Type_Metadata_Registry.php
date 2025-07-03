<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Block_Types\Block_Type_Metadata_Util
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Block_Types;

/**
 * Class with utilities to register block types from metadata.
 *
 * @since n.e.x.t
 */
class Block_Type_Metadata_Util {

	/**
	 * Registers a block type from the metadata stored in the `block.json` file.
	 *
	 * For better performance, especially in case you need to register multiple block types, it is recommended to use
	 * {@see Block_Type_Metadata_Util::register_block_types_from_metadata_collection()} instead.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $file_or_folder Path to the `block.json` file or its parent directory.
	 * @return bool True on success, false on failure.
	 */
	public function register_block_type_from_metadata( string $file_or_folder ): bool {
		return (bool) register_block_type_from_metadata( $file_or_folder );
	}

	/**
	 * Registers all block types from a block metadata collection.
	 *
	 * A metadata collection requires a built PHP manifest file containing all data from the individual `block.json`
	 * files from each block type.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $path     The absolute base path for the collection ( e.g., WP_PLUGIN_DIR . '/my-plugin/blocks/' ).
	 * @param string $manifest The absolute path to the manifest file containing the metadata collection.
	 * @return bool True on success, false on failure.
	 */
	public function register_block_types_from_metadata_collection( string $path, string $manifest ): bool {
		// Introduced in WordPress 6.7.
		if ( class_exists( 'WP_Block_Metadata_Registry' ) ) {
			/*
			 * This does not actually register block types, but is helpful for performance.
			 * We could also use `wp_register_block_metadata_collection()` here, but it does not return a value.
			 */
			if ( ! \WP_Block_Metadata_Registry::register_collection( $path, $manifest ) ) {
				return false;
			}

			// Introduced in WordPress 6.8.
			if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
				// Provide only the $path here to rely on the collection registered above.
				wp_register_block_types_from_metadata_collection( $path );
				return true;
			}
		} elseif ( ! file_exists( $manifest ) ) { // Basic check taken over from Core implementation.
			_doing_it_wrong(
				__METHOD__,
				esc_html__( 'The specified manifest file does not exist.', 'default' ),
				'6.7.0'
			);
			return false;
		}

		$manifest_data            = require $manifest;
		$path_with_trailing_slash = trailingslashit( $path );
		foreach ( array_keys( $manifest_data ) as $block_type ) {
			register_block_type_from_metadata( $path_with_trailing_slash . $block_type );
		}

		return true;
	}
}
