<?php
/**
 * RecentlyViewed — horizontal list of recently viewed products.
 *
 * Expected data: title, items (list of product names/links).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-recently-viewed" data-phantom-anim="reveal">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-recently-viewed__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<ul class="phantom-recently-viewed__list">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php
			$item = is_array( $item ) ? $item : array();
			$name = isset( $item['name'] ) ? (string) $item['name'] : '';
			$url  = isset( $item['link'] ) ? (string) $item['link'] : '';
			?>
			<?php if ( '' !== $name ) : ?>
				<li class="phantom-recently-viewed__item">
					<a class="phantom-recently-viewed__link" href="<?php echo $view->url( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
						<?php echo $view->e( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</section>
