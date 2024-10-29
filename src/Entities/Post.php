<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post
 *
 * @since   0.1.0
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Contracts\Entity;
use WP_Post;
use WP_User;

/**
 * Class representing a WordPress post.
 *
 * @since 0.1.0
 */
class Post implements Entity {

	/**
	 * @var int|null
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var User|null
	 */
	protected $author;

	/**
	 * @var bool|null
	 */
	protected $is_public;

	/**
	 * @var string|null
	 */
	protected $url;

	/**
	 * @var string|null
	 */
	protected $edit_url;

	/**
	 * @param string $title
	 * @param string $content
	 */
	public function __construct( string $title, string $content ) {
		$this->title   = $title;
		$this->content = $content;
	}

	/**
	 * Factory.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $wp_post The underlying WordPress post object.
	 */
	public static function convert( WP_Post $wp_post ) {
		$post = new static( $wp_post->post_title, $wp_post->post_content );
		$post->set_id( $wp_post->ID )
		     ->set_author( new User( new WP_User( $wp_post->post_author ) ) )
		     ->set_is_public( is_post_publicly_viewable( $wp_post ) );

		$permalink = get_permalink( $wp_post );
		if ( is_string( $permalink ) ) {
			$post->set_url( $permalink );
		}

		$edit_url = get_edit_post_link( $wp_post, 'raw' );
		if ( is_string( $edit_url ) ) {
			$post->set_edit_url( $edit_url );
		}
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function get_content(): string {
		return $this->content;
	}

	/**
	 * Gets the post ID.
	 * Returns null for non-existing posts (that have not been saved yet)
	 *
	 * @since 0.1.0
	 *
	 * @return int|null The post ID.
	 */
	public function get_id(): ?int {
		return $this->id ?? null;
	}

	/**
	 * @param int $id
	 * @return $this
	 */
	public function set_id( int $id ): Post {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return User|null
	 */
	public function get_author(): ?User {
		return $this->author ?? null;
	}

	/**
	 * @param User $author
	 * @return $this
	 */
	public function set_author( User $author ): Post {
		$this->author = $author;

		return $this;
	}

	/**
	 * Checks whether the post is publicly accessible.
	 *
	 * @since 0.1.0
	 *
	 * @return bool|null True if the post is public, false otherwise.
	 */
	public function is_public(): ?bool {
		return $this->is_public;
	}

	/**
	 * @param bool $is_public
	 * @return $this
	 */
	public function set_is_public( bool $is_public ): Post {
		$this->is_public = $is_public;

		return $this;
	}

	/**
	 * Gets the post's primary URL.
	 *
	 * @since 0.1.0
	 *
	 * @return string|null Post permalink, or empty string if none.
	 */
	public function get_url(): ?string {
		return $this->url ?? null;
	}

	/**
	 * @param string $url
	 * @return $this
	 */
	public function set_url( string $url ): Post {
		$this->url = $url;

		return $this;
	}

	/**
	 * Gets the post's edit URL, if the current user is able to edit it.
	 *
	 * @since 0.1.0
	 *
	 * @return string|null URL to edit the post, or empty string if unable to edit.
	 */
	public function get_edit_url(): ?string {
		return $this->edit_url ?? null;
	}

	/**
	 * @param string $edit_url
	 * @return $this
	 */
	public function set_edit_url( string $edit_url ): Post {
		$this->edit_url = $edit_url;

		return $this;
	}

	/**
	 * Gets the value for the given field of the post.
	 *
	 * @since 0.1.0
	 *
	 * @param string $field The field identifier.
	 * @return mixed Value for the field, `null` if not set.
	 */
	public function get_field_value( string $field ) {
		// @TODO: remove this method in favor of specific ones
		if ( ! isset( $this->id ) ) {
			return null;
		}

		switch ( $field ) {
			// Post status has special handling.
			case 'post_status':
				return get_post_status( $this->id );
			default:
				$wp_post = get_post( $this->id );

				return $wp_post->$field ?? null;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function serialize(): array {
		return [
			'post_title'   => $this->get_title(),
			'post_content' => $this->get_content(),
			'post_author'  => $this->get_author()->get_id(),
		];
	}
}
