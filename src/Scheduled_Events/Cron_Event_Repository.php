<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Cron_Event_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events;

use Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Scheduled_Event;
use Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Scheduled_Event_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Exception\Invalid_Event_Data_Exception;

/**
 * Class for a repository of WordPress Cron events.
 *
 * @since n.e.x.t
 */
class Cron_Event_Repository implements Scheduled_Event_Repository {

	/**
	 * Checks whether a Cron event for the given hook and hook arguments exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $hook      Action hook to execute when the Cron event is run.
	 * @param mixed[] $hook_args Array containing arguments to pass to the hook's callback function, or empty array to
	 *                           pass none. Must match the exact $hook_args provided when scheduling the Cron event.
	 * @return bool True if the Cron event exists, false otherwise.
	 */
	public function exists( string $hook, array $hook_args ): bool {
		$event = wp_get_scheduled_event( $hook, $hook_args );
		return (bool) $event;
	}

	/**
	 * Gets the Cron event for a given hook and hook arguments from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $hook      Action hook to execute when the Cron event is run.
	 * @param mixed[] $hook_args Array containing arguments to pass to the hook's callback function, or empty array to
	 *                           pass none. Must match the exact $hook_args provided when scheduling the Cron event.
	 * @return Scheduled_Event|null The Cron event, or `null` if no value exists.
	 */
	public function get( string $hook, array $hook_args ): ?Scheduled_Event {
		$event = wp_get_scheduled_event( $hook, $hook_args );
		if ( ! $event ) {
			return null;
		}

		return new Cron_Event( $event );
	}

	/**
	 * Adds a new Cron event to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $hook       Action hook to execute when the Cron event is run.
	 * @param mixed[]              $hook_args  Array containing arguments to pass to the hook's callback function, or
	 *                                         empty array to pass none. Must match the exact $hook_args provided when
	 *                                         scheduling the Cron event.
	 * @param array<string, mixed> $event_args {
	 *     Optional. Arguments for scheduling the Cron event.
	 *
	 *     @type int    $timestamp  The Unix timestamp when the Cron event should run. Default is the current time.
	 *     @type string $recurrence If this is a recurring Cron event, the schedule at which it should recur. Default
	 *                              is none, i.e. not a recurring event.
	 * }
	 * @return bool True on success, false on failure.
	 *
	 * @throws Invalid_Event_Data_Exception Thrown when adding the Cron event fails and `WP_DEBUG` is enabled.
	 */
	public function add( string $hook, array $hook_args, array $event_args = array() ): bool {
		if ( ! isset( $event_args['timestamp'] ) || ! $event_args['timestamp'] ) {
			$event_args['timestamp'] = time();
		}

		if ( isset( $event_args['recurrence'] ) && '' !== $event_args['recurrence'] ) {
			$result = wp_schedule_event(
				$event_args['timestamp'],
				$event_args['recurrence'],
				$hook,
				$hook_args,
				true
			);
		} else {
			$result = wp_schedule_single_event(
				$event_args['timestamp'],
				$hook,
				$hook_args,
				true
			);
		}

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Event_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return false;
		}

		return (bool) $result;
	}

	/**
	 * Deletes the Cron event for a given hook and hook arguments from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string  $hook      Action hook to execute when the Cron event is run.
	 * @param mixed[] $hook_args Array containing arguments to pass to the hook's callback function, or empty array to
	 *                           pass none. Must match the exact $hook_args provided when scheduling the Cron event.
	 * @return bool True on success, false on failure.
	 */
	public function delete( string $hook, array $hook_args ): bool {
		return (bool) wp_clear_scheduled_hook( $hook, $hook_args );
	}

	/**
	 * Deletes all Cron events for a given hook from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $hook Action hook to execute when the Cron event is run.
	 * @return bool True on success, false on failure.
	 */
	public function delete_all( string $hook ): bool {
		return (bool) wp_unschedule_hook( $hook );
	}
}
