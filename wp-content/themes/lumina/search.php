<?php
/**
 * Search — search results WP hierarchy file (standalone theme shell).
 *
 * Phase 16 (Safe Rebranding): original markup, never derived from a parent
 * theme. Delegates the results loop to the framework's partial layer.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="lumina-main site-main">
	<?php
	if ( have_posts() ) :
		?>
		<header class="lumina-search__header">
			<h1 class="lumina-search__title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search results for: %s', 'lumina' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
		</header>
		<?php
		while ( have_posts() ) :
			the_post();

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- partial HTML is escaped at the leaf via ViewContext helpers.
			echo \Lumina\Core\Templates\View::partial( 'content', array() );
		endwhile;
	else :
		?>
		<section class="lumina-empty">
			<h1 class="lumina-empty__title"><?php esc_html_e( 'Nothing found', 'lumina' ); ?></h1>
			<p class="lumina-empty__text"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'lumina' ); ?></p>
		</section>
		<?php
	endif;
	?>
</main>
<?php
get_footer();
