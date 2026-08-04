<?php
/**
 * Module 404 — not-found state with search fallback.
 *
 * Expected data: title, text, search_label, home_label, home_url.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-404">
	<h1 class="lumina-404__title"><?php echo $view->e( $view->prop( 'title', 'Page not found' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h1>
	<?php if ( $view->prop( 'text' ) ) : ?>
		<p class="lumina-404__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<?php if ( $view->prop( 'search_label' ) ) : ?>
		<form class="lumina-404__search" role="search" method="get" action="<?php echo $view->url( $view->prop( 'search_url', '#' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
			<label class="screen-reader-text" for="lumina-404-search"><?php echo $view->e( $view->prop( 'search_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></label>
			<input id="lumina-404-search" type="search" name="s" placeholder="<?php echo $view->attr( $view->prop( 'search_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" />
			<button type="submit" class="lumina-btn lumina-btn--primary"><?php echo $view->e( $view->prop( 'search_label', 'Search' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
		</form>
	<?php endif; ?>
	<?php if ( $view->prop( 'home_label' ) ) : ?>
		<a class="lumina-btn lumina-btn--ghost" href="<?php echo $view->url( $view->prop( 'home_url', '#' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $view->prop( 'home_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
	<?php endif; ?>
</section>
