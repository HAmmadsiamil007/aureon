<?php
/**
 * Ferm Living site header — exact frozen source DOM structure.
 *
 * Key:    'shell/header' (override)
 * Source: fermliving.com header structure
 * Props:  same schema as engine shell/header:
 *         brand, brand_url, menu, icons, cart_count.
 * Contract: keeps #header, .header-icon, .cart-count aria-labels —
 *           platform JS (AJAX cart, search, drawer) operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$brand      = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url  = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$menu       = isset( $componentData['menu'] ) ? (array) $componentData['menu'] : array();
$icons      = isset( $componentData['icons'] ) ? (array) $componentData['icons'] : array();
$cart_count = isset( $componentData['cart_count'] ) ? (int) $componentData['cart_count'] : 0;

$search   = isset( $icons['search'] ) ? $icons['search'] : '#';
$wishlist = isset( $icons['wishlist'] ) ? $icons['wishlist'] : '#';
$cart     = isset( $icons['cart'] ) ? $icons['cart'] : '#';
$account  = isset( $icons['account'] ) ? $icons['account'] : '#';

$is_home = is_front_page() || ( is_home() && ! is_paged() );
$header_class = 'header header--transparent has-bar fixed left-0 top-0 z-[12] w-full [backface-visibility:hidden] transition-transform duration-200 tab_l:duration-500 ease-in-out';

?>
<section
  data-section-id='header'
  data-section-type='header'
  data-component='header'
  data-template='<?php echo esc_attr( $is_home ? 'index' : 'page' ); ?>'
  data-always-solid='false'
  class='fixed z-[12]'
>
  <header
    class='<?php echo esc_attr( $header_class ); ?>'
    role='banner'
  >
    
      <div
        class='relative z-[2] bg-canvas text-center text-sm text-black backdrop-blur-0 transition-all duration-200 tab_l:duration-500 ease-in-out'
        data-header-bar
      >
        <div class='limit mx-auto my-0 flex h-8 w-full max-w-[var(--site-max-width)] items-center justify-center px-4 py-0 font-medium [font-smooth:always] [font-smoothing:antialiased] tab_p:pt-0 [&_*]:[font-smooth:inherit] [&_*]:[font-smoothing:inherit]'>
          <div class='grid-12 relative h-8 w-full overflow-hidden'>
            <div class='col-span-6 hidden tab_l:block'></div>

            <div class='col-span-12 h-8 md:col-span-6'>
              <?php /* USP Header — rotating announcements */ ?>
              <div
                class='relative flex h-full w-full items-center justify-between overflow-hidden font-secondary text-sm'
                data-component='uspHeader'
                data-speed='<?php echo esc_attr( aureon_get_option( 'ferm_announcement_speed', 4000 ) ); ?>'
                data-usp-length='<?php echo esc_attr( count( aureon_get_option( 'ferm_announcement_items', array() ) ) ); ?>'
                role='region'
                aria-label='Announcements'
                aria-roledescription='carousel'
              >
                <div
                  class='left-0 right-0 top-[5px] flex w-full items-center justify-between'
                  aria-live='polite'
                  aria-atomic='true'
                >
                  <?php
                  $usp_items = aureon_get_option( 'ferm_announcement_items', array() );
                  $usp_index = 0;
                  foreach ( $usp_items as $item ) :
                    $usp_text = isset( $item['text'] ) ? $item['text'] : '';
                    $usp_url  = isset( $item['url'] ) ? $item['url'] : '';
                    $usp_index++;
                    $is_first = ( 1 === $usp_index );
                    $aria_hidden = $is_first ? 'false' : 'true';
                    $animate_class = $is_first ? ' animate-in' : '';
                    ?>
                    <?php if ( $usp_url ) : ?>
                      <a
                        data-usp-item
                        data-usp-index='<?php echo esc_attr( $usp_index ); ?>'
                        href='<?php echo esc_url( $usp_url ); ?>'
                        aria-hidden='<?php echo esc_attr( $aria_hidden ); ?>'
                        class='usp-text absolute block w-full -translate-y-full cursor-pointer overflow-hidden text-ellipsis whitespace-nowrap text-left font-normal leading-[16px] no-underline opacity-0 [color:inherit] hover:!opacity-50 [&_p]:!m-0 [&_p]:text-center [&_p]:leading-normal [&_p]:![font-size:inherit] [&_p]:tab_p:text-left<?php echo esc_attr( $animate_class ); ?>'
                      >
                        <p><?php echo esc_html( $usp_text ); ?></p>
                      </a>
                    <?php else : ?>
                      <div
                        data-usp-item
                        data-usp-index='<?php echo esc_attr( $usp_index ); ?>'
                        aria-hidden='<?php echo esc_attr( $aria_hidden ); ?>'
                        class='usp-text absolute block w-full -translate-y-full cursor-default overflow-hidden text-ellipsis whitespace-nowrap text-left font-normal leading-[16px] no-underline opacity-0 transition-all duration-300 ease-in-out [color:inherit] [&_p]:!m-0 [&_p]:text-center [&_p]:leading-normal [&_p]:![font-size:inherit] [&_p]:tab_p:text-left<?php echo esc_attr( $animate_class ); ?>'
                      >
                        <p><?php echo esc_html( $usp_text ); ?></p>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                  
                  <span class='w-full text-right hidden font-normal [color:inherit] tab_p:block'>
                    <span data-usp-current-index>1</span> / <?php echo esc_attr( count( $usp_items ) ); ?>
                  </span>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    

    <div
      class='absolute left-0  top-8  w-full bg-cream transition-all duration-200 ease-in-out tab_l:duration-500'
      data-header-nav
    >
      <div
        class='limit mx-auto my-0 flex max-w-[var(--site-max-width)] justify-between tab_l:grid-12'
        data-header-inner
      >
        <div
          class='header__logo static col-span-6 flex transform-none items-center gap-10 px-0 py-3  font-secondary [&>*]:!m-0 [&>*]:p-0'
          data-header-logo
          data-header-box
        >
          <a class='logo' href='<?php echo esc_url( $brand_url ); ?>' aria-label='<?php echo esc_attr( $brand ); ?> - Home'>
            <?php echo wp_kses_post( $brand ); ?>
          </a>

          <div
            class='hidden tab_l:block'
            data-language-container
          >
            <?php /* Language selector — bridges to WPML/Polylang if available */ ?>
            <div class='relative flex rounded-[4px] pl-2'>
              <select
                onchange='window.location.href = this.value + window.location.pathname.replace(/^\/[a-z]{2}-[a-z]{2}/, "") + window.location.search'
                data-language-select
                aria-label='Stores'
                class="relative w-full min-w-32 cursor-pointer appearance-none rounded-none border-none bg-transparent bg-[url('')] py-0 pl-0 pr-4 text-[13px] font-normal leading-[18px] text-black outline-none focus:outline-0 focus:[box-shadow:none]"
              >
                <?php
                $languages = apply_filters( 'wpml_active_languages', array() );
                if ( ! empty( $languages ) ) :
                  foreach ( $languages as $lang ) :
                    ?>
                    <option
                      value='<?php echo esc_url( $lang['url'] ); ?>'
                      <?php echo $lang['active'] ? 'selected' : ''; ?>
                    >
                      <?php echo esc_html( $lang['native_name'] ); ?>
                    </option>
                    <?php
                  endforeach;
                else :
                  ?>
                  <option value='<?php echo esc_url( home_url() ); ?>' selected>English</option>
                  <?php
                endif;
                ?>
              </select>
              <span class='absolute right-2  top-2 -translate-y-1/2' aria-hidden='true'>
                <svg
                  width='6px'
                  height='5px'
                  viewBox='0 0 6 5'
                  version='1.1'
                  xmlns='http://www.w3.org/2000/svg'
                  xmlns:xlink='http://www.w3.org/1999/xlink'
                  class='h-[10px] w-[5px] [&_path]:fill-black'
                >
                  <g id="smallarrow" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="square">
                    <g id="ChevronDownSVG" transform="translate(3.000000, 2.500000) rotate(-270.000000) translate(-3.000000, -2.500000) translate(1.000000, 0.000000)" stroke="currentColor">
                      <g id="Chevron_Down_SVG" transform="translate(-0.000000, 0.000000)">
                        <path d="M3.41287879,2.5 L0.967269822,0.0800570952" id="Line-5"></path>
                        <path d="M3.41287879,2.5 L0.967269822,4.9199429" id="Line-6"></path>
                      </g>
                    </g>
                  </g>
                </svg>
              </span>
            </div>
          </div>
        </div>

        <div
          data-header-box-right
          data-header-box
          class='col-span-6 flex items-center justify-between font-secondary'
        >
          <div class='nav header__navigation hidden items-center justify-start gap-[24px] text-sm tab_l:flex'>
            <?php foreach ( $menu as $item ) :
              $label        = isset( $item['label'] ) ? $item['label'] : '';
              $url          = isset( $item['url'] ) ? $item['url'] : '#';
              $has_children = ! empty( $item['children'] );
              ?>
              <a
                href='<?php echo esc_url( $url ); ?>'
                class='animation-underline leading-none'
                <?php if ( $has_children ) : ?>
                  data-header-link
                  aria-haspopup='true'
                  aria-expanded='false'
                <?php endif; ?>
              ><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
          </div>

          <div
            data-header-right
            class='hidden items-center gap-[24px] text-sm font-normal not-italic tab_l:flex [&>*]:!m-0 [&>*]:p-0 [&_a]:no-underline'
          >
            
            <div class='search'>
              <button
                type='button'
                class='icon appearance-none border-0 bg-transparent p-2 -m-2 cursor-pointer'
                data-search
                aria-label='Search'
                title='Search'
              >
                <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' class='h-5 w-5'>
                  <circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/>
                  <path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
                </svg>
                <span class='sr-only'>Search</span>
              </button>
            </div>

            <a
              href='<?php echo esc_url( $wishlist ); ?>'
              class='icon'
              data-wishlist
              aria-label='Wishlist'
            >
              Wishlist
            </a>

            <button
              class='relative text-black '
              data-main-cart-button
            >
              <span class='hidden md:block'>
                Cart
              </span>
              <span
                class='absolute -right-2.5 -top-1 flex h-3 w-3 items-center justify-center rounded-full bg-black pt-px text-[8px] font-bold text-cream <?php echo 0 === $cart_count ? 'hidden' : ''; ?>'
                data-cart-count
                aria-hidden='true'
              ><?php echo esc_html( $cart_count ); ?></span>
              <span class='sr-only' data-cart-count-label>Cart (<?php echo esc_html( $cart_count ); ?>)</span>
            </button>

            <?php if ( is_user_logged_in() ) : ?>
              <a
                class='text-black'
                href='<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>'
              >Account</a>
            <?php else : ?>
              <a
                class='text-black'
                href='<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>'
              >Login</a>
            <?php endif; ?>
          </div>

          
          <div
            data-header-box
            data-header-right
            class='mt-[6px] flex items-center gap-2 font-secondary text-sm font-normal not-italic tab_l:!hidden [&>*]:!m-0 [&>*]:p-0'
          >
            
            <div class='search'>
              <button
                type='button'
                class='icon appearance-none border-0 bg-transparent p-2 -m-2 cursor-pointer'
                data-search
                aria-label='Search'
                title='Search'
              >
                <svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' class='h-6 w-6'>
                  <circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/>
                  <path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
                </svg>
                <span class='sr-only'>Search</span>
              </button>
            </div>

            <a
              href='<?php echo esc_url( $wishlist ); ?>'
              class='icon'
              data-wishlist
              aria-label='Wishlist'
              title='Wishlist'
            >
              <svg class='h-6 w-6' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d="M11.226 6.87717L11.961 7.79592C11.981 7.82094 12.019 7.82094 12.039 7.79592L12.774 6.87717C13.4699 6.00733 14.5235 5.50098 15.6374 5.50098C16.6099 5.50098 17.5426 5.88731 18.2302 6.57498L18.3281 6.67285C19.0785 7.42319 19.5 8.44087 19.5 9.50201V9.86553C19.5 10.3899 19.4155 10.9108 19.2496 11.4083L19.0534 11.9971C18.7287 12.9712 18.2342 13.8801 17.5928 14.6818L17.4072 14.9138C16.9289 15.5118 16.3858 16.0549 15.7878 16.5332L15.6832 16.6169C14.7951 17.3274 13.8071 17.9031 12.7511 18.3255C12.2689 18.5184 11.7311 18.5184 11.2489 18.3255C10.1929 17.9031 9.20492 17.3274 8.31681 16.6169L8.21216 16.5332C7.61423 16.0549 7.07112 15.5118 6.59277 14.9138L6.40719 14.6818C5.76579 13.8801 5.27132 12.9712 4.94663 11.9971L4.75036 11.4083C4.58454 10.9108 4.5 10.3899 4.5 9.86553V9.50201C4.5 8.44087 4.92154 7.42319 5.67187 6.67285L5.76975 6.57498C6.45742 5.88731 7.3901 5.50098 8.36261 5.50098C9.47655 5.50098 10.5301 6.00733 11.226 6.87717Z" stroke="currentColor" stroke-width="1.25"/>
              </svg>
            </a>

            <button
              class='relative text-black '
              data-mobile-cart-button
              aria-label='Cart'
            >
              <svg class='h-6 w-6' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d="M6.46397 8.20717L4.55093 19.2072C4.52434 19.3601 4.64203 19.5 4.79723 19.5H19.2028C19.358 19.5 19.4757 19.3601 19.4491 19.2072L17.536 8.20716C17.5152 8.08742 17.4113 8 17.2897 8H6.71027C6.58873 8 6.4848 8.08742 6.46397 8.20717Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
                <path d="M9.5 11C9.5 8.68633 9.5 5.63294 9.5 4.74931C9.5 4.61124 9.61193 4.5 9.75 4.5H14.25C14.3881 4.5 14.5 4.61193 14.5 4.75V11" stroke="currentColor" stroke-width="1.25"/>
              </svg>
              <span
                class='absolute -right-0.5 -top-0.5 flex h-3 w-3 items-center justify-center rounded-full bg-black pt-px text-[8px] font-bold text-cream <?php echo 0 === $cart_count ? 'hidden' : ''; ?>'
                data-cart-count
                aria-hidden='true'
              ><?php echo esc_html( $cart_count ); ?></span>
            </button>
            
            <button
              type='button'
              class='flex h-6 w-6 appearance-none border-0 bg-transparent p-0 cursor-pointer items-center justify-end text-black'
              data-mobile-menu-link
              aria-label='Menu'
              aria-expanded='false'
              title='Menu'
            >
              <span class='mobile-menu-icon mobile-menu-icon--closed'>
                <svg class='h-[16px] w-[16px]' width='14' height='12' viewBox='0 0 14 12' fill='none' xmlns='http://www.w3.org/2000/svg'>
                  <g id="Group 2602">
                    <path id="Vector 301" d="M0 1H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path>
                    <path id="Vector 302" d="M3.5 6H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path>
                    <path id="Vector 304" d="M2 11H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path>
                  </g>
                </svg>
              </span>
              <span class='mobile-menu-icon mobile-menu-icon--open'>
                <svg width='12' height='12' viewBox='0 0 12 12' fill='none' xmlns='http://www.w3.org/2000/svg' class='h-[16px] w-[16px]'>
                  <g id="Group 2602">
                    <path id="Vector 301" d="M1.05078 11L10.9503 1.10051" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"></path>
                    <path id="Vector 304" d="M1.05078 1L10.9503 10.8995" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"></path>
                  </g>
                </svg>
              </span>
            </button>
          </div>
        </div>
      </div>

      
      <div
        class='megamenu-wrapper z-1 relative hidden tab_l:block'
        data-component='megaMenu'
        data-megamenus
      >
        <?php foreach ( $menu as $item ) :
          $label        = isset( $item['label'] ) ? $item['label'] : '';
          $children     = isset( $item['children'] ) ? (array) $item['children'] : array();
          $menu_type    = isset( $item['type'] ) ? $item['type'] : 'megamenu';
          
          if ( empty( $children ) ) {
            continue;
          }
          ?>
          <div
            class='megamenu closed fixed z-[-2] w-full bg-cream transition-all delay-100 duration-[0.6s] ease-in-out'
            data-megamenu
            data-megamenu-type='<?php echo esc_attr( $menu_type ); ?>'
            data-megamenu-menu-point='<?php echo esc_attr( $label ); ?>'
            aria-hidden='true'
          >
            <div class='megamenu__inner limit grid grid-cols-12 gap-[24px] min-h-[350px] pb-6'>
              <?php if ( 'megamenu' === $menu_type ) : ?>
                <?php /* Standard mega menu — static menu + dynamic submenus + image */ ?>
                <div class='megamenu__static-menu col-span-3 flex flex-col pt-4 text-sm text-black'>
                  <?php foreach ( $children as $child ) :
                    $child_label = isset( $child['label'] ) ? $child['label'] : '';
                    $child_url   = isset( $child['url'] ) ? $child['url'] : '#';
                    ?>
                    <div class='megamenu__item pb-2'>
                      <a href='<?php echo esc_url( $child_url ); ?>' class='animation-underline'><?php echo esc_html( $child_label ); ?></a>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class='megamenu__dynamic-wrapper col-span-9 grid grid-cols-12 gap-[24px]' data-dynamic-menu-wrapper>
                  <?php /* Dynamic menus would be populated by adapter data */ ?>
                </div>
              <?php elseif ( 'two_column_megamenu' === $menu_type ) : ?>
                <?php /* Two column mega menu — two columns with images */ ?>
                <div class='col-span-12 grid w-full grid-cols-12 gap-6'>
                  <?php foreach ( array_chunk( $children, ceil( count( $children ) / 2 ) ) as $chunk ) : ?>
                    <div class='col-span-6 grid grid-cols-10'>
                      <div class='megamenu__static-menu col-span-4 flex flex-col pt-4 text-sm text-black'>
                        <?php foreach ( $chunk as $child ) :
                          $child_label = isset( $child['label'] ) ? $child['label'] : '';
                          $child_url   = isset( $child['url'] ) ? $child['url'] : '#';
                          ?>
                          <div class='megamenu__item inline-[14px] pb-2'>
                            <a href='<?php echo esc_url( $child_url ); ?>' class='animation-underline'><?php echo esc_html( $child_label ); ?></a>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <div class='megamenu__single-image col-span-6 overflow-hidden text-black'>
                        <?php /* Image placeholder — would be populated by adapter data */ ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php elseif ( 'rooms_megamenu' === $menu_type ) : ?>
                <?php /* Rooms mega menu — room categories with images */ ?>
                <div class='col-span-12 grid w-full grid-cols-12 gap-6'>
                  <?php foreach ( $children as $child ) :
                    $child_label = isset( $child['label'] ) ? $child['label'] : '';
                    $child_url   = isset( $child['url'] ) ? $child['url'] : '#';
                    ?>
                    <div class='col-span-3'>
                      <div class='megamenu__item pb-2'>
                        <a href='<?php echo esc_url( $child_url ); ?>' class='animation-underline'><?php echo esc_html( $child_label ); ?></a>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </header>
</section>

<?php /* Pre-rendered search overlay — prevents main.js from injecting AETHER fallback markup. */ ?>
<div id="searchOverlay" class="search-overlay" aria-hidden="true">
  <div class="search-overlay-header">
    <span>Search</span>
    <button class="search-overlay-close search-close" aria-label="Close search">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>
  <input type="text"
         class="search-overlay-input search-input"
         placeholder="Search Ferm Living..."
         aria-label="Search Ferm Living"
         autofocus>
  <div class="search-suggestions">
    <p class="search-suggestion-label">Popular Searches</p>
    <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="search-suggestion">Furniture</a>
    <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="search-suggestion">Lighting</a>
    <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="search-suggestion">Accessories</a>
  </div>
</div>

<?php /* Spacer to offset fixed header */ ?>
<div class="header-spacer" aria-hidden="true"></div>