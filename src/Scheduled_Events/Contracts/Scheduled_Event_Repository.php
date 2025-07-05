<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Scheduled_Event_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts;

/**
 * Interface for a repository of scheduled events.
 *
 * @since n.e.x.t
 */
interface Scheduled_Event_Repository {

	/**
	 * Checks whether a scheduled event for the given hook and hook arguments exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $hook      Action hook to execute when the scheduled event is run.
	 * @param mixed[] $hook_args Array containing arguments to pass to the hook's callback function, or empty array to
	 *                           pass none. Must match the exact $hook_args provided when scheduling the scheduled
	 *                           event.
	 * @return bool True if the scheduled event exists, false otherwise.
	 */
	public function exists( string $hook, array $hook_args ): bool;

	/**
	 * Gets the scheduled event for a given hook and hook arguments from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $hook      Action hook to execute when the scheduled event is run.
	 * @param mixed[] $hook_args Array containing arguments to pass to the hook's callback function, or empty array to
	 *                           pass none. Must match the exact $hook_args provided when scheduling the scheduled
	 *                           event.
	 * @return Scheduled_Event|null The scheduled event, or `null` if no value exists.
	 */
	public function get( string $hook, array $hook_args ): ?Scheduled_Event;

	/**
	 * Adds a new scheduled event to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $hook       Action hook to execute when the scheduled event is run.
	 * @param mixed[]              $hook_args  Array containing arguments to pass to the hook's callback function, or
	 *                                         empty array to pass none. Must match the exact $hook_args provided when
	 *                                         scheduling the scheduled event.
	 * @param array<string, mixed> $event_args {
	 *     Optional. Arguments for scheduling the scheduled event.
	 *
	 *     @type int    $timestamp  The Unix timestamp when the scheduled event should run. Default is the current
	 *                              time.
	 *     @type string $recurrence If this is a recurring scheduled event, the schedule at which it should recur.
	 *                              Default is none, i.e. not a recurring event.
	 * }
	 * @return bool True on success, false on failure.
	 */
	public function add( string $hook, array $hook_args, array $event_args = array() ): bool;

	/**
	 * Deletes the scheduled event for a given hook and hook arguments from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $hook      Action hook to execute when the scheduled event is run.
	 * @param mixed[] $hook_args Array containing arguments to pass to the hook's callback function, or empty array to
	 *                           pass none. Must match the exact $hook_args provided when scheduling the scheduled
	 *                           event.
	 * @return bool True on success, false on failure.
	 */
	public function delete( string $hook, array $hook_args ): bool;

	/**
	 * Deletes all scheduled events for a given hook from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $hook Action hook to execute when the scheduled event is run.
	 * @return bool True on success, false on failure.
	 */
	public function delete_all( string $hook ): bool;
}
