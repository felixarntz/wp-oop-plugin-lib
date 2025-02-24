<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Admin_Link_Collection
 *
 * @since 0.2.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links;

use ArrayIterator;
use Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Contracts\Admin_Link;
use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Collection;
use InvalidArgumentException;
use Traversable;

/**
 * Class for a collection of admin links.
 *
 * @since 0.2.0
 */
class Admin_Link_Collection implements Collection {

	/**
	 * Admin links stored in the collection.
	 *
	 * @since 0.2.0
	 * @var Admin_Link[]
	 */
	private $admin_links = array();

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param Admin_Link[] $admin_links List of admin link instances.
	 *
	 * @throws InvalidArgumentException Thrown when the given admin links are invalid.
	 */
	public function __construct( array $admin_links ) {
		foreach ( $admin_links as $admin_page ) {
			if ( ! $admin_page instanceof Admin_Link ) {
				throw new InvalidArgumentException(
					esc_html__( 'Invalid admin link provided for admin link collection.', 'wp-oop-plugin-lib' )
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
	 * @return ArrayIterator<int, Admin_Link> Collection iterator.
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
