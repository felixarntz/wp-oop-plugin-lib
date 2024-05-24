<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts;

/**
 * Interface for an admin page.
 *
 * @since n.e.x.t
 */
interface Admin_Page {

	/**
	 * Gets the admin page slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page slug.
	 */
	public function get_slug(): string;

	/**
	 * Gets the admin page title.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page title.
	 */
	public function get_title(): string;

	/**
	 * Gets the admin page's required capability.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page capability.
	 */
	public function get_capability(): string;

	/**
	 * Initializes functionality for the admin page.
	 *
	 * @since n.e.x.t
	 */
	public function load(): void;

	/**
	 * Renders the admin page.
	 *
	 * @since n.e.x.t
	 */
	public function render(): void;
}
