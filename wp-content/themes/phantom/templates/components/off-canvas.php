<?php
/**
 * OffCanvas — slide-in drawer with focus-trapped content slot.
 *
 * Expected data: title, position, content (slot HTML).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div
	class="phantom-off-canvas phantom-off-canvas--<?php echo $view->attr( $view->prop( 'position', 'left' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	role="dialog"
	aria-modal="true"
	aria-label="<?php echo $view->attr( $view->prop( 'title', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	hidden
	data-phantom-off-canvas
>
	<div class="phantom-off-canvas__header">
		<?php if ( $view->prop( 'title' ) ) : ?>
			<h2 class="phantom-off-canvas__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h2>
		<?php endif; ?>
		<button class="phantom-off-canvas__close" type="button" aria-label="<?php echo $view->attr( 'Close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-off-canvas-close>
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<div class="phantom-off-canvas__body">
		<?php echo $view->prop( 'content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?>
	</div>
</div>
