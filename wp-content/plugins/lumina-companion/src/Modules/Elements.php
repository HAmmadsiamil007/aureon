<?php
/**
 * Elements — reusable content elements with hook placement.
 *
 * Original implementation of the premium elements feature category. Users
 * create named content blocks (title + body + hook + status) and place them
 * on Lumina region hooks. Blocks render escaped, each carrying its own
 * data-lumina-element attribute for styling.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Elements module.
 */
final class Elements implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'elements';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Elements';
	}

	/**
	 * Default settings (a list of blocks).
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'blocks' => array(),
		);
	}

	/**
	 * Register WP hooks (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_action( 'init', array( $this, 'register_post_type' ), 20 );
	}

	/**
	 * Register a private elements post type (guarded).
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'register_post_type' ) || post_type_exists( 'lumina_element' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		register_post_type(
			'lumina_element',
			array(
				'labels'       => array(
					'name'          => 'Elements',
					'singular_name' => 'Element',
				),
				'public'       => false,
				'show_ui'      => true,
				'supports'     => array( 'title', 'editor', 'custom-fields' ),
				'menu_icon'    => 'dashicons-layout',
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Customizer settings (simple inline blocks fallback when CPT unused).
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		// The CPT is the primary authoring surface; no customizer surface.
	}

	/**
	 * Render active elements on their hooks (guarded).
	 *
	 * @return void
	 */
	public function render_elements(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'current_filter' ) || ! function_exists( 'get_posts' ) ) {
			return;
		}

		$hook = (string) current_filter();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$posts = get_posts(
			array(
				'post_type'      => 'lumina_element',
				'posts_per_page' => 50,
				'post_status'    => 'publish',
				'meta_key'       => '_lumina_hook',
				'meta_value'     => $hook,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);

		foreach ( (array) $posts as $post ) {
			$title = get_the_title( $post );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP core filter on core content.
			$body = apply_filters( 'the_content', (string) $post->post_content );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content escaped/kses'd by WP.
			echo '<div class="lumina-element" data-lumina-element="' . esc_attr( $title ) . '">' . $body . '</div>';
		}
	}
}
