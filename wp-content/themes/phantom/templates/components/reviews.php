<?php
/**
 * Reviews — review list with aggregate rating summary.
 *
 * Expected data: rating, count, items (author, date, rating, text).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-reviews" aria-label="Reviews">
	<?php if ( $view->prop( 'rating' ) ) : ?>
		<div class="phantom-reviews__summary">
			<span class="phantom-reviews__score"><?php echo $view->e( number_format( (float) $view->prop( 'rating' ), 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			<span class="phantom-reviews__count">(<?php echo (int) $view->prop( 'count', 0 ); ?>)</span>
		</div>
	<?php endif; ?>
	<ul class="phantom-reviews__list">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<li class="phantom-reviews__item">
				<header class="phantom-reviews__item-head">
					<span class="phantom-reviews__author"><?php echo $view->e( $item['author'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<span class="phantom-reviews__date"><?php echo $view->e( $item['date'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				</header>
				<p class="phantom-reviews__text"><?php echo $view->e( $item['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
