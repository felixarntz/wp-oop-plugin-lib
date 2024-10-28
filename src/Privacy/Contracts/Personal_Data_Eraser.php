<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts\Personal_Data_Eraser
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;

/**
 * Interface for a WordPress personal data eraser.
 *
 * @since 0.1.0
 */
interface Personal_Data_Eraser extends With_Key, With_Registration_Args {

	/**
	 * Gets the user-facing friendly name for the data eraser.
	 *
	 * @since 0.1.0
	 *
	 * @return string Data eraser name.
	 */
	public function get_name(): string;

	/**
	 * Erases personal data for the given email address and page.
	 *
	 * @since 0.1.0
	 *
	 * @param string $email_address Email address to erase personal data for.
	 * @param int    $page          Optional. Page index. A higher value than 1 is only passed if the overall process
	 *                              requires more than one request, typically to avoid timeouts.
	 * @return array<string, mixed> {
	 *     Data erasure results.
	 *
	 *     @type bool     $items_removed  Whether items were actually removed.
	 *     @type bool     $items_retained Whether items were retained.
	 *     @type string[] $messages       An array of messages to add to the personal data export file.
	 *     @type bool     $done           Whether the eraser is finished.
	 * }
	 */
	public function erase_data( string $email_address, int $page = 1 ): array;
}
