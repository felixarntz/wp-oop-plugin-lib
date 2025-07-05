<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Schedule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\With_Registration_Args;

/**
 * Interface for a WordPress schedule.
 *
 * @since n.e.x.t
 */
interface Schedule extends With_Key, With_Registration_Args {

	/**
	 * Gets the user-facing friendly name for the schedule.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Schedule name.
	 */
	public function get_name(): string;

	/**
	 * Gets the schedule interval in seconds.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Schedule interval in seconds.
	 */
	public function get_interval(): int;
}
