<?php
/**
 * Tabs — accessible tabbed interface (WAI-ARIA tabs pattern).
 *
 * Expected data: title, tabs (label, content).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-tabs" data-phantom-tabs>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-tabs__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-tabs__nav" role="tablist" aria-label="<?php echo $view->attr( $view->prop( 'title', 'Tabs' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<?php foreach ( (array) $view->prop( 'tabs', array() ) as $index => $tab ) : ?>
			<button
				type="button"
				class="phantom-tabs__tab"
				role="tab"
				id="phantom-tab-<?php echo (int) $index; ?>"
				aria-controls="phantom-tab-panel-<?php echo (int) $index; ?>"
				aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
				data-tab-index="<?php echo (int) $index; ?>"
			>
				<?php echo $view->e( $tab['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( (array) $view->prop( 'tabs', array() ) as $index => $tab ) : ?>
		<div
			class="phantom-tabs__panel"
			role="tabpanel"
			id="phantom-tab-panel-<?php echo (int) $index; ?>"
			aria-labelledby="phantom-tab-<?php echo (int) $index; ?>"
			<?php echo 0 !== $index ? 'hidden' : ''; ?>
		>
			<?php echo $view->e( $tab['content'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</div>
	<?php endforeach; ?>
</div>
