<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page\Abstract_Admin_Page_Implementation_With_Args
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Admin_Page;

use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pages\Abstract_Admin_Page;

class Abstract_Admin_Page_Implementation_With_Args extends Abstract_Admin_Page {

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
		parent::__construct();
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

	protected function slug(): string {
		return $this->args['slug'];
	}

	protected function title(): string {
		return $this->args['title'];
	}

	protected function capability(): string {
		return $this->args['capability'];
	}
}
