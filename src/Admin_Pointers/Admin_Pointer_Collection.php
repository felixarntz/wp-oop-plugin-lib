<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Admin_Pointer_Collection
 *
 * @since 0.2.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Contracts\Admin_Pointer;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of admin pointers.
 *
 * @since 0.2.0
 */
class Admin_Pointer_Collection implements Collection {

	/**
	 * Admin pointers stored in the collection.
	 *
	 * @since 0.2.0
	 * @var Admin_Pointer[]
	 */
	private $admin_links = array();

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param Admin_Pointer[] $admin_links List of admin pointer instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given admin pointers are invalid.
	 */
	public function __construct( array $admin_links ) {
		foreach ( $admin_links as $admin_page ) {
			if ( ! $admin_page instanceof Admin_Pointer ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid admin pointer provided for admin pointer collection.', 'wp-oop-plugin-lib' )
				);
			}
			$this->admin_links[] = $admin_page;
		}
	}

	/**
	 * Returns an iterator for the collection.
	 *
	 * @since 0.2.0
	 *
	 * @return ArrayIterator<int, Admin_Pointer> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->admin_links );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since 0.2.0
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->admin_links );
	}
}
