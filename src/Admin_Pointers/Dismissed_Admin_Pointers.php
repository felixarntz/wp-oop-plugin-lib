<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers\Dismissed_Admin_Pointers
 *
 * @since 0.2.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Admin_Pointers;

use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Key;
use Felix_Arntz\WP_OOP_Plugin_Lib\Meta\Meta_Repository;

/**
 * Class for managing dismissed admin pointers.
 *
 * @since 0.2.0
 */
class Dismissed_Admin_Pointers {

	/**
	 * User ID.
	 *
	 * @since 0.2.0
	 * @var int
	 */
	private $user_id;

	/**
	 * Meta key.
	 *
	 * @since 0.2.0
	 * @var Meta_Key
	 */
	private $meta_key;

	/**
	 * Constructor.
	 *
	 * @since 0.2.0
	 *
	 * @param int             $user_id         User ID.
	 * @param Meta_Repository $meta_repository User meta repository.
	 */
	public function __construct( int $user_id, Meta_Repository $meta_repository ) {
		$this->user_id  = $user_id;
		$this->meta_key = new Meta_Key(
			$meta_repository,
			'dismissed_wp_pointers',
			array(
				'type'   => 'string',
				'single' => true,
			)
		);
	}

	/**
	 * Checks whether an admin pointer is dismissed for the user.
	 *
	 * @since 0.2.0
	 *
	 * @param string $pointer_slug Admin pointer slug.
	 * @return bool True if the admin pointer is dismissed, false otherwise.
	 */
	public function is_pointer_dismissed( string $pointer_slug ): bool {
		return in_array( $pointer_slug, $this->get_dismissed_pointers(), true );
	}

	/**
	 * Dismisses an admin pointer for the user.
	 *
	 * @since 0.2.0
	 *
	 * @param string $pointer_slug Admin pointer slug.
	 */
	public function dismiss_pointer( string $pointer_slug ): void {
		$dismissed_pointers = $this->get_dismissed_pointers();
		if ( in_array( $pointer_slug, $dismissed_pointers, true ) ) {
			return;
		}

		$dismissed_pointers[] = $pointer_slug;
		$this->set_dismissed_pointers( $dismissed_pointers );
	}

	/**
	 * Un-dismisses an admin pointer for the user.
	 *
	 * @since 0.2.0
	 *
	 * @param string $pointer_slug Admin pointer slug.
	 */
	public function undismiss_pointer( string $pointer_slug ): void {
		$dismissed_pointers = $this->get_dismissed_pointers();
		$found_index        = array_search( $pointer_slug, $dismissed_pointers, true );
		if ( false === $found_index ) {
			return;
		}

		unset( $dismissed_pointers[ $found_index ] );
		$this->set_dismissed_pointers( $dismissed_pointers );
	}

	/**
	 * Gets the dismissed admin pointers.
	 *
	 * @since 0.2.0
	 *
	 * @return string[] Dismissed admin pointers.
	 */
	private function get_dismissed_pointers(): array {
		return explode( ',', (string) $this->meta_key->get_value( $this->user_id ) );
	}

	/**
	 * Sets the dismissed admin pointers.
	 *
	 * @since 0.2.0
	 *
	 * @param string[] $dismissed_pointers Dismissed admin pointers.
	 */
	private function set_dismissed_pointers( array $dismissed_pointers ): void {
		$this->meta_key->update_value( $this->user_id, implode( ',', $dismissed_pointers ) );
	}
}
