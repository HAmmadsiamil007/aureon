<?php
/**
 * MobileNav — responsive navigation trigger + panel (off-canvas style).
 *
 * Expected data: items (list of ['label','url']), trigger_label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-mobile-nav" data-lumina-mobile-nav>
	<button
		class="lumina-mobile-nav__trigger"
		type="button"
		aria-expanded="false"
		aria-controls="lumina-mobile-nav-panel"
		aria-label="<?php echo $view->attr( $view->prop( 'trigger_label', 'Menu' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
		data-lumina-mobile-nav-trigger
	>
		<span class="lumina-mobile-nav__burger" aria-hidden="true"></span>
	</button>

	<div id="lumina-mobile-nav-panel" class="lumina-mobile-nav__panel" hidden data-lumina-mobile-nav-panel>
		<ul class="lumina-mobile-nav__list">
			<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
				<?php $item = is_array( $item ) ? $item : array(); ?>
				<li class="lumina-mobile-nav__item">
					<a class="lumina-mobile-nav__link" href="<?php echo $view->url( isset( $item['url'] ) ? (string) $item['url'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
						<?php echo $view->e( isset( $item['label'] ) ? (string) $item['label'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
