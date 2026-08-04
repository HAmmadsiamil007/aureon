<?php
/**
 * Timeline — chronological milestone list.
 *
 * Expected data: items (date, title, text).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<ol class="lumina-timeline">
	<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
		<li class="lumina-timeline__item" data-lumina-anim="reveal">
			<?php if ( ! empty( $item['date'] ) ) : ?>
				<time class="lumina-timeline__date" datetime="<?php echo $view->attr( $item['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"><?php echo $view->e( $item['date'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></time>
			<?php endif; ?>
			<h3 class="lumina-timeline__title"><?php echo $view->e( $item['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
			<?php if ( ! empty( $item['text'] ) ) : ?>
				<p class="lumina-timeline__text"><?php echo $view->e( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
