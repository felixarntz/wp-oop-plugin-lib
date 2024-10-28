<?php
/**
 * Interface Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Interface for a collection.
 *
 * @since 0.1.0
 */
interface Collection extends IteratorAggregate, Countable {

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return Traversable Collection iterator.
	 */
	public function getIterator(): Traversable; /* @phpstan-ignore-line */

	/**
	 * Returns the size of the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return int Collection size.
	 */
	public function count(): int;
}
