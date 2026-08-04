<?php
/**
 * MiniCart — compact cart summary: count, total, item list, links.
 *
 * Expected data: count, total, items (list of ['name','quantity','line_total']),
 * cart_url, checkout_url.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-mini-cart" data-phantom-mini-cart>
	<button class="phantom-mini-cart__toggle" type="button" aria-expanded="false" aria-controls="phantom-mini-cart-panel" data-phantom-mini-cart-toggle>
		<span class="phantom-mini-cart__icon" aria-hidden="true">&#128722;</span>
		<span class="phantom-mini-cart__count" data-phantom-mini-cart-count><?php echo $view->e( (string) $view->prop( 'count', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
	</button>

	<div id="phantom-mini-cart-panel" class="phantom-mini-cart__panel" hidden data-phantom-mini-cart-panel>
		<?php if ( (int) $view->prop( 'count', 0 ) > 0 ) : ?>
			<ul class="phantom-mini-cart__items">
				<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
					<?php $item = is_array( $item ) ? $item : array(); ?>
					<li class="phantom-mini-cart__item">
						<span class="phantom-mini-cart__item-name"><?php echo $view->e( isset( $item['name'] ) ? (string) $item['name'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<span class="phantom-mini-cart__item-qty">x <?php echo $view->e( isset( $item['quantity'] ) ? (string) $item['quantity'] : '0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $view->prop( 'total' ) ) : ?>
				<p class="phantom-mini-cart__total"><?php echo $view->e( $view->prop( 'total' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
			<?php endif; ?>
		<?php else : ?>
			<p class="phantom-mini-cart__empty"><?php echo $view->e( 'Your cart is empty.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>

		<?php if ( $view->prop( 'cart_url' ) || $view->prop( 'checkout_url' ) ) : ?>
			<div class="phantom-mini-cart__actions">
				<?php if ( $view->prop( 'cart_url' ) ) : ?>
					<a class="phantom-mini-cart__link" href="<?php echo $view->url( $view->prop( 'cart_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'View cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
				<?php endif; ?>
				<?php if ( $view->prop( 'checkout_url' ) ) : ?>
					<a class="phantom-mini-cart__link phantom-mini-cart__link--checkout" href="<?php echo $view->url( $view->prop( 'checkout_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'Checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
