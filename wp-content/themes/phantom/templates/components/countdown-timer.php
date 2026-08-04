<?php
/**
 * CountdownTimer — live countdown to a target date (aria-live polite).
 *
 * Expected data: target (ISO-8601 date string), label.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-countdown-timer" aria-live="polite" data-phantom-countdown data-target="<?php echo $view->attr( $view->prop( 'target', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<?php if ( $view->prop( 'label' ) ) : ?>
		<p class="phantom-countdown-timer__label"><?php echo $view->e( $view->prop( 'label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></p>
	<?php endif; ?>
	<div class="phantom-countdown-timer__units" data-phantom-countdown-units>
		<span class="phantom-countdown-timer__unit" data-unit="days">--</span>
		<span class="phantom-countdown-timer__unit" data-unit="hours">--</span>
		<span class="phantom-countdown-timer__unit" data-unit="minutes">--</span>
		<span class="phantom-countdown-timer__unit" data-unit="seconds">--</span>
	</div>
</div>
