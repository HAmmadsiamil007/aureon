<?php
require_once '/var/www/html/wp-load.php';

echo "=== Boosting Thursday Boots Bestsellers ===\n";

$products = get_posts( array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
) );

$boosted = 0;
$featured = 0;

foreach ( $products as $p ) {
    $sku   = get_post_meta( $p->ID, '_sku', true );
    $title = $p->post_title;
    $tl    = strtolower( $title );

    // Boost key styles by title matching
    $boost = false;
    if ( strpos( $tl, 'captain' ) !== false ||
         strpos( $tl, 'president' ) !== false ||
         strpos( $tl, 'era' ) !== false ||
         strpos( $tl, 'lincoln' ) !== false ||
         strpos( $tl, 'diplomat' ) !== false ||
         strpos( $tl, 'encore' ) !== false ||
         strpos( $tl, 'court' ) !== false ||
         strpos( $tl, 'vista' ) !== false ||
         strpos( $tl, 'commonwealth' ) !== false ||
         strpos( $tl, 'soleil' ) !== false ||
         strpos( $tl, 'vesper' ) !== false ) {

        $sales = rand( 30, 200 );
        update_post_meta( $p->ID, 'total_sales', $sales );
        $boosted++;
        $boost = true;

        // Set featured for top styles
        if ( $boosted <= 10 && (
            strpos( $tl, 'captain' ) !== false ||
            strpos( $tl, 'era' ) !== false ||
            strpos( $tl, 'lincoln' ) !== false ) ) {
            wp_set_object_terms( $p->ID, 'featured', 'product_tag' );
            update_post_meta( $p->ID, '_featured', 'yes' );
            $featured++;
            echo "  + Featured: {$title} ({$sales} sales)\n";
        }
    }
}

echo "\n  Boosted {$boosted} products total\n";
echo "  Set {$featured} as featured\n";

// Verify top sellers
echo "\n=== Top Sellers ===\n";
$top = get_posts( array(
    'post_type'      => 'product',
    'posts_per_page' => 4,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'total_sales',
    'order'          => 'DESC',
) );

foreach ( $top as $p ) {
    $sales = get_post_meta( $p->ID, 'total_sales', true );
    $price = get_post_meta( $p->ID, '_price', true );
    echo "  {$p->post_title} (\${$price}) - {$sales} sales\n";
}

// Total product count
$count = wp_count_posts( 'product' );
echo "\n  Total published products: {$count->publish}\n";
