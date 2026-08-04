<?php
/**
 * Banner — promotional band with title, text, link and actions slot.
 *
 * Expected data: title, text, link, class (variant-merged),
 * actions (slot HTML).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="<?php echo $view->attr( $view->prop( 'class', 'lumina-banner' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-anim="reveal">
	<div class="lumina-banner__inner">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="lumina-banner__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<?php if ( $view->prop( 'text' ) ) : ?>
			<p class="lumina-banner__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
		<?php if ( $view->prop( 'actions' ) ) : ?>
			<div class="lumina-banner__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>
</div>
