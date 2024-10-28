<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts;

/**
 * Interface for an admin page.
 *
 * @since 0.1.0
 */
interface Admin_Page {

	/**
	 * Gets the admin page slug.
	 *
	 * @since 0.1.0
	 *
	 * @return string Admin page slug.
	 */
	public function get_slug(): string;

	/**
	 * Gets the admin page title.
	 *
	 * @since 0.1.0
	 *
	 * @return string Admin page title.
	 */
	public function get_title(): string;

	/**
	 * Gets the admin page's required capability.
	 *
	 * @since 0.1.0
	 *
	 * @return string Admin page capability.
	 */
	public function get_capability(): string;

	/**
	 * Initializes functionality for the admin page.
	 *
	 * @since 0.1.0
	 */
	public function load(): void;

	/**
	 * Renders the admin page.
	 *
	 * @since 0.1.0
	 */
	public function render(): void;
}
