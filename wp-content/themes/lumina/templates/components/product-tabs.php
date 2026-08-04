<?php
/**
 * ProductTabs — accessible tablist for product information.
 *
 * Expected data: tabs (list of ['label' => string, 'content' => string]).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-product-tabs" data-lumina-tabs>
	<div class="lumina-product-tabs__list" role="tablist" aria-label="<?php echo $view->attr( 'Product information' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<?php foreach ( (array) $view->prop( 'tabs' ) as $index => $tab ) : ?>
			<?php
			$tab   = is_array( $tab ) ? $tab : array();
			$label = isset( $tab['label'] ) ? (string) $tab['label'] : '';
			?>
			<?php if ( '' !== $label ) : ?>
				<button
					class="lumina-product-tabs__tab"
					type="button"
					role="tab"
					id="lumina-tab-<?php echo (int) $index; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer cast. ?>"
					aria-controls="lumina-tabpanel-<?php echo (int) $index; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer cast. ?>"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean-gated literal. ?>"
					data-lumina-tab
				>
					<?php echo $view->e( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
				</button>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>

	<?php foreach ( (array) $view->prop( 'tabs' ) as $index => $tab ) : ?>
		<?php
		$tab   = is_array( $tab ) ? $tab : array();
		$label = isset( $tab['label'] ) ? (string) $tab['label'] : '';
		?>
		<?php if ( '' !== $label ) : ?>
			<div
				class="lumina-product-tabs__panel"
				role="tabpanel"
				id="lumina-tabpanel-<?php echo (int) $index; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer cast. ?>"
				aria-labelledby="lumina-tab-<?php echo (int) $index; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer cast. ?>"
				<?php echo 0 !== $index ? 'hidden' : ''; ?>
				data-lumina-tab-panel
			>
				<?php echo $view->html( isset( $tab['content'] ) ? (string) $tab['content'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized by ViewContext::html(). ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
