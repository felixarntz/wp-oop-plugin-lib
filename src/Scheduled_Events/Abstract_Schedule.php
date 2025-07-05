<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Abstract_Schedule
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events;

use Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Schedule;

/**
 * Base class representing a WordPress schedule.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Schedule implements Schedule {

	/**
	 * Schedule key.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $key;

	/**
	 * Schedule name.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $name;

	/**
	 * Schedule interval in seconds.
	 *
	 * @since n.e.x.t
	 * @var int
	 */
	private $interval;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 */
	public function __construct() {
		$this->key      = $this->key();
		$this->name     = $this->name();
		$this->interval = $this->interval();
	}

	/**
	 * Gets the key of the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Schedule key.
	 */
	final public function get_key(): string {
		return $this->key;
	}

	/**
	 * Gets the user-facing friendly name for the schedule.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Schedule name.
	 */
	final public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the schedule interval in seconds.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Schedule interval in seconds.
	 */
	final public function get_interval(): int {
		return $this->interval;
	}

	/**
	 * Gets the registration arguments for the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Schedule registration arguments.
	 */
	final public function get_registration_args(): array {
		return array(
			'display'  => $this->get_name(),
			'interval' => $this->get_interval(),
		);
	}

	/**
	 * Returns the key of the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Schedule key.
	 */
	abstract protected function key(): string;

	/**
	 * Returns the user-facing friendly name for the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Schedule name.
	 */
	abstract protected function name(): string;

	/**
	 * Returns the schedule interval in seconds.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Schedule interval in seconds.
	 */
	abstract protected function interval(): int;
}
