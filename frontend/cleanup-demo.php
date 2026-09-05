<?php
require_once '/var/www/html/wp-load.php';

echo "=== Cleaning Up Demo Products ===\n";

// Delete old demo products that aren't Thursday Boots (SKU doesn't start with thursday-)
$old_products = get_posts( array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
) );

$deleted = 0;
foreach ( $old_products as $p ) {
    $sku = get_post_meta( $p->ID, '_sku', true );
    // Keep Thursday Boots products (SKU starts with thursday-)
    if ( strpos( $sku, 'thursday-' ) !== 0 ) {
        wp_delete_post( $p->ID, true );
        $deleted++;
    }
}
echo "  Deleted {$deleted} old demo products\n";

// Promote some Thursday Boots bestsellers with fake sales data
$boost_skus = array(
    'thursday-captain-turkish-coffee'  => 150,
    'thursday-captain-matte-black'     => 120,
    'thursday-president-snuff'         => 100,
    'thursday-diplomat-black'          => 90,
    'thursday-heritage-captain-stormking-brownstone' => 85,
    'thursday-lincoln-pebbled-black'   => 80,
    'thursday-era-granite'             => 75,
    'thursday-encore-white-black'      => 70,
    'thursday-venus-dark-brown'        => 65,
    'thursday-soleil-black'            => 60,
);

$boosted = 0;
foreach ( $boost_skus as $sku => $sales ) {
    $pid = wc_get_product_id_by_sku( $sku );
    if ( $pid ) {
        update_post_meta( $pid, 'total_sales', $sales );
        $boosted++;
    }
}
echo "  Boosted {$boosted} products with sales data\n";

// Set some as featured
$featured_skus = array(
    'thursday-captain-turkish-coffee',
    'thursday-captain-matte-black',
    'thursday-president-snuff',
    'thursday-era-granite',
    'thursday-lincoln-pebbled-black',
);

$featured = 0;
foreach ( $featured_skus as $sku ) {
    $pid = wc_get_product_id_by_sku( $sku );
    if ( $pid ) {
        wp_set_object_terms( $pid, 'featured', 'product_tag' );
        update_post_meta( $pid, '_featured', 'yes' );
        $featured++;
    }
}
echo "  Set {$featured} products as featured\n";

echo "\n=== Done ===\n";
