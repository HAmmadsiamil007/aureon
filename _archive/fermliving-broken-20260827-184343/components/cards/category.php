<?php
/**
 * Ferm Living Category Card - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$category = $data["category"] ?? [];
$name     = $category["name"] ?? "";
$count    = $category["count"] ?? "";
$image    = $category["image"] ?? "";
$alt      = $category["alt"] ?? "Shop " . $name;
$url      = $category["url"] ?? "#";
$modifier = $category["modifier"] ?? "";
$reveal   = ! empty( $category["behavior"]["reveal"] );
?>
<div class="category-card group relative overflow-hidden <?php echo $reveal ? "reveal" : ""; ?> <?php echo esc_attr( $modifier ); ?>">
  <a href="<?php echo esc_url( $url ); ?>" class="absolute inset-0 block no-underline z-10" aria-label="<?php echo esc_attr( $alt ); ?>"></a>
  <div class="relative aspect-[4/5] overflow-hidden">
    <?php if ( $image ) : ?>
    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" class="absolute left-0 top-0 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
    <?php endif; ?>
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
  </div>
  <div class="absolute left-4 bottom-4 z-10 text-left text-cream">
    <h3 class="font-primary text-2xl font-medium leading-[1.15]"><?php echo esc_html( $name ); ?></h3>
    <?php if ( $count ) : ?><p class="text-sm font-normal mt-1"><?php echo esc_html( $count ); ?></p><?php endif; ?>
  </div>
</div>
