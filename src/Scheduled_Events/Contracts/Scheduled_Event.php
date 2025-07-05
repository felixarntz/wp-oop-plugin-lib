<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Scheduled_Event
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts;

/**
 * Interface for a scheduled event.
 *
 * @since n.e.x.t
 */
interface Scheduled_Event {

	/**
	 * Gets the action hook to execute when the scheduled event is run.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Action hook to execute when the scheduled event is run.
	 */
	public function get_hook(): string;

	/**
	 * Gets the arguments to pass to the hook's callback function when the scheduled event is run.
	 *
	 * @since n.e.x.t
	 *
	 * @return mixed[] Array containing arguments to pass to the hook's callback function, or empty array to
	 *                 pass none.
	 */
	public function get_hook_args(): array;

	/**
	 * Gets the timestamp when the scheduled event should run.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The Unix timestamp when the scheduled event should run.
	 */
	public function get_timestamp(): int;

	/**
	 * Gets the recurrence schedule for the scheduled event.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The recurrence schedule for the scheduled event, or an empty string if it is not a recurring
	 *                event.
	 */
	public function get_recurrence(): string;

	/**
	 * Gets the interval for the scheduled event, if it is a recurring scheduled event.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The interval in seconds for the scheduled event, or 0 if it is not a recurring event.
	 */
	public function get_interval(): int;
}
