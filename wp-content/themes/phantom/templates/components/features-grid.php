<?php
/**
 * Features grid — icon + title + text feature blocks.
 *
 * Expected data: title, items (icon, title, text).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-features">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h2 class="phantom-features__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
	<?php endif; ?>
	<div class="phantom-features__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<div class="phantom-features__item" data-phantom-anim="reveal">
				<?php if ( ! empty( $item['icon'] ) ) : ?>
					<span class="phantom-features__icon" aria-hidden="true"><?php echo $view->e( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
				<?php endif; ?>
				<h3 class="phantom-features__item-title"><?php echo $view->e( $item['title'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
				<?php if ( ! empty( $item['text'] ) ) : ?>
					<p class="phantom-features__text"><?php echo $view->e( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
