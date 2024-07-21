<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Term
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity;
use WP_Term;

/**
 * Class representing a WordPress term.
 *
 * @since n.e.x.t
 */
class Term implements Entity {

	/**
	 * The underlying WordPress term object.
	 *
	 * @since n.e.x.t
	 * @var WP_Term
	 */
	private $wp_obj;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_Term $term The underlying WordPress term object.
	 */
	public function __construct( WP_Term $term ) {
		$this->wp_obj = $term;
	}

	/**
	 * Gets the term ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The term ID.
	 */
	public function get_id(): int {
		return (int) $this->wp_obj->term_id;
	}

	/**
	 * Checks whether the term is publicly accessible.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the term is public, false otherwise.
	 */
	public function is_public(): bool {
		return is_taxonomy_viewable( $this->wp_obj->taxonomy );
	}

	/**
	 * Gets the term's primary URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Term link, or empty string if none.
	 */
	public function get_url(): string {
		$url = get_term_link( $this->wp_obj );
		if ( is_wp_error( $url ) ) {
			return '';
		}
		return (string) $url;
	}

	/**
	 * Gets the term's edit URL, if the current user is able to edit it.
	 *
	 * @since n.e.x.t
	 *
	 * @return string URL to edit the term, or empty string if unable to edit.
	 */
	public function get_edit_url(): string {
		// Despite the second parameter being optional, it is required to get the correct URL.
		return (string) get_edit_term_link( $this->wp_obj, $this->wp_obj->taxonomy );
	}

	/**
	 * Gets the value for the given field of the term.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $field The field identifier.
	 * @return mixed Value for the field, `null` if not set.
	 */
	public function get_field_value( string $field ) {
		return $this->wp_obj->$field ?? null;
	}
}
