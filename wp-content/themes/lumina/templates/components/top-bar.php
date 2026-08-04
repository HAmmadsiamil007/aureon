<?php
/**
 * TopBar — utility strip above the header with left/right slots.
 *
 * Expected data: left, right, left_slot (slot HTML), right_slot (slot HTML).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-top-bar">
	<div class="lumina-top-bar__inner">
		<div class="lumina-top-bar__left">
			<?php if ( $view->prop( 'left_slot' ) ) : ?>
				<?php echo $view->prop( 'left_slot' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?>
			<?php elseif ( $view->prop( 'left' ) ) : ?>
				<?php echo $view->e( $view->prop( 'left' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			<?php endif; ?>
		</div>

		<div class="lumina-top-bar__right">
			<?php if ( $view->prop( 'right_slot' ) ) : ?>
				<?php echo $view->prop( 'right_slot' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?>
			<?php elseif ( $view->prop( 'right' ) ) : ?>
				<?php echo $view->e( $view->prop( 'right' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			<?php endif; ?>
		</div>
	</div>
</div>
