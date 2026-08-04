<?php
/**
 * OrderSummary — thank-you/order confirmation summary.
 *
 * Expected data: order_id, status, total, items (list of
 * ['name','quantity','total']).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-order-summary">
	<p class="phantom-order-summary__status"><?php echo $view->e( $view->prop( 'status', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>

	<?php if ( (int) $view->prop( 'order_id', 0 ) > 0 ) : ?>
		<p class="phantom-order-summary__id">
			<?php echo $view->e( 'Order' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?> #<?php echo $view->e( (string) $view->prop( 'order_id' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</p>
	<?php endif; ?>

	<ul class="phantom-order-summary__items">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php $item = is_array( $item ) ? $item : array(); ?>
			<li class="phantom-order-summary__item">
				<span class="phantom-order-summary__item-name"><?php echo $view->e( isset( $item['name'] ) ? (string) $item['name'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				<span class="phantom-order-summary__item-meta">x <?php echo $view->e( isset( $item['quantity'] ) ? (string) $item['quantity'] : '0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $view->prop( 'total' ) ) : ?>
		<p class="phantom-order-summary__total"><?php echo $view->e( $view->prop( 'total' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
</div>
