<?php
/**
 * Ferm Living Product Gallery - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$gallery = $data["gallery"] ?? [];
$product_id = $data["product_id"] ?? 0;
?>
<div class="product-gallery relative" data-component="productGallery" data-product-id="<?php echo esc_attr( $product_id ); ?>">
  <div class="product-gallery__main relative aspect-square overflow-hidden">
    <?php if ( ! empty( $gallery ) ) : $first = $gallery[0]; ?>
    <img src="<?php echo esc_url( $first["src"] ?? "" ); ?>" alt="<?php echo esc_attr( $first["alt"] ?? "" ); ?>" loading="eager" class="absolute left-0 top-0 h-full w-full object-cover" data-main-image>
    <?php endif; ?>
  </div>
  <?php if ( count( $gallery ) > 1 ) : ?>
  <div class="product-gallery__thumbs flex gap-2 mt-4 overflow-x-auto pb-2">
    <?php foreach ( $gallery as $index => $img ) : ?>
    <button type="button" class="flex-shrink-0 h-20 w-20 rounded border-2 border-transparent hover:border-black/50 transition-colors" aria-label="<?php echo esc_attr( $img["alt"] ?? "Image " . ( $index + 1 ) ); ?>" data-thumb-index="<?php echo esc_attr( $index ); ?>">
      <img src="<?php echo esc_url( $img["src"] ?? "" ); ?>" alt="" loading="lazy" class="h-full w-full object-cover rounded">
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
