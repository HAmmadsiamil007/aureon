<?php
/**
 * Alert — persistent status banner (role=alert for errors).
 *
 * Expected data: message, type, title, dismissible.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div
	class="phantom-alert phantom-alert--<?php echo $view->attr( $view->prop( 'type', 'info' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	role="<?php echo 'error' === $view->prop( 'type' ) ? 'alert' : 'status'; ?>"
>
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h3 class="phantom-alert__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	<?php endif; ?>
	<?php if ( $view->prop( 'message' ) ) : ?>
		<p class="phantom-alert__message"><?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<?php if ( $view->prop( 'dismissible' ) ) : ?>
		<button type="button" class="phantom-alert__close" data-phantom-alert-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Dismiss' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
	<?php endif; ?>
</div>
