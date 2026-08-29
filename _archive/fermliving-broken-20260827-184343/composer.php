<?php
/**
 * Ferm Living Design Pack - Composer
 * Controls homepage section sequence and adapter data overrides.
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
if ( ! function_exists( "aether_active_design" ) || "fermliving" !== aether_active_design() ) { return; }
$mapper = __DIR__ . "/mapper/ferm-mapper.php"; if ( file_exists( $mapper ) ) { require_once $mapper; }

/* Homepage section sequence */
add_filter( "aether_frontpage_sections", "ferm_homepage_sections" );
function ferm_homepage_sections() { return [ "hero", "categories", "editorial-split", "bestsellers", "room-grid", "newsletter" ]; }

/* Site data */
add_filter( "aether_adapter_site_data", "ferm_site_data" );
function ferm_site_data( $data ) { if ( is_array( $data ) ) { $data["name"] = "Ferm Living"; } return $data; }

/* Header data */
add_filter( "aether_adapter_header_data", "ferm_header_data" );
function ferm_header_data( $data ) { $cart_count = 0; if ( function_exists( "WC" ) && WC()->cart ) { $cart_count = (int) WC()->cart->get_cart_contents_count(); } $data["cart_count"] = $cart_count; $data["is_home"] = is_front_page() || ( is_home() && ! is_paged() ); return $data; }

/* Hero data */
add_filter( "aether_adapter_hero_data", "ferm_hero_data" );
function ferm_hero_data( $data ) { $slides = aureon_get_option( "aether_hero_slides", [] ); if ( is_string( $slides ) && "" !== trim( $slides ) ) { $slides = json_decode( $slides, true ); } if ( ! empty( $slides ) && is_array( $slides ) ) { $data["slides"] = $slides; } return $data; }

/* Announcement data */
add_filter( "aether_adapter_announcement_data", "ferm_announcement_data" );
function ferm_announcement_data( $data ) { return [ "items" => aureon_get_option( "ferm_announcement_items", [] ) ]; }

/* Footer data */
add_filter( "aether_adapter_footer_data", "ferm_footer_data" );
function ferm_footer_data( $data ) { return [ "usp_items" => aureon_get_option( "aether_footer_usp_items", [] ), "newsletter" => [ "heading" => aureon_get_option( "aether_newsletter_heading", "Ferm Living news" ), "text" => aureon_get_option( "aether_newsletter_text", "" ) ], "columns" => aureon_get_option( "aether_footer_columns", [] ), "legal" => [ [ "label" => "Terms and Conditions", "url" => "#" ], [ "label" => "Privacy Policy", "url" => "#" ], [ "label" => "Cookies", "url" => "#" ], [ "label" => "Follow Us", "url" => "#" ] ], "payments" => aureon_get_option( "aether_footer_payments", [] ), "socials" => aureon_get_option( "aether_social_items", [] ) ]; }

/* WC Products */
add_filter( "aether_adapter_wc_products_data", "ferm_wc_products_data" );
function ferm_wc_products_data( $data ) { if ( is_array( $data ) && isset( $data["items"] ) && is_array( $data["items"] ) ) { foreach ( $data["items"] as &$product ) { $product = ferm_remap_product( $product ); } } return $data; }

/* Demo Products */
add_filter( "aether_demo_products", "ferm_demo_products", 10, 2 );
function ferm_demo_products( $items, $query_args ) { $pack_dir = aether_active_design_dir(); $json_file = $pack_dir . "data/products.json"; if ( ! file_exists( $json_file ) ) { return $items; } $raw = json_decode( (string) file_get_contents( $json_file ), true ); if ( ! is_array( $raw ) ) { return $items; } $pack_url = aether_pack_url(); $result = []; foreach ( $raw as $product ) { $image = isset( $product["image"] ) ? $product["image"] : ""; if ( $image && strpos( $image, "http" ) === false ) { $image = $pack_url . $image; } $result[] = [ "id" => isset( $product["id"] ) ? (int) $product["id"] : 0, "name" => isset( $product["name"] ) ? $product["name"] : "", "price" => isset( $product["price"] ) ? $product["price"] : "", "price_plain" => isset( $product["price"] ) ? $product["price"] : "", "old_price_plain" => "", "tagline" => isset( $product["tagline"] ) ? $product["tagline"] : "", "rating" => 0, "reviews" => 0, "image" => $image, "alt" => isset( $product["name"] ) ? $product["name"] : "", "url" => isset( $product["url"] ) ? esc_url_raw( $product["url"] ) : "", "badge" => isset( $product["badge"] ) ? $product["badge"] : "", "add_to_cart_url" => "", "product_type" => "simple", "behavior" => [ "tilt" => true ], "badges" => isset( $product["badge"] ) && $product["badge"] ? [ $product["badge"] ] : [], "swatches" => isset( $product["colors"] ) ? ferm_format_swatches( $product["colors"], $product["url"] ?? "" ) : [] ]; } $per_page = isset( $query_args["posts_per_page"] ) ? (int) $query_args["posts_per_page"] : 8; $paged = isset( $query_args["paged"] ) ? (int) $query_args["paged"] : 1; return array_slice( $result, ( $paged - 1 ) * $per_page, $per_page ); }

/* Single Product */
add_filter( "aether_adapter_product_data", "ferm_product_data" );
function ferm_product_data( $data ) { return ferm_remap_product( $data ); }

/* WC Categories */
add_filter( "aether_adapter_wc_categories_data", "ferm_wc_categories_data" );
function ferm_wc_categories_data( $data ) { return $data; }

/* Demo Categories */
add_filter( "aether_demo_categories", "ferm_demo_categories", 10, 2 );
function ferm_demo_categories( $items, $args ) { $pack_dir = aether_active_design_dir(); $json_file = $pack_dir . "data/categories.json"; if ( ! file_exists( $json_file ) ) { return $items; } $raw = json_decode( (string) file_get_contents( $json_file ), true ); if ( ! is_array( $raw ) ) { return $items; } $pack_url = aether_pack_url(); $result = []; foreach ( $raw as $cat ) { $image = isset( $cat["image"] ) ? $cat["image"] : ""; if ( $image && strpos( $image, "http" ) === false ) { $image = $pack_url . $image; } $count_label = isset( $cat["count"] ) ? $cat["count"] : ""; if ( is_numeric( $count_label ) ) { $count_label = sprintf( _n( "%d Product", "%d Products", (int) $count_label, "aureon" ), (int) $count_label ); } $result[] = [ "name" => isset( $cat["name"] ) ? $cat["name"] : "", "count" => $count_label, "image" => $image, "alt" => isset( $cat["name"] ) ? sprintf( __( "Shop %s", "aureon" ), $cat["name"] ) : "", "url" => isset( $cat["url"] ) ? $cat["url"] : "#", "modifier" => isset( $cat["modifier"] ) ? $cat["modifier"] : "", "behavior" => [ "reveal" => true ] ]; } return $result; }

/* WC Filter */
add_filter( "aether_adapter_wc_filter_data", "ferm_wc_filter_data" );
function ferm_wc_filter_data( $data ) { $categories = aureon_get_option( "aether_category_items", [] ); if ( ! empty( $categories ) && is_array( $categories ) ) { $filters = []; foreach ( $categories as $cat ) { $filters[] = [ "label" => isset( $cat["name"] ) ? $cat["name"] : "", "url" => isset( $cat["url"] ) ? $cat["url"] : "#", "active" => false ]; } $data["filters"] = $filters; } return $data; }

/* Blog */
add_filter( "aether_adapter_blog_data", "ferm_blog_data" );
function ferm_blog_data( $data ) { if ( is_array( $data ) ) { $data["label"] = "Stories"; $data["title"] = "From the Ferm Living Journal"; } return $data; }

/* About */
add_filter( "aether_adapter_about_data", "ferm_about_data" );
function ferm_about_data( $data ) { return [ "heading" => aureon_get_option( "aether_about_heading", "About Ferm Living" ), "body" => aureon_get_option( "aether_about_body", "" ), "features" => aureon_get_option( "aether_about_features", [] ), "values" => aureon_get_option( "aether_about_values", [] ), "stats" => aureon_get_option( "aether_about_stats", [] ) ]; }

/* Contact */
add_filter( "aether_adapter_contact_data", "ferm_contact_data" );
function ferm_contact_data( $data ) { return [ "heading" => "Get in Touch", "address" => "Ferm Living ApS\nNA,rrebrogade 42\n2200 Copenhagen N\nDenmark", "phone" => "+45 7022 7523", "email" => "info@fermliving.com", "hours" => "Monday - Friday: 9:00 - 17:00 CET" ]; }

/* Search */
add_filter( "aether_adapter_search_data", "ferm_search_data" );
function ferm_search_data( $data ) { return [ "placeholder" => aureon_get_option( "aether_search_placeholder", "Search Ferm Living..." ), "suggestions" => [ "Furniture", "Lighting", "Accessories", "Kids", "Kitchen" ] ]; }

/* Shop Hero */
add_filter( "aether_adapter_shop_hero_data", "ferm_shop_hero_data" );
function ferm_shop_hero_data( $data ) { return [ "label" => "", "title" => "Shop", "subtitle" => "" ]; }

/* Newsletter */
add_filter( "aether_adapter_newsletter_data", "ferm_newsletter_data" );
function ferm_newsletter_data( $data ) { return [ "heading" => aureon_get_option( "aether_newsletter_heading", "Ferm Living news" ), "text" => aureon_get_option( "aether_newsletter_text", "Get exclusive drops, early access, and Ferm Living news." ) ]; }

/* Cart AJAX Handlers */
add_action( "wp_ajax_ferm_cart_add", "ferm_wc_ajax_cart_add" );
add_action( "wp_ajax_nopriv_ferm_cart_add", "ferm_wc_ajax_cart_add" );
add_action( "wp_ajax_ferm_cart_update", "ferm_wc_ajax_cart_update" );
add_action( "wp_ajax_nopriv_ferm_cart_update", "ferm_wc_ajax_cart_update" );
add_action( "wp_ajax_ferm_cart_get", "ferm_wc_ajax_cart_get" );
add_action( "wp_ajax_nopriv_ferm_cart_get", "ferm_wc_ajax_cart_get" );

function ferm_wc_ajax_cart_add() { if ( ! function_exists( "WC" ) ) { wp_send_json_error( "WooCommerce not available" ); } $product_id = isset( $_POST["product_id"] ) ? absint( $_POST["product_id"] ) : 0; $quantity = isset( $_POST["quantity"] ) ? absint( $_POST["quantity"] ) : 1; if ( ! $product_id ) { wp_send_json_error( "Invalid product" ); } $added = WC()->cart->add_to_cart( $product_id, $quantity ); if ( $added ) { $response = ferm_build_cart_response(); wp_send_json_success( $response ); } else { wp_send_json_error( "Could not add to cart" ); } }
function ferm_wc_ajax_cart_update() { if ( ! function_exists( "WC" ) ) { wp_send_json_error( "WooCommerce not available" ); } $updates_json = isset( $_POST["updates"] ) ? sanitize_text_field( wp_unslash( $_POST["updates"] ) ) : "{}"; $updates = json_decode( $updates_json, true ); if ( is_array( $updates ) ) { foreach ( $updates as $cart_item_key => $quantity ) { WC()->cart->set_quantity( $cart_item_key, $quantity ); } } $response = ferm_build_cart_response(); wp_send_json_success( $response ); }
function ferm_wc_ajax_cart_get() { if ( ! function_exists( "WC" ) ) { wp_send_json_error( "WooCommerce not available" ); } $response = ferm_build_cart_response(); wp_send_json_success( $response ); }
function ferm_build_cart_response() { $cart = WC()->cart; $items = []; foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) { $product = $cart_item["data"]; if ( ! $product ) { continue; } $items[] = [ "id" => $cart_item["product_id"], "variant_id" => isset( $cart_item["variation_id"] ) ? $cart_item["variation_id"] : $cart_item["product_id"], "quantity" => $cart_item["quantity"], "title" => $product->get_name(), "price" => (int) round( (float) $product->get_price() * 100 ), "line_price" => (int) round( (float) $cart_item["line_total"] * 100 ), "variant_title" => "", "product_id" => $cart_item["product_id"], "url" => get_permalink( $cart_item["product_id"] ), "image" => wp_get_attachment_url( $product->get_image_id() ) ]; } return [ "item_count" => $cart->get_cart_contents_count(), "items" => $items, "total_price" => (int) round( (float) $cart->get_cart_contents_total() * 100 ), "sections" => [] ]; }

/* Enqueue cart bridge */
add_action( "wp_enqueue_scripts", "ferm_enqueue_cart_bridge", 5 );
function ferm_enqueue_cart_bridge() { if ( ! function_exists( "aether_active_design" ) || "fermliving" !== aether_active_design() ) { return; } $pack_url = aether_pack_url(); if ( ! $pack_url ) { return; } wp_register_script( "ferm-cart-bridge", $pack_url . "assets/ferm-data-shims.js", [], "1.0.0", true ); wp_localize_script( "ferm-cart-bridge", "ferm_bridge", [ "ajax_url" => admin_url( "admin-ajax.php" ), "nonce" => wp_create_nonce( "ferm_cart_nonce" ) ] ); }

/* Enqueue product JS */
add_action( "wp_enqueue_scripts", "ferm_enqueue_product_js", 15 );
function ferm_enqueue_product_js() { if ( ! function_exists( "aether_active_design" ) || "fermliving" !== aether_active_design() ) { return; } if ( ! function_exists( "is_product" ) || ! is_product() ) { return; } $pack_url = aether_pack_url(); if ( ! $pack_url ) { return; } wp_enqueue_script( "ferm-product", $pack_url . "assets/product.fa97565a5f.js", [ "ferm-cart-bridge" ], "1.0.0", true ); }

/* Product Remapping */
function ferm_remap_product( $product ) { if ( ! is_array( $product ) ) { return $product; } $badge = ""; if ( ! empty( $product["badge"] ) && "Sale" === $product["badge"] ) { $badge = "Sale"; } elseif ( ! empty( $product["tagline"] ) && strpos( $product["tagline"], "Certified" ) !== false ) { $badge = "Certified"; } elseif ( ! empty( $product["badge"] ) && "New" === $product["badge"] ) { $badge = "New"; } $product["badge"] = $badge; if ( empty( $product["swatches"] ) && ! empty( $product["colors"] ) ) { $product["swatches"] = ferm_format_swatches( $product["colors"], $product["url"] ?? "" ); } return $product; }
function ferm_format_swatches( $colors, $base_url = "" ) { $swatches = []; if ( ! is_array( $colors ) ) { return $swatches; } foreach ( $colors as $color ) { $hex = isset( $color["hex"] ) ? $color["hex"] : ""; $name = isset( $color["name"] ) ? $color["name"] : ""; if ( empty( $hex ) ) { continue; } $label = $name; if ( ! empty( $color["secondary_name"] ) ) { $label .= " / " . $color["secondary_name"]; } $swatches[] = [ "color" => $hex, "label" => $label, "url" => $base_url ]; } return $swatches; }
