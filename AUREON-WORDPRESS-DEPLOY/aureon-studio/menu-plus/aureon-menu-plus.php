<?php
/**
 * The Menu Plus module.
 *
 * @since 1.0.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'AUREON_MENU_PLUS_VERSION' ) ) {
	define( 'AUREON_MENU_PLUS_VERSION', AUREON_STUDIO_VERSION );
}

// Include functions identical between standalone add-on and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/aureon-menu-plus.php';
require plugin_dir_path( __FILE__ ) . 'fields/slideout-nav-colors.php';
