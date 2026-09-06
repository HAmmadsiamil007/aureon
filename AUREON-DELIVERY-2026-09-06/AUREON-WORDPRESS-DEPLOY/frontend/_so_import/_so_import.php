<?php
/**
 * Sole Origine -> AUREON/Vineta import (disposable QA-migration script).
 * Reads wp-content/frontend/_so_import/so_products.json
 */
if (!defined('ABSPATH')) { define('ABSPATH', '/var/www/html/'); }
$log = array('categories' => 0, 'products' => 0, 'media' => 0, 'media_fail' => array());

$data = json_decode(file_get_contents(ABSPATH . 'wp-content/frontend/_so_import/so_products.json'), true);
if (!is_array($data) || !count($data)) { echo json_encode(array('fatal' => 'dataset missing')); exit; }

// ---- 0. Currency: CHF (matches source store) ----
update_option('woocommerce_currency', 'CHF');
update_option('woocommerce_currency_pos', 'left_space');
$log['currency'] = get_option('woocommerce_currency');

// ---- 1. Categories (name => slug; hierarchy) ----
$cats = array(
  'MEN Collection'                => 'men_collection',
  'MEN PREMIUM'                   => 'men-premium',
  'PREMIUM LOAFERS'               => 'premium-loafers',
  'Executive Leather Collection'  => 'executive-leather-collection',
  'WOMEN Collection'              => 'women-collection',
  "WOMEN'S COLLECTION 2026"       => 'womens-collection-2026',
  'Pearl Elegance Collection'     => 'pearl-elegance-collection',
);
$catIds = array();
foreach ($cats as $name => $slug) {
  $parent = 0;
  if ($slug === 'men-premium')            $parent = $catIds['men_collection'];
  if ($slug === 'womens-collection-2026' || $slug === 'pearl-elegance-collection') $parent = $catIds['women-collection'];
  $term = term_exists($slug, 'product_cat');
  if (!$term) {
    $term = wp_insert_term($name, 'product_cat', array('slug' => $slug, 'parent' => $parent));
  }
  if (is_wp_error($term)) { $log['cat_errors'][] = $name . ': ' . $term->get_error_message(); continue; }
  $tid = is_array($term) ? $term['term_id'] : (int) $term;
  $catIds[$slug] = $tid;
  $log['categories']++;
}

// ---- 2. Clean previous import (idempotent) ----
$old = get_posts(array('post_type' => 'product', 'numberposts' => -1, 'post_status' => 'any', 'meta_key' => '_sku', 'meta_value' => 'SO-', 'meta_compare' => 'LIKE'));
foreach ($old as $o) { wp_delete_post($o->ID, true); }

// ---- 3. Media sideload helper ----
function so_sideload($url, $title) {
  $ctx = stream_context_create(array('http' => array('timeout' => 30, 'user_agent' => 'Mozilla/5.0 (AUREON import)')));
  $bin = @file_get_contents($url, false, $ctx);
  if ($bin === false || strlen($bin) < 100) return 0;
  $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
  if (!in_array($ext, array('jpg', 'jpeg', 'png', 'webp', 'gif'), true)) $ext = 'jpg';
  $name = sanitize_file_name($title . '-' . wp_generate_password(6, false) . '.' . $ext);
  $up = wp_upload_bits($name, null, $bin);
  if (!empty($up['error'])) return 0;
  $filetype = wp_check_filetype($name);
  $att = array('post_mime_type' => $filetype['type'], 'post_title' => $title, 'post_status' => 'inherit');
  $aid = wp_insert_attachment($att, $up['file']);
  if (!$aid) return 0;
  require_once ABSPATH . 'wp-admin/includes/image.php';
  wp_update_attachment_metadata($aid, wp_generate_attachment_metadata($aid, $up['file']));
  return $aid;
}

// ---- 4. Products ----
foreach ($data as $p) {
  $sku = 'SO-' . strtoupper(str_replace('-', '', $p['slug']));
  $pid = wc_get_product_id_by_sku($sku);
  if (!$pid) {
    $pid = wp_insert_post(array(
      'post_type' => 'product', 'post_title' => $p['title'],
      'post_name' => $p['slug'], 'post_status' => $p['draft'] ? 'draft' : 'publish',
    ));
  }
  $prod = new WC_Product_Simple($pid);
  $prod->set_sku($sku);
  if ($p['regular']) $prod->set_regular_price($p['regular']);
  if ($p['sale']) $prod->set_sale_price($p['sale']);
  if ($p['desc']) $prod->set_description($p['desc']);
  $cids = array();
  foreach ($p['categories'] as $cn) {
    $key = isset($cats[$cn]) ? $cats[$cn] : null;
    if ($key && isset($catIds[$key])) $cids[] = $catIds[$key];
  }
  if ($cids) $prod->set_category_ids($cids);
  $prod->set_stock_status($p['draft'] ? 'outofstock' : 'instock');
  $prod->set_status($p['draft'] ? 'draft' : 'publish');

  // images
  $imgIds = array();
  $limit = 0;
  foreach (array_slice($p['images'], 0, 5) as $u) {
    $aid = so_sideload($u, $p['title'] . ' ' . (count($imgIds) + 1));
    if ($aid) { $imgIds[] = $aid; $log['media']++; } else { $log['media_fail'][] = $u; }
  }
  if ($imgIds) {
    $prod->set_image_id($imgIds[0]);
    if (count($imgIds) > 1) $prod->set_gallery_image_ids(array_slice($imgIds, 1));
  }
  $prod->save();
  $log['products']++;
}

// ---- 5. Logo + favicon ----
$logoUrl = 'https://soleorigine.com/wp-content/uploads/2026/08/cropped-file_00000000993881f89e559e0c25e9218c.png';
$favUrl  = 'https://soleorigine.com/wp-content/uploads/2026/06/cropped-cropped-IMG-20260627-WA0030.jpg';
$logoId = so_sideload($logoUrl, 'Sole Origine Logo');
if ($logoId) { set_theme_mod('custom_logo', $logoId); $log['logo'] = $logoId; }
$favId = so_sideload($favUrl, 'Sole Origine Favicon');
if ($favId) { update_option('site_icon', $favId); $log['favicon'] = $favId; }

// ---- 6. Menus ----
function so_menu_items($menuId, $items) {
  $existing = wp_get_nav_menu_items($menuId);
  foreach ((array) $existing as $it) { wp_delete_post($it->ID, true); }
  $order = 0;
  foreach ($items as $it) {
    wp_update_nav_menu_item($menuId, 0, array(
      'menu-item-title' => $it[0], 'menu-item-url' => $it[1],
      'menu-item-type' => 'custom', 'menu-item-status' => 'publish',
      'menu-item-position' => ++$order,
    ));
  }
}
$pageBySlug = function ($slug) {
  $p = get_page_by_path($slug);
  return $p ? get_permalink($p->ID) : '';
};
$shopUrl = get_permalink(wc_get_page_id('shop'));
$accUrl = get_permalink(wc_get_page_id('myaccount'));
$cartUrl = get_permalink(wc_get_page_id('cart'));
$catUrl = function ($slug) { return home_url('/product-category/' . $slug . '/'); };

$primary = array(
  array('Home', home_url('/')),
  array('Shop', $shopUrl),
  array('MEN Collection', $catUrl('men_collection')),
  array('WOMEN Collection', $catUrl('women-collection')),
  array('Premium Loafers', $catUrl('premium-loafers')),
  array('Executive Leather Collection', $catUrl('executive-leather-collection')),
  array('Blog', $pageBySlug('blog') ? $pageBySlug('blog') : home_url('/blog/')),
  array('My Account', $accUrl),
  array('Cart', $cartUrl),
);
$footer = array(
  array('Shop', $shopUrl),
  array('MEN Collection', $catUrl('men_collection')),
  array('WOMEN Collection', $catUrl('women-collection')),
  array('Premium Loafers', $catUrl('premium-loafers')),
  array('Contact Us', $pageBySlug('contact') ? $pageBySlug('contact') : home_url('/contact/')),
);
$m1 = wp_get_nav_menu_object('Primary Menu');
if (!$m1) $m1 = wp_create_nav_menu('Primary Menu');
$m2 = wp_get_nav_menu_object('Footer Menu');
if (!$m2) $m2 = wp_create_nav_menu('Footer Menu');
so_menu_items($m1->term_id, $primary);
so_menu_items($m2->term_id, $footer);
$locations = get_theme_mod('nav_menu_locations', array());
$locations['primary'] = $m1->term_id;
$locations['footer'] = $m2->term_id;
set_theme_mod('nav_menu_locations', $locations);
$log['menus'] = array('primary' => $m1->term_id, 'footer' => $m2->term_id);

// ---- 7. Customizer: black scheme + typography (pack keys) ----
$bucket = get_option('aureon_settings', array());
if (!is_array($bucket)) $bucket = array();
$bucket['aether_color_accent'] = '#D4A017';
$bucket['aether_color_accent_hover'] = '#F0C040';
$bucket['aether_color_text'] = '#FFFFFF';
$bucket['aether_color_muted'] = '#9CA3AF';
$bucket['aether_color_border'] = '#2A2A2A';
$bucket['aether_color_surface'] = '#141414';
$bucket['aether_color_bg'] = '#0A0A0A';
$bucket['aether_font_body'] = 'Albert Sans, sans-serif';
$bucket['aether_font_heading'] = 'Playfair Display, serif';
$bucket['aether_announcement_items'] = array(
  array('text' => 'Free express shipping on all orders over CHF 150', 'visible' => true),
  array('text' => 'Handcrafted in premium leather — Goodyear welt construction', 'visible' => true),
);
$bucket['aether_newsletter_heading'] = 'Join the Sole Origine Circle';
$bucket['aether_newsletter_text'] = 'Early access to new collections and private sales.';
update_option('aureon_settings', $bucket);
$log['customizer'] = array_keys($bucket);

echo json_encode($log, JSON_PRETTY_PRINT);