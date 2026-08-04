<?php
/**
 * Faceted nav — SEO-friendly faceted navigation sidebar/panel.
 *
 * Expected data: title, facets (label, name, values with count), active.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<nav class="lumina-faceted" aria-label="<?php echo $view->attr( $view->prop( 'title', 'Shop by' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<h3 class="lumina-faceted__title"><?php echo $view->e( $view->prop( 'title', 'Shop by' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	<?php foreach ( (array) $view->prop( 'facets', array() ) as $facet ) : ?>
		<div class="lumina-faceted__facet">
			<h4 class="lumina-faceted__facet-title"><?php echo $view->e( $facet['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h4>
			<ul class="lumina-faceted__list">
				<?php foreach ( (array) ( $facet['values'] ?? array() ) as $value ) : ?>
					<li class="lumina-faceted__item">
						<a
							class="lumina-faceted__link<?php echo in_array( $value['value'] ?? '', (array) $view->prop( 'active', array() ), true ) ? ' is-active' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
							href="<?php echo $view->url( $value['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"
						>
							<span><?php echo $view->e( $value['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
							<span class="lumina-faceted__count"><?php echo (int) ( $value['count'] ?? 0 ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</nav>
