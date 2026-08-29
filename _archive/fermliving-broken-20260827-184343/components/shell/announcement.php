<?php
/**
 * Ferm Living Announcement Bar
 * Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$items = $data["announcement"]["items"] ?? [];
$speed = $data["announcement"]["speed"] ?? 4000;
?>
<div class="relative z-[2] bg-canvas text-center text-sm text-black backdrop-blur-0 transition-all duration-200 tab_l:duration-500 ease-in-out" data-header-bar>
  <div class="limit mx-auto my-0 flex h-8 w-full max-w-[var(--site-max-width)] items-center justify-center px-4 py-0 font-medium [font-smooth:always] [font-smoothing:antialiased] tab_p:pt-0 [&_*]:[font-smooth:inherit] [&_*]:[font-smoothing:inherit]">
    <div class="grid-12 relative h-8 w-full overflow-hidden">
      <div class="col-span-6 hidden tab_l:block"></div>
      <div class="col-span-12 h-8 md:col-span-6">
        <div class="relative flex h-full w-full items-center justify-between overflow-hidden font-secondary text-sm" data-component="uspHeader" data-speed="<?php echo esc_attr( $speed ); ?>" data-usp-length="<?php echo esc_attr( count( $items ) ); ?>" role="region" aria-label="Announcements" aria-roledescription="carousel">
          <div class="left-0 right-0 top-[5px] flex w-full items-center justify-between" aria-live="polite" aria-atomic="true">
            <?php foreach ( $items as $index => $item ) : 
              $url = $item["url"] ?? "#";
              $text = $item["text"] ?? "";
              $is_first = ( 0 === $index );
            ?>
            <a data-usp-item data-usp-index="<?php echo esc_attr( $index ); ?>" href="<?php echo esc_url( $url ); ?>" aria-hidden="<?php echo $is_first ? "false" : "true"; ?>" class="usp-text absolute block w-full -translate-y-full cursor-pointer overflow-hidden text-ellipsis whitespace-nowrap text-left font-normal leading-[16px] no-underline opacity-0 [color:inherit] hover:!opacity-50 [&_p]:!m-0 [&_p]:text-center [&_p]:leading-normal [&_p]:![font-size:inherit] [&_p]:tab_p:text-left <?php echo $is_first ? "animate-in" : ""; ?>">
              <p><?php echo wp_kses_post( $text ); ?></p>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
