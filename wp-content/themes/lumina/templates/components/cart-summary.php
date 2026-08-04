<?php
/**
 * CartSummary — totals block (subtotal, shipping, total) + checkout action.
 *
 * Expected data: subtotal, shipping, total, items, checkout_url.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-cart-summary">
	<h2 class="lumina-cart-summary__title"><?php echo $view->e( 'Order summary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>

	<dl class="lumina-cart-summary__rows">
		<div class="lumina-cart-summary__row">
			<dt><?php echo $view->e( 'Subtotal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dt>
			<dd><?php echo $view->e( $view->prop( 'subtotal', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dd>
		</div>
		<div class="lumina-cart-summary__row">
			<dt><?php echo $view->e( 'Shipping' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dt>
			<dd><?php echo $view->e( $view->prop( 'shipping', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dd>
		</div>
		<div class="lumina-cart-summary__row lumina-cart-summary__row--total">
			<dt><?php echo $view->e( 'Total' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dt>
			<dd><?php echo $view->e( $view->prop( 'total', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dd>
		</div>
	</dl>

	<?php if ( $view->prop( 'checkout_url' ) ) : ?>
		<a class="lumina-cart-summary__checkout" href="<?php echo $view->url( $view->prop( 'checkout_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'Proceed to checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
	<?php endif; ?>
</div>
