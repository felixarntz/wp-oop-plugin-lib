<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Abstract_Admin_Page_Link_Pointer
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Admin_Page_Link;
use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Contracts\Admin_Pointer;

/**
 * Base class representing a WP Admin Pointer that links to an admin page.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Admin_Page_Link_Pointer implements Admin_Pointer {

	/**
	 * Admin page link.
	 *
	 * @since n.e.x.t
	 * @var Admin_Page_Link
	 */
	protected $admin_page_link;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Admin_Page_Link $admin_page_link Admin page link.
	 */
	public function __construct( Admin_Page_Link $admin_page_link ) {
		$this->admin_page_link = $admin_page_link;
	}

	/**
	 * Gets the admin pointer slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin pointer slug.
	 */
	final public function get_slug(): string {
		return $this->admin_page_link->get_slug();
	}

	/**
	 * Gets the required capability to access the linked area.
	 *
	 * This can be used to check whether the link should be displayed to the current user or not.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Required capability.
	 */
	final public function get_capability(): string {
		return $this->admin_page_link->get_capability();
	}

	/**
	 * Renders the admin pointer content HTML.
	 *
	 * @since n.e.x.t
	 */
	abstract public function render(): void;

	/**
	 * Checks whether the pointer should be displayed on the current screen.
	 *
	 * The method should not check whether anything related to the current user, such as whether they have the required
	 * capability or whether they have already dismissed the pointer. This is handled separately.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $hook_suffix The current admin screen hook suffix.
	 * @return bool True if the pointer is active, false otherwise.
	 */
	public function is_active( string $hook_suffix ): bool {
		return in_array( $hook_suffix, array( 'index.php', 'plugins.php' ), true );
	}

	/**
	 * Gets the selector for the target element the pointer should be attached to.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Pointer target selector.
	 */
	public function get_target_selector(): string {
		if ( preg_match( '/([a-z0-9-]+\.php)\?page=/', $this->admin_page_link->get_url(), $matches ) ) {
			$admin_file = $matches[1];
		} else {
			$admin_file = 'admin.php';
		}

		switch ( $admin_file ) {
			case 'index.php':
				return '#menu-dashboard';
			case 'edit.php':
				return '#menu-posts';
			case 'upload.php':
				return '#menu-media';
			case 'edit-comments.php':
				return '#menu-comments';
			case 'themes.php':
				return '#menu-appearance';
			case 'plugins.php':
				return '#menu-plugins';
			case 'users.php':
				return '#menu-users';
			case 'tools.php':
				return '#menu-tools';
			case 'options-general.php':
				return '#menu-settings';
		}

		return "toplevel_page_{$this->admin_page_link->get_slug()}";
	}
}
