<?php
/**
 * QuickView — button that opens a product quick-view modal.
 *
 * Expected data: label, product_id.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<button
	class="lumina-quick-view"
	type="button"
	data-lumina-quick-view
	data-lumina-product-id="<?php echo $view->attr( (string) $view->prop( 'product_id', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
>
	<?php echo $view->e( $view->prop( 'label', 'Quick view' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
</button>
