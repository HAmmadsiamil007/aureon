<?php
/**
 * CheckoutBlocks — checkout field blocks (billing/shipping/payment regions).
 * Field rendering follows the provided schema; values never echo raw.
 *
 * Expected data: fields (list of ['name','label','type','required','options']),
 * checkout_url.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-checkout-blocks">
	<form class="phantom-checkout-blocks__form" method="post" action="<?php echo $view->url( $view->prop( 'checkout_url', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
		<?php foreach ( (array) $view->prop( 'fields' ) as $field ) : ?>
			<?php
			$field    = is_array( $field ) ? $field : array();
			$name     = isset( $field['name'] ) ? (string) $field['name'] : '';
			$label    = isset( $field['label'] ) ? (string) $field['label'] : '';
			$type     = isset( $field['type'] ) ? (string) $field['type'] : 'text';
			$required = ! empty( $field['required'] );
			?>
			<?php if ( '' !== $name ) : ?>
				<div class="phantom-checkout-blocks__field">
					<label for="phantom-checkout-<?php echo $view->attr( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
						<?php echo $view->e( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
						<?php if ( $required ) : ?>
							<span aria-hidden="true">*</span>
						<?php endif; ?>
					</label>
					<input
						id="phantom-checkout-<?php echo $view->attr( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
						type="<?php echo $view->attr( $type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
						name="<?php echo $view->attr( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
						<?php echo $required ? 'required' : ''; ?>
					/>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<button class="phantom-checkout-blocks__submit" type="submit"><?php echo $view->e( 'Place order' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></button>
	</form>
</div>
