<?php
/**
 * Empty state — friendly no-results placeholder.
 *
 * Expected data: title, text, action (label, href).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-empty-state">
	<h3 class="lumina-empty-state__title"><?php echo $view->e( $view->prop( 'title', 'Nothing here yet' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	<?php if ( $view->prop( 'text' ) ) : ?>
		<p class="lumina-empty-state__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<?php if ( $view->prop( 'action' ) && ! empty( $view->prop( 'action' )['href'] ) ) : ?>
		<a class="lumina-btn lumina-btn--primary" href="<?php echo $view->url( $view->prop( 'action' )['href'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"><?php echo $view->e( $view->prop( 'action' )['label'] ?? 'Explore' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></a>
	<?php endif; ?>
</div>
