<?php
/**
 * BackToTop — scroll-to-top control revealed after page scroll.
 *
 * Expected data: label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<button
	class="phantom-back-to-top"
	type="button"
	aria-label="<?php echo $view->attr( $view->prop( 'label', 'Back to top' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
	hidden
	data-phantom-back-to-top
>
	<span aria-hidden="true">&#8593;</span>
</button>
