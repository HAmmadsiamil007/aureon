<?php
/**
 * Notification — transient status banner (role=status, auto-dismissible).
 *
 * Expected data: message, type, dismiss_label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div
	class="phantom-notification phantom-notification--<?php echo $view->attr( $view->prop( 'type', 'info' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	role="status"
	data-phantom-notification
	<?php echo $view->prop( 'dismissible' ) ? 'data-dismissible' : ''; ?>
>
	<p class="phantom-notification__message"><?php echo $view->e( $view->prop( 'message', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php if ( $view->prop( 'dismissible' ) ) : ?>
		<button type="button" class="phantom-notification__close" data-phantom-notification-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Dismiss' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
	<?php endif; ?>
</div>
