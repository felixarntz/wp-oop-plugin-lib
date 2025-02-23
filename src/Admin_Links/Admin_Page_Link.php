<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Admin_Page_Link
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Contracts\Admin_Link;
use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Menu;
use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Site_Env;

/**
 * Class representing a link to a WordPress admin page.
 *
 * @since n.e.x.t
 */
class Admin_Page_Link implements Admin_Link {

	/**
	 * WordPress admin menu containing the page.
	 *
	 * @since n.e.x.t
	 * @var Admin_Menu
	 */
	private $admin_menu;

	/**
	 * The admin page.
	 *
	 * @since n.e.x.t
	 * @var Admin_Page
	 */
	private $admin_page;

	/**
	 * Site environment.
	 *
	 * @since n.e.x.t
	 * @var Site_Env
	 */
	private $site_env;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Admin_Menu $admin_menu ordPress admin menu.
	 * @param Admin_Page $admin_page The admin page.
	 * @param Site_Env   $site_env   Site environment.
	 */
	public function __construct( Admin_Menu $admin_menu, Admin_Page $admin_page, Site_Env $site_env ) {
		$this->admin_menu = $admin_menu;
		$this->admin_page = $admin_page;
		$this->site_env   = $site_env;
	}

	/**
	 * Gets the admin link slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin link slug.
	 */
	public function get_slug(): string {
		return $this->admin_page->get_slug();
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
	public function get_capability(): string {
		return $this->admin_page->get_capability();
	}

	/**
	 * Gets the admin link URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The admin link URL.
	 */
	public function get_url(): string {
		$menu_slug = $this->admin_menu->get_slug();
		if ( str_ends_with( $menu_slug, '.php' ) ) {
			$menu_file = $menu_slug;
		} else {
			$menu_file = 'admin.php';
		}

		return add_query_arg(
			'page',
			$this->admin_page->get_slug(),
			$this->site_env->admin_url( $menu_file )
		);
	}

	/**
	 * Gets the admin link label.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The admin link label.
	 */
	public function get_label(): string {
		return $this->admin_page->get_title();
	}
}
