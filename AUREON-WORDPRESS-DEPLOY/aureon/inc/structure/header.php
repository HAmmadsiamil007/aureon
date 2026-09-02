<?php
/**
 * Header elements.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aureon_construct_header' ) ) {
	add_action( 'aureon_header', 'aureon_construct_header' );
	/**
	 * Build the header.
	 *
	 * @since 1.3.42
	 */
	function aureon_construct_header() {
		?>
		<header <?php aureon_do_attr( 'header' ); ?>>
			<div <?php aureon_do_attr( 'inside-header' ); ?>>
				<?php
				/**
				 * aureon_before_header_content hook.
				 *
				 * @since 0.1
				 */
				do_action( 'aureon_before_header_content' );

				if ( ! aureon_is_using_flexbox() ) {
					// Add our main header items.
					aureon_header_items();
				}

				/**
				 * aureon_after_header_content hook.
				 *
				 * @since 0.1
				 *
				 * @hooked aureon_add_navigation_float_right - 5
				 */
				do_action( 'aureon_after_header_content' );
				?>
			</div>
		</header>
		<?php
	}
}

if ( ! function_exists( 'aureon_header_items' ) ) {
	/**
	 * Build the header contents.
	 * Wrapping this into a function allows us to customize the order.
	 *
	 * @since 1.2.9.7
	 */
	function aureon_header_items() {
		$order = apply_filters(
			'aureon_header_items_order',
			array(
				'header-widget',
				'site-branding',
				'logo',
			)
		);

		foreach ( $order as $item ) {
			if ( 'header-widget' === $item ) {
				aureon_construct_header_widget();
			}

			if ( 'site-branding' === $item ) {
				aureon_construct_site_title();
			}

			if ( 'logo' === $item ) {
				aureon_construct_logo();
			}
		}
	}
}

if ( ! function_exists( 'aureon_construct_logo' ) ) {
	/**
	 * Build the logo
	 *
	 * @since 1.3.28
	 */
	function aureon_construct_logo() {
		$logo_url = ( function_exists( 'the_custom_logo' ) && get_theme_mod( 'custom_logo' ) ) ? wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) : false;
		$logo_url = ( $logo_url ) ? $logo_url[0] : aureon_get_option( 'logo' );

		$logo_url = esc_url( apply_filters( 'aureon_logo', $logo_url ) );
		$retina_logo_url = esc_url( apply_filters( 'aureon_retina_logo', aureon_get_option( 'retina_logo' ) ) );

		// If we don't have a logo, bail.
		if ( empty( $logo_url ) ) {
			return;
		}

		/**
		 * aureon_before_logo hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_before_logo' );

		$attr = apply_filters(
			'aureon_logo_attributes',
			array(
				'class' => 'header-image is-logo-image',
				'alt'   => esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
				'src'   => $logo_url,
			)
		);

		$data = get_theme_mod( 'custom_logo' ) && ( '' !== $retina_logo_url || aureon_is_using_flexbox() )
			? wp_get_attachment_metadata( get_theme_mod( 'custom_logo' ) )
			: false;

		if ( '' !== $retina_logo_url ) {
			$attr['srcset'] = $logo_url . ' 1x, ' . $retina_logo_url . ' 2x';
		}

		if ( $data ) {
			if ( isset( $data['width'] ) ) {
				$attr['width'] = $data['width'];
			}

			if ( isset( $data['height'] ) ) {
				$attr['height'] = $data['height'];
			}
		}

		$attr = array_map( 'esc_attr', $attr );

		$html_attr = '';
		foreach ( $attr as $name => $value ) {
			$html_attr .= " $name=" . '"' . $value . '"';
		}

		// Print our HTML.
		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'aureon_logo_output',
			sprintf(
				'<div class="site-logo">
					<a href="%1$s" rel="home">
						<img %2$s />
					</a>
				</div>',
				esc_url( apply_filters( 'aureon_logo_href', home_url( '/' ) ) ),
				$html_attr
			),
			$logo_url,
			$html_attr
		);

		/**
		 * aureon_after_logo hook.
		 *
		 * @since 0.1
		 */
		do_action( 'aureon_after_logo' );
	}
}

if ( ! function_exists( 'aureon_construct_site_title' ) ) {
	/**
	 * Build the site title and tagline.
	 *
	 * @since 1.3.28
	 */
	function aureon_construct_site_title() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			aureon_get_defaults()
		);

		// Get the title and tagline.
		$title = get_bloginfo( 'title' );
		$tagline = get_bloginfo( 'description' );

		// If the disable title checkbox is checked, or the title field is empty, return true.
		$disable_title = ( '1' == $aureon_settings['hide_title'] || '' == $title ) ? true : false; // phpcs:ignore

		// If the disable tagline checkbox is checked, or the tagline field is empty, return true.
		$disable_tagline = ( '1' == $aureon_settings['hide_tagline'] || '' == $tagline ) ? true : false;  // phpcs:ignore

		$schema_type = aureon_get_schema_type();

		// Build our site title.
		$site_title = apply_filters(
			'aureon_site_title_output',
			sprintf(
				'<%1$s class="main-title"%4$s>
					<a href="%2$s" rel="home">%3$s</a>
				</%1$s>',
				( is_front_page() && is_home() ) ? 'h1' : 'p',
				esc_url( apply_filters( 'aureon_site_title_href', home_url( '/' ) ) ),
				get_bloginfo( 'name' ),
				'microdata' === aureon_get_schema_type() ? ' itemprop="headline"' : ''
			)
		);

		// Build our tagline.
		$site_tagline = apply_filters(
			'aureon_site_description_output',
			sprintf(
				'<p class="site-description"%2$s>%1$s</p>',
				html_entity_decode( get_bloginfo( 'description', 'display' ) ), // phpcs:ignore
				'microdata' === aureon_get_schema_type() ? ' itemprop="description"' : ''
			)
		);

		// Site title and tagline.
		if ( false === $disable_title || false === $disable_tagline ) {
			if ( aureon_needs_site_branding_container() ) {
				echo '<div class="site-branding-container">';
				aureon_construct_logo();
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- outputting site title and tagline. False positive.
			echo apply_filters(
				'aureon_site_branding_output',
				sprintf(
					'<div class="site-branding">
						%1$s
						%2$s
					</div>',
					( ! $disable_title ) ? $site_title : '',
					( ! $disable_tagline ) ? $site_tagline : ''
				)
			);

			if ( aureon_needs_site_branding_container() ) {
				echo '</div>';
			}
		}
	}
}

add_filter( 'aureon_header_items_order', 'aureon_reorder_inline_site_branding' );
/**
 * Remove the logo from it's usual position.
 *
 * @since 2.3
 * @param array $order Order of the header items.
 */
function aureon_reorder_inline_site_branding( $order ) {
	if ( ! aureon_get_option( 'inline_logo_site_branding' ) || ! aureon_has_logo_site_branding() ) {
		return $order;
	}

	return array(
		'header-widget',
		'site-branding',
	);
}

if ( ! function_exists( 'aureon_construct_header_widget' ) ) {
	/**
	 * Build the header widget.
	 *
	 * @since 1.3.28
	 */
	function aureon_construct_header_widget() {
		if ( is_active_sidebar( 'header' ) ) :
			?>
			<div class="header-widget">
				<?php dynamic_sidebar( 'header' ); ?>
			</div>
			<?php
		endif;
	}
}

add_action( 'aureon_before_header_content', 'aureon_do_site_logo', 5 );
/**
 * Add the site logo to our header.
 * Only added if we aren't using floats to preserve backwards compatibility.
 *
 * @since 3.0.0
 */
function aureon_do_site_logo() {
	if ( ! aureon_is_using_flexbox() || aureon_needs_site_branding_container() ) {
		return;
	}

	aureon_construct_logo();
}

add_action( 'aureon_before_header_content', 'aureon_do_site_branding' );
/**
 * Add the site branding to our header.
 * Only added if we aren't using floats to preserve backwards compatibility.
 *
 * @since 3.0.0
 */
function aureon_do_site_branding() {
	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	aureon_construct_site_title();
}

add_action( 'aureon_after_header_content', 'aureon_do_header_widget' );
/**
 * Add the header widget to our header.
 * Only used when grid isn't using floats to preserve backwards compatibility.
 *
 * @since 3.0.0
 */
function aureon_do_header_widget() {
	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	aureon_construct_header_widget();
}

if ( ! function_exists( 'aureon_top_bar' ) ) {
	add_action( 'aureon_before_header', 'aureon_top_bar', 5 );
	/**
	 * Build our top bar.
	 *
	 * @since 1.3.45
	 */
	function aureon_top_bar() {
		if ( ! is_active_sidebar( 'top-bar' ) ) {
			return;
		}
		?>
		<div <?php aureon_do_attr( 'top-bar' ); ?>>
			<div <?php aureon_do_attr( 'inside-top-bar' ); ?>>
				<?php dynamic_sidebar( 'top-bar' ); ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'aureon_pingback_header' ) ) {
	add_action( 'wp_head', 'aureon_pingback_header' );
	/**
	 * Add a pingback url auto-discovery header for singularly identifiable articles.
	 *
	 * @since 1.3.42
	 */
	function aureon_pingback_header() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}
}

if ( ! function_exists( 'aureon_add_viewport' ) ) {
	add_action( 'wp_head', 'aureon_add_viewport', 1 );
	/**
	 * Add viewport to wp_head.
	 *
	 * @since 1.1.0
	 */
	function aureon_add_viewport() {
		echo apply_filters( 'aureon_meta_viewport', '<meta name="viewport" content="width=device-width, initial-scale=1">' );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_action( 'aureon_before_header', 'aureon_do_skip_to_content_link', 2 );
/**
 * Add skip to content link before the header.
 *
 * @since 2.0
 */
function aureon_do_skip_to_content_link() {
	printf(
		'<a class="screen-reader-text skip-link" href="#content" title="%1$s">%2$s</a>',
		esc_attr__( 'Skip to content', 'aureon' ),
		esc_html__( 'Skip to content', 'aureon' )
	);
}
