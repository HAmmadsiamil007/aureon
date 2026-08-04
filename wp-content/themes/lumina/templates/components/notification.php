<?php
/**
 * Notification — transient status banner (role=status, auto-dismissible).
 *
 * Expected data: message, type, dismiss_label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div
	class="lumina-notification lumina-notification--<?php echo $view->attr( $view->prop( 'type', 'info' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	role="status"
	data-lumina-notification
	<?php echo $view->prop( 'dismissible' ) ? 'data-dismissible' : ''; ?>
>
	<p class="lumina-notification__message"><?php echo $view->e( $view->prop( 'message', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php if ( $view->prop( 'dismissible' ) ) : ?>
		<button type="button" class="lumina-notification__close" data-lumina-notification-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Dismiss' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
	<?php endif; ?>
</div>
