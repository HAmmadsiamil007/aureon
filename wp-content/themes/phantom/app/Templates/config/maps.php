<?php
/**
 * Canonical template composition maps (Phase 12).
 *
 * Maps every frontend template slug to an ordered region → component
 * sequence consumed by Templates\Composer. Props may be static arrays or
 * callables `fn(array $data): array` resolved lazily against the request
 * data (posts, products, settings, …) — templates never hardcode business
 * logic, never touch WordPress/WooCommerce globals, and reference only
 * registry components (plan §Phase 12 acceptance).
 *
 * @package Phantom\Core\Templates
 * @since 0.12.0
 */

declare( strict_types=1 );

return array(
	// ------------------------------------------------------------------
	// Site shell
	// ------------------------------------------------------------------
	'header'      => array(
		'main' => array(
			array(
				'component' => 'header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['site_name'] ?? '' ),
					'items' => (array) ( $data['menu'] ?? array() ),
				),
			),
		),
	),
	'footer'      => array(
		'main' => array(
			array(
				'component' => 'footer',
				'props'     => static fn( array $data ): array => array(
					'columns'   => array(
						array(
							'name'  => 'footer-columns',
							'props' => array(
								'columns' => (array) ( $data['footer_columns'] ?? array() ),
							),
						),
					),
					'copyright' => array(
						array(
							'name'  => 'copyright',
							'props' => array(
								'text'  => (string) ( $data['copyright'] ?? '' ),
								'links' => (array) ( $data['legal_links'] ?? array() ),
							),
						),
					),
				),
			),
		),
	),

	// ------------------------------------------------------------------
	// Home / landing
	// ------------------------------------------------------------------
	'home'        => array(
		'hero' => array(
			array(
				'component' => 'hero',
				'props'     => static fn( array $data ): array => array(
					'title'   => (string) ( $data['hero_title'] ?? '' ),
					'text'    => (string) ( $data['hero_text'] ?? '' ),
					'eyebrow' => (string) ( $data['hero_eyebrow'] ?? '' ),
					'image'   => (string) ( $data['hero_image'] ?? '' ),
					'actions' => (array) ( $data['hero_actions'] ?? array() ),
				),
			),
		),
		'main' => array(
			array(
				'component' => 'featured-collection',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['featured_title'] ?? 'Featured' ),
					'items' => (array) ( $data['featured_items'] ?? array() ),
				),
			),
			array(
				'component' => 'products-grid',
				'props'     => static fn( array $data ): array => array(
					'title'    => (string) ( $data['products_title'] ?? 'Products' ),
					'products' => (array) ( $data['products'] ?? array() ),
				),
			),
			array(
				'component' => 'testimonials',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['testimonials_title'] ?? 'Testimonials' ),
					'items' => (array) ( $data['testimonials'] ?? array() ),
				),
			),
		),
		'cta'  => array(
			array(
				'component' => 'cta',
				'props'     => static fn( array $data ): array => array(
					'title'   => (string) ( $data['cta_title'] ?? '' ),
					'text'    => (string) ( $data['cta_text'] ?? '' ),
					'actions' => (array) ( $data['cta_actions'] ?? array() ),
				),
			),
		),
	),
	'landing'     => array(
		'main' => array(
			array(
				'component' => 'hero',
				'props'     => static fn( array $data ): array => array(
					'title'   => (string) ( $data['hero_title'] ?? '' ),
					'text'    => (string) ( $data['hero_text'] ?? '' ),
					'image'   => (string) ( $data['hero_image'] ?? '' ),
					'actions' => (array) ( $data['hero_actions'] ?? array() ),
				),
			),
			array(
				'component' => 'features-grid',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['features_title'] ?? 'Features' ),
					'items' => (array) ( $data['features'] ?? array() ),
				),
			),
			array(
				'component' => 'pricing-table',
				'props'     => static fn( array $data ): array => array(
					'title'    => (string) ( $data['pricing_title'] ?? '' ),
					'price'    => (string) ( $data['pricing_price'] ?? '' ),
					'period'   => (string) ( $data['pricing_period'] ?? '' ),
					'features' => (array) ( $data['pricing_features'] ?? array() ),
					'cta'      => (array) ( $data['pricing_cta'] ?? array() ),
				),
			),
			array(
				'component' => 'faq',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['faq_title'] ?? 'FAQ' ),
					'items' => (array) ( $data['faq_items'] ?? array() ),
				),
			),
			array(
				'component' => 'newsletter',
				'props'     => static fn( array $data ): array => array(
					'title'        => (string) ( $data['newsletter_title'] ?? '' ),
					'text'         => (string) ( $data['newsletter_text'] ?? '' ),
					'submit_label' => (string) ( $data['newsletter_submit'] ?? 'Subscribe' ),
				),
			),
		),
	),

	// ------------------------------------------------------------------
	// Commerce
	// ------------------------------------------------------------------
	'shop'        => array(
		'main' => array(
			array(
				'component' => 'archive-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? '' ),
					'text'  => (string) ( $data['text'] ?? '' ),
				),
			),
			array(
				'component' => 'faceted-nav',
				'props'     => static fn( array $data ): array => array(
					'title'  => (string) ( $data['filters_title'] ?? 'Shop by' ),
					'facets' => (array) ( $data['facets'] ?? array() ),
				),
			),
			array(
				'component' => 'products-grid',
				'props'     => static fn( array $data ): array => array(
					'title'    => (string) ( $data['title'] ?? 'Products' ),
					'products' => (array) ( $data['products'] ?? array() ),
				),
			),
			array(
				'component' => 'pagination',
				'props'     => static fn( array $data ): array => array(
					'current'  => (int) ( $data['current_page'] ?? 1 ),
					'total'    => (int) ( $data['total_pages'] ?? 1 ),
					'page_url' => (string) ( $data['base_url'] ?? '#' ),
				),
			),
		),
	),
	'product'     => array(
		'main' => array(
			array(
				'component' => 'breadcrumb',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['breadcrumbs'] ?? array() ),
				),
			),
			array(
				'component' => 'product-gallery',
				'props'     => static fn( array $data ): array => array(
					'images' => (array) ( $data['gallery'] ?? array() ),
				),
			),
			array(
				'component' => 'product-tabs',
				'props'     => static fn( array $data ): array => array(
					'tabs' => (array) ( $data['tabs'] ?? array() ),
				),
			),
			array(
				'component' => 'reviews',
				'props'     => static fn( array $data ): array => array(
					'rating' => (float) ( $data['rating'] ?? 0.0 ),
					'count'  => (int) ( $data['rating_count'] ?? 0 ),
					'items'  => (array) ( $data['reviews'] ?? array() ),
				),
			),
			array(
				'component' => 'related-posts',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['related_title'] ?? 'Related' ),
					'posts' => (array) ( $data['related'] ?? array() ),
				),
			),
		),
	),
	'cart'        => array(
		'main' => array(
			array(
				'component' => 'cart-summary',
				'props'     => static fn( array $data ): array => array(
					'rows'  => (array) ( $data['rows'] ?? array() ),
					'total' => (string) ( $data['total'] ?? '' ),
				),
			),
			array(
				'component' => 'cart-drawer',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['items'] ?? array() ),
					'total' => (string) ( $data['total'] ?? '' ),
				),
			),
			array(
				'component' => 'order-summary',
				'props'     => static fn( array $data ): array => array(
					'rows'  => (array) ( $data['rows'] ?? array() ),
					'total' => (string) ( $data['total'] ?? '' ),
				),
			),
		),
	),
	'checkout'    => array(
		'main' => array(
			array(
				'component' => 'checkout-blocks',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['items'] ?? array() ),
					'total' => (string) ( $data['total'] ?? '' ),
				),
			),
			array(
				'component' => 'order-summary',
				'props'     => static fn( array $data ): array => array(
					'rows'  => (array) ( $data['rows'] ?? array() ),
					'total' => (string) ( $data['total'] ?? '' ),
				),
			),
		),
	),
	'thank-you'   => array(
		'main' => array(
			array(
				'component' => 'empty-state',
				'props'     => static fn( array $data ): array => array(
					'title'  => (string) ( $data['title'] ?? 'Thank you' ),
					'text'   => (string) ( $data['text'] ?? '' ),
					'action' => (array) ( $data['action'] ?? array() ),
				),
			),
			array(
				'component' => 'order-summary',
				'props'     => static fn( array $data ): array => array(
					'rows'  => (array) ( $data['rows'] ?? array() ),
					'total' => (string) ( $data['total'] ?? '' ),
				),
			),
		),
	),
	'account'     => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'My Account' ),
				),
			),
			array(
				'component' => 'tabs',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Account' ),
					'tabs'  => (array) ( $data['tabs'] ?? array() ),
				),
			),
		),
	),
	'wishlist'    => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Wishlist' ),
				),
			),
			array(
				'component' => 'products-grid',
				'props'     => static fn( array $data ): array => array(
					'products' => (array) ( $data['products'] ?? array() ),
				),
			),
		),
	),
	'compare'     => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Compare' ),
				),
			),
			array(
				'component' => 'products-grid',
				'props'     => static fn( array $data ): array => array(
					'products' => (array) ( $data['products'] ?? array() ),
				),
			),
		),
	),

	// ------------------------------------------------------------------
	// Blog & content
	// ------------------------------------------------------------------
	'blog'        => array(
		'main' => array(
			array(
				'component' => 'archive-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Blog' ),
					'text'  => (string) ( $data['text'] ?? '' ),
				),
			),
			array(
				'component' => 'blog-grid',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Latest posts' ),
					'posts' => (array) ( $data['posts'] ?? array() ),
				),
			),
			array(
				'component' => 'pagination',
				'props'     => static fn( array $data ): array => array(
					'current'  => (int) ( $data['current_page'] ?? 1 ),
					'total'    => (int) ( $data['total_pages'] ?? 1 ),
					'page_url' => (string) ( $data['base_url'] ?? '#' ),
				),
			),
		),
	),
	'single-post' => array(
		'main' => array(
			array(
				'component' => 'breadcrumb',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['breadcrumbs'] ?? array() ),
				),
			),
			array(
				'component' => 'blog-card',
				'props'     => static fn( array $data ): array => array(
					'title'    => (string) ( $data['title'] ?? '' ),
					'url'      => (string) ( $data['url'] ?? '#' ),
					'image'    => (string) ( $data['image'] ?? '' ),
					'excerpt'  => (string) ( $data['excerpt'] ?? '' ),
					'date'     => (string) ( $data['date'] ?? '' ),
					'category' => (string) ( $data['category'] ?? '' ),
				),
			),
			array(
				'component' => 'author-box',
				'props'     => static fn( array $data ): array => array(
					'name'   => (string) ( $data['author_name'] ?? '' ),
					'bio'    => (string) ( $data['author_bio'] ?? '' ),
					'avatar' => (string) ( $data['author_avatar'] ?? '' ),
					'url'    => (string) ( $data['author_url'] ?? '' ),
				),
			),
			array(
				'component' => 'related-posts',
				'props'     => static fn( array $data ): array => array(
					'title' => 'Related posts',
					'posts' => (array) ( $data['related'] ?? array() ),
				),
			),
			array(
				'component' => 'comments',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['comments'] ?? array() ),
				),
			),
		),
	),
	'archive'     => array(
		'main' => array(
			array(
				'component' => 'archive-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Archive' ),
					'text'  => (string) ( $data['text'] ?? '' ),
				),
			),
			array(
				'component' => 'blog-grid',
				'props'     => static fn( array $data ): array => array(
					'posts' => (array) ( $data['posts'] ?? array() ),
				),
			),
			array(
				'component' => 'pagination',
				'props'     => static fn( array $data ): array => array(
					'current'  => (int) ( $data['current_page'] ?? 1 ),
					'total'    => (int) ( $data['total_pages'] ?? 1 ),
					'page_url' => (string) ( $data['base_url'] ?? '#' ),
				),
			),
		),
	),
	'author'      => array(
		'main' => array(
			array(
				'component' => 'author-box',
				'props'     => static fn( array $data ): array => array(
					'name'   => (string) ( $data['author_name'] ?? '' ),
					'bio'    => (string) ( $data['author_bio'] ?? '' ),
					'avatar' => (string) ( $data['author_avatar'] ?? '' ),
					'url'    => (string) ( $data['author_url'] ?? '' ),
				),
			),
			array(
				'component' => 'blog-grid',
				'props'     => static fn( array $data ): array => array(
					'posts' => (array) ( $data['posts'] ?? array() ),
				),
			),
			array(
				'component' => 'pagination',
				'props'     => static fn( array $data ): array => array(
					'current'  => (int) ( $data['current_page'] ?? 1 ),
					'total'    => (int) ( $data['total_pages'] ?? 1 ),
					'page_url' => (string) ( $data['base_url'] ?? '#' ),
				),
			),
		),
	),
	'search'      => array(
		'main' => array(
			array(
				'component' => 'search-results',
				'props'     => static fn( array $data ): array => array(
					'heading' => 'Search results',
					'query'   => (string) ( $data['query'] ?? '' ),
					'count'   => (int) ( $data['count'] ?? 0 ),
					'results' => (array) ( $data['results'] ?? array() ),
				),
			),
			array(
				'component' => 'empty-state',
				'props'     => static fn( array $data ): array => array(
					'title' => (int) ( $data['count'] ?? 0 ) > 0 ? '' : 'Nothing found',
					'text'  => (int) ( $data['count'] ?? 0 ) > 0 ? '' : 'Try a different search.',
				),
			),
			array(
				'component' => 'pagination',
				'props'     => static fn( array $data ): array => array(
					'current'  => (int) ( $data['current_page'] ?? 1 ),
					'total'    => (int) ( $data['total_pages'] ?? 1 ),
					'page_url' => (string) ( $data['base_url'] ?? '#' ),
				),
			),
		),
	),
	'not-found'   => array(
		'main' => array(
			array(
				'component' => 'module-404',
				'props'     => static fn( array $data ): array => array(
					'title'        => 'Page not found',
					'text'         => (string) ( $data['text'] ?? 'The page you are looking for does not exist.' ),
					'search_label' => 'Search',
					'search_url'   => (string) ( $data['search_url'] ?? '#' ),
					'home_label'   => 'Back to home',
					'home_url'     => (string) ( $data['home_url'] ?? '#' ),
				),
			),
		),
	),

	// ------------------------------------------------------------------
	// Utility pages
	// ------------------------------------------------------------------
	'contact'     => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Contact' ),
					'text'  => (string) ( $data['text'] ?? '' ),
				),
			),
			array(
				'component' => 'newsletter',
				'props'     => static fn( array $data ): array => array(
					'title'        => (string) ( $data['title'] ?? 'Get in touch' ),
					'submit_label' => 'Send',
				),
			),
			array(
				'component' => 'faq',
				'props'     => static fn( array $data ): array => array(
					'title' => 'Common questions',
					'items' => (array) ( $data['faq_items'] ?? array() ),
				),
			),
		),
	),
	'about'       => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'About' ),
					'text'  => (string) ( $data['text'] ?? '' ),
				),
			),
			array(
				'component' => 'timeline',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['timeline'] ?? array() ),
				),
			),
			array(
				'component' => 'team',
				'props'     => static fn( array $data ): array => array(
					'title'   => 'Our team',
					'members' => (array) ( $data['members'] ?? array() ),
				),
			),
			array(
				'component' => 'statistics',
				'props'     => static fn( array $data ): array => array(
					'items' => (array) ( $data['stats'] ?? array() ),
				),
			),
		),
	),
	'faq-page'    => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'FAQ' ),
				),
			),
			array(
				'component' => 'faq',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Frequently asked questions' ),
					'items' => (array) ( $data['faq_items'] ?? array() ),
				),
			),
			array(
				'component' => 'cta',
				'props'     => static fn( array $data ): array => array(
					'title'   => (string) ( $data['cta_title'] ?? '' ),
					'actions' => (array) ( $data['cta_actions'] ?? array() ),
				),
			),
		),
	),
	'privacy'     => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Privacy Policy' ),
				),
			),
			array(
				'component' => 'accordion',
				'props'     => static fn( array $data ): array => array(
					'title' => 'Policy details',
					'items' => (array) ( $data['sections'] ?? array() ),
				),
			),
		),
	),
	'terms'       => array(
		'main' => array(
			array(
				'component' => 'page-header',
				'props'     => static fn( array $data ): array => array(
					'title' => (string) ( $data['title'] ?? 'Terms of Service' ),
				),
			),
			array(
				'component' => 'accordion',
				'props'     => static fn( array $data ): array => array(
					'title' => 'Terms',
					'items' => (array) ( $data['sections'] ?? array() ),
				),
			),
		),
	),
);
