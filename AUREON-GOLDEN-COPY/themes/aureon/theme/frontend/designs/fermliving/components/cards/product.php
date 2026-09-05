<?php
/**
 * Ferm Living product card — image carousel, badges, wishlist heart, swatches, "+ Add to Cart".
 *
 * Key:    'card/product' (override)
 * Source: fermliving.com product card structure
 * Props:  same schema as engine card/product + swatches array.
 * Contract: keeps .product-card, .product-card-image, .product-card-info,
 *           .product-card-name, .product-card-price, .add-to-cart-btn
 *           data-product-id, data-product-type — AJAX cart, wishlist JS operate unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$id              = isset( $componentData['id'] ) ? (int) $componentData['id'] : 0;
$name            = isset( $componentData['name'] ) ? $componentData['name'] : '';
$price           = isset( $componentData['price'] ) ? $componentData['price'] : '';
$image           = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt             = isset( $componentData['alt'] ) ? $componentData['alt'] : $name;
$url             = isset( $componentData['url'] ) ? $componentData['url'] : '#';
$badge           = isset( $componentData['badge'] ) ? $componentData['badge'] : '';
$add_to_cart_url = isset( $componentData['add_to_cart_url'] ) ? $componentData['add_to_cart_url'] : '';
$product_type    = isset( $componentData['product_type'] ) ? $componentData['product_type'] : 'simple';
$swatches        = isset( $componentData['swatches'] ) ? (array) $componentData['swatches'] : array();
$images          = isset( $componentData['images'] ) ? (array) $componentData['images'] : array();

if ( ! $name ) {
	return;
}

// Build add-to-cart URL.
if ( $add_to_cart_url ) {
	$aether_cta_url = $add_to_cart_url;
} elseif ( $id ) {
	$aether_cta_url = add_query_arg( 'add-to-cart', $id, $url );
} else {
	$aether_cta_url = $url;
}

// Badge class.
$aether_badge_class = '';
if ( 'New' === $badge ) {
	$aether_badge_class = ' badge-new';
} elseif ( 'Certified' === $badge ) {
	$aether_badge_class = ' badge-certified';
} elseif ( 'Sale' === $badge ) {
	$aether_badge_class = ' badge-sale';
}

// Collect all images for carousel (primary + additional).
$all_images = array();
if ( $image ) {
	$all_images[] = array( 'src' => $image, 'alt' => $alt );
}
foreach ( $images as $img ) {
	$img_src = isset( $img['src'] ) ? $img['src'] : ( is_string( $img ) ? $img : '' );
	$img_alt = isset( $img['alt'] ) ? $img['alt'] : $alt;
	if ( $img_src && $img_src !== $image ) {
		$all_images[] = array( 'src' => $img_src, 'alt' => $img_alt );
	}
}
$has_carousel = count( $all_images ) > 1;
?>
<div class="product-card" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?> data-component="productThumb">

	<?php /* Image area */ ?>
	<div class="product-card-image">
		<?php if ( $has_carousel ) : ?>
			<?php /* Image carousel */ ?>
			<div class="product-card-carousel" data-component="productThumbCarousel" role="group" aria-roledescription="Image carousel" aria-label="<?php echo esc_attr( $name ); ?>">
				<div class="product-card-carousel-viewport">
					<div class="product-card-carousel-track">
						<?php foreach ( $all_images as $img ) : ?>
							<div class="product-card-carousel-slide">
								<a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $name ); ?>" class="product-card-image-link">
									<img loading="lazy"
										 src="<?php echo esc_url( $img['src'] ); ?>"
										 alt="<?php echo esc_attr( $img['alt'] ); ?>"
										 width="600"
										 height="800"
										 decoding="async">
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php /* Carousel dots */ ?>
				<div class="product-card-dots">
					<?php foreach ( $all_images as $i => $img ) : ?>
						<button type="button"
								class="product-card-dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
								aria-label="Go to image <?php echo esc_attr( $i + 1 ); ?>"
								<?php echo 0 === $i ? 'aria-current="true"' : ''; ?>></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php else : ?>
			<?php /* Single image */ ?>
			<a href="<?php echo esc_url( $url ); ?>" class="product-card-image-link" aria-label="<?php echo esc_attr( $name ); ?>">
				<?php if ( $image ) : ?>
					<img loading="lazy"
						 src="<?php echo esc_url( $image ); ?>"
						 alt="<?php echo esc_attr( $alt ); ?>"
						 width="600"
						 height="800"
						 decoding="async">
				<?php endif; ?>
			</a>
		<?php endif; ?>

		<?php /* Badges */ ?>
		<?php if ( $badge ) : ?>
			<div class="product-card-badges">
				<span class="product-badge<?php echo esc_attr( $aether_badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
			</div>
		<?php endif; ?>

		<?php /* Wishlist button */ ?>
		<div class="product-card-wishlist">
			<button type="button"
					data-wishlist-button
					data-product-id="<?php echo esc_attr( $id ); ?>"
					aria-label="Add to wishlist">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M11.226 6.87717L11.961 7.79592C11.981 7.82094 12.019 7.82094 12.039 7.79592L12.774 6.87717C13.4699 6.00733 14.5235 5.50098 15.6374 5.50098C16.6099 5.50098 17.5426 5.88731 18.2302 6.57498L18.3281 6.67285C19.0785 7.42319 19.5 8.44087 19.5 9.50201V9.86553C19.5 10.3899 19.4155 10.9108 19.2496 11.4083L19.0534 11.9971C18.7287 12.9712 18.2342 13.8801 17.5928 14.6818L17.4072 14.9138C16.9289 15.5118 16.3858 16.0549 15.7878 16.5332L15.6832 16.6169C14.7951 17.3274 13.8071 17.9031 12.7511 18.3255C12.2689 18.5184 11.7311 18.5184 11.2489 18.3255C10.1929 17.9031 9.20492 17.3274 8.31681 16.6169L8.21216 16.5332C7.61423 16.0549 7.07112 15.5118 6.59277 14.9138L6.40719 14.6818C5.76579 13.8801 5.27132 12.9712 4.94663 11.9971L4.75036 11.4083C4.58454 10.9108 4.5 10.3899 4.5 9.86553V9.50201C4.5 8.44087 4.92154 7.42319 5.67187 6.67285L5.76975 6.57498C6.45742 5.88731 7.3901 5.50098 8.36261 5.50098C9.47655 5.50098 10.5301 6.00733 11.226 6.87717Z" stroke="currentColor" stroke-width="1.25"/>
				</svg>
			</button>
		</div>

		<?php /* Color swatches — positioned at bottom-left of image like real Ferm */ ?>
		<?php if ( ! empty( $swatches ) ) : ?>
			<div class="product-card-swatches">
				<?php foreach ( $swatches as $swatch ) :
					$swatch_color = isset( $swatch['color'] ) ? $swatch['color'] : '';
					$swatch_url   = isset( $swatch['url'] ) ? $swatch['url'] : '#';
					$swatch_label = isset( $swatch['label'] ) ? $swatch['label'] : '';
					if ( empty( $swatch_color ) ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( $swatch_url ); ?>"
					   class="product-card-swatch"
					   title="<?php echo esc_attr( $swatch_label ); ?>"
					   aria-label="<?php echo esc_attr( $swatch_label ); ?>">
						<span class="product-card-swatch-inner" style="background-color: <?php echo esc_attr( $swatch_color ); ?>"></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php /* Product info */ ?>
	<div class="product-card-info">
		<a href="<?php echo esc_url( $url ); ?>" class="product-card-name" data-product-click="true" data-product-title="<?php echo esc_attr( $name ); ?>" data-product-price="<?php echo esc_attr( $id ); ?>" data-product-id="<?php echo esc_attr( $id ); ?>">
			<?php echo esc_html( $name ); ?>
		</a>
		<div class="product-card-price">
			<?php // WC prices arrive as sanitized HTML (bdi > amount); esc_html would mangle them.
			echo wp_kses_post( $price ); ?>
		</div>
	</div>

	<?php /* Add to Cart CTA */ ?>
	<div class="product-card-cta">
		<a href="<?php echo esc_url( $aether_cta_url ); ?>"
		   class="btn product-card-add-to-cart"
		   <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?>
		   data-product-type="<?php echo esc_attr( $product_type ); ?>"
		   data-button-add-to-cart>+ Add to Cart</a>
	</div>
</div>
