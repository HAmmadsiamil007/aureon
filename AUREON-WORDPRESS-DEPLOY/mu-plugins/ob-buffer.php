<?php
/**
 * Output buffer for WooCommerce redirects.
 * Buffers all output so early notices dont prevent redirects.
 * Cleans (discards) the buffer at template_redirect so headers can be sent.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
    ob_start();
    add_action( 'template_redirect', function() {
        while ( ob_get_level() ) {
            ob_end_clean();
        }
    }, 0 );
}
