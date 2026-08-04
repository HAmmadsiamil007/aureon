<?php
/**
 * WishlistButton — toggle button reflecting active state (aria-pressed).
 *
 * Expected data: label, active_label, product_id, active.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<button
	class="phantom-wishlist-button<?php echo $view->prop( 'active' ) ? ' is-active' : ''; ?>"
	type="button"
	aria-pressed="<?php echo $view->prop( 'active' ) ? 'true' : 'false'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean-gated literal. ?>"
	data-phantom-wishlist
	data-phantom-product-id="<?php echo $view->attr( (string) $view->prop( 'product_id', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
>
	<span class="phantom-wishlist-button__icon" aria-hidden="true">&#9825;</span>
	<span class="phantom-wishlist-button__label">
		<?php echo $view->e( $view->prop( 'active' ) ? $view->prop( 'active_label', 'In wishlist' ) : $view->prop( 'label', 'Add to wishlist' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
	</span>
</button>
