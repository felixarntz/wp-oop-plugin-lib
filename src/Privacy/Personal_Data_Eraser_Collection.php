<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Personal_Data_Eraser_Collection
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts\Personal_Data_Eraser;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of personal data erasers.
 *
 * @since 0.1.0
 */
class Personal_Data_Eraser_Collection implements Collection {

	/**
	 * Personal data erasers stored in the collection.
	 *
	 * @since 0.1.0
	 * @var Personal_Data_Eraser[]
	 */
	private $erasers = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Personal_Data_Eraser[] $erasers List of personal data eraser instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given erasers are invalid.
	 */
	public function __construct( array $erasers ) {
		foreach ( $erasers as $eraser ) {
			if ( ! $eraser instanceof Personal_Data_Eraser ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid eraser provided for personal data eraser collection.', 'wp-oop-plugin-lib' )
				);
			}
			$this->erasers[] = $eraser;
		}
	}

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return ArrayIterator<int, Personal_Data_Eraser> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->erasers );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->erasers );
	}
}
