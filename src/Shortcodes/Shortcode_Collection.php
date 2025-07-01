<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Shortcode_Collection
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Contracts\Shortcode;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of shortcodes.
 *
 * @since n.e.x.t
 */
class Shortcode_Collection implements Collection {

	/**
	 * Shortcodes stored in the collection.
	 *
	 * @since n.e.x.t
	 * @var Shortcode[]
	 */
	private $shortcodes = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param Shortcode[] $shortcodes List of shortcode instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given shortcodes are invalid.
	 */
	public function __construct( array $shortcodes ) {
		foreach ( $shortcodes as $shortcode ) {
			if ( ! $shortcode instanceof Shortcode ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid shortcode provided for shortcode collection.', 'wp-oop-plugin-lib' )
				);
			}
			$this->shortcodes[] = $shortcode;
		}
	}

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since n.e.x.t
	 *
	 * @return ArrayIterator<int, Shortcode> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->shortcodes );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->shortcodes );
	}
}
