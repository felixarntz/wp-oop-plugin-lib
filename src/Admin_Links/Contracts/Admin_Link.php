<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Contracts\Admin_Link
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Contracts;

/**
 * Interface for a WP Admin link.
 *
 * @since n.e.x.t
 */
interface Admin_Link {

	/**
	 * Gets the admin link slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin link slug.
	 */
	public function get_slug(): string;

	/**
	 * Gets the required capability to access the linked area.
	 *
	 * This can be used to check whether the link should be displayed to the current user or not.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Required capability.
	 */
	public function get_capability(): string;

	/**
	 * Gets the admin link URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The admin link URL.
	 */
	public function get_url(): string;

	/**
	 * Gets the admin link label.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The admin link label.
	 */
	public function get_label(): string;
}
