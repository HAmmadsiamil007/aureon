<?php
/**
 * Blog grid — post card grid.
 *
 * Expected data: title, posts (card props), columns.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-blog-grid">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="lumina-blog-grid__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="lumina-blog-grid__grid" data-lumina-columns="<?php echo $view->attr( $view->prop( 'columns', '3' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
		<?php foreach ( (array) $view->prop( 'posts', array() ) as $post ) : ?>
			<article class="lumina-card lumina-card--post" data-lumina-anim="reveal">
				<?php if ( ! empty( $post['image'] ) ) : ?>
					<a class="lumina-card__media" href="<?php echo $view->url( $post['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" tabindex="-1" aria-hidden="true">
						<img src="<?php echo $view->url( $post['image'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" />
					</a>
				<?php endif; ?>
				<div class="lumina-card__body">
					<?php if ( ! empty( $post['category'] ) ) : ?>
						<span class="lumina-card__category"><?php echo $view->e( $post['category'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
					<h3 class="lumina-card__title">
						<a href="<?php echo $view->url( $post['url'] ?? '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $post['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
					</h3>
					<?php if ( ! empty( $post['excerpt'] ) ) : ?>
						<p class="lumina-card__excerpt"><?php echo $view->e( $post['excerpt'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $post['date'] ) ) : ?>
						<time class="lumina-card__date" datetime="<?php echo $view->attr( $post['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"><?php echo $view->e( $post['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></time>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
