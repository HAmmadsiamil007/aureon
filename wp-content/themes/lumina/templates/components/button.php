<?php
/**
 * Button — minimal presentational component used to validate the Phase 5
 * Component Registry (variants, props, and the `[lumina:button]` DSL).
 *
 * Consumes a ViewContext ($view): every field is escaped through the context
 * helpers. Presentational only — no business logic, no WordPress globals.
 *
 * Expected data: label, href, class (variant-merged), size.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<?php if ( $view->prop( 'href' ) ) : ?>
	<a
		class="<?php echo $view->attr( $view->prop( 'class', 'lumina-btn lumina-btn--primary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
		href="<?php echo $view->url( $view->prop( 'href' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"
		data-lumina-size="<?php echo $view->attr( $view->prop( 'size', 'md' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	>
		<?php echo $view->e( $view->prop( 'label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
	</a>
<?php else : ?>
	<button
		type="button"
		class="<?php echo $view->attr( $view->prop( 'class', 'lumina-btn lumina-btn--primary' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
		data-lumina-size="<?php echo $view->attr( $view->prop( 'size', 'md' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	>
		<?php echo $view->e( $view->prop( 'label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
	</button>
<?php endif; ?>
