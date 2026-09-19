<?php
/**
 * The Copyright module.
 *
 * @since 1.0.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'AUREON_COPYRIGHT_VERSION' ) ) {
	define( 'AUREON_COPYRIGHT_VERSION', AUREON_STUDIO_VERSION );
}

// Include functions identical between standalone addon and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
