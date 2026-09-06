<?php
/**
 * The Page Header module.
 *
 * @since 1.1.0
 * @deprecated 1.7.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'AUREON_PAGE_HEADER_VERSION' ) ) {
	define( 'AUREON_PAGE_HEADER_VERSION', AUREON_STUDIO_VERSION );
}

// Include assets unique to this addon.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
