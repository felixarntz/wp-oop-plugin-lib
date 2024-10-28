<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Abstract_Personal_Data_Exporter
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Contracts\Personal_Data_Exporter;

/**
 * Base class representing a WordPress personal data exporter.
 *
 * @since 0.1.0
 */
abstract class Abstract_Personal_Data_Exporter implements Personal_Data_Exporter {

	/**
	 * Data exporter key.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $key;

	/**
	 * Data exporter name.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	private $name;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->key  = $this->key();
		$this->name = $this->name();
	}

	/**
	 * Gets the key of the data exporter.
	 *
	 * @since 0.1.0
	 *
	 * @return string Data exporter key.
	 */
	final public function get_key(): string {
		return $this->key;
	}

	/**
	 * Gets the user-facing friendly name for the data exporter.
	 *
	 * @since 0.1.0
	 *
	 * @return string Data exporter name.
	 */
	final public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the registration arguments for the data exporter.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, mixed> Data exporter registration arguments.
	 */
	final public function get_registration_args(): array {
		return array(
			'exporter_friendly_name' => $this->get_name(),
			'callback'               => array( $this, 'export_data' ),
		);
	}

	/**
	 * Exports personal data for the given email address and page.
	 *
	 * @since 0.1.0
	 *
	 * @param string $email_address Email address to export personal data for.
	 * @param int    $page          Optional. Page index. A higher value than 1 is only passed if the overall process
	 *                              requires more than one request, typically to avoid timeouts.
	 * @return array<string, mixed> {
	 *     Data export results.
	 *
	 *     @type array<string, mixed>[] $data An array of data groups to export, including their data.
	 *     @type bool                   $done Whether the exporter is finished.
	 * }
	 */
	final public function export_data( string $email_address, int $page = 1 ): array {
		$result = new Personal_Data_Export_Result();
		$this->process_export_data_request( $result, $email_address, $page );
		return $result->to_array();
	}

	/**
	 * Returns the key of the data exporter.
	 *
	 * @since 0.1.0
	 *
	 * @return string Data exporter key.
	 */
	abstract protected function key(): string;

	/**
	 * Returns the user-facing friendly name for the data exporter.
	 *
	 * @since 0.1.0
	 *
	 * @return string Data exporter name.
	 */
	abstract protected function name(): string;

	/**
	 * Processes a request to export personal data for the given email address and page.
	 *
	 * @since 0.1.0
	 *
	 * @param Personal_Data_Export_Result $result        Result object to amend.
	 * @param string                      $email_address Email address to export personal data for.
	 * @param int                         $page          Optional. Page index. A higher value than 1 is only passed if
	 *                                                   the overall process requires more than one request, typically
	 *                                                   to avoid timeouts.
	 */
	abstract protected function process_export_data_request( Personal_Data_Export_Result $result, string $email_address, int $page = 1 ): void; // phpcs:ignore Generic.Files.LineLength.TooLong
}
