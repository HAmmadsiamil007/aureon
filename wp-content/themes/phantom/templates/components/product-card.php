<?php
/**
 * ProductCard — commerce product card: media, name, rating, price, badges
 * and an actions slot. Pure presentational — data arrives via adapters.
 *
 * Expected data: name, price, regular_price, image, image_alt, link, rating,
 * rating_count, badges (list of badge labels), actions (slot HTML).
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<article class="phantom-product-card" data-phantom-anim="reveal">
	<?php if ( $view->prop( 'image' ) ) : ?>
		<a class="phantom-product-card__media" href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" tabindex="-1">
			<img
				src="<?php echo $view->url( $view->prop( 'image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>"
				alt="<?php echo $view->attr( $view->prop( 'image_alt', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>"
				loading="lazy"
			/>
		</a>
	<?php endif; ?>

	<?php if ( is_array( $view->prop( 'badges' ) ) && array() !== $view->prop( 'badges' ) ) : ?>
		<ul class="phantom-product-card__badges">
			<?php foreach ( $view->prop( 'badges' ) as $badge ) : ?>
				<li class="phantom-product-card__badge"><?php echo $view->e( $badge ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="phantom-product-card__body">
		<h3 class="phantom-product-card__title">
			<a href="<?php echo $view->url( $view->prop( 'link' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>">
				<?php echo $view->e( $view->prop( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>
			</a>
		</h3>

		<?php if ( (float) $view->prop( 'rating', 0.0 ) > 0.0 ) : ?>
			<div class="phantom-product-card__rating" data-phantom-rating="<?php echo $view->attr( (string) $view->prop( 'rating' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
				<?php echo $view->e( (string) $view->prop( 'rating' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>/5
				<?php if ( (int) $view->prop( 'rating_count', 0 ) > 0 ) : ?>
					<span class="phantom-product-card__rating-count">(<?php echo $view->e( (string) $view->prop( 'rating_count' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?>)</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="phantom-product-card__price">
			<?php if ( $view->prop( 'regular_price' ) && $view->prop( 'price' ) !== $view->prop( 'regular_price' ) ) : ?>
				<s class="phantom-product-card__price-old"><?php echo $view->e( $view->prop( 'regular_price' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></s>
			<?php endif; ?>
			<span class="phantom-product-card__price-current"><?php echo $view->e( $view->prop( 'price' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::e(). ?></span>
		</div>
	</div>

	<?php if ( $view->prop( 'actions' ) ) : ?>
		<div class="phantom-product-card__actions"><?php echo $view->prop( 'actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- registry-rendered slot HTML from escaped leaves. ?></div>
	<?php endif; ?>
</article>
