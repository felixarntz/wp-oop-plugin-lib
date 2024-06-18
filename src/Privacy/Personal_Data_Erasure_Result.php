<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Personal_Data_Erasure_Result
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Arrayable;

/**
 * Class representing a result from a personal data erasure operation.
 *
 * @since n.e.x.t
 */
final class Personal_Data_Erasure_Result implements Arrayable {

	/**
	 * Result data.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $data;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 */
	public function __construct() {
		$this->data = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	/**
	 * Sets whether items were actually removed.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $items_removed Whether items were actually removed.
	 */
	public function set_items_removed( bool $items_removed ): void {
		$this->data['items_removed'] = $items_removed;
	}

	/**
	 * Sets whether items were retained.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $items_retained Whether items were retained.
	 */
	public function set_items_retained( bool $items_retained ): void {
		$this->data['items_retained'] = $items_retained;
	}

	/**
	 * Adds a message, e.g. a notice about the operation.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $message Message to add.
	 */
	public function add_message( string $message ): void {
		$this->data['messages'][] = $message;
	}

	/**
	 * Sets whether the eraser is finished.
	 *
	 * @since n.e.x.t
	 *
	 * @param bool $done Whether the eraser is finished.
	 */
	public function set_done( bool $done ): void {
		$this->data['done'] = $done;
	}

	/**
	 * Returns the array representation.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Array representation.
	 */
	public function to_array(): array {
		return $this->data;
	}
}
