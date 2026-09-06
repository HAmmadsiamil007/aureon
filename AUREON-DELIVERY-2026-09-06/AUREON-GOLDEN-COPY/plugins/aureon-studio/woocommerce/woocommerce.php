<?php
/**
 * The WooCommerce module.
 *
 * @since 1.3.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
define( 'AUREON_WOOCOMMERCE_VERSION', AUREON_STUDIO_VERSION );

// Include functions identical between standalone addon and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
require plugin_dir_path( __FILE__ ) . 'fields/woocommerce-colors.php';
