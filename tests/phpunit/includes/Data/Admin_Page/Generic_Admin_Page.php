<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page\Generic_Admin_Page
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Contracts\Admin_Page;

class Generic_Admin_Page implements Admin_Page {

	private $args;

	public function __construct( array $args ) {
		$this->args = wp_parse_args(
			$args,
			array(
				'slug'            => '',
				'title'           => '',
				'capability'      => '',
				'load_callback'   => null,
				'render_callback' => null,
			)
		);
	}

	public function get_slug(): string {
		return $this->args['slug'];
	}

	public function get_title(): string {
		return $this->args['title'];
	}

	public function get_capability(): string {
		return $this->args['capability'];
	}

	public function load(): void {
		if ( ! $this->args['load_callback'] ) {
			return;
		}
		$this->args['load_callback']();
	}

	public function render(): void {
		if ( ! $this->args['render_callback'] ) {
			return;
		}
		$this->args['render_callback']();
	}
}
