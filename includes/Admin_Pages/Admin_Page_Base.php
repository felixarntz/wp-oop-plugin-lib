<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Admin_Page_Base
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page;

/**
 * Base class representing a WordPress admin page.
 *
 * @since n.e.x.t
 */
abstract class Admin_Page_Base implements Admin_Page {

	/**
	 * Admin page slug.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $slug;

	/**
	 * Admin page title.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $title;

	/**
	 * Admin page capability.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $capability;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 */
	public function __construct() {
		$this->slug       = $this->slug();
		$this->title      = $this->title();
		$this->capability = $this->capability();
	}

	/**
	 * Gets the admin page slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page slug.
	 */
	final public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Gets the admin page title.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page title.
	 */
	final public function get_title(): string {
		return $this->title;
	}

	/**
	 * Gets the admin page's required capability.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page capability.
	 */
	final public function get_capability(): string {
		return $this->capability;
	}

	/**
	 * Initializes functionality for the admin page.
	 *
	 * @since n.e.x.t
	 */
	abstract public function load(): void;

	/**
	 * Renders the admin page.
	 *
	 * @since n.e.x.t
	 */
	abstract public function render(): void;

	/**
	 * Returns the admin page slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page slug.
	 */
	abstract protected function slug(): string;

	/**
	 * Returns the admin page title.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page title.
	 */
	abstract protected function title(): string;

	/**
	 * Returns the admin page's required capability.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Admin page capability.
	 */
	abstract protected function capability(): string;
}
