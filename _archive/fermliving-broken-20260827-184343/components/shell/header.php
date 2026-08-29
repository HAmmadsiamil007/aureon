<?php
/**
 * Ferm Living Header - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$cart_count = (int) ( $data["header"]["cart_count"] ?? 0 );
$is_home    = (bool) ( $data["header"]["is_home"] ?? false );
$site_name  = $data["site"]["name"] ?? "Ferm Living";
$site_url   = home_url( "/" );
$logo_svg   = file_get_contents( aether_active_design_dir() . "assets/logo.svg" );
?>
<section data-section-id="header" data-section-type="header" data-component="header" data-template="<?php echo $is_home ? "index" : "default"; ?>" data-always-solid="false" class="fixed z-[12]">
  <header class="header header--transparent has-bar fixed left-0 top-0 z-[12] w-full [backface-visibility:hidden] transition-transform duration-200 tab_l:duration-500 ease-in-out" role="banner">
    <div class="header__inner limit mx-auto flex w-full max-w-[var(--site-max-width)] items-center justify-between px-4 py-4 md:px-6">
      <div class="header__left flex items-center gap-6">
        <button type="button" class="icon appearance-none border-0 bg-transparent p-2 -m-2 cursor-pointer md:hidden" data-search aria-label="Search" title="Search">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"><circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/><path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/></svg>
        </button>
        <div class="header__logo static col-span-6 flex transform-none items-center gap-10 px-0 py-3 font-secondary [&>*]:!m-0 [&>*]:p-0" data-header-logo data-header-box>
          <a class="logo" href="<?php echo esc_url( $site_url ); ?>" aria-label="<?php echo esc_attr( $site_name . " - Home" ); ?>"><?php echo $logo_svg ?: "<svg id=\"b\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 738.01 693.99\" class=\"h-8 pt-[3px] tab_l:h-[45px] fill-black\"><g id=\"c\"><path d=\"M164.73,536.33h-34.05c3.61,4.74,6.09,10.38,6.09,19.4v114.57c0,9.02-2.48,14.66-6.09,19.4h34.05c-3.61-4.74-6.09-10.37-6.09-19.4v-114.57c0-9.02,2.48-14.66,6.09-19.4Z\"/></g></svg>"; ?></a>
        </div>
      </div>
      <nav class="header__nav hidden tab_l:flex items-center gap-8" data-component="navigation" data-megamenus>
        <?php wp_nav_menu( [ "theme_location" => "primary", "container" => false, "items_wrap" => "%3$s", "walker" => new Ferm_MegaMenu_Walker() ] ); ?>
      </nav>
      <div class="header__right flex items-center gap-2 font-secondary text-sm font-normal not-italic tab_l:!hidden [&>*]:!m-0 [&>*]:p-0">
        <div class="search">
          <button type="button" class="icon appearance-none border-0 bg-transparent p-2 -m-2 cursor-pointer" data-search aria-label="Search" title="Search"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"><circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/><path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/></svg><span class="sr-only">Search</span></button>
        </div>
        <a href="<?php echo esc_url( get_permalink( get_option( "woocommerce_myaccount_page_id" ) ) ); ?>" class="icon" data-wishlist aria-label="Wishlist" title="Wishlist"><svg class="h-6 w-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.226 6.87717L11.961 7.79592C11.981 7.82094 12.019 7.82094 12.039 7.79592L12.774 6.87717C13.4699 6.00733 14.5235 5.50098 15.6374 5.50098C16.6099 5.50098 17.5426 5.88731 18.2302 6.57498L18.3281 6.67285C19.0785 7.42319 19.5 8.44087 19.5 9.50201V9.86553C19.5 10.3899 19.4155 10.9108 19.2496 11.4083L19.0534 11.9971C18.7287 12.9712 18.2342 13.8801 17.5928 14.6818L17.4072 14.9138C16.9289 15.5118 16.3858 16.0549 15.7878 16.5332L15.6832 16.6169C14.7951 17.3274 13.8071 17.9031 12.7511 18.3255C12.2689 18.5184 11.7311 18.5184 11.2489 18.3255C10.1929 17.9031 9.20492 17.3274 8.31681 16.6169L8.21216 16.5332C7.61423 16.0549 7.07112 15.5118 6.59277 14.9138L6.40719 14.6818C5.76579 13.8801 5.27132 12.9712 4.94663 11.9971L4.75036 11.4083C4.58454 10.9108 4.5 10.3899 4.5 9.86553V9.50201C4.5 8.44087 4.92154 7.42319 5.67187 6.67285L5.76975 6.57498C6.45742 5.88731 7.3901 5.50098 8.36261 5.50098C9.47655 5.50098 10.5301 6.00733 11.226 6.87717Z" stroke="currentColor" stroke-width="1.25"/></svg></a>
        <button type="button" class="relative flex h-10 w-10 appearance-none border-0 bg-transparent p-0 cursor-pointer items-center justify-center text-black" data-header-cart aria-label="Cart" aria-expanded="false" data-cart-count="<?php echo esc_attr( $cart_count ); ?>">
          <svg class="h-6 w-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 21V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2M7 21a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2.5l1.5-3h5l1.5 3H21a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-black text-[10px] font-medium text-white" aria-hidden="true" data-cart-count><?php echo esc_html( $cart_count ); ?></span>
          <span class="sr-only" data-cart-count-label><?php echo sprintf( _n( "Cart (%d)", "Cart (%d)", $cart_count, "aureon" ), $cart_count ); ?></span>
        </button>
        <button type="button" class="flex h-6 w-6 appearance-none border-0 bg-transparent p-0 cursor-pointer items-center justify-end text-black" data-mobile-menu-link aria-label="Menu" aria-expanded="false" title="Menu"><span class="mobile-menu-icon mobile-menu-icon--closed"><svg class="h-[16px] w-[16px]" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Group 2602"><path id="Vector 301" d="M0 1H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path><path id="Vector 302" d="M3.5 6H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path><path id="Vector 304" d="M2 11H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path></g></svg></span><span class="mobile-menu-icon mobile-menu-icon--open"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-[16px] w-[16px]"><g id="Group 2602"><path id="Vector 301" d="M1.05078 11L10.9503 1.10051" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"></path><path id="Vector 304" d="M1.05078 1L10.9503 10.8995" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"></path></g></svg></span></button>
      </div>
    </div>
    <div class="megamenu-wrapper z-1 relative hidden tab_l:block" data-component="megaMenu" data-megamenus><?php echo ferm_get_megamenus(); ?></div>
  </header>
</section>
<?php
if ( ! class_exists( "Ferm_MegaMenu_Walker" ) ) {
  class Ferm_MegaMenu_Walker extends Walker_Nav_Menu {
    function start_lvl( &$output, $depth = 0, $args = [] ) { if ( 0 === $depth ) { $output .= "<div class=\"megamenu-wrapper z-1 relative hidden tab_l:block\" data-component=\"megaMenu\" data-megamenus>"; } else { $output .= "<ul class=\"sub-menu\">"; } }
    function end_lvl( &$output, $depth = 0, $args = [] ) { if ( 0 === $depth ) { $output .= "</div>"; } else { $output .= "</ul>"; } }
  }
}
function ferm_get_megamenus() { /* return static megamenu HTML from template */ return ""; }
