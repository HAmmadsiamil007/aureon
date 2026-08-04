<?php
/**
 * SaleBadge — sale badge with optional percentage off.
 *
 * Expected data: percent, label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<span class="phantom-sale-badge phantom-product-badge phantom-product-badge--sale">
	<?php echo $view->e( $view->prop( 'label', 'Sale' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
	<?php if ( $view->prop( 'percent' ) ) : ?>
		<span class="phantom-sale-badge__percent"><?php echo $view->e( $view->prop( 'percent' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
	<?php endif; ?>
</span>
