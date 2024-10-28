<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts\Personal_Data_Exporter
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;

/**
 * Interface for a WordPress personal data exporter.
 *
 * @since 0.1.0
 */
interface Personal_Data_Exporter extends With_Key, With_Registration_Args {

	/**
	 * Gets the user-facing friendly name for the data exporter.
	 *
	 * @since 0.1.0
	 *
	 * @return string Data exporter name.
	 */
	public function get_name(): string;

	/**
	 * Exports personal data for the given email address and page.
	 *
	 * @since 0.1.0
	 *
	 * @param string $email_address Email address to export personal data for.
	 * @param int    $page          Optional. Page index. A higher value than 1 is only passed if the overall process
	 *                              requires more than one request, typically to avoid timeouts.
	 * @return array<string, mixed> {
	 *     Data export results.
	 *
	 *     @type array<string, mixed>[] $data An array of data groups to export, including their data.
	 *     @type bool                   $done Whether the exporter is finished.
	 * }
	 */
	public function export_data( string $email_address, int $page = 1 ): array;
}
