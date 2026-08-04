<?php
/**
 * CartDrawer — off-canvas cart panel with item list and checkout action.
 *
 * Expected data: count, total, items (list of ['name','quantity','line_total']),
 * cart_url, checkout_url.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<aside class="phantom-cart-drawer" role="dialog" aria-modal="true" aria-label="<?php echo $view->attr( 'Cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" hidden data-phantom-cart-drawer>
	<div class="phantom-cart-drawer__header">
		<h2 class="phantom-cart-drawer__title"><?php echo $view->e( 'Your cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<button class="phantom-cart-drawer__close" type="button" aria-label="<?php echo $view->attr( 'Close cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-cart-drawer-close>
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<div class="phantom-cart-drawer__body">
		<?php if ( (int) $view->prop( 'count', 0 ) > 0 ) : ?>
			<ul class="phantom-cart-drawer__items">
				<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
					<?php $item = is_array( $item ) ? $item : array(); ?>
					<li class="phantom-cart-drawer__item">
						<span class="phantom-cart-drawer__item-name"><?php echo $view->e( isset( $item['name'] ) ? (string) $item['name'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<span class="phantom-cart-drawer__item-qty">x <?php echo $view->e( isset( $item['quantity'] ) ? (string) $item['quantity'] : '0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<span class="phantom-cart-drawer__item-total"><?php echo $view->e( isset( $item['line_total'] ) ? (string) $item['line_total'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="phantom-cart-drawer__total"><?php echo $view->e( $view->prop( 'total', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php else : ?>
			<p class="phantom-cart-drawer__empty"><?php echo $view->e( 'Your cart is empty.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $view->prop( 'checkout_url' ) ) : ?>
		<div class="phantom-cart-drawer__footer">
			<a class="phantom-cart-drawer__checkout" href="<?php echo $view->url( $view->prop( 'checkout_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'Checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
		</div>
	<?php endif; ?>
</aside>
