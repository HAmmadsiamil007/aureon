<?php
/**
 * OffCanvas — slide-in drawer with focus-trapped content slot.
 *
 * Expected data: title, position, content (slot HTML).
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<div
	class="lumina-off-canvas lumina-off-canvas--<?php echo $view->attr( $view->prop( 'position', 'left' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	role="dialog"
	aria-modal="true"
	aria-label="<?php echo $view->attr( $view->prop( 'title', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	hidden
	data-lumina-off-canvas
>
	<div class="lumina-off-canvas__header">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="lumina-off-canvas__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<button class="lumina-off-canvas__close" type="button" aria-label="<?php echo $view->attr( 'Close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-lumina-off-canvas-close>
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<div class="lumina-off-canvas__body">
		<?php echo $view->prop( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?>
	</div>
</div>
