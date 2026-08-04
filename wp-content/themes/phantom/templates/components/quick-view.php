<?php
/**
 * QuickView — button that opens a product quick-view modal.
 *
 * Expected data: label, product_id.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<button
	class="phantom-quick-view"
	type="button"
	data-phantom-quick-view
	data-phantom-product-id="<?php echo $view->attr( (string) $view->prop( 'product_id', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
>
	<?php echo $view->e( $view->prop( 'label', 'Quick view' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
</button>
