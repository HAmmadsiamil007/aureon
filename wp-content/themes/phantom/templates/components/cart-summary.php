<?php
/**
 * CartSummary — totals block (subtotal, shipping, total) + checkout action.
 *
 * Expected data: subtotal, shipping, total, items, checkout_url.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-cart-summary">
	<h2 class="phantom-cart-summary__title"><?php echo $view->e( 'Order summary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>

	<dl class="phantom-cart-summary__rows">
		<div class="phantom-cart-summary__row">
			<dt><?php echo $view->e( 'Subtotal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dt>
			<dd><?php echo $view->e( $view->prop( 'subtotal', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dd>
		</div>
		<div class="phantom-cart-summary__row">
			<dt><?php echo $view->e( 'Shipping' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dt>
			<dd><?php echo $view->e( $view->prop( 'shipping', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dd>
		</div>
		<div class="phantom-cart-summary__row phantom-cart-summary__row--total">
			<dt><?php echo $view->e( 'Total' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dt>
			<dd><?php echo $view->e( $view->prop( 'total', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></dd>
		</div>
	</dl>

	<?php if ( $view->prop( 'checkout_url' ) ) : ?>
		<a class="phantom-cart-summary__checkout" href="<?php echo $view->url( $view->prop( 'checkout_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'Proceed to checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
	<?php endif; ?>
</div>
