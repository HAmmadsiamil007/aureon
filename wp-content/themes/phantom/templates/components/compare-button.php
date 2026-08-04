<?php
/**
 * CompareButton — toggle button reflecting compare-list membership.
 *
 * Expected data: label, product_id, active.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<button
	class="phantom-compare-button<?php echo $view->prop( 'active' ) ? ' is-active' : ''; ?>"
	type="button"
	aria-pressed="<?php echo $view->prop( 'active' ) ? 'true' : 'false'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean-gated literal. ?>"
	data-phantom-compare
	data-phantom-product-id="<?php echo $view->attr( (string) $view->prop( 'product_id', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
>
	<span class="phantom-compare-button__icon" aria-hidden="true">&#8645;</span>
	<span class="phantom-compare-button__label"><?php echo $view->e( $view->prop( 'label', 'Compare' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
</button>
