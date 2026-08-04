<?php
/**
 * Blog — blog/archive layout settings for the Lumina theme.
 *
 * Original implementation of the premium blog feature category. Controls the
 * blog archive presentation: column count, featured-image visibility,
 * excerpt length, and meta display. Consumed by the Lumina composition
 * pipeline via template-data injection; no template overrides required.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Blog module.
 */
final class Blog implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'blog';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Blog';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'columns'         => 2,
			'show_featured'   => true,
			'show_excerpt'    => true,
			'excerpt_length'  => 55,
			'show_meta'       => true,
			'show_date'       => true,
			'show_author'     => true,
			'show_categories' => true,
			'show_read_more'  => true,
		);
	}

	/**
	 * Register WP hooks (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_filter' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_filter( 'excerpt_length', array( $this, 'excerpt_length' ), 20 );
	}

	/**
	 * Customizer settings.
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		$this->add_setting(
			$customizer,
			'columns',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label'   => 'Archive columns',
				'type'    => 'select',
				'choices' => array(
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'show_featured',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show featured images',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_excerpt',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show excerpts',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'excerpt_length',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Excerpt length (words)',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'show_meta',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show post meta',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_date',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show date',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_author',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show author',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_categories',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show categories',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_read_more',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show read-more link',
				'type'  => 'checkbox',
			)
		);
	}

	/**
	 * Excerpt length filter (guarded, WP-free safe).
	 *
	 * @param int $length Default length.
	 * @return int
	 */
	public function excerpt_length( int $length ): int {
		return (int) $this->setting( 'excerpt_length', $length );
	}

	/**
	 * Inject blog settings into archive/blog template data.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		if ( in_array( $slug, array( 'blog', 'archive', 'author', 'search' ), true ) ) {
			$data['blog'] = array(
				'columns'         => (int) $this->setting( 'columns', 2 ),
				'show_featured'   => (bool) $this->setting( 'show_featured', true ),
				'show_excerpt'    => (bool) $this->setting( 'show_excerpt', true ),
				'show_meta'       => (bool) $this->setting( 'show_meta', true ),
				'show_date'       => (bool) $this->setting( 'show_date', true ),
				'show_author'     => (bool) $this->setting( 'show_author', true ),
				'show_categories' => (bool) $this->setting( 'show_categories', true ),
				'show_read_more'  => (bool) $this->setting( 'show_read_more', true ),
			);
		}

		return $data;
	}
}
