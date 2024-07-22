<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Installer\Abstract_Installer_Implementation
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Data\Installer;

use Felix_Arntz\WP_OOP_Plugin_Lib\Installation\Abstract_Installer;

class Abstract_Installer_Implementation extends Abstract_Installer {

	private $install_calls   = array();
	private $upgrade_calls   = array();
	private $uninstall_calls = array();

	protected function install_data(): void {
		$this->install_calls[] = array(
			'site_id' => get_current_blog_id(),
		);
	}

	protected function upgrade_data( string $old_version ): void {
		$this->upgrade_calls[] = array(
			'site_id'     => get_current_blog_id(),
			'old_version' => $old_version,
		);
	}

	protected function uninstall_data(): void {
		$this->uninstall_calls[] = array(
			'site_id' => get_current_blog_id(),
		);
	}

	public function get_install_call_count() {
		return count( $this->install_calls );
	}

	public function get_upgrade_call_count() {
		return count( $this->upgrade_calls );
	}

	public function get_uninstall_call_count() {
		return count( $this->uninstall_calls );
	}

	public function get_last_install_call() {
		return end( $this->install_calls );
	}

	public function get_last_upgrade_call() {
		return end( $this->upgrade_calls );
	}

	public function get_last_uninstall_call() {
		return end( $this->uninstall_calls );
	}
}
