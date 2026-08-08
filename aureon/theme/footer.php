<?php
/**
 * The template for displaying the footer.
 *
 * Stage 2 — the AETHER shell footer is composed entirely by the frontend
 * engine; this template closes the document.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

aether_compose_footer();

// Quick-view modal (JS-filled with product data via admin-ajax).
if ( function_exists( 'aether_render_component' ) ) {
	aether_render_component( 'commerce/quick-view' );
}

wp_footer();
?>

</body>
</html>
