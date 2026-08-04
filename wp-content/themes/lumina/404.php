<?php
/**
 * 404 — not-found WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Delegates to the framework's composition layer (module-404).
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="lumina-main site-main">
	<?php
	if ( class_exists( \Lumina\Core\Templates\View::class ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML is escaped at the leaf via ViewContext helpers.
		echo \Lumina\Core\Templates\View::compose( 'not-found', apply_filters( 'lumina_template_data', array(), 'not-found' ) );
	}
	?>
</main>
<?php
get_footer();
