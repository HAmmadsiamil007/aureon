<?php
/**
 * CTA — call-to-action band with title, text and actions slot.
 *
 * Expected data: title, text, actions (slot HTML).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-cta" data-phantom-anim="reveal">
	<div class="phantom-cta__inner">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="phantom-cta__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<?php if ( $view->prop( 'text' ) ) : ?>
			<p class="phantom-cta__text"><?php echo $view->e( $view->prop( 'text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
		<?php endif; ?>
		<?php if ( $view->prop( 'actions' ) ) : ?>
			<div class="phantom-cta__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
		<?php endif; ?>
	</div>
</section>
