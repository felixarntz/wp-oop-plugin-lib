<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Schedule_Hook_Registrar
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Array_Registry;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Hook_Registrar;

/**
 * Class that adds the relevant hook to register schedules.
 *
 * @since n.e.x.t
 */
class Schedule_Hook_Registrar implements Hook_Registrar {

	/**
	 * Adds a callback that registers the schedules to the relevant hook.
	 *
	 * The callback receives a registry instance as the sole parameter, allowing to call the
	 * {@see Array_Registry::register()} method.
	 *
	 * @since n.e.x.t
	 *
	 * @param callable $register_callback Callback to register the schedules.
	 */
	public function add_register_callback( callable $register_callback ): void {
		add_filter(
			'cron_schedules',
			function ( $schedules ) use ( $register_callback ) {
				$schedule_registry = new Array_Registry( $schedules );
				$register_callback( $schedule_registry );
				return $schedule_registry->to_array();
			}
		);
	}
}
