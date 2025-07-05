<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Schedule_Collection
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use Felix_Arntz\WP_OOP_Plugin_Lib\Scheduled_Events\Contracts\Schedule;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of schedules.
 *
 * @since n.e.x.t
 */
class Schedule_Collection implements Collection {

	/**
	 * Schedules stored in the collection.
	 *
	 * @since n.e.x.t
	 * @var Schedule[]
	 */
	private $schedules = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Schedule[] $schedules List of schedule instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given schedules are invalid.
	 */
	public function __construct( array $schedules ) {
		foreach ( $schedules as $schedule ) {
			if ( ! $schedule instanceof Schedule ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid schedule provided for schedule collection.', 'wp-oop-plugin-lib' )
				);
			}
			$this->schedules[] = $schedule;
		}
	}

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since n.e.x.t
	 *
	 * @return ArrayIterator<int, Schedule> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->schedules );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->schedules );
	}
}
