<?php
/**
 * Modal — accessible dialog with focus trap surface.
 *
 * Expected data: trigger (label, href), title, content, dismiss_label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-modal" data-phantom-modal>
	<?php if ( $view->prop( 'trigger' ) ) : ?>
		<button
			type="button"
			class="phantom-btn phantom-btn--primary"
			data-phantom-modal-open
			aria-haspopup="dialog"
		>
			<?php echo $view->e( $view->prop( 'trigger' )['label'] ?? 'Open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</button>
	<?php endif; ?>
	<div class="phantom-modal__overlay" data-phantom-modal-overlay hidden>
		<div class="phantom-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="phantom-modal-title" tabindex="-1">
			<header class="phantom-modal__head">
				<h2 class="phantom-modal__title" id="phantom-modal-title"><?php echo $view->e( $view->prop( 'title', 'Modal' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
				<button type="button" class="phantom-modal__close" data-phantom-modal-close aria-label="<?php echo $view->attr( $view->prop( 'dismiss_label', 'Close' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">&times;</button>
			</header>
			<div class="phantom-modal__body"><?php echo $view->e( $view->prop( 'content', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></div>
		</div>
	</div>
</div>
