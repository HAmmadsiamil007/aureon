<?php
/**
 * Create test WooCommerce products for Vineta integration testing.
 * Run once, then delete.
 */
require_once '/var/www/html/wp-load.php';

// Find existing simple product
$existing = get_posts(array('post_type' => 'product', 'title' => 'Vineta Test Simple Product', 'post_status' => 'publish', 'numberposts' => 1));
$simple = !empty($existing) ? wc_get_product($existing[0]->ID) : null;
if (!$simple) {
    $simple = new WC_Product_Simple();
    $simple->set_name('Vineta Test Simple Product');
    $simple->set_regular_price('29.99');
    $simple->set_sale_price('24.99');
    $simple->set_sku('VTS-001');
    $simple->set_description('A simple test product for Vineta integration testing. This product verifies that WooCommerce product data flows correctly through the Vineta template.');
    $simple->set_short_description('Simple test product for Vineta.');
    $simple->set_status('publish');
    $simple->set_catalog_visibility('visible');
    $simple->set_manage_stock(true);
    $simple->set_stock_quantity(100);
    $simple->set_weight('0.5');
    $simple->save();
    echo "Created simple product: {$simple->get_id()}\n";
} else {
    echo "Simple product exists: {$simple->get_id()}\n";
}

// Find existing variable product
$existing_var = get_posts(array('post_type' => 'product', 'title' => 'Vineta Test Variable Product', 'post_status' => 'publish', 'numberposts' => 1));
$variable = !empty($existing_var) ? wc_get_product($existing_var[0]->ID) : null;
if (!$variable) {
    $variable = new WC_Product_Variable();
    $variable->set_name('Vineta Test Variable Product');
    $variable->set_description('A variable test product for Vineta integration testing. Tests variation selection, price display, stock management, and add-to-cart with variations.');
    $variable->set_short_description('Variable test product for Vineta.');
    $variable->set_status('publish');
    $variable->set_catalog_visibility('visible');
    $variable->set_sku('VTV-001');
    $variable->save();
    echo "Created variable product: {$variable->get_id()}\n";
} else {
    echo "Variable product exists: {$variable->get_id()}\n";
}

// Create attributes for variable product
$color_attr = new WC_Product_Attribute();
$color_attr->set_name('Color');
$color_attr->set_options(array('Red', 'Blue', 'Green'));
$color_attr->set_position(0);
$color_attr->set_visible(true);
$color_attr->set_variation(true);

$size_attr = new WC_Product_Attribute();
$size_attr->set_name('Size');
$size_attr->set_options(array('S', 'M', 'L'));
$size_attr->set_position(1);
$size_attr->set_visible(true);
$size_attr->set_variation(true);

$variable->set_attributes(array($color_attr, $size_attr));
$variable->save();

// Create variations
$colors = array('Red', 'Blue', 'Green');
$sizes = array('S', 'M', 'L');
$prices = array('Red' => 39.99, 'Blue' => 42.99, 'Green' => 37.99);
$stocks = array('S' => 20, 'M' => 30, 'L' => 15);

foreach ($colors as $color) {
    foreach ($sizes as $size) {
        $variation = new WC_Product_Variation();
        $variation->set_parent_id($variable->get_id());
        $variation->set_name($variable->get_name() . ' - ' . $color . ', ' . $size);
        $variation->set_sku("VTV-{$color}-{$size}");
        $variation->set_regular_price($prices[$color]);
        $variation->set_manage_stock(true);
        $variation->set_stock_quantity($stocks[$size]);
        $variation->set_stock_status('instock');
        $variation->set_attributes(array(
            'Color' => $color,
            'Size' => $size,
        ));
        $variation->save();
    }
}
echo "Created " . (count($colors) * count($sizes)) . " variations\n";

// Create a product category
$cat = get_term_by('slug', 'vineta-test', 'product_cat');
if (!$cat) {
    $cat_id = wp_insert_term('Vineta Test', 'product_cat', array('slug' => 'vineta-test'));
    if (!is_wp_error($cat_id)) {
        // Assign products to category
        wp_set_object_terms($simple->get_id(), array('Vineta Test'), 'product_cat');
        wp_set_object_terms($variable->get_id(), array('Vineta Test'), 'product_cat');
        echo "Created category: Vineta Test (ID: {$cat_id['term_id']})\n";
    }
} else {
    echo "Category exists: Vineta Test (ID: {$cat->term_id})\n";
}

// Verify
echo "\n=== PRODUCT SUMMARY ===\n";
$simple = wc_get_product($simple->get_id());
$variable = wc_get_product($variable->get_id());
echo "Simple: {$simple->get_name()} (SKU: {$simple->get_sku()}, Price: \${$simple->get_price()})\n";
echo "Variable: {$variable->get_name()} (SKU: {$variable->get_sku()}, Variations: " . count($variable->get_children()) . ")\n";
echo "Category: Vineta Test\n";
echo "\nDone\n";
