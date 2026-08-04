<?php
/**
 * Reviews — review list with aggregate rating summary.
 *
 * Expected data: rating, count, items (author, date, rating, text).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-reviews" aria-label="Reviews">
	<?php if ( $view->prop( 'rating' ) ) : ?>
		<div class="lumina-reviews__summary">
			<span class="lumina-reviews__score"><?php echo $view->e( number_format( (float) $view->prop( 'rating' ), 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			<span class="lumina-reviews__count">(<?php echo (int) $view->prop( 'count', 0 ); ?>)</span>
		</div>
	<?php endif; ?>
	<ul class="lumina-reviews__list">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<li class="lumina-reviews__item">
				<header class="lumina-reviews__item-head">
					<span class="lumina-reviews__author"><?php echo $view->e( $item['author'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<span class="lumina-reviews__date"><?php echo $view->e( $item['date'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				</header>
				<p class="lumina-reviews__text"><?php echo $view->e( $item['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
