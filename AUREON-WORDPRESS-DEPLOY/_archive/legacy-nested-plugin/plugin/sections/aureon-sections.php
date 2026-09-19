<?php
/**
 * The Sections module.
 *
 * @since 1.0.0
 * @deprecated 2.0.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'AUREON_SECTIONS_VERSION' ) ) {
	define( 'AUREON_SECTIONS_VERSION', AUREON_STUDIO_VERSION );
}

// Include functions identical between standalone addon and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/aureon-sections.php';
