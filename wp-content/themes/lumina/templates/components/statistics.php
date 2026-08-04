<?php
/**
 * Statistics — numbers band.
 *
 * Expected data: items (value, suffix, label).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<section class="lumina-statistics">
	<div class="lumina-statistics__grid">
		<?php foreach ( (array) $view->prop( 'items', array() ) as $item ) : ?>
			<div class="lumina-statistics__item">
				<span class="lumina-statistics__value">
					<?php echo (int) ( $item['value'] ?? 0 ); ?>
					<?php if ( ! empty( $item['suffix'] ) ) : ?>
						<span class="lumina-statistics__suffix"><?php echo $view->e( $item['suffix'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
					<?php endif; ?>
				</span>
				<span class="lumina-statistics__label"><?php echo $view->e( $item['label'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
