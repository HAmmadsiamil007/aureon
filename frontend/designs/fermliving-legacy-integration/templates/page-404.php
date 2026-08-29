<?php
/**
 * Ferm Living 404 Page Template
 *
 * Renders frozen source DOM structure for not-found pages.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="content" id="main-content">
	<section class="headspace">
		<div class="ferm-404" data-ferm-404>
			<div class="limit">

				<div class="ferm-404__code">404</div>

				<h1 class="ferm-404__heading"><?php esc_html_e( 'Page not found', 'aureon' ); ?></h1>

				<p class="ferm-404__text">
					<?php esc_html_e( 'The page you\'re looking for doesn\'t exist or has been moved. Let us help you find your way back.', 'aureon' ); ?>
				</p>

				<div class="ferm-404__actions">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ferm-404__btn ferm-404__btn--primary">
						<?php esc_html_e( 'Back to homepage', 'aureon' ); ?>
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="ferm-404__btn">
						<?php esc_html_e( 'Browse shop', 'aureon' ); ?>
					</a>
				</div>

			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
