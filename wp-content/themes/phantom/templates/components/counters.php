<?php
/**
 * Counters — animated counters with target values and duration.
 *
 * Expected data: items (target, suffix, label), duration.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-counters" data-phantom-counters data-duration="<?php echo $view->attr( $view->prop( 'duration', '1200' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<div class="phantom-counters__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<div class="phantom-counters__item">
				<span class="phantom-counters__value" data-count-target="<?php echo (int) ( $item['target'] ?? 0 ); ?>">
					<?php echo (int) ( $item['target'] ?? 0 ); ?>
					<?php if ( ! empty( $item['suffix'] ) ) : ?>
						<span class="phantom-counters__suffix"><?php echo $view->e( $item['suffix'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</span>
				<span class="phantom-counters__label"><?php echo $view->e( $item['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
