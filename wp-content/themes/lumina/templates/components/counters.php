<?php
/**
 * Counters — animated counters with target values and duration.
 *
 * Expected data: items (target, suffix, label), duration.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-counters" data-lumina-counters data-duration="<?php echo $view->attr( $view->prop( 'duration', '1200' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<div class="lumina-counters__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<div class="lumina-counters__item">
				<span class="lumina-counters__value" data-count-target="<?php echo (int) ( $item['target'] ?? 0 ); ?>">
					<?php echo (int) ( $item['target'] ?? 0 ); ?>
					<?php if ( ! empty( $item['suffix'] ) ) : ?>
						<span class="lumina-counters__suffix"><?php echo $view->e( $item['suffix'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</span>
				<span class="lumina-counters__label"><?php echo $view->e( $item['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
