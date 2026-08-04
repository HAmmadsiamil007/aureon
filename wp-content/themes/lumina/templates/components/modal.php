<?php
/**
 * Modal — accessible dialog with focus trap surface.
 *
 * Expected data: trigger (label, href), title, content, dismiss_label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div class="lumina-modal" data-lumina-modal>
	<?php if ( $view->prop( 'trigger' ) ) : ?>
		<button
			type="button"
			class="lumina-btn lumina-btn--primary"
			data-lumina-modal-open
			aria-haspopup="dialog"
		>
			<?php echo $view->e( $view->prop( 'trigger' )['label'] ?? 'Open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</button>
	<?php endif; ?>
	<div class="lumina-modal__overlay" data-lumina-modal-overlay hidden>
		<div class="lumina-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="lumina-modal-title" tabindex="-1">
			<header class="lumina-modal__head">
				<h2 class="lumina-modal__title" id="lumina-modal-title"><?php echo $view->e( $view->prop( 'title', 'Modal' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
				<button type="button" class="lumina-modal__close" data-lumina-modal-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Close' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
			</header>
			<div class="lumina-modal__body"><?php echo $view->e( $view->prop( 'content', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></div>
		</div>
	</div>
</div>
