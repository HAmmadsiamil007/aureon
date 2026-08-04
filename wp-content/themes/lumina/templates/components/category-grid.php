<?php
/**
 * CategoryGrid — grid of category tiles (name + optional image + link).
 *
 * Expected data: items (list of ['name','url','image','image_alt']),
 * columns.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<ul class="lumina-category-grid" data-lumina-columns="<?php echo $view->attr( (string) $view->prop( 'columns', 3 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
		<?php
		$item = is_array( $item ) ? $item : array();
		$name = isset( $item['name'] ) ? (string) $item['name'] : '';
		$url  = isset( $item['url'] ) ? (string) $item['url'] : '';
		?>
		<?php if ( '' !== $name ) : ?>
			<li class="lumina-category-grid__item">
				<a class="lumina-category-grid__link" href="<?php echo $view->url( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
					<?php if ( ! empty( $item['image'] ) ) : ?>
						<img class="lumina-category-grid__image" src="<?php echo $view->url( (string) $item['image'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( isset( $item['image_alt'] ) ? (string) $item['image_alt'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" loading="lazy" />
					<?php endif; ?>
					<span class="lumina-category-grid__name"><?php echo $view->e( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				</a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
