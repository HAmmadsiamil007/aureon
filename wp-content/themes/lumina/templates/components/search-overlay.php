<?php
/**
 * SearchOverlay — full-screen accessible search dialog.
 *
 * Expected data: action, placeholder.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-search-overlay" role="dialog" aria-modal="true" aria-label="<?php echo $view->attr( 'Search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" hidden data-lumina-search-overlay>
	<button class="lumina-search-overlay__close" type="button" aria-label="<?php echo $view->attr( 'Close search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-search-overlay-close>
		<span aria-hidden="true">&times;</span>
	</button>

	<form class="lumina-search-overlay__form" role="search" method="get" action="<?php echo $view->url( $view->prop( 'action', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
		<label class="lumina-search-overlay__label" for="lumina-search-input"><?php echo $view->e( 'Search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></label>
		<input
			id="lumina-search-input"
			class="lumina-search-overlay__input"
			type="search"
			name="s"
			placeholder="<?php echo $view->attr( $view->prop( 'placeholder', 'Search…' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
			autocomplete="off"
			data-lumina-search-overlay-input
		/>
		<button class="lumina-search-overlay__submit" type="submit"><?php echo $view->e( 'Search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
	</form>
</div>
