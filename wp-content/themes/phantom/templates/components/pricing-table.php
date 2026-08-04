<?php
/**
 * Pricing table — plan card with features list.
 *
 * Expected data: title, price, period, features, cta (label, href), featured.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<section class="phantom-pricing<?php echo $view->prop( 'featured' ) ? ' is-featured' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
	<?php if ( $view->prop( 'title' ) ) : ?>
		<h3 class="phantom-pricing__title"><?php echo $view->e( $view->prop( 'title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></h3>
	<?php endif; ?>
	<div class="phantom-pricing__price">
		<span class="phantom-pricing__amount"><?php echo $view->e( $view->prop( 'price', '0' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
		<?php if ( $view->prop( 'period' ) ) : ?>
			<span class="phantom-pricing__period"><?php echo $view->e( $view->prop( 'period' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
		<?php endif; ?>
	</div>
	<ul class="phantom-pricing__features">
		<?php foreach ( (array) $view->prop( 'features', array() ) as $feature ) : ?>
			<li class="phantom-pricing__feature"><?php echo $view->e( $feature ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></li>
		<?php endforeach; ?>
	</ul>
	<?php if ( $view->prop( 'cta' ) && ! empty( $view->prop( 'cta' )['href'] ) ) : ?>
		<a class="phantom-btn phantom-btn--primary" href="<?php echo $view->url( $view->prop( 'cta' )['href'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
			<?php echo $view->e( $view->prop( 'cta' )['label'] ?? 'Choose plan' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
		</a>
	<?php endif; ?>
</section>
