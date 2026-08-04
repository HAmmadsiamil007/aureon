<?php
/**
 * Popup — delayed marketing overlay (dismissible, consent-aware).
 *
 * Expected data: title, content, delay, dismiss_label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-popup" data-phantom-popup data-delay="<?php echo $view->attr( $view->prop( 'delay', '5000' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" hidden>
	<div class="phantom-popup__inner" role="dialog" aria-modal="true" aria-labelledby="phantom-popup-title" tabindex="-1">
		<button type="button" class="phantom-popup__close" data-phantom-popup-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Close' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="phantom-popup__title" id="phantom-popup-title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<div class="phantom-popup__body"><?php echo $view->e( $view->prop( 'content', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></div>
	</div>
</div>
