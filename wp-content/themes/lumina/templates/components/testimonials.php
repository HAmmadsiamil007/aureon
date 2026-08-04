<?php
/**
 * Testimonials — quote grid assembled from ViewModel data.
 *
 * Expected data: title, items (quote, name, role, avatar).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-testimonials">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="lumina-testimonials__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="lumina-testimonials__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<figure class="lumina-testimonials__item" data-lumina-anim="reveal">
				<blockquote class="lumina-testimonials__quote"><?php echo $view->e( $item['quote'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></blockquote>
				<figcaption class="lumina-testimonials__meta">
					<?php if ( ! empty( $item['avatar'] ) ) : ?>
						<img class="lumina-testimonials__avatar" src="<?php echo $view->url( $item['avatar'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" width="48" height="48" />
					<?php endif; ?>
					<span class="lumina-testimonials__name"><?php echo $view->e( $item['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php if ( ! empty( $item['role'] ) ) : ?>
						<span class="lumina-testimonials__role"><?php echo $view->e( $item['role'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</figcaption>
			</figure>
		<?php endforeach; ?>
	</div>
</section>
