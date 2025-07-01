<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Contracts\Shortcode
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;

/**
 * Interface for a shortcode.
 *
 * @since n.e.x.t
 */
interface Shortcode extends With_Registration_Args {

	/**
	 * Gets the shortcode tag.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Shortcode tag.
	 */
	public function get_tag(): string;

	/**
	 * Gets the shortcode callback.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Shortcode callback.
	 */
	public function get_callback(): callable;
}
