<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Links\Admin_Link_Collection
 *
 * @since n.e.x.t
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
 * @since n.e.x.t
 */
class Admin_Link_Collection implements Collection {

	/**
	 * Admin links stored in the collection.
	 *
	 * @since n.e.x.t
	 * @var Admin_Link[]
	 */
	private $admin_links = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
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
	 * @since n.e.x.t
	 *
	 * @return ArrayIterator<int, Admin_Link> Collection iterator.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->admin_links );
	}

	/**
	 * Returns the size of the collection.
	 *
	 * @since n.e.x.t
	 *
	 * @return int Collection size.
	 */
	public function count(): int {
		return count( $this->admin_links );
	}
}
