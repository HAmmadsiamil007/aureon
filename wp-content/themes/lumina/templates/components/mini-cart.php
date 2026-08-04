<?php
/**
 * MiniCart — compact cart summary: count, total, item list, links.
 *
 * Expected data: count, total, items (list of ['name','quantity','line_total']),
 * cart_url, checkout_url.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-mini-cart" data-lumina-mini-cart>
	<button class="lumina-mini-cart__toggle" type="button" aria-expanded="false" aria-controls="lumina-mini-cart-panel" data-lumina-mini-cart-toggle>
		<span class="lumina-mini-cart__icon" aria-hidden="true">&#128722;</span>
		<span class="lumina-mini-cart__count" data-lumina-mini-cart-count><?php echo $view->e( (string) $view->prop( 'count', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
	</button>

	<div id="lumina-mini-cart-panel" class="lumina-mini-cart__panel" hidden data-lumina-mini-cart-panel>
		<?php if ( (int) $view->prop( 'count', 0 ) > 0 ) : ?>
			<ul class="lumina-mini-cart__items">
				<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
					<?php $item = is_array( $item ) ? $item : array(); ?>
					<li class="lumina-mini-cart__item">
						<span class="lumina-mini-cart__item-name"><?php echo $view->e( isset( $item['name'] ) ? (string) $item['name'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
						<span class="lumina-mini-cart__item-qty">x <?php echo $view->e( isset( $item['quantity'] ) ? (string) $item['quantity'] : '0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $view->prop( 'total' ) ) : ?>
				<p class="lumina-mini-cart__total"><?php echo $view->e( $view->prop( 'total' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
			<?php endif; ?>
		<?php else : ?>
			<p class="lumina-mini-cart__empty"><?php echo $view->e( 'Your cart is empty.' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>

		<?php if ( $view->prop( 'cart_url' ) || $view->prop( 'checkout_url' ) ) : ?>
			<div class="lumina-mini-cart__actions">
				<?php if ( $view->prop( 'cart_url' ) ) : ?>
					<a class="lumina-mini-cart__link" href="<?php echo $view->url( $view->prop( 'cart_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'View cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
				<?php endif; ?>
				<?php if ( $view->prop( 'checkout_url' ) ) : ?>
					<a class="lumina-mini-cart__link lumina-mini-cart__link--checkout" href="<?php echo $view->url( $view->prop( 'checkout_url' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( 'Checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
