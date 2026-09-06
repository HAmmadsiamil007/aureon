<?php
/**
 * Sole Origine → local AUREON/WooCommerce master sync.
 * Idempotent. Reads wp-content/frontend/_so_import/so-import.json.
 *
 * Covers: purge demo products, category tree, product upsert, image
 * sideload, brand identity (name/logo/favicon), hero slides, monochrome
 * palette + typography (customizer buckets), nav menus.
 */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/var/www/html/' ); }
error_reporting( E_ALL & ~E_DEPRECATED & ~E_NOTICE );
$log = array( 'purged' => 0, 'categories' => array(), 'products' => 0, 'media' => 0, 'media_fail' => array(), 'menu' => array(), 'options' => array() );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-includes/pluggable.php';

$json_file = ABSPATH . 'wp-content/frontend/_so_import/so-import.json';
$data = json_decode( file_get_contents( $json_file ), true );
if ( empty( $data['products'] ) ) { echo wp_json_encode( array( 'fatal' => 'dataset missing' ) ); exit; }
$site = get_option( 'siteurl', 'http://localhost:8080' );

/** Remote sideload → attachment id (idempotent by md5(url) in meta). */
function so_media( $url, $title ) {
	$key = '_so_src_md5';
	$md5 = md5( $url );
	$existing = get_posts( array( 'post_type' => 'attachment', 'numberposts' => 1, 'post_status' => 'inherit', 'meta_key' => $key, 'meta_value' => $md5, 'fields' => 'ids' ) );
	if ( $existing ) { return (int) $existing[0]; }
	$ctx = stream_context_create( array( 'http' => array( 'timeout' => 40, 'user_agent' => 'Mozilla/5.0 (AUREON sync)' ) ) );
	$bin = @file_get_contents( $url, false, $ctx );
	if ( $bin === false || strlen( $bin ) < 200 ) { return 0; }
	$path = parse_url( $url, PHP_URL_PATH );
	$base = sanitize_file_name( basename( $path ) );
	$ext  = strtolower( pathinfo( $base, PATHINFO_EXTENSION ) );
	if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp', 'gif' ), true ) ) { $ext = 'jpg'; }
	$base = preg_replace( '/\.(jpe?g|png|webp|gif)$/i', '', $base ) . '-' . substr( $md5, 0, 6 ) . '.' . $ext;
	$up = wp_upload_bits( $base, null, $bin );
	if ( ! empty( $up['error'] ) ) { return 0; }
	$ft = wp_check_filetype( $up['file'] );
	$att = array( 'post_mime_type' => $ft['type'], 'post_title' => $title, 'post_status' => 'inherit' );
	$aid = wp_insert_attachment( $att, $up['file'] );
	if ( ! $aid || is_wp_error( $aid ) ) { return 0; }
	$meta = wp_generate_attachment_metadata( $aid, $up['file'] );
	wp_update_attachment_metadata( $aid, $meta );
	update_post_meta( $aid, $key, $md5 );
	return (int) $aid;
}

// ───────────────────────────────────────────────────────────────
// 0. Currency (matches source store: CHF)
// ───────────────────────────────────────────────────────────────
update_option( 'woocommerce_currency', 'CHF' );
update_option( 'woocommerce_currency_pos', 'left_space' );
$log['currency'] = get_option( 'woocommerce_currency' );

// ───────────────────────────────────────────────────────────────
// 1. Purge non-SO products (keep only SOLEORIGINE catalog)
// ───────────────────────────────────────────────────────────────
$all = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
foreach ( $all as $pid ) {
	$sku = get_post_meta( $pid, '_sku', true );
	if ( strpos( (string) $sku, 'SO-' ) === 0 ) { continue; }
	wp_delete_post( $pid, true );
	$log['purged']++;
}

// ───────────────────────────────────────────────────────────────
// 2. Category tree (hierarchy mirrors the source)
// ───────────────────────────────────────────────────────────────
$cats = array(
	'MEN Collection'               => array( 'slug' => 'men_collection',                 'parent' => null,  'order' => 1 ),
	'MEN PREMIUM'                  => array( 'slug' => 'men-premium',                    'parent' => 'men_collection', 'order' => 2 ),
	'PREMIUM LOAFERS'              => array( 'slug' => 'premium-loafers',                'parent' => null,  'order' => 3 ),
	'Executive Leather Collection' => array( 'slug' => 'executive-leather-collection',   'parent' => null,  'order' => 4 ),
	'WOMEN Collection'             => array( 'slug' => 'women-collection',               'parent' => null,  'order' => 5 ),
	"WOMEN'S COLLECTION 2026"      => array( 'slug' => 'womens-collection-2026',         'parent' => 'women-collection', 'order' => 6 ),
	'Pearl Elegance Collection'    => array( 'slug' => 'pearl-elegance-collection',      'parent' => 'women-collection', 'order' => 7 ),
);
$catIds = array();
foreach ( $cats as $name => $c ) {
	$parent_id = $c['parent'] ? $catIds[ $c['parent'] ] : 0;
	$term = term_exists( $c['slug'], 'product_cat' );
	if ( ! $term ) {
		$term = wp_insert_term( $name, 'product_cat', array( 'slug' => $c['slug'], 'parent' => $parent_id ) );
		if ( is_wp_error( $term ) ) { $log['categories'][ $name ] = 'insert-error: ' . $term->get_error_message(); continue; }
		$tid = is_array( $term ) ? $term['term_id'] : (int) $term;
	} else {
		$tid = is_array( $term ) ? $term['term_id'] : (int) $term;
		$cur = get_term( $tid, 'product_cat' );
		if ( $cur && (int) $cur->parent !== (int) $parent_id ) {
			wp_update_term( $tid, 'product_cat', array( 'parent' => $parent_id, 'name' => $name ) );
		}
	}
	$catIds[ $c['slug'] ] = (int) $tid;
	$log['categories'][ $name ] = $tid;
}
// drop demo-only product_cat terms that no longer hold products (except uncategorized)
$keep_slugs = array_values( array_map( function ( $c ) { return $c['slug']; }, $cats ) );
$keep_slugs[] = 'uncategorized';
$existing_terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'id=>slug' ) );
foreach ( $existing_terms as $tid => $slug ) {
	if ( in_array( $slug, $keep_slugs, true ) ) { continue; }
	$cnt = (int) get_term( $tid, 'product_cat' )->count;
	if ( $cnt === 0 ) { wp_delete_term( $tid, 'product_cat' ); $log['categories']['removed_' . $slug] = $tid; }
}

// name → term id map
$nameToId = array();
foreach ( $catIds as $slug => $tid ) { $nameToId[ get_term( $tid, 'product_cat' )->name ] = $tid; }

// ───────────────────────────────────────────────────────────────
// 3. Product upsert + 4. images
// ───────────────────────────────────────────────────────────────
foreach ( $data['products'] as $p ) {
	$sku = $p['sku'];
	$pid = wc_get_product_id_by_sku( $sku );
	$is_new = ! $pid;
	if ( ! $pid ) {
		$pid = wp_insert_post( array( 'post_type' => 'product', 'post_status' => $p['status'], 'post_title' => $p['title'], 'post_content' => $p['desc'], 'post_excerpt' => $p['short_desc'] ) );
	} else {
		wp_update_post( array( 'ID' => $pid, 'post_title' => $p['title'], 'post_content' => $p['desc'], 'post_excerpt' => $p['short_desc'], 'post_status' => $p['status'] ) );
	}
	if ( ! $pid || is_wp_error( $pid ) ) { $log['media_fail'][] = 'product-create ' . $sku; continue; }
	$prod = wc_get_product( $pid );
	if ( ! $prod ) { continue; }
	$prod->set_sku( $sku );
	$prod->set_regular_price( '' !== $p['regular'] ? $p['regular'] : '' );
	$prod->set_sale_price( '' !== $p['sale'] ? $p['sale'] : '' );
	$prod->set_manage_stock( false );
	$prod->set_stock_status( ! empty( $p['instock'] ) ? 'instock' : 'outofstock' );
	// categories (mirror source assignments incl. parent+child where present)
	$term_ids = array();
	foreach ( (array) $p['cats'] as $cn ) { if ( isset( $nameToId[ $cn ] ) ) { $term_ids[] = $nameToId[ $cn ]; } }
	if ( $term_ids ) { wp_set_object_terms( $pid, array_values( array_unique( $term_ids ) ), 'product_cat' ); }

	// images: first = featured, rest = gallery
	$img_ids = array();
	foreach ( (array) $p['images'] as $iu ) {
		$aid = so_media( $iu, $p['title'] );
		if ( $aid ) { $img_ids[] = $aid; } else { $log['media_fail'][] = basename( parse_url( $iu, PHP_URL_PATH ) ); }
	}
	if ( $img_ids ) {
		$prod->set_image_id( $img_ids[0] );
		$prod->set_gallery_image_ids( array_slice( $img_ids, 1, 12 ) );
	}
	$prod->save();
	$log['products']++;
	if ( $is_new ) { $log['created'][] = $sku; }
}

wc_delete_product_transients();
$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'ids' ) );
foreach ( (array) $terms as $tid ) { wp_update_term_count_now( array( $tid ), 'product_cat' ); }

// ───────────────────────────────────────────────────────────────
// 5. Brand identity + hero + customizer buckets
// ───────────────────────────────────────────────────────────────
// 5a. Logo & favicon & hero images (source URLs)
$assets = array(
	'logo'    => array( 'https://soleorigine.com/wp-content/uploads/2026/08/cropped-file_00000000993881f89e559e0c25e9218c.png', 'Sole Origine Logo' ),
	'favicon' => array( 'https://soleorigine.com/wp-content/uploads/2026/06/cropped-cropped-IMG-20260627-WA0030.jpg', 'Sole Origine Favicon' ),
	'hero1'   => array( 'https://soleorigine.com/wp-content/uploads/2026/07/file_00000000cb807208a9188c7631d0e453.png', 'Sole Origine Hero Mens Collection' ),
	'hero2'   => array( 'https://soleorigine.com/wp-content/uploads/2026/07/file_00000000912c72098d7b1ff2f2a53860.png', 'Sole Origine Hero Womens Collection' ),
);
foreach ( $assets as $k => $a ) { $log['assets'][ $k ] = so_media( $a[0], $a[1] ); }

// 5b. Site identity
update_option( 'blogname', 'Sole Origine' );
update_option( 'blogdescription', 'Premium handcrafted leather footwear' );
if ( ! empty( $log['assets']['logo'] ) ) {
	set_theme_mod( 'custom_logo', (int) $log['assets']['logo'] );
	update_option( 'site_icon', (int) $log['assets']['favicon'] ? (int) $log['assets']['favicon'] : (int) get_option( 'site_icon' ) );
}

// 5c. Monochrome black & white palette + typography (Customizer bucket)
$palette = array(
	'aether_color_bg'           => '#000000',
	'aether_color_surface'      => '#0A0A0A',
	'aether_color_surface_2'    => '#121212',
	'aether_color_surface_3'    => '#1A1A1A',
	'aether_color_text'         => '#FFFFFF',
	'aether_color_muted'        => '#A1A1AA',
	'aether_color_border'       => '#2A2A2A',
	'aether_color_accent'       => '#FFFFFF',
	'aether_color_accent_hover' => '#D4D4D8',
	'aether_font_heading'       => 'Playfair Display, serif',
	'aether_font_body'          => 'Albert Sans, sans-serif',
);
$bucket = get_option( 'aureon_settings', array() );
if ( ! is_array( $bucket ) ) { $bucket = array(); }
$bucket = array_merge( $bucket, $palette );
update_option( 'aureon_settings', $bucket );
// Mirror top-level legacy layer so every resolver path agrees.
foreach ( $palette as $k => $v ) { update_option( $k, $v ); }
$log['options']['bucket_keys'] = array_keys( $palette );

// 5d. Hero slides via Customizer (aether_hero_slides)
$hero_url_1 = wp_get_attachment_url( (int) $log['assets']['hero1'] );
$hero_url_2 = wp_get_attachment_url( (int) $log['assets']['hero2'] );
$bucket['aether_hero_slides'] = array(
	array(
		'id' => 'so-hero-1', 'visible' => true,
		'badge' => 'COLLECTIONS', 'headline' => "Men's Collection",
		'subline' => 'Timeless shoes for the modern gentleman. Crafted from origin. Made to last.',
		'image' => $hero_url_1, 'mobile_image' => $hero_url_1, 'image_alt' => "Men's Collection",
		'overlay' => 'rgba(0,0,0,0.35)',
		'primary_cta' => array( 'label' => 'Explore Collection', 'url' => $site . '/shop/' ),
		'secondary_cta' => array( 'label' => '', 'url' => '' ),
	),
	array(
		'id' => 'so-hero-2', 'visible' => true,
		'badge' => 'COLLECTIONS', 'headline' => "Women's Collection",
		'subline' => 'Refined silhouettes, hand-finished in premium leather.',
		'image' => $hero_url_2, 'mobile_image' => $hero_url_2, 'image_alt' => "Women's Collection",
		'overlay' => 'rgba(0,0,0,0.35)',
		'primary_cta' => array( 'label' => 'Explore Collection', 'url' => $site . '/shop/' ),
		'secondary_cta' => array( 'label' => '', 'url' => '' ),
	),
);
update_option( 'aureon_settings', $bucket );
$log['hero'] = array( '1' => $hero_url_1, '2' => $hero_url_2 );

// ───────────────────────────────────────────────────────────────
// 6. Menus (mirror source header nav; dedupe & local URLs)
// ───────────────────────────────────────────────────────────────
function so_menu_item( $menu_id, $title, $url, $parent = 0, $order = 0 ) {
	return wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title' => $title,
		'menu-item-url' => $url,
		'menu-item-status' => 'publish',
		'menu-item-type' => 'custom',
		'menu-item-parent-id' => $parent,
		'menu-item-position' => $order,
	) );
}
foreach ( array( 'Primary Menu', 'Footer Menu' ) as $mname ) {
	$existing = get_term_by( 'name', $mname, 'nav_menu' );
	if ( $existing ) { wp_delete_nav_menu( $existing->term_id ); }
}
$primary = wp_create_nav_menu( 'Primary Menu' );
$footer  = wp_create_nav_menu( 'Footer Menu' );
$sc = $site . '/product-category/';
$top = array(
	'Home' => $site . '/',
	'Shop' => $site . '/shop/',
	'MEN Collection' => $sc . 'men_collection/',
	'WOMEN Collection' => $sc . 'women-collection/',
	'Our Story' => $site . '/about-us/',
	'Contact' => $site . '/contact/',
);
$parent_men = array();
$parent_wom = array();
$order = 1;
foreach ( $top as $title => $url ) {
	$item = so_menu_item( $primary, $title, $url, 0, $order++ );
	if ( 'MEN Collection' === $title ) { $parent_men['primary'] = $item; }
	if ( 'WOMEN Collection' === $title ) { $parent_wom['primary'] = $item; }
}
// primary submenus (source hierarchy)
so_menu_item( $primary, 'MEN PREMIUM', $sc . 'men_collection/men-premium/', $parent_men['primary'], 2 );
so_menu_item( $primary, 'PREMIUM LOAFERS', $sc . 'premium-loafers/', $parent_men['primary'], 3 );
so_menu_item( $primary, 'Executive Leather Collection', $sc . 'executive-leather-collection/', $parent_men['primary'], 4 );
so_menu_item( $primary, "WOMEN'S COLLECTION 2026", $sc . 'women-collection/womens-collection-2026/', $parent_wom['primary'], 6 );
so_menu_item( $primary, 'Pearl Elegance Collection', $sc . 'women-collection/pearl-elegance-collection/', $parent_wom['primary'], 7 );
// secondary utility items at the end of the primary menu
so_menu_item( $primary, 'My Account', $site . '/my-account/', 0, 20 );
so_menu_item( $primary, 'Cart', $site . '/cart/', 0, 21 );

// footer: shop + collections + story/contact + policies
$footer_spec = array(
	'Shop' => $site . '/shop/',
	'MEN Collection' => $sc . 'men_collection/',
	'WOMEN Collection' => $sc . 'women-collection/',
	'Premium Loafers' => $sc . 'premium-loafers/',
	'Our Story' => $site . '/about-us/',
	'Contact Us' => $site . '/contact-us/',
	'Shipping' => $site . '/shipping/',
	'Return & Refund' => $site . '/return-and-refund/',
	'Privacy Policy' => $site . '/privacy-policy/',
	'Terms & Conditions' => $site . '/term-and-condition/',
);
$o = 1;
foreach ( $footer_spec as $title => $url ) { so_menu_item( $footer, $title, $url, 0, $o++ ); }

// assign locations
$locations = get_theme_mod( 'nav_menu_locations', array() );
if ( ! is_array( $locations ) ) { $locations = array(); }
$locations['primary'] = $primary;
$locations['footer']  = $footer;
set_theme_mod( 'nav_menu_locations', $locations );
$log['menu'] = array( 'primary' => $primary, 'footer' => $footer );

echo wp_json_encode( $log, JSON_PRETTY_PRINT );
