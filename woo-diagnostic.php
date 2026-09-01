<?php
/**
 * INFINITYFREE DIAGNOSTIC — WooCommerce status check
 * 
 * Upload to: wp-content/mu-plugins/woo-diagnostic.php
 * Visit: https://fermliving.wuaze.com/?diag=1
 * Read the output, then DELETE this file.
 */
add_action( 'wp_loaded', function() {
    if ( ! isset( $_GET['diag'] ) ) {
        return;
    }
    header( 'Content-Type: text/plain' );

    echo "=== INFINITYFREE WOOCOMMERCE DIAGNOSTIC ===\n\n";

    // 1. WooCommerce status
    echo "1. WOOCOMMERCE STATUS\n";
    echo "   class_exists('WooCommerce'): " . ( class_exists( 'WooCommerce' ) ? 'YES' : 'NO' ) . "\n";
    echo "   is_plugin_active: ";
    if ( function_exists( 'is_plugin_active' ) ) {
        echo ( is_plugin_active( 'woocommerce/woocommerce.php' ) ? 'YES' : 'NO' ) . "\n";
    } else {
        echo "is_plugin_active NOT AVAILABLE\n";
    }
    echo "   WC_VERSION: " . ( defined( 'WC_VERSION' ) ? WC_VERSION : 'NOT DEFINED' ) . "\n";

    // 2. Function check
    echo "\n2. FUNCTION AVAILABILITY\n";
    $funcs = array( 'is_account_page', 'is_cart', 'is_checkout', 'is_product', 'is_woocommerce' );
    foreach ( $funcs as $fn ) {
        echo "   " . str_pad( $fn, 25 ) . " = " . ( function_exists( $fn ) ? 'YES' : 'NO' ) . "\n";
    }

    // 3. Active plugins
    echo "\n3. ACTIVE PLUGINS\n";
    $active = get_option( 'active_plugins', array() );
    if ( empty( $active ) ) {
        echo "   (none)\n";
    } else {
        foreach ( $active as $p ) {
            echo "   - $p\n";
        }
    }

    // 4. WooCommerce files
    echo "\n4. WOOCOMMERCE FILES CHECK\n";
    $wp_woo = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
    echo "   woocommerce.php exists: " . ( file_exists( $wp_woo ) ? 'YES' : 'NO' ) . "\n";
    $wc_cond = WP_PLUGIN_DIR . '/woocommerce/includes/wc-conditional-functions.php';
    echo "   wc-conditional-functions.php exists: " . ( file_exists( $wc_cond ) ? 'YES' : 'NO' ) . "\n";

    // 5. Environment
    echo "\n5. ENVIRONMENT\n";
    echo "   WordPress: " . get_bloginfo( 'version' ) . "\n";
    echo "   PHP: " . phpversion() . "\n";
    echo "   WP_PLUGIN_DIR: " . WP_PLUGIN_DIR . "\n";

    echo "\n=== END DIAGNOSTIC ===\n";
    echo "\nIF WooCommerce is NO / NOT ACTIVE:\n";
    echo "  → Go to wp-admin/plugins.php and activate WooCommerce\n";
    echo "  → This is the root cause of the is_account_page() fatal\n";
    exit;
});
