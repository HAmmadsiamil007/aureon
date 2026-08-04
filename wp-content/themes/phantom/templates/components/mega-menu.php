<?php
/**
 * MegaMenu — keyboard-accessible multi-column navigation panel.
 *
 * Expected data: items (list of ['label' => string, 'url' => string,
 * 'children' => list of ['label','url']]).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<nav class="phantom-mega-menu" aria-label="<?php echo $view->attr( 'Mega menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-mega-menu>
	<ul class="phantom-mega-menu__list">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php
			$item     = is_array( $item ) ? $item : array();
			$label    = isset( $item['label'] ) ? (string) $item['label'] : '';
			$url      = isset( $item['url'] ) ? (string) $item['url'] : '';
			$children = isset( $item['children'] ) && is_array( $item['children'] ) ? $item['children'] : array();
			?>
			<?php if ( '' !== $label ) : ?>
				<li class="phantom-mega-menu__item<?php echo array() !== $children ? ' phantom-mega-menu__item--has-children' : ''; ?>">
					<a class="phantom-mega-menu__link" href="<?php echo $view->url( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
						<?php echo $view->e( $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</a>

					<?php if ( array() !== $children ) : ?>
						<ul class="phantom-mega-menu__sub">
							<?php foreach ( $children as $child ) : ?>
								<?php $child = is_array( $child ) ? $child : array(); ?>
								<li class="phantom-mega-menu__sub-item">
									<a class="phantom-mega-menu__sub-link" href="<?php echo $view->url( isset( $child['url'] ) ? (string) $child['url'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
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
