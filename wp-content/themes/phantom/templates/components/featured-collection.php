<?php
/**
 * FeaturedCollection — curated collection header + grid + view-all link.
 *
 * Expected data: title, subtitle, items (product props), link, link_label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-featured-collection" data-phantom-anim="reveal">
	<div class="phantom-featured-collection__header">
		<div>
			<?php if ( $view->prop( 'title' ) ) : ?>
				<h2 class="phantom-featured-collection__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
			<?php endif; ?>
			<?php if ( $view->prop( 'subtitle' ) ) : ?>
				<p class="phantom-featured-collection__subtitle"><?php echo $view->e( $view->prop( 'subtitle' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $view->prop( 'link' ) ) : ?>
			<a class="phantom-featured-collection__link" href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
				<?php echo $view->e( $view->prop( 'link_label', 'View all' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="phantom-featured-collection__grid" data-phantom-columns="4">
		<?php foreach ( (array) $view->prop( 'items' ) as $item ) : ?>
			<?php
			$item = is_array( $item ) ? $item : array();
			?>
			<div class="phantom-featured-collection__cell">
				<?php if ( isset( $item['name'] ) ) : ?>
					<a class="phantom-featured-collection__name" href="<?php echo $view->url( isset( $item['link'] ) ? (string) $item['link'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
						<?php echo $view->e( (string) $item['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
