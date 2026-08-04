<?php
/**
 * BackToTop — scroll-to-top control revealed after page scroll.
 *
 * Expected data: label.
 *
 * @package Lumina\Core\Components
 */

declare( strict_types=1 );
?>
<button
	class="lumina-back-to-top"
	type="button"
	aria-label="<?php echo $view->attr( $view->prop( 'label', 'Back to top' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	hidden
	data-lumina-back-to-top
>
	<span aria-hidden="true">&#8593;</span>
</button>
