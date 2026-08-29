<?php
/**
 * Ferm Living Mobile Chrome - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
?>
<div class="mobile-menu fixed z-[-1] hidden h-full w-full overflow-hidden bg-cream tab_l:hidden" data-mobile-menu data-component="mobileMenu">
  <div class="mobile-menu__inner limit flex h-full flex-col justify-between overflow-y-auto pt-[66px]">
    <div class="mobile-menu__top-links flex flex-1 flex-col gap-[24px]">
      <?php $menu_items = [ "Shop", "Inspiration", "Rooms", "Professionals" ]; foreach ( $menu_items as $item ) : ?>
      <div class="mobile-menu__top-link text-2xl leading-[28px]"><a href="<?php echo esc_url( home_url( "/" ) ); ?>" data-mobile-menu-top-link="<?php echo esc_attr( $item ); ?>" class="mobile-menu__link"><?php echo esc_html( $item ); ?></a></div>
      <?php endforeach; ?>
    </div>
    <div class="mobile-menu__bottom-links mb-[66px] mt-6 flex flex-1 items-center justify-between">
      <a class="w-[50%] text-2xl text-black" href="<?php echo esc_url( get_permalink( get_option( "woocommerce_myaccount_page_id" ) ) ); ?>">Login</a>
      <div class="w-[50%]" data-language-container><?php echo ferm_language_selector(); ?></div>
    </div>
  </div>
</div>
<?php
function ferm_language_selector() { $current = get_locale(); $options = [ "https://fermliving.com" => "Denmark / DKK", "https://fermliving.us/" => "United States / USD", "https://fermliving.co.uk/" => "United Kingdom / GBP", "https://fermliving.se/" => "Sweden / SEK" ]; $html = "<select onchange=\"window.location.href = this.value + window.location.pathname.replace(/^\\/[a-z]{2}-[a-z]{2}/, \"\") + window.location.search\" data-language-select aria-label=\"Stores\" class=\"relative w-full min-w-32 cursor-pointer appearance-none rounded-none border-none bg-transparent bg-[url(\"\")] py-0 pl-0 pr-4 text-[13px] font-normal leading-[18px] text-black outline-none focus:outline-0 focus:[box-shadow:none]\">"; foreach ( $options as $url => $label ) { $selected = ( strpos( $current, "en" ) !== false && strpos( $url, "fermliving.com" ) !== false ) ? "selected" : ""; $html .= "<option value=\"" . esc_url( $url ) . "\" $selected>$label</option>"; } $html .= "</select>"; return $html; }
