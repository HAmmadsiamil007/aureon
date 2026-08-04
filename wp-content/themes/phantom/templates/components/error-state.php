<?php
/**
 * Error state — error boundary fallback.
 *
 * Expected data: title, message, retry_label, retry_url.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-error-state" role="alert">
	<h3 class="phantom-error-state__title"><?php echo $view->e( $view->prop( 'title', 'Something went wrong' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	<?php if ( $view->prop( 'message' ) ) : ?>
		<p class="phantom-error-state__message"><?php echo $view->e( $view->prop( 'message' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<?php if ( $view->prop( 'retry_label' ) ) : ?>
		<a class="phantom-btn phantom-btn--primary" href="<?php echo $view->url( $view->prop( 'retry_url', '#' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $view->prop( 'retry_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
	<?php endif; ?>
</div>
