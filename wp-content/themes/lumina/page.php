<?php
/**
 * Page — static page WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Delegates content to the framework's partial layer.
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
		echo \Lumina\Core\Templates\View::partial( 'content', array() );
	endwhile;
	?>
</main>
<?php
get_footer();
