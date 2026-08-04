<?php
/**
 * Popup — delayed marketing overlay (dismissible, consent-aware).
 *
 * Expected data: title, content, delay, dismiss_label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-popup" data-lumina-popup data-delay="<?php echo $view->attr( $view->prop( 'delay', '5000' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" hidden>
	<div class="lumina-popup__inner" role="dialog" aria-modal="true" aria-labelledby="lumina-popup-title" tabindex="-1">
		<button type="button" class="lumina-popup__close" data-lumina-popup-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Close' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="lumina-popup__title" id="lumina-popup-title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<div class="lumina-popup__body"><?php echo $view->e( $view->prop( 'content', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></div>
	</div>
</div>
