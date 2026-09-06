<?php
/**
 * Blog module.
 *
 * @since 1.1.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version. This used to be a standalone plugin, so we need to keep this constant.
if ( ! defined( 'AUREON_BLOG_VERSION' ) ) {
	define( 'AUREON_BLOG_VERSION', AUREON_STUDIO_VERSION );
}

// Include functions identical between standalone addon and Aureon Studio.
require plugin_dir_path( __FILE__ ) . 'functions/aureon-blog.php';
