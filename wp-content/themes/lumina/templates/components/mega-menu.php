<?php
/**
 * MegaMenu — keyboard-accessible multi-column navigation panel.
 *
 * Expected data: items (list of ['label' => string, 'url' => string,
 * 'children' => list of ['label','url']]).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<nav class="lumina-mega-menu" aria-label="<?php echo $view->attr( 'Mega menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-mega-menu>
	<ul class="lumina-mega-menu__list">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php
			$item     = is_array( $item ) ? $item : array();
			$label    = isset( $item['label'] ) ? (string) $item['label'] : '';
			$url      = isset( $item['url'] ) ? (string) $item['url'] : '';
			$children = isset( $item['children'] ) && is_array( $item['children'] ) ? $item['children'] : array();
			?>
			<?php if ( '' !== $label ) : ?>
				<li class="lumina-mega-menu__item<?php echo array() !== $children ? ' lumina-mega-menu__item--has-children' : ''; ?>">
					<a class="lumina-mega-menu__link" href="<?php echo $view->url( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
						<?php echo $view->e( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</a>

					<?php if ( array() !== $children ) : ?>
						<ul class="lumina-mega-menu__sub">
							<?php foreach ( $children as $child ) : ?>
								<?php $child = is_array( $child ) ? $child : array(); ?>
								<li class="lumina-mega-menu__sub-item">
									<a class="lumina-mega-menu__sub-link" href="<?php echo $view->url( isset( $child['url'] ) ? (string) $child['url'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
										<?php echo $view->e( isset( $child['label'] ) ? (string) $child['label'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</nav>
