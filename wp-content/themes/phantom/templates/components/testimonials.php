<?php
/**
 * Testimonials — quote grid assembled from ViewModel data.
 *
 * Expected data: title, items (quote, name, role, avatar).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-testimonials">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-testimonials__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-testimonials__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<figure class="phantom-testimonials__item" data-phantom-anim="reveal">
				<blockquote class="phantom-testimonials__quote"><?php echo $view->e( $item['quote'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></blockquote>
				<figcaption class="phantom-testimonials__meta">
					<?php if ( ! empty( $item['avatar'] ) ) : ?>
						<img class="phantom-testimonials__avatar" src="<?php echo $view->url( $item['avatar'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" width="48" height="48" />
					<?php endif; ?>
					<span class="phantom-testimonials__name"><?php echo $view->e( $item['name'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php if ( ! empty( $item['role'] ) ) : ?>
						<span class="phantom-testimonials__role"><?php echo $view->e( $item['role'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</figcaption>
			</figure>
		<?php endforeach; ?>
	</div>
</section>
