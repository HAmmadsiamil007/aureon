<?php
/**
 * DIAGNOSTIC: WooCommerce boot-order trace
 * Must-use plugin version — runs after all plugins load.
 * Visit /?trace=1 on frontend or /wp-admin/?trace=1 for admin.
 * DELETE AFTER USE.
 */
add_action( 'wp_loaded', function() {
    if ( ! isset( $_GET['trace'] ) ) {
        return;
    }
    header( 'Content-Type: text/plain' );

    echo "=== WOOCOMMERCE BOOT-ORDER TRACE ===\n\n";

    // 1. WooCommerce status
    $woo_active = class_exists( 'WooCommerce' ) && is_plugin_active( 'woocommerce/woocommerce.php' );
    echo "WooCommerce active: " . ( $woo_active ? 'YES' : 'NO' ) . "\n";
    echo "class WooCommerce: " . ( class_exists( 'WooCommerce' ) ? 'YES' : 'NO' ) . "\n";

    if ( defined( 'WC_VERSION' ) ) {
        echo "WC_VERSION constant: " . WC_VERSION . "\n";
    } else {
        echo "WC_VERSION constant: NOT DEFINED\n";
    }

    // 2. Function availability matrix
    $funcs = array(
        'is_account_page',
        'is_product',
        'is_product_category',
        'is_shop',
        'is_cart',
        'is_checkout',
        'is_woocommerce',
        'is_order_received_page',
        'wc_get_page_id',
        'woocommerce_content',
        'wc_print_notices',
    );

    echo "\n=== FUNCTION AVAILABILITY ===\n";
    foreach ( $funcs as $fn ) {
        $exists = function_exists( $fn );
        echo str_pad( $fn, 30 ) . " = " . ( $exists ? 'YES' : 'NO' ) . "\n";
    }

    // 3. Source of is_account_page
    echo "\n=== SOURCE OF is_account_page ===\n";
    if ( function_exists( 'is_account_page' ) ) {
        $ref = new ReflectionFunction( 'is_account_page' );
        echo "File: " . $ref->getFileName() . "\n";
        echo "Line: " . $ref->getStartLine() . "\n";
    } else {
        echo "NOT DEFINED — function does not exist\n";
    }

    // 4. Key WooCommerce files
    echo "\n=== KEY WOOCOMMERCE FILES ===\n";
    $wc_files = array(
        'includes/wc-conditional-functions.php',
        'includes/wc-account-functions.php',
        'includes/class-woocommerce.php',
        'woocommerce.php',
    );
    $loaded = get_included_files();
    foreach ( $wc_files as $file ) {
        $found = false;
        foreach ( $loaded as $inc ) {
            if ( strpos( $inc, $file ) !== false ) {
                $found = true;
                break;
            }
        }
        echo str_pad( $file, 50 ) . " = " . ( $found ? 'LOADED' : 'NOT LOADED' ) . "\n";
    }

    // 5. template_include hooks
    echo "\n=== template_include HOOKS (priority => callbacks) ===\n";
    global $wp_filter;
    if ( isset( $wp_filter['template_include'] ) ) {
        foreach ( $wp_filter['template_include']->callbacks as $priority => $hooks ) {
            foreach ( $hooks as $key => $hook ) {
                $fn_name = 'unknown';
                if ( is_string( $hook['function'] ) ) {
                    $fn_name = $hook['function'];
                } elseif ( is_array( $hook['function'] ) ) {
                    if ( is_string( $hook['function'][0] ) ) {
                        $fn_name = $hook['function'][0] . '::' . $hook['function'][1];
                    } elseif ( is_object( $hook['function'][0] ) ) {
                        $fn_name = get_class( $hook['function'][0] ) . '::' . $hook['function'][1];
                    }
                } elseif ( is_object( $hook['function'] ) && $hook['function'] instanceof Closure ) {
                    $fn_name = '{Closure}';
                }
                echo "  Priority " . str_pad( $priority, 5 ) . " : $fn_name\n";
            }
        }
    } else {
        echo "  No template_include hooks found\n";
    }

    // 6. Active theme
    echo "\n=== ACTIVE THEME ===\n";
    $theme = wp_get_theme();
    echo "Name: " . $theme->get( 'Name' ) . "\n";
    echo "Template dir: " . $theme->get_template_directory() . "\n";

    // 7. Environment
    echo "\n=== ENVIRONMENT ===\n";
    echo "WordPress: " . get_bloginfo( 'version' ) . "\n";
    echo "PHP: " . phpversion() . "\n";
    echo "Server: " . php_uname( 's' ) . "\n";

    // 8. All active plugins
    echo "\n=== ACTIVE PLUGINS ===\n";
    $active = get_option( 'active_plugins', array() );
    foreach ( $active as $plugin ) {
        echo "  - $plugin\n";
    }
    if ( empty( $active ) ) {
        echo "  (none)\n";
    }

    exit;
});
