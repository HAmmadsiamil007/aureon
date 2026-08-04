<?php
/**
 * ProductGallery — accessible image gallery: main figure + thumbnail list.
 *
 * Expected data: images (list of URLs), alt.
 *
 * @package Phantom\Core\Components
 */

declare( strict_types=1 );
?>
<div class="phantom-product-gallery" data-phantom-gallery>
	<?php
	$images = (array) $view->prop( 'images' );
	$main   = isset( $images[0] ) ? (string) $images[0] : '';
	?>
	<figure class="phantom-product-gallery__main">
		<?php if ( '' !== $main ) : ?>
			<img src="<?php echo $view->url( $main ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="<?php echo $view->attr( $view->prop( 'alt', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-gallery-main />
		<?php endif; ?>
	</figure>

	<?php if ( count( $images ) > 1 ) : ?>
		<ul class="phantom-product-gallery__thumbs" aria-label="<?php echo $view->attr( 'Product images' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>">
			<?php foreach ( $images as $index => $image ) : ?>
				<li class="phantom-product-gallery__thumb">
					<button class="phantom-product-gallery__thumb-button<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" aria-label="<?php echo $view->attr( sprintf( 'Show image %d', $index + 1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::attr(). ?>" data-phantom-gallery-thumb>
						<img src="<?php echo $view->url( (string) $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by ViewContext::url(). ?>" alt="" loading="lazy" />
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
