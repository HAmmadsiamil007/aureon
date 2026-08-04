<?php
/**
 * Brands — brand directory grid.
 *
 * Expected data: title, brands (name, url, logo).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-brands">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-brands__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<ul class="phantom-brands__grid">
		<?php foreach ( (array) $view->prop( 'brands', array() ) as $brand ) : ?>
			<li class="phantom-brands__item">
				<a class="phantom-brands__link" href="<?php echo $view->url( $brand['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
					<?php if ( ! empty( $brand['logo'] ) ) : ?>
						<img class="phantom-brands__logo" src="<?php echo $view->url( $brand['logo'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $brand['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
					<?php else : ?>
						<span class="phantom-brands__name"><?php echo $view->e( $brand['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
