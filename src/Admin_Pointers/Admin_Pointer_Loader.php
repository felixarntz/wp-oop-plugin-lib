<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Admin_Pointer_Loader
 *
 * @since 0.2.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers;

use Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Script_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Style_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Current_User;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;

/**
 * Class for loading admin pointers.
 *
 * @since 0.2.0
 */
class Admin_Pointer_Loader {

	/**
	 * Admin pointer collection.
	 *
	 * @since 0.2.0
	 * @var Admin_Pointer_Collection
	 */
	private $admin_pointer_collection;

	/**
	 * Script registry.
	 *
	 * @since 0.2.0
	 * @var Script_Registry
	 */
	private $script_registry;

	/**
	 * Style registry.
	 *
	 * @since 0.2.0
	 * @var Style_Registry
	 */
	private $style_registry;

	/**
	 * Dismissed admin pointers.
	 *
	 * @since 0.2.0
	 * @var Dismissed_Admin_Pointers
	 */
	private $dismissed_pointers;

	/**
	 * Current user object.
	 *
	 * @since 0.2.0
	 * @var Current_User
	 */
	private $current_user;

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param Admin_Pointer_Collection $admin_pointer_collection Admin pointer collection.
	 * @param Script_Registry          $script_registry          Script registry.
	 * @param Style_Registry           $style_registry           Style registry.
	 * @param Meta_Repository          $meta_repository          User meta repository.
	 * @param Current_User             $current_user             Current user object.
	 */
	public function __construct(
		Admin_Pointer_Collection $admin_pointer_collection,
		Script_Registry $script_registry,
		Style_Registry $style_registry,
		Meta_Repository $meta_repository,
		Current_User $current_user
	) {
		$this->admin_pointer_collection = $admin_pointer_collection;
		$this->script_registry          = $script_registry;
		$this->style_registry           = $style_registry;
		$this->current_user             = $current_user;
		$this->dismissed_pointers       = new Dismissed_Admin_Pointers(
			$this->current_user->get_id(),
			$meta_repository
		);
	}

	/**
	 * Loads the admin pointers in the collection that are active on the current screen and haven't been dismissed yet.
	 *
	 * In case of any relevant pointers, the 'wp-pointer' script will be enqueued, with inline scripts added for each
	 * pointer.
	 *
	 * @since 0.2.0
	 *
	 * @param string $hook_suffix The current admin screen hook suffix.
	 */
	public function load_pointers( string $hook_suffix ): void {
		if ( '' === $hook_suffix ) {
			return;
		}

		$has_pointers = false;
		foreach ( $this->admin_pointer_collection as $admin_pointer ) {
			if ( $this->dismissed_pointers->is_pointer_dismissed( $admin_pointer->get_slug() ) ) {
				continue;
			}

			if ( ! $this->current_user->has_cap( $admin_pointer->get_capability() ) ) {
				continue;
			}

			if ( ! $admin_pointer->is_active( $hook_suffix ) ) {
				continue;
			}

			$has_pointers = true;

			ob_start();
			$admin_pointer->render();
			$admin_pointer_content = trim( ob_get_clean() );

			$script = sprintf(
				'
				jQuery( function() {
					var options = {
						content: %1$s,
						position: "top",
						pointerWidth: 420,
						close: function() {
							jQuery.post(
								window.ajaxurl,
								{
									pointer: %2$s,
									action:  "dismiss-wp-pointer",
								}
							);
						}
					};

					jQuery( %3$s ).pointer( options ).pointer( "open" );
				} );
				',
				wp_json_encode( $admin_pointer_content ),
				wp_json_encode( $admin_pointer->get_slug() ),
				wp_json_encode( $admin_pointer->get_target_selector() )
			);

			$this->script_registry->add_inline_code( 'wp-pointer', $script );
		}

		if ( ! $has_pointers ) {
			return;
		}

		$this->style_registry->enqueue( 'wp-pointer' );
		$this->script_registry->enqueue( 'wp-pointer' );
	}
}
