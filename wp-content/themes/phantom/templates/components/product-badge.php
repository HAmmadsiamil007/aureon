<?php
/**
 * ProductBadge — tone-aware pill label (default/sale/new/hot).
 *
 * Expected data: label, tone.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<span class="phantom-product-badge phantom-product-badge--<?php echo $view->attr( $view->prop( 'tone', 'default' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<?php echo $view->e( $view->prop( 'label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
</span>
