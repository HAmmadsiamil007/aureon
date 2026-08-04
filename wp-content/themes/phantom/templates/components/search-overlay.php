<?php
/**
 * SearchOverlay — full-screen accessible search dialog.
 *
 * Expected data: action, placeholder.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-search-overlay" role="dialog" aria-modal="true" aria-label="<?php echo $view->attr( 'Search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" hidden data-phantom-search-overlay>
	<button class="phantom-search-overlay__close" type="button" aria-label="<?php echo $view->attr( 'Close search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-search-overlay-close>
		<span aria-hidden="true">&times;</span>
	</button>

	<form class="phantom-search-overlay__form" role="search" method="get" action="<?php echo $view->url( $view->prop( 'action', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
		<label class="phantom-search-overlay__label" for="phantom-search-input"><?php echo $view->e( 'Search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></label>
		<input
			id="phantom-search-input"
			class="phantom-search-overlay__input"
			type="search"
			name="s"
			placeholder="<?php echo $view->attr( $view->prop( 'placeholder', 'Search…' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
			autocomplete="off"
			data-phantom-search-overlay-input
		/>
		<button class="phantom-search-overlay__submit" type="submit"><?php echo $view->e( 'Search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
	</form>
</div>
