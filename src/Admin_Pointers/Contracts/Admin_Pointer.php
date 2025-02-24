<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Contracts\Admin_Pointer
 *
 * @since 0.2.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Contracts;

/**
 * Interface for a WP Admin pointer.
 *
 * @since 0.2.0
 */
interface Admin_Pointer {

	/**
	 * Gets the admin pointer slug.
	 *
	 * @since 0.2.0
	 *
	 * @return string Admin pointer slug.
	 */
	public function get_slug(): string;

	/**
	 * Gets the required capability to access the linked area.
	 *
	 * This can be used to check whether the link should be displayed to the current user or not.
	 *
	 * @since 0.2.0
	 *
	 * @return string Required capability.
	 */
	public function get_capability(): string;

	/**
	 * Renders the admin pointer content HTML.
	 *
	 * @since 0.2.0
	 */
	public function render(): void;

	/**
	 * Checks whether the pointer should be displayed on the current screen.
	 *
	 * The method should not check whether anything related to the current user, such as whether they have the required
	 * capability or whether they have already dismissed the pointer. This is handled separately.
	 *
	 * @since 0.2.0
	 *
	 * @param string $hook_suffix The current admin screen hook suffix.
	 * @return bool True if the pointer is active, false otherwise.
	 */
	public function is_active( string $hook_suffix ): bool;

	/**
	 * Gets the selector for the target element the pointer should be attached to.
	 *
	 * @since 0.2.0
	 *
	 * @return string Pointer target selector.
	 */
	public function get_target_selector(): string;
}
