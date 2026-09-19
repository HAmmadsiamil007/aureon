<?php
/**
 * Ferm Living design pack — homepage composition.
 *
 * Hooks into the 'aether_frontpage_sections' filter to define the exact
 * section sequence that reproduces the frozen Ferm Living homepage:
 *
 *   1. Hero split (2 panels)
 *   2. Category grid (7 categories)
 *   3. Editorial split — Kids + 4 kids products
 *   4. Editorial split — Storage + 4 storage products
 *   5. Editorial split — Sustainability
 *   6. Room grid (6 rooms)
 *   7. Newsletter
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'aether_frontpage_sections', 'ferm_living_frontpage_sections' );
add_filter( 'aether_adapter_shop_hero_data', 'ferm_living_shop_hero_data' );
add_filter( 'aether_adapter_about_data', 'ferm_living_about_data' );
add_filter( 'aether_demo_products', 'ferm_living_demo_products', 10, 2 );
add_filter( 'aether_demo_categories', 'ferm_living_demo_categories', 10, 2 );

/**
 * Define the Ferm Living homepage section sequence.
 *
 * @param array $sections Default section list.
 * @return array Modified section list.
 */
function ferm_living_frontpage_sections( $sections ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $sections;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';

	return array(
		'hero',
		'categories',

		/* ── Kids editorial + products ──────────────────────────── */
		array(
			'id'   => 'ferm-editorial-split',
			'data' => array(
				'title'     => 'Bestsellers for Kids',
				'text'      => '',
				'image'     => $assets . '/editorial/kids.webp',
				'image_alt' => 'Ferm Living Kids Bestsellers',
				'cta_label' => 'Shop Now',
				'cta_url'   => home_url('/collections/kids-bestsellers'),
				'reverse'   => false,
			),
		),
		'bestsellers',

		/* ── Storage editorial + products ───────────────────────── */
		array(
			'id'   => 'ferm-editorial-split',
			'data' => array(
				'title'     => 'Storage Solutions',
				'text'      => '',
				'image'     => $assets . '/editorial/storage.webp',
				'image_alt' => 'Ferm Living Storage Solutions',
				'cta_label' => 'Shop Now',
				'cta_url'   => home_url('/collections/storage'),
				'reverse'   => true,
			),
		),
		'secondary_products',

		/* ── Sustainability editorial ───────────────────────────── */
		array(
			'id'   => 'ferm-editorial-split',
			'data' => array(
				'title'     => 'Guided by Sustainability',
				'text'      => '',
				'image'     => $assets . '/editorial/sustainability.webp',
				'image_alt' => 'Ferm Living Certified Products',
				'cta_label' => 'Discover Certified Products',
				'cta_url'   => home_url('/collections/certified-products'),
				'reverse'   => false,
			),
		),

		/* ── Room grid ──────────────────────────────────────────── */
		'ferm-room-grid',

		'newsletter',
	);
}

/**
 * Override shop hero adapter data for Ferm Living.
 */
function ferm_living_shop_hero_data( $data ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	$data['subtitle'] = 'Danish design for modern living';

	return $data;
}

/**
 * Override about page adapter data for Ferm Living.
 *
 * Uses real copy from frozen fermliving.com/pages/about-ferm-living.html
 */
function ferm_living_about_data( $data ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';

	return array(
		'label'    => 'About Us',
		'title'    => 'About Ferm Living',
		'subtitle' => 'Danish design since 2005',
		'mission'  => array(
			'label'  => 'Our Story',
			'title'  => 'Designed to Make a Difference',
			'text'   => array(
				"Life is full of contrasts. As we navigate expectations and dreams in search of meaning and comfort, we long for a balanced life with room to be ourselves. A place where we can realise the true value of things and feel at home. Driven by a love for authentic design and a commitment to responsible choices, we craft honest products and calm environments that help you balance life's contrasts.",
				"From our home in Copenhagen, we collaborate with artisans around the world, fusing our Scandinavian mindset with global skills and traditions.",
				"Our collections are defined by soft forms, rich textures, and curious details, allowing you to create composed atmospheres with a touch of the unexpected. From materials and processes to production and delivery, we challenge ourselves to help shape a sustainable future, making it easier for you to make responsible choices.",
			),
			'image'  => '',
			'alt'    => 'Ferm Living Copenhagen',
		),
		'features' => array(
			'label' => 'Our Approach',
			'title' => 'What We Stand For',
			'items' => array(
				array(
					'icon'        => 'fa-leaf',
					'title'       => 'Scandinavian Mindset',
					'description' => 'Rooted in Copenhagen, we fuse Nordic simplicity with global craftsmanship.',
				),
				array(
					'icon'        => 'fa-hand-holding-heart',
					'title'       => 'Responsible Choices',
					'description' => 'From materials to delivery, we challenge ourselves to shape a sustainable future.',
				),
				array(
					'icon'        => 'fa-home',
					'title'       => 'Authentic Design',
					'description' => 'Soft forms, rich textures, and curious details define our collections.',
				),
				array(
					'icon'        => 'fa-globe',
					'title'       => 'Global Collaboration',
					'description' => 'We collaborate with artisans around the world, blending skills and traditions.',
				),
			),
		),
		'story'    => array(
			'quote' => 'We create collections of furniture, accessories and lighting, so you can create space to feel comfortably you.',
		),
		'values'   => array(
			'label' => 'Our Values',
			'title' => 'What Drives Us',
			'items' => array(
				array(
					'icon'        => 'fa-leaf',
					'title'       => 'Honest Products',
					'description' => 'We craft products that help you create space to feel comfortably you.',
				),
				array(
					'icon'        => 'fa-users',
					'title'       => 'Calm Environments',
					'description' => 'Our collections create composed atmospheres with a touch of the unexpected.',
				),
				array(
					'icon'        => 'fa-star',
					'title'       => 'Global Collaboration',
					'description' => 'We collaborate with artisans around the world, blending skills and traditions.',
				),
			),
		),
		'stats'    => array(
			'items' => array(
				array( 'number' => 'Copenhagen', 'label' => 'Headquarters' ),
				array( 'number' => '2005',        'label' => 'Founded' ),
				array( 'number' => '50+',         'label' => 'Countries' ),
			),
		),
	);
}

function ferm_living_demo_products( $items, $query_args ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $items;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	$all_products = array(
		/* ── Furniture ─────────────────────────────────────────── */
		array(
			'name'        => 'Parcel Hallway Cabinet',
			'price'       => '€899.00',
			'image'       => $assets . '/products/parcel-hallway-cabinet.png',
			'alt'         => 'Ferm Living Parcel Hallway Cabinet',
			'badge'       => 'Featured',
			'url'         => $shop_url,
			'tagline'     => 'Minimalist steel storage',
			'category'    => 'furniture',
		),
		array(
			'name'        => 'Kona Bookcase',
			'price'       => '€1,299.00',
			'image'       => $assets . '/products/kona-bookcase.png',
			'alt'         => 'Ferm Living Kona Bookcase',
			'badge'       => 'Featured',
			'url'         => $shop_url,
			'tagline'     => 'Elegant oak shelving',
			'category'    => 'furniture',
		),
		array(
			'name'        => 'Haze Wall Cabinet',
			'price'       => '€449.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Haze Wall Cabinet',
			'badge'       => 'New',
			'url'         => $shop_url,
			'tagline'     => 'Smoked glass wall storage',
			'category'    => 'furniture',
		),
		array(
			'name'        => 'Ripple Glasses Set',
			'price'       => '€59.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Ripple Glasses Set',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Handcrafted mouth-blown glass',
			'category'    => 'kitchen',
		),
		array(
			'name'        => 'Terrace Table',
			'price'       => '€1,899.00',
			'image'       => $assets . '/products/kona-bookcase.png',
			'alt'         => 'Ferm Living Terrace Table',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Solid oak dining table',
			'category'    => 'furniture',
		),
		array(
			'name'        => 'Form Chair',
			'price'       => '€349.00',
			'image'       => $assets . '/products/parcel-hallway-cabinet.png',
			'alt'         => 'Ferm Living Form Chair',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Moulded shell seat',
			'category'    => 'furniture',
		),

		/* ── Lighting ──────────────────────────────────────────── */
		array(
			'name'        => 'Haze Pendant',
			'price'       => '€299.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Haze Pendant',
			'badge'       => 'New',
			'url'         => $shop_url,
			'tagline'     => 'Smoked glass pendant light',
			'category'    => 'lighting',
		),
		array(
			'name'        => 'Ripple Pendant Large',
			'price'       => '€399.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Ripple Pendant Large',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Mouth-blown glass pendant',
			'category'    => 'lighting',
		),
		array(
			'name'        => 'Punctual Floor Lamp',
			'price'       => '€549.00',
			'image'       => $assets . '/products/parcel-hallway-cabinet.png',
			'alt'         => 'Ferm Living Punctual Floor Lamp',
			'badge'       => 'Featured',
			'url'         => $shop_url,
			'tagline'     => 'Brass and marble floor lamp',
			'category'    => 'lighting',
		),
		array(
			'name'        => 'Kizu Table Lamp',
			'price'       => '€229.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Kizu Table Lamp',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Lacquer and marble table lamp',
			'category'    => 'lighting',
		),

		/* ── Accessories ───────────────────────────────────────── */
		array(
			'name'        => 'Donkey Soft Toy',
			'price'       => '€49.00',
			'image'       => $assets . '/products/donkey-soft-toy.png',
			'alt'         => 'Ferm Living Donkey Soft Toy',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Playful decor for kids and adults',
			'category'    => 'accessories',
		),
		array(
			'name'        => 'Swif Bird Garland',
			'price'       => '€39.00',
			'image'       => $assets . '/products/swif-bird-garland.png',
			'alt'         => 'Ferm Living Swif Bird Garland',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Decorative paper garland',
			'category'    => 'accessories',
		),
		array(
			'name'        => 'Paper Pulp Box',
			'price'       => '€29.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Paper Pulp Box',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Sustainable paper storage',
			'category'    => 'accessories',
		),
		array(
			'name'        => 'Plant Box',
			'price'       => '€179.00',
			'image'       => $assets . '/products/pear-braided-storage.png',
			'alt'         => 'Ferm Living Plant Box',
			'badge'       => 'Bestseller',
			'url'         => $shop_url,
			'tagline'     => 'Powder-coated steel planter',
			'category'    => 'accessories',
		),
		array(
			'name'        => 'Twisted Vase',
			'price'       => '€89.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Twisted Vase',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Organic ceramic form',
			'category'    => 'accessories',
		),
		array(
			'name'        => 'Era Cushion',
			'price'       => '€69.00',
			'image'       => $assets . '/products/willora-braided-storage.png',
			'alt'         => 'Ferm Living Era Cushion',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Bouclé throw cushion',
			'category'    => 'textiles',
		),

		/* ── Kids ──────────────────────────────────────────────── */
		array(
			'name'        => 'Pear Braided Storage',
			'price'       => '€89.00',
			'image'       => $assets . '/products/pear-braided-storage.png',
			'alt'         => 'Ferm Living Pear Braided Storage',
			'badge'       => 'New',
			'url'         => $shop_url,
			'tagline'     => 'Organic forms meets practical storage',
			'category'    => 'kids',
		),
		array(
			'name'        => 'Willora Braided Storage',
			'price'       => '€69.00',
			'image'       => $assets . '/products/willora-braided-storage.png',
			'alt'         => 'Ferm Living Willora Braided Storage',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Handcrafted braided organizer',
			'category'    => 'kids',
		),
		array(
			'name'        => 'Donkey Seat',
			'price'       => '€119.00',
			'image'       => $assets . '/products/donkey-soft-toy.png',
			'alt'         => 'Ferm Living Donkey Seat',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Playful childrens seat',
			'category'    => 'kids',
		),
		array(
			'name'        => 'Miniature House Shelf',
			'price'       => '€79.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Miniature House Shelf',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Wooden house-shaped shelf',
			'category'    => 'kids',
		),
		array(
			'name'        => 'Punctual Kids Lamp',
			'price'       => '€149.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Punctual Kids Lamp',
			'badge'       => 'New',
			'url'         => $shop_url,
			'tagline'     => 'Colorful childrens lamp',
			'category'    => 'kids',
		),

		/* ── Textiles ──────────────────────────────────────────── */
		array(
			'name'        => 'Rounded Throw',
			'price'       => '€129.00',
			'image'       => $assets . '/products/willora-braided-storage.png',
			'alt'         => 'Ferm Living Rounded Throw',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Merino wool blend throw',
			'category'    => 'textiles',
		),
		array(
			'name'        => 'Semifina Duvet Cover',
			'price'       => '€159.00',
			'image'       => $assets . '/products/willora-braided-storage.png',
			'alt'         => 'Ferm Living Semifina Duvet Cover',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Organic cotton percale',
			'category'    => 'textiles',
		),

		/* ── Kitchen ───────────────────────────────────────────── */
		array(
			'name'        => 'Pompom Kitchen Textile',
			'price'       => '€25.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Pompom Kitchen Textile',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Organic cotton dish towel',
			'category'    => 'kitchen',
		),
		array(
			'name'        => 'Terrace Candle Holder',
			'price'       => '€49.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Terrace Candle Holder',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Powder-coated steel holder',
			'category'    => 'kitchen',
		),

		/* ── Outdoor ───────────────────────────────────────────── */
		array(
			'name'        => 'Plant Box Large',
			'price'       => '€299.00',
			'image'       => $assets . '/products/pear-braided-storage.png',
			'alt'         => 'Ferm Living Plant Box Large',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Outdoor steel planter',
			'category'    => 'outdoor',
		),
		array(
			'name'        => 'Terrace Side Table',
			'price'       => '€449.00',
			'image'       => $assets . '/products/kona-bookcase.png',
			'alt'         => 'Ferm Living Terrace Side Table',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Oak and steel side table',
			'category'    => 'outdoor',
		),

		/* ── Additional items for sale/pagination ──────────────── */
		array(
			'name'        => 'Signal Mirror',
			'price'       => '€199.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Signal Mirror',
			'badge'       => 'Sale',
			'url'         => $shop_url,
			'tagline'     => 'Brass-framed wall mirror',
			'category'    => 'accessories',
		),
		array(
			'name'        => 'Bau Bowl',
			'price'       => '€39.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Bau Bowl',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Stoneware serving bowl',
			'category'    => 'kitchen',
		),
		array(
			'name'        => 'Era Armchair',
			'price'       => '€1,199.00',
			'image'       => $assets . '/products/parcel-hallway-cabinet.png',
			'alt'         => 'Ferm Living Era Armchair',
			'badge'       => 'New',
			'url'         => $shop_url,
			'tagline'     => 'Bouclé upholstered armchair',
			'category'    => 'furniture',
		),
		array(
			'name'        => 'Rest Sofa',
			'price'       => '€2,499.00',
			'image'       => $assets . '/products/kona-bookcase.png',
			'alt'         => 'Ferm Living Rest Sofa',
			'badge'       => '',
			'url'         => $shop_url,
			'tagline'     => 'Modular fabric sofa',
			'category'    => 'furniture',
		),
	);

	// Category-aware filtering for shop/collection routes.
	$filtered = $all_products;
	$tax_query = isset( $query_args['tax_query'] ) ? $query_args['tax_query'] : array();
	$category_slug = '';

	// Detect category from tax_query (WooCommerce product_cat).
	if ( ! empty( $tax_query ) && is_array( $tax_query ) ) {
		foreach ( $tax_query as $tq ) {
			if ( isset( $tq['taxonomy'] ) && 'product_cat' === $tq['taxonomy'] && ! empty( $tq['terms'] ) ) {
				$terms = $tq['terms'];
				$category_slug = is_array( $terms ) ? reset( $terms ) : $terms;
				break;
			}
		}
	}

	// Filter by category slug if detected.
	if ( '' !== $category_slug ) {
		$filtered = array_filter( $filtered, function ( $p ) use ( $category_slug ) {
			return isset( $p['category'] ) && $p['category'] === $category_slug;
		} );
		$filtered = array_values( $filtered );
	}

	// Return a subset based on query args (default 4, offset 0).
	$per_page = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : 4;
	$offset   = isset( $query_args['paged'] ) ? ( (int) $query_args['paged'] - 1 ) * $per_page : 0;

	return array_slice( $filtered, $offset, $per_page );
}

/**
 * Provide Ferm Living demo categories when the Ferm design is active.
 *
 * @param array $items Current items (empty by default from adapter).
 * @param array $args  Adapter args.
 * @return array Ferm Living reference categories or empty array.
 */
function ferm_living_demo_categories( $items, $args ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $items;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';

	$categories = array(
		array( 'name' => 'Furniture',    'image' => $assets . '/categories/furniture.webp',    'modifier' => 'large' ),
		array( 'name' => 'Lighting',     'image' => $assets . '/categories/lighting.webp',     'modifier' => '' ),
		array( 'name' => 'Accessories',  'image' => $assets . '/categories/accessories.webp',  'modifier' => '' ),
		array( 'name' => 'Kids',         'image' => $assets . '/categories/kids.webp',         'modifier' => '' ),
		array( 'name' => 'Textiles',     'image' => $assets . '/categories/textiles.webp',     'modifier' => '' ),
		array( 'name' => 'Kitchen',      'image' => $assets . '/categories/kitchen.webp',      'modifier' => '' ),
		array( 'name' => 'Outdoor',      'image' => $assets . '/categories/outdoor.webp',      'modifier' => '' ),
	);

	$shop_url = function_exists( 'wc_get_page_permalink' )
		? wc_get_page_permalink( 'shop' )
		: home_url( '/shop/' );

	$items = array();
	foreach ( $categories as $cat ) {
		$items[] = array(
			'name'     => $cat['name'],
			'count'    => '',
			'image'    => $cat['image'],
			'alt'      => sprintf( __( 'Shop %s', 'aureon' ), $cat['name'] ),
			'url'      => $shop_url,
			'modifier' => $cat['modifier'],
			'behavior' => array( 'reveal' => true ),
		);
	}

	return $items;
}

/**
 * Override contact adapter data for Ferm Living.
 *
 * Replaces AETHER address/hours with Ferm Living Copenhagen info.
 */
add_filter( 'aether_adapter_contact_data', 'ferm_living_contact_data' );

function ferm_living_contact_data( $data ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	$data['info'] = array(
		array(
			'icon'  => 'fa-location-dot',
			'title' => __( 'Address', 'aureon' ),
			'lines' => array( 'Ferm Living ApS', 'Refshalevej 163A', '1432 Copenhagen K', 'Denmark' ),
		),
		array(
			'icon'  => 'fa-envelope',
			'title' => __( 'Email', 'aureon' ),
			'lines' => array( get_option( 'admin_email', 'info@fermliving.com' ) ),
			'href'  => 'mailto:' . get_option( 'admin_email', 'info@fermliving.com' ),
		),
		array(
			'icon'  => 'fa-clock',
			'title' => __( 'Hours', 'aureon' ),
			'lines' => array( 'Mon—Fri 9am—5pm CET' ),
		),
	);

	return $data;
}

/**
 * Override blog section data for Ferm Living.
 *
 * Replaces "The AETHER Dispatch" / "Latest From the Void" with Ferm branding.
 */
add_filter( 'aether_section_data', 'ferm_living_blog_override', 20, 2 );

function ferm_living_blog_override( $data, $id ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	if ( 'blog-grid' === $id ) {
		if ( empty( $data['label'] ) || 'Journal' === $data['label'] || __( 'Journal', 'aureon' ) === $data['label'] ) {
			$data['label'] = 'Journal';
		}
		if ( empty( $data['title'] ) || 'From the Void' === $data['title'] || __( 'From the Void', 'aureon' ) === $data['title'] || 'Latest From the Void' === $data['title'] ) {
			$data['title'] = 'From Ferm Living';
		}
	}

	return $data;
}

/**
 * Override footer data for Ferm Living.
 *
 * Replaces AETHER newsletter text and footer columns with Ferm-appropriate content.
 */
add_filter( 'aether_adapter_footer_data', 'ferm_living_footer_data' );

function ferm_living_footer_data( $data ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	$data['newsletter'] = array(
		'heading' => 'Stay Updated',
		'text'    => 'Sign up for news, offers and inspiration. No spam, ever.',
	);

	$data['columns'] = array(
		array(
			'heading' => 'Shop',
			'links'   => array(
				array( 'label' => 'Furniture',    'url' => $shop_url ),
				array( 'label' => 'Lighting',     'url' => $shop_url ),
				array( 'label' => 'Accessories',  'url' => $shop_url ),
				array( 'label' => 'Kids',         'url' => $shop_url ),
				array( 'label' => 'Textiles',     'url' => $shop_url ),
			),
		),
		array(
			'heading' => 'Support',
			'links'   => array(
				array( 'label' => 'Contact Us',       'url' => home_url( '/contact/' ) ),
				array( 'label' => 'Shipping Info',     'url' => home_url( '/faq/#shipping' ) ),
				array( 'label' => 'Returns & Exchanges', 'url' => home_url( '/faq/#returns' ) ),
				array( 'label' => 'FAQ',              'url' => home_url( '/faq/' ) ),
			),
		),
		array(
			'heading' => 'Company',
			'links'   => array(
				array( 'label' => 'About Ferm Living', 'url' => home_url( '/about/' ) ),
				array( 'label' => 'Journal',           'url' => home_url( '/blog/' ) ),
				array( 'label' => 'Sustainability',    'url' => home_url( '/about/' ) ),
				array( 'label' => 'Press',             'url' => home_url( '/about/' ) ),
			),
		),
	);

	$data['legal'] = array(
		array( 'label' => 'Privacy Policy', 'url' => home_url( '/privacy-policy/' ) ),
		array( 'label' => 'Terms of Use',   'url' => home_url( '/term-of-use/' ) ),
		array( 'label' => 'Cookie Policy',  'url' => home_url( '/cookie-policy/' ) ),
	);

	return $data;
}

/**
 * Provide room grid data for the Ferm Living homepage.
 *
 * Fills the ferm-room-grid section with actual room imagery from the pack.
 */
add_filter( 'aether_section_data', 'ferm_living_room_grid_data', 20, 2 );

function ferm_living_room_grid_data( $data, $id ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	if ( 'ferm-room-grid' !== $id ) {
		return $data;
	}

	// Only fill if empty (don't override Customizer-configured data).
	if ( ! empty( $data['items'] ) ) {
		return $data;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	$data['items'] = array(
		array(
			'title' => 'The Living Room',
			'image' => $assets . '/rooms/living-room.webp',
			'url'   => $shop_url,
			'links' => array(
				array( 'label' => 'Sofas',     'url' => $shop_url ),
				array( 'label' => 'Tables',    'url' => $shop_url ),
				array( 'label' => 'Storage',   'url' => $shop_url ),
			),
		),
		array(
			'title' => 'The Bedroom',
			'image' => $assets . '/rooms/bedroom.webp',
			'url'   => $shop_url,
			'links' => array(
				array( 'label' => 'Beds',      'url' => $shop_url ),
				array( 'label' => 'Nightstands', 'url' => $shop_url ),
				array( 'label' => 'Textiles',  'url' => $shop_url ),
			),
		),
		array(
			'title' => 'The Kitchen',
			'image' => $assets . '/rooms/kitchen.webp',
			'url'   => $shop_url,
			'links' => array(
				array( 'label' => 'Storage',   'url' => $shop_url ),
				array( 'label' => 'Kitchenware', 'url' => $shop_url ),
			),
		),
		array(
			'title' => 'The Hallway',
			'image' => $assets . '/rooms/hallway.webp',
			'url'   => $shop_url,
			'links' => array(
				array( 'label' => 'Cabinets',  'url' => $shop_url ),
				array( 'label' => 'Mirrors',   'url' => $shop_url ),
			),
		),
		array(
			'title' => 'Kids',
			'image' => $assets . '/rooms/kids.webp',
			'url'   => $shop_url,
			'links' => array(
				array( 'label' => 'Furniture', 'url' => $shop_url ),
				array( 'label' => 'Decor',     'url' => $shop_url ),
			),
		),
		array(
			'title' => 'Green Space',
			'image' => $assets . '/rooms/green-space.webp',
			'url'   => $shop_url,
			'links' => array(
				array( 'label' => 'Outdoor',   'url' => $shop_url ),
				array( 'label' => 'Planters',  'url' => $shop_url ),
			),
		),
	);

	return $data;
}

/**
 * Override the page hero for blog/about/contact when Ferm design is active.
 *
 * Intercepts the page-title hero component data to replace AETHER text.
 */
add_filter( 'aether_component_data', 'ferm_living_page_hero_override', 20, 2 );

function ferm_living_page_hero_override( $data, $id ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	if ( 'hero/page-title' !== $id ) {
		return $data;
	}

	// Blog page overrides.
	if ( is_home() || is_post_type_archive( 'post' ) ) {
		$data['label']    = 'Journal';
		$data['title']    = 'From Ferm Living';
		$data['subtitle'] = 'Stories about design, sustainability, and living well';
	}

	// Contact page overrides.
	if ( is_page( 'contact' ) ) {
		$data['label']    = 'Contact';
		$data['title']    = 'Get in Touch';
		$data['subtitle'] = 'Questions about an order, or the collection? We are here to help.';
	}

	return $data;
}

/**
 * Override FAQ section text for Ferm Living.
 */
add_filter( 'aether_section_data', 'ferm_living_faq_override', 20, 2 );

function ferm_living_faq_override( $data, $id ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	if ( 'faq' === $id ) {
		if ( empty( $data['subtitle'] ) || str_contains( $data['subtitle'], 'AETHER' ) ) {
			$data['subtitle'] = 'Everything you need to know about Ferm Living.';
		}
	}

	return $data;
}

/**
 * Override search overlay text for Ferm Living.
 *
 * Already handled in header.php component — this filter is a safety net
 * for any search-related data that flows through the adapter.
 */
add_filter( 'aether_adapter_search_data', 'ferm_living_search_data' );

function ferm_living_search_data( $data ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	$data['placeholder'] = 'Search Ferm Living...';
	$data['suggestions'] = array( 'Furniture', 'Lighting', 'Accessories', 'Kids', 'Textiles' );

	return $data;
}

/**
 * Override author bio fallback text for Ferm Living.
 */
add_filter( 'aether_component_data', 'ferm_living_author_bio_override', 20, 2 );

function ferm_living_author_bio_override( $data, $id ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $data;
	}

	if ( 'content/author-bio' === $id ) {
		if ( empty( $data['description'] ) || str_contains( $data['description'], 'AETHER' ) ) {
			$data['description'] = 'Written by the Ferm Living team.';
		}
	}

	return $data;
}
