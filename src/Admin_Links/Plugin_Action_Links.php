<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Plugin_Action_Links
 *
 * @since 0.2.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Current_User;

/**
 * Class for displaying a collection of links as plugin action links.
 *
 * @since 0.2.0
 */
class Plugin_Action_Links {

	/**
	 * Admin link collection.
	 *
	 * @since 0.2.0
	 * @var Admin_Link_Collection
	 */
	private $admin_link_collection;

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
	 * @param Admin_Link_Collection $admin_link_collection Admin link collection.
	 * @param Current_User          $current_user          Current user object.
	 */
	public function __construct( Admin_Link_Collection $admin_link_collection, Current_User $current_user ) {
		$this->admin_link_collection = $admin_link_collection;
		$this->current_user          = $current_user;
	}

	/**
	 * Gets the links as HTML `a` tags, keyed by their slug.
	 *
	 * Only links that the current user has the required capability for will be included.
	 *
	 * @since 0.2.0
	 *
	 * @return string[] HTML `a` tags, keyed by their slug.
	 */
	public function get_tags(): array {
		$tags = array();
		foreach ( $this->admin_link_collection as $admin_link ) {
			if ( ! $this->current_user->has_cap( $admin_link->get_capability() ) ) {
				continue;
			}
			$tags[ $admin_link->get_slug() ] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $admin_link->get_url() ),
				esc_html( $admin_link->get_label() )
			);
		}
		return $tags;
	}
}
