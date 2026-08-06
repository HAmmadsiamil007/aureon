<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * AETHER-styled 404 page.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="error-404" id="error404">
	<div class="container">
		<div class="error-content" data-reveal>
			<span class="error-code">404</span>
			<h1 class="error-title">Page Not Found</h1>
			<p class="error-text">The page you're looking for doesn't exist or has been moved.</p>
			<div class="error-actions">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">Back to Home</a>
				<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="btn btn-outline">Shop Now</a>
			</div>
			<div class="error-search">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
