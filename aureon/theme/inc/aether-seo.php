<?php
/**
 * AETHER SEO — Open Graph, Twitter Cards, Schema.org, Canonical URLs.
 *
 * Adds comprehensive SEO meta tags and structured data to front-end pages.
 * All output is filterable for child themes and plugins.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Open Graph Meta Tags ──────────────────────────────────────
add_action( 'wp_head', 'aether_output_opengraph_tags', 1 );
/**
 * Output Open Graph meta tags for social sharing.
 */
function aether_output_opengraph_tags() {
	if ( is_admin() ) {
		return;
	}

	$og = aether_get_opengraph_data();
	foreach ( $og as $property => $content ) {
		if ( ! empty( $content ) ) {
			printf( '<meta property="%s" content="%s">', esc_attr( $property ), esc_attr( $content ) );
			echo "\n";
		}
	}
}

/**
 * Build the Open Graph data array.
 *
 * @return array Key-value pairs of og: properties.
 */
function aether_get_opengraph_data() {
	$site_name = get_bloginfo( 'name' );
	$site_url  = home_url( '/' );
	$site_desc = get_bloginfo( 'description' );

	$og = array(
		'og:site_name'  => $site_name,
		'og:url'        => $site_url,
		'og:type'       => 'website',
		'og:title'      => $site_name,
		'og:description' => $site_desc,
	);

	// Default image (fallback to site logo/screenshot).
	$default_image = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : '';
	if ( ! $default_image ) {
		$default_image = get_template_directory_uri() . '/screenshot.jpg';
	}
	$og['og:image']        = $default_image;
	$og['og:image:width']  = '1200';
	$og['og:image:height'] = '630';

	// ─── Singular posts/pages ──────────────────────────────────
	if ( is_singular() ) {
		$post_id    = get_the_ID();
		$title      = get_the_title();
		$excerpt    = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 30, '...' );
		$url        = get_permalink();
		$post_type  = get_post_type();

		$og['og:title']       = $title;
		$og['og:description'] = $excerpt;
		$og['og:url']         = $url;

		// Featured image.
		if ( has_post_thumbnail( $post_id ) ) {
			$img = wp_get_attachment_image_url( $post_id, 'large' );
			if ( $img ) {
				$og['og:image']       = $img;
				$og['og:image:alt']   = get_post_meta( $post_id, '_wp_attachment_image_alt', true );
			}
		}

		// Article type.
		if ( 'post' === $post_type ) {
			$og['og:type']        = 'article';
			$og['article:author'] = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
			$og['article:published_time'] = get_the_date( 'c', $post_id );
			$og['article:modified_time']  = get_the_modified_date( 'c', $post_id );

			$categories = get_the_category( $post_id );
			if ( ! empty( $categories ) ) {
				$og['article:section'] = $categories[0]->name;
			}

			$tags = get_the_tags( $post_id );
			if ( $tags ) {
				foreach ( $tags as $tag ) {
					$og['article:tag'][] = $tag->name;
				}
			}
		}

		// Product type (WooCommerce).
		if ( 'product' === $post_type && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$og['og:type']        = 'product';
				$og['og:title']       = $product->get_name();
				$og['og:description'] = wp_strip_all_tags( $product->get_short_description() );
				$og['product:price:amount']   = $product->get_price();
				$og['product:price:currency'] = get_woocommerce_currency();

				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$img = wp_get_attachment_image_url( $image_id, 'large' );
					if ( $img ) {
						$og['og:image']     = $img;
						$og['og:image:alt'] = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					}
				}
			}
		}
	}

	// ─── Taxonomy archives ─────────────────────────────────────
	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term ) {
			$og['og:title']       = single_term_title( '', false );
			$og['og:description'] = wp_trim_words( wp_strip_all_tags( term_description() ), 30, '...' );
			$og['og:url']         = get_term_link( $term );
			$og['og:type']        = 'website';
		}
	}

	// ─── Author archives ───────────────────────────────────────
	if ( is_author() ) {
		$author = get_queried_object();
		if ( $author ) {
			$og['og:title']       = $author->display_name;
			$og['og:description'] = get_the_author_meta( 'description', $author->ID ) ?: $site_desc;
			$og['og:type']        = 'profile';
			$og['profile:first_name'] = $author->first_name;
			$og['profile:last_name']  = $author->last_name;
		}
	}

	return apply_filters( 'aether_opengraph_data', $og );
}

// ─── Twitter Card Meta Tags ────────────────────────────────────
add_action( 'wp_head', 'aether_output_twitter_cards', 2 );
/**
 * Output Twitter Card meta tags.
 */
function aether_output_twitter_cards() {
	if ( is_admin() ) {
		return;
	}

	$card = aether_get_twitter_card_data();
	foreach ( $card as $name => $content ) {
		if ( ! empty( $content ) ) {
			if ( is_array( $content ) ) {
				foreach ( $content as $val ) {
					printf( '<meta name="%s" content="%s">', esc_attr( $name ), esc_attr( $val ) );
					echo "\n";
				}
			} else {
				printf( '<meta name="%s" content="%s">', esc_attr( $name ), esc_attr( $content ) );
				echo "\n";
			}
		}
	}
}

/**
 * Build Twitter Card data.
 *
 * @return array Key-value pairs of twitter: meta properties.
 */
function aether_get_twitter_card_data() {
	$card = array(
		'twitter:card'        => 'summary_large_image',
		'twitter:site'        => get_theme_mod( 'aether_social_twitter', '' ),
		'twitter:title'       => get_bloginfo( 'name' ),
		'twitter:description' => get_bloginfo( 'description' ),
		'twitter:image'       => get_template_directory_uri() . '/screenshot.jpg',
	);

	if ( is_singular() ) {
		$card['twitter:title']       = get_the_title();
		$card['twitter:description'] = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 30, '...' );

		if ( has_post_thumbnail() ) {
			$img = wp_get_attachment_image_url( get_the_ID(), 'large' );
			if ( $img ) {
				$card['twitter:image'] = $img;
				$card['twitter:image:alt'] = get_post_meta( get_the_ID(), '_wp_attachment_image_alt', true );
			}
		}

		if ( 'post' === get_post_type() ) {
			$card['twitter:label1'] = 'Written by';
			$card['twitter:data1']  = get_the_author();
			$card['twitter:label2'] = 'Filed under';
			$card['twitter:data2']  = get_the_category_list( ', ' );
		}
	}

	return apply_filters( 'aether_twitter_card_data', $card );
}

// ─── Schema.org Structured Data ────────────────────────────────
add_action( 'wp_head', 'aether_output_structured_data', 3 );
/**
 * Output JSON-LD structured data.
 */
function aether_output_structured_data() {
	if ( is_admin() ) {
		return;
	}

	$schemas = aether_get_structured_data();
	foreach ( $schemas as $schema ) {
		if ( ! empty( $schema ) ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
		}
	}
}

/**
 * Build all structured data schemas.
 *
 * @return array Array of schema arrays.
 */
function aether_get_structured_data() {
	$schemas = array();

	// ─── Organization Schema (always on front page) ───────────
	if ( is_front_page() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		$logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';

		$org = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Organization',
			'name'        => get_bloginfo( 'name' ),
			'url'         => home_url( '/' ),
			'description' => get_bloginfo( 'description' ),
		);

		if ( $logo_url ) {
			$org['logo'] = $logo_url;
		}

		// Social profiles.
		$social_profiles = array();
		$social_keys = array(
			'aether_social_instagram' => 'https://instagram.com/',
			'aether_social_twitter'   => 'https://twitter.com/',
			'aether_social_facebook'  => 'https://facebook.com/',
			'aether_social_youtube'   => 'https://youtube.com/',
			'aether_social_tiktok'    => 'https://tiktok.com/',
		);
		foreach ( $social_keys as $mod => $prefix ) {
			$url = get_theme_mod( $mod, '' );
			if ( $url && '#' !== $url ) {
				$social_profiles[] = $url;
			}
		}
		if ( ! empty( $social_profiles ) ) {
			$org['sameAs'] = $social_profiles;
		}

		$schemas[] = $org;

		// ─── WebSite Schema (with SearchAction) ────────────────
		$schemas[] = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'        => 'EntryPoint',
					'urlTemplate'  => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	// ─── BreadcrumbList Schema ────────────────────────────────
	if ( ! is_front_page() ) {
		$breadcrumbs = aether_get_breadcrumb_data();
		if ( ! empty( $breadcrumbs ) ) {
			$schema = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $breadcrumbs,
			);
			$schemas[] = $schema;
		}
	}

	// ─── Product Schema (WooCommerce single product) ──────────
	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$image_id  = $product->get_image_id();
			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src();

			$schema = array(
				'@context'    => 'https://schema.org',
				'@type'       => 'Product',
				'name'        => $product->get_name(),
				'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
				'image'       => $image_url,
				'url'         => get_permalink(),
				'sku'         => $product->get_sku(),
				'brand'       => array(
					'@type' => 'Brand',
					'name'  => get_bloginfo( 'name' ),
				),
				'offers'      => array(
					'@type'           => 'Offer',
					'url'             => get_permalink(),
					'priceCurrency'   => get_woocommerce_currency(),
					'price'           => $product->get_price(),
					'availability'    => $product->is_in_stock()
						? 'https://schema.org/InStock'
						: 'https://schema.org/OutOfStock',
					'itemCondition'   => 'https://schema.org/NewCondition',
					'seller'          => array(
						'@type' => 'Organization',
						'name'  => get_bloginfo( 'name' ),
					),
				),
			);

			// Aggregate rating.
			if ( $product->get_rating_count() > 0 ) {
				$schema['aggregateRating'] = array(
					'@type'       => 'AggregateRating',
					'ratingValue' => $product->get_average_rating(),
					'reviewCount' => $product->get_rating_count(),
					'bestRating'  => '5',
					'worstRating' => '1',
				);
			}

			$schemas[] = $schema;
		}
	}

	// ─── Article Schema (single post) ─────────────────────────
	if ( is_singular( 'post' ) ) {
		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'description'   => wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 30, '...' ),
			'url'           => get_permalink(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => get_the_author(),
				'url'   => get_author_posts_url( get_the_author_meta( 'ID' ) ),
			),
			'publisher'     => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => get_theme_mod( 'custom_logo' ) ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : home_url( '/favicon.ico' ),
				),
			),
		);

		if ( has_post_thumbnail() ) {
			$schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		}

		$schemas[] = $schema;
	}

	return apply_filters( 'aether_structured_data', $schemas );
}

/**
 * Build breadcrumb data for Schema.org.
 *
 * @return array Breadcrumb list items.
 */
function aether_get_breadcrumb_data() {
	$items = array();
	$position = 1;

	// Home.
	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => __( 'Home', 'aureon' ),
		'item'     => home_url( '/' ),
	);

	if ( is_singular() ) {
		$post_type = get_post_type();
		if ( 'product' === $post_type ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => __( 'Shop', 'aureon' ),
				'item'     => wc_get_page_permalink( 'shop' ),
			);

			// Product category.
			$terms = get_the_terms( get_the_ID(), 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => $terms[0]->name,
					'item'     => get_term_link( $terms[0] ),
				);
			}
		} elseif ( 'post' === $post_type ) {
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => __( 'Blog', 'aureon' ),
					'item'     => home_url( '/blog/' ),
				);
			}
		}

		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
		);
	} elseif ( is_post_type_archive( 'product' ) || is_shop() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => __( 'Shop', 'aureon' ),
		);
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => single_term_title( '', false ),
			);
		}
	} elseif ( is_page() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
		);
	}

	return $items;
}

// ─── Canonical URL ─────────────────────────────────────────────
add_action( 'wp_head', 'aether_output_canonical', 4 );
/**
 * Output canonical URL to prevent duplicate content issues.
 */
function aether_output_canonical() {
	if ( is_admin() ) {
		return;
	}

	$canonical = aether_get_canonical_url();
	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s">', esc_url( $canonical ) );
		echo "\n";
	}
}

/**
 * Get the canonical URL for the current page.
 *
 * @return string|false Canonical URL or false if not needed.
 */
function aether_get_canonical_url() {
	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_front_page() ) {
		return home_url( '/' );
	}

	// For archives, use the current URL without pagination parameters.
	if ( is_archive() || is_home() ) {
		return get_pagenum_link( 1, false );
	}

	return false;
}

// ─── Extra SEO Meta Tags ───────────────────────────────────────
add_action( 'wp_head', 'aether_output_extra_seo_meta', 5 );
/**
 * Output additional SEO-related meta tags.
 */
function aether_output_extra_seo_meta() {
	if ( is_admin() ) {
		return;
	}

	// Author meta.
	if ( is_singular( 'post' ) ) {
		printf( '<meta name="author" content="%s">', esc_attr( get_the_author() ) );
		echo "\n";
	}

	// Robots meta (noindex on search, author archives if disabled).
	if ( is_search() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}

	if ( is_author() && ! get_option( 'show_avatars' ) ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}

	// Theme color for mobile browsers.
	$theme_color = get_theme_mod( 'aether_color_void', '#09090B' );
	printf( '<meta name="theme-color" content="%s">', esc_attr( $theme_color ) );
	echo "\n";

	// Geo meta tags.
	?>
	<meta name="geo.region" content="US">
	<meta name="geo.placename" content="United States">
	<?php
}

// ─── Sitemap Helper (for plugin integration) ───────────────────
/**
 * Check if a sitemap plugin is active.
 *
 * @return bool True if a sitemap solution is detected.
 */
function aether_has_sitemap() {
	// Yoast SEO.
	if ( defined( 'WPSEO_VERSION' ) ) {
		return true;
	}
	// Rank Math.
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		return true;
	}
	// XML Sitemap (core).
	if ( get_option( 'blog_public' ) ) {
		return true;
	}
	return false;
}
