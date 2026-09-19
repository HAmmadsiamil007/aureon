<?php
/**
 * The legacy hooks module.
 *
 * @since 1.0.0
 * @deprecated 1.7.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'AUREON_HOOKS_VERSION' ) ) {
	define( 'AUREON_HOOKS_VERSION', AUREON_STUDIO_VERSION );
}

// Include functions identical between standalone addon and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
