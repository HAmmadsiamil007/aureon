<?php
/**
 * Tabs — accessible tabbed interface (WAI-ARIA tabs pattern).
 *
 * Expected data: title, tabs (label, content).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-tabs" data-lumina-tabs>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="lumina-tabs__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="lumina-tabs__nav" role="tablist" aria-label="<?php echo $view->attr( $view->prop( 'title', 'Tabs' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<?php foreach ( (array) $view->prop( 'tabs', array() ) as $index => $tab ) : ?>
			<button
				type="button"
				class="lumina-tabs__tab"
				role="tab"
				id="lumina-tab-<?php echo (int) $index; ?>"
				aria-controls="lumina-tab-panel-<?php echo (int) $index; ?>"
				aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
				data-tab-index="<?php echo (int) $index; ?>"
			>
				<?php echo $view->e( $tab['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( (array) $view->prop( 'tabs', array() ) as $index => $tab ) : ?>
		<div
			class="lumina-tabs__panel"
			role="tabpanel"
			id="lumina-tab-panel-<?php echo (int) $index; ?>"
			aria-labelledby="lumina-tab-<?php echo (int) $index; ?>"
			<?php echo 0 !== $index ? 'hidden' : ''; ?>
		>
			<?php echo $view->e( $tab['content'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</div>
	<?php endforeach; ?>
</div>
