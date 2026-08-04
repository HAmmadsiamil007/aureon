<?php
/**
 * Accordion — accessible disclosure pattern (WCAG): each item pairs a
 * button trigger with an aria-controlled panel.
 *
 * Expected data: items (list of ['title' => string, 'content' => string]).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-accordion" data-lumina-accordion>
	<?php foreach ( (array) $view->prop( 'items' ) as $index => $item ) : ?>
		<?php
		$item  = is_array( $item ) ? $item : array();
		$title = isset( $item['title'] ) ? (string) $item['title'] : '';
		$body  = isset( $item['content'] ) ? (string) $item['content'] : '';
		?>
		<?php if ( '' !== $title ) : ?>
			<div class="lumina-accordion__item">
				<h3 class="lumina-accordion__heading">
					<button
						class="lumina-accordion__trigger"
						type="button"
						aria-expanded="false"
						aria-controls="lumina-accordion-panel-<?php echo (int) $index; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer cast. ?>"
						data-lumina-accordion-trigger
					>
						<?php echo $view->e( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</button>
				</h3>
				<div
					id="lumina-accordion-panel-<?php echo (int) $index; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer cast. ?>"
					class="lumina-accordion__panel"
					role="region"
					hidden
					data-lumina-accordion-panel
				>
					<?php echo $view->html( $body ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized by ViewContext::html(). ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
