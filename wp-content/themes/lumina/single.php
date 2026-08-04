<?php
/**
 * Single — single post WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Delegates content to the framework's partial layer; comments flow
 * through the public comments_template() API.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="lumina-main site-main">
	<?php
	while ( have_posts() ) :
		the_post();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- partial HTML is escaped at the leaf via ViewContext helpers.
		echo \Lumina\Core\Templates\View::partial( 'content-single', array() );

		if ( function_exists( 'comments_template' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			comments_template();
		}
	endwhile;

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section HTML is escaped at the leaf via the render pipeline.
	echo \Lumina\Core\Templates\View::section( 'after-main' );
	?>
</main>
<?php
get_footer();
