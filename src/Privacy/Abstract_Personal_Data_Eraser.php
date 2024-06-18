<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Abstract_Personal_Data_Eraser
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts\Personal_Data_Eraser;

/**
 * Base class representing a WordPress personal data eraser.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Personal_Data_Eraser implements Personal_Data_Eraser {

	/**
	 * Data eraser key.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $key;

	/**
	 * Data eraser name.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $name;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 */
	public function __construct() {
		$this->key  = $this->key();
		$this->name = $this->name();
	}

	/**
	 * Gets the key of the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Data eraser key.
	 */
	final public function get_key(): string {
		return $this->key;
	}

	/**
	 * Gets the user-facing friendly name for the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Data eraser name.
	 */
	final public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the registration arguments for the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Data eraser registration arguments.
	 */
	final public function get_registration_args(): array {
		return array(
			'eraser_friendly_name' => $this->get_name(),
			'callback'             => array( $this, 'erase_data' ),
		);
	}

	/**
	 * Erases personal data for the given email address and page.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $email_address Email address to erase personal data for.
	 * @param int    $page          Optional. Page index. A higher value than 1 is only passed if the overall process
	 *                              requires more than one request, typically to avoid timeouts.
	 * @return array<string, mixed> {
	 *     Data erasure results.
	 *
	 *     @type bool     $items_removed  Whether items were actually removed.
	 *     @type bool     $items_retained Whether items were retained.
	 *     @type string[] $messages       An array of messages to add to the personal data export file.
	 *     @type bool     $done           Whether the eraser is finished.
	 * }
	 */
	final public function erase_data( string $email_address, int $page = 1 ): array {
		$result = new Personal_Data_Erasure_Result();
		$this->process_erase_data_request( $result, $email_address, $page );
		return $result->to_array();
	}

	/**
	 * Returns the key of the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Data eraser key.
	 */
	abstract protected function key(): string;

	/**
	 * Returns the user-facing friendly name for the data eraser.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Data eraser name.
	 */
	abstract protected function name(): string;

	/**
	 * Processes a request to erase personal data for the given email address and page.
	 *
	 * @since n.e.x.t
	 *
	 * @param Personal_Data_Erasure_Result $result        Result object to amend.
	 * @param string                       $email_address Email address to erase personal data for.
	 * @param int                          $page          Optional. Page index. A higher value than 1 is only passed if
	 *                                                    the overall process requires more than one request, typically
	 *                                                    to avoid timeouts.
	 */
	abstract protected function process_erase_data_request( Personal_Data_Erasure_Result $result, string $email_address, int $page = 1 ): void; // phpcs:ignore Generic.Files.LineLength.TooLong
}
