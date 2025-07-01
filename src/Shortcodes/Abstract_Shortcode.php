<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Abstract_Shortcode
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes;

use Felix_Arntz\WP_OOP_Plugin_Lib\Shortcodes\Contracts\Shortcode;

/**
 * Base class representing a shortcode.
 *
 * @since n.e.x.t
 */
abstract class Abstract_Shortcode implements Shortcode {

	/**
	 * Shortcode tag.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $tag;

	/**
	 * Default shortcode attributes.
	 *
	 * @since n.e.x.t
	 * @var array<string, mixed>
	 */
	private $default_atts = array();

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 */
	public function __construct() {
		$this->tag          = $this->tag();
		$this->default_atts = $this->default_atts();
	}

	/**
	 * Gets the shortcode tag.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Shortcode tag.
	 */
	final public function get_tag(): string {
		return $this->tag;
	}

	/**
	 * Gets the shortcode callback.
	 *
	 * @since n.e.x.t
	 *
	 * @return callable Shortcode callback.
	 */
	final public function get_callback(): callable {
		return function ( array $atts, string $content = '' ) {
			// Parse the attributes, merging with defaults.
			$atts = shortcode_atts( $this->default_atts, $atts, $this->tag );

			// Handle the shortcode and return the output.
			return $this->handle_shortcode( $atts, $content );
		};
	}

	/**
	 * Gets the registration arguments for the shortcode.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Shortcode registration arguments.
	 */
	final public function get_registration_args(): array {
		return array(
			'callback' => $this->get_callback(),
		);
	}

	/**
	 * Returns the shortcode tag.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Shortcode tag.
	 */
	abstract protected function tag(): string;

	/**
	 * Handles a shortcode instance with the given (sanitized) attributes and content.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $atts    Shortcode attributes, already parsed with defaults.
	 * @param string               $content Optional. Shortcode content, if any. Default empty string.
	 * @return string Shortcode output.
	 */
	abstract protected function handle_shortcode( array $atts, string $content = '' ): string;

	/**
	 * Returns the default shortcode attributes.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Default shortcode attributes.
	 */
	protected function default_atts(): array {
		return array();
	}
}
