<?php
/**
 * Ferm Living Room Grid Section - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$rooms = $data["rooms"] ?? [];
if ( empty( $rooms ) ) { return; }
?>
<section class="section py-16 tab_l:py-24" data-component="roomGrid">
  <div class="limit mx-auto px-4 tab_l:px-6">
    <div class="grid-12">
      <div class="col-span-12 mb-8 tab_l:mb-12">
        <h2 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15] text-black">Rooms</h2>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
        <?php foreach ( $rooms as $room ) : $name = $room["name"] ?? ""; $image = $room["image"] ?? ""; $alt = $room["alt"] ?? $name; $url = $room["url"] ?? "#"; ?>
        <div class="room-card group relative overflow-hidden aspect-square">
          <a href="<?php echo esc_url( $url ); ?>" class="absolute inset-0 block no-underline z-10" aria-label="<?php echo esc_attr( $name ); ?>"></a>
          <div class="relative aspect-square overflow-hidden">
            <?php if ( $image ) : ?>
            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" class="absolute left-0 top-0 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
          </div>
          <div class="absolute left-4 bottom-4 z-10 text-left text-cream">
            <h3 class="font-primary text-xl font-medium leading-[1.15]"><?php echo esc_html( $name ); ?></h3>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
