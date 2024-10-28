<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Personal_Data_Erasure_Result
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Arrayable;

/**
 * Class representing a result from a personal data erasure operation.
 *
 * @since 0.1.0
 */
final class Personal_Data_Erasure_Result implements Arrayable {

	/**
	 * Result data.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $data;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @param bool $items_removed Whether items were actually removed.
	 */
	public function set_items_removed( bool $items_removed ): void {
		$this->data['items_removed'] = $items_removed;
	}

	/**
	 * Sets whether items were retained.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $items_retained Whether items were retained.
	 */
	public function set_items_retained( bool $items_retained ): void {
		$this->data['items_retained'] = $items_retained;
	}

	/**
	 * Adds a message, e.g. a notice about the operation.
	 *
	 * @since 0.1.0
	 *
	 * @param string $message Message to add.
	 */
	public function add_message( string $message ): void {
		$this->data['messages'][] = $message;
	}

	/**
	 * Sets whether the eraser is finished.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $done Whether the eraser is finished.
	 */
	public function set_done( bool $done ): void {
		$this->data['done'] = $done;
	}

	/**
	 * Returns the array representation.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Array representation.
	 */
	public function to_array(): array {
		return $this->data;
	}
}
