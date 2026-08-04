<?php
/**
 * StickyAddToCart — sticky purchase bar revealed on scroll past product.
 *
 * Expected data: name, price, product_id, label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-sticky-add-to-cart" hidden data-phantom-sticky-atc>
	<div class="phantom-sticky-add-to-cart__info">
		<?php if ( $view->prop( 'name' ) ) : ?>
			<span class="phantom-sticky-add-to-cart__name"><?php echo $view->e( $view->prop( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
		<?php endif; ?>
		<?php if ( $view->prop( 'price' ) ) : ?>
			<span class="phantom-sticky-add-to-cart__price"><?php echo $view->e( $view->prop( 'price' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
		<?php endif; ?>
	</div>

	<button class="phantom-sticky-add-to-cart__button" type="submit" data-phantom-product-id="<?php echo $view->attr( (string) $view->prop( 'product_id', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<?php echo $view->e( $view->prop( 'label', 'Add to cart' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
	</button>
</div>
