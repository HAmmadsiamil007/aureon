<?php
/**
 * The Disable Elements module.
 *
 * @since 1.0.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'AUREON_DE_VERSION' ) ) {
	define( 'AUREON_DE_VERSION', AUREON_STUDIO_VERSION );
}

// Include functions identical between standalone addon and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
