<?php
/**
 * Categories — section title + category grid composition.
 *
 * Expected data: title, items (list of category props).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-categories" data-phantom-anim="reveal">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-categories__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-categories__grid" data-phantom-columns="3">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php
			$item = is_array( $item ) ? $item : array();
			$name = isset( $item['name'] ) ? (string) $item['name'] : '';
			$url  = isset( $item['url'] ) ? (string) $item['url'] : '';
			?>
			<?php if ( '' !== $name ) : ?>
				<a class="phantom-categories__item" href="<?php echo $view->url( $url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
					<span class="phantom-categories__name"><?php echo $view->e( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				</a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>
