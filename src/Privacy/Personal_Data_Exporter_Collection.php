<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Personal_Data_Exporter_Collection
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts\Personal_Data_Exporter;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of personal data exporters.
 *
 * @since 0.1.0
 */
class Personal_Data_Exporter_Collection implements Collection {

	/**
	 * Personal data exporters stored in the collection.
	 *
	 * @since 0.1.0
	 * @var Personal_Data_Exporter[]
	 */
	private $exporters = array();

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param Personal_Data_Exporter[] $exporters List of personal data exporter instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given exporters are invalid.
	 */
	public function __construct( array $exporters ) {
		foreach ( $exporters as $exporter ) {
			if ( ! $exporter instanceof Personal_Data_Exporter ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid exporter provided for personal data exporter collection.', 'wp-oop-plugin-lib' ) // phpcs:ignore Generic.Files.LineLength.TooLong
				);
			}
			$this->exporters[] = $exporter;
		}
	}

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return ArrayIterator<int, Personal_Data_Exporter> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->exporters );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->exporters );
	}
}
