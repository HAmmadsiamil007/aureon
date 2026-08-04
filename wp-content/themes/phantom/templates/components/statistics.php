<?php
/**
 * Statistics — numbers band.
 *
 * Expected data: items (value, suffix, label).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-statistics">
	<div class="phantom-statistics__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<div class="phantom-statistics__item">
				<span class="phantom-statistics__value">
					<?php echo (int) ( $item['value'] ?? 0 ); ?>
					<?php if ( ! empty( $item['suffix'] ) ) : ?>
						<span class="phantom-statistics__suffix"><?php echo $view->e( $item['suffix'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</span>
				<span class="phantom-statistics__label"><?php echo $view->e( $item['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
