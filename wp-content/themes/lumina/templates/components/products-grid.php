<?php
/**
 * ProductsGrid — section title + product cells grid + optional view-all.
 *
 * Expected data: title, items (product props), columns, link, link_label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-products-grid" data-lumina-anim="reveal">
	<div class="lumina-products-grid__header">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="lumina-products-grid__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<?php if ( $view->prop( 'link' ) ) : ?>
			<a class="lumina-products-grid__link" href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
				<?php echo $view->e( $view->prop( 'link_label', 'View all' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</a>
		<?php endif; ?>
	</div>

	<ul class="lumina-products-grid__list" data-lumina-columns="<?php echo $view->attr( (string) $view->prop( 'columns', 4 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php if ( is_array( $item ) ) : ?>
				<li class="lumina-products-grid__cell"><?php echo $view->e( isset( $item['name'] ) ? (string) $item['name'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</section>
