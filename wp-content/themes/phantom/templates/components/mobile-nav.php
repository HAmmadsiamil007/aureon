<?php
/**
 * MobileNav — responsive navigation trigger + panel (off-canvas style).
 *
 * Expected data: items (list of ['label','url']), trigger_label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-mobile-nav" data-phantom-mobile-nav>
	<button
		class="phantom-mobile-nav__trigger"
		type="button"
		aria-expanded="false"
		aria-controls="phantom-mobile-nav-panel"
		aria-label="<?php echo $view->attr( $view->prop( 'trigger_label', 'Menu' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
		data-phantom-mobile-nav-trigger
	>
		<span class="phantom-mobile-nav__burger" aria-hidden="true"></span>
	</button>

	<div id="phantom-mobile-nav-panel" class="phantom-mobile-nav__panel" hidden data-phantom-mobile-nav-panel>
		<ul class="phantom-mobile-nav__list">
			<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
				<?php $item = is_array( $item ) ? $item : array(); ?>
				<li class="phantom-mobile-nav__item">
					<a class="phantom-mobile-nav__link" href="<?php echo $view->url( isset( $item['url'] ) ? (string) $item['url'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
						<?php echo $view->e( isset( $item['label'] ) ? (string) $item['label'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
