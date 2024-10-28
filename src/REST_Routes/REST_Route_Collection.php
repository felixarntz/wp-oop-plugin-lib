<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\REST_Route_Collection
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use Felix_Arntz\WP_OOP_Plugin_Lib\REST_Routes\Contracts\REST_Route;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of REST routes.
 *
 * @since 0.1.0
 */
class REST_Route_Collection implements Collection {

	/**
	 * REST routes stored in the collection.
	 *
	 * @since 0.1.0
	 * @var REST_Route[]
	 */
	private $routes = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param REST_Route[] $routes List of REST route instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given routes are invalid.
	 */
	public function __construct( array $routes ) {
		foreach ( $routes as $route ) {
			if ( ! $route instanceof REST_Route ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid route provided for REST route collection.', 'wp-oop-plugin-lib' )
				);
			}
			$this->routes[] = $route;
		}
	}

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return ArrayIterator<int, REST_Route> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->routes );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->routes );
	}
}
