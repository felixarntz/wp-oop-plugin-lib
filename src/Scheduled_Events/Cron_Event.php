<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Cron_Event
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events;

use Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Scheduled_Event;

/**
 * Class representing a WordPress Cron event.
 *
 * @since n.e.x.t
 */
class Cron_Event implements Scheduled_Event {

	/**
	 * Underlying event object as returned by `wp_get_scheduled_event()`.
	 *
	 * @since n.e.x.t
	 * @var object
	 */
	private $wp_obj;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param object $post Underlying event object as returned by `wp_get_scheduled_event()`.
	 */
	public function __construct( object $post ) {
		$this->wp_obj = $post;
	}

	/**
	 * Gets the action hook to execute when the scheduled event is run.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Action hook to execute when the scheduled event is run.
	 */
	public function get_hook(): string {
		return $this->wp_obj->hook ?? '';
	}

	/**
	 * Gets the arguments to pass to the hook's callback function when the scheduled event is run.
	 *
	 * @since n.e.x.t
	 *
	 * @return mixed[] Array containing arguments to pass to the hook's callback function, or empty array to
	 *                 pass none.
	 */
	public function get_hook_args(): array {
		return $this->wp_obj->args ?? array();
	}

	/**
	 * Gets the timestamp when the scheduled event should run.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The Unix timestamp when the scheduled event should run.
	 */
	public function get_timestamp(): int {
		if ( ! isset( $this->wp_obj->timestamp ) ) {
			return 0;
		}

		return (int) $this->wp_obj->timestamp;
	}

	/**
	 * Gets the recurrence schedule for the scheduled event.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The recurrence schedule for the scheduled event, or an empty string if it is not a recurring
	 *                event.
	 */
	public function get_recurrence(): string {
		if ( ! isset( $this->wp_obj->schedule ) || ! $this->wp_obj->schedule ) {
			return '';
		}

		return (string) $this->wp_obj->schedule;
	}

	/**
	 * Gets the interval for the scheduled event, if it is a recurring scheduled event.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The interval in seconds for the scheduled event, or 0 if it is not a recurring event.
	 */
	public function get_interval(): int {
		if ( ! isset( $this->wp_obj->interval ) ) {
			return 0;
		}

		return (int) $this->wp_obj->interval;
	}
}
