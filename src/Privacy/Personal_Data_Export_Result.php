<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Privacy\Personal_Data_Export_Result
 *
 * @since 0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Privacy;

use Felix_Arntz\WP_OOP_Plugin_Lib\General\Contracts\Arrayable;

/**
 * Class representing a result from a personal data export operation.
 *
 * @since 0.1.0
 */
final class Personal_Data_Export_Result implements Arrayable {

	/**
	 * Result data.
	 *
	 * @since 0.1.0
	 * @var array<string, mixed>
	 */
	private $data;

	/**
	 * Group information.
	 *
	 * @since 0.1.0
	 * @var array<string, array<string, string>>
	 */
	private $group_info;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->data       = array(
			'data' => array(),
			'done' => true,
		);
		$this->group_info = array();
	}

	/**
	 * Sets the label and description for a specific group.
	 *
	 * This method does not actually add any data to the result, but defines metadata for a group which can then be
	 * referenced within the results.
	 *
	 * @since 0.1.0
	 *
	 * @param string $group_id          Unique group identifier.
	 * @param string $group_label       Label for the group.
	 * @param string $group_description Description for the group.
	 */
	public function set_group_info( string $group_id, string $group_label, string $group_description ): void {
		$this->group_info[ $group_id ] = array(
			'group_id'          => $group_id,
			'group_label'       => $group_label,
			'group_description' => $group_description,
		);
	}

	/**
	 * Adds an export data entry for a group and item.
	 *
	 * @since 0.1.0
	 *
	 * @param string $group_id Group identifier.
	 * @param string $item_id  Unique item identifier within the group.
	 * @param string $name     Field name.
	 * @param string $value    Field value.
	 */
	public function add_item_entry( string $group_id, string $item_id, string $name, string $value ): void {
		$this->add_item_entries(
			$group_id,
			$item_id,
			array(
				array(
					'name'  => $name,
					'value' => $value,
				),
			)
		);
	}

	/**
	 * Adds export data entries for a group and item.
	 *
	 * @since 0.1.0
	 *
	 * @param string                  $group_id Unique group identifier.
	 * @param string                  $item_id  Unique item identifier within the group.
	 * @param array<string, string>[] $data     List of associative arrays with 'name' and 'value' keys.
	 */
	public function add_item_entries( string $group_id, string $item_id, array $data ): void {
		$key = "{$group_id}:{$item_id}";

		// If there's no entry for this group and item yet, start one.
		if ( ! isset( $this->data['data'][ $key ] ) ) {
			$this->data['data'][ $key ] = array(
				'group_id' => $group_id,
				'item_id'  => $item_id,
				'data'     => $data,
			);
			return;
		}

		foreach ( $data as $entry ) {
			$this->data['data'][ $key ]['data'][] = $entry;
		}
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
		$output = array(
			'data' => array(),
			'done' => $this->data['done'],
		);

		// Flatten the data and include group information.
		foreach ( $this->data['data'] as $group_item ) {
			if ( isset( $this->group_info[ $group_item['group_id'] ] ) ) {
				$output['data'][] = array_merge(
					$this->group_info[ $group_item['group_id'] ],
					$group_item
				);
			} else {
				$output['data'][] = $group_item;
			}
		}

		return $output;
	}
}
