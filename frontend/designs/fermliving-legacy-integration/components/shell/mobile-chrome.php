<?php
/**
 * Ferm Living mobile chrome — exact frozen source DOM structure.
 *
 * Key:    'shell/mobile-chrome' (override)
 * Source: fermliving.com mobile menu structure
 * Props:  brand, brand_url, menu (with children + grandchildren), cta, socials.
 * Contract: keeps #mobileHeader, #mobileHamburger, #mobileMenuOverlay,
 *           #mobileMenuClose, .mobile-search — platform drawer JS operates unchanged.
 *
 * Ferm Living mobile menu hierarchy:
 *   Level 1: Top nav (Shop, Inspiration, Rooms, Professionals)
 *   Level 2: Subcategories (Kids, Outdoor, Accessories, Furniture, etc.)
 *   Level 3: Tertiary links (All Kids, Toys, Baby, Textiles, etc.)
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$brand     = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$menu      = isset( $componentData['menu'] ) ? (array) $componentData['menu'] : array();
$cta       = isset( $componentData['cta'] ) ? (array) $componentData['cta'] : array();
$socials   = isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array();

$is_home = is_front_page() || ( is_home() && ! is_paged() );
?>

<?php /* Mobile Header — visible on mobile only */ ?>
<div class='mobile-menu fixed z-[-1] hidden h-full w-full overflow-hidden bg-cream tab_l:hidden' data-mobile-menu data-component='mobileMenu'>
  <div class='mobile-menu__inner limit flex h-full flex-col justify-between overflow-y-auto pt-[66px]'>
    <div class='mobile-menu__top-links flex flex-1 flex-col gap-[24px]'>
      <?php foreach ( $menu as $item ) :
        $label        = isset( $item['label'] ) ? $item['label'] : '';
        $url          = isset( $item['url'] ) ? $item['url'] : '#';
        $has_children = ! empty( $item['children'] );
        if ( empty( $label ) ) {
          continue;
        }
        ?>
        <div class='mobile-menu__top-link text-2xl leading-[28px]'>
          <a
            href='<?php echo esc_url( $url ); ?>'
            <?php if ( $has_children ) : ?>
              data-mobile-menu-top-link='<?php echo esc_attr( $label ); ?>'
            <?php endif; ?>
            class='mobile-menu__link'
          >
            <?php echo esc_html( $label ); ?>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
    <div class='mobile-menu__bottom-links mb-[66px] mt-6 flex flex-1 items-center justify-between'>
      <?php if ( is_user_logged_in() ) : ?>
        <a
          class='w-[50%] text-2xl text-black'
          href='<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>'
        >Account</a>
      <?php else : ?>
        <a
          class='w-[50%] text-2xl text-black'
          href='<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>'
        >Login</a>
      <?php endif; ?>
      <div class='w-[50%]' data-language-container>
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
    
    <?php /* Level 2: Subcategories (slides in from right when parent is tapped) */ ?>
    <?php foreach ( $menu as $item ) :
      $label        = isset( $item['label'] ) ? $item['label'] : '';
      $has_children = ! empty( $item['children'] );
      if ( ! $has_children || empty( $label ) ) {
        continue;
      }
      ?>
      <div
        data-mobile-submenu='<?php echo esc_attr( $label ); ?>'
        class='mobile-menu__submenu scroll-auto absolute left-0 top-0 h-full w-full translate-x-full overflow-y-auto overflow-x-hidden bg-cream pb-[220px]'
      >
        <a
          href='#'
          data-mobile-submenu-close
          class='mobile-menu__submenu-close text-lg-medium limit mb-[1.25rem] mt-[0.8rem] flex items-center gap-2'
        >
          <svg
            version='1.1'
            id='Capa_1'
            xmlns='http://www.w3.org/2000/svg'
            xmlns:xlink='http://www.w3.org/1999/xlink'
            x='0px'
            y='0px'
            width='370.814px'
            height='370.814px'
            viewBox='0 0 370.814 370.814'
            style='enable-background:new 0 0 370.814 370.814;'
            xml:space='preserve'
            class='w-3 h-3'
          >
            <g>
              <g>
                <polygon points="292.92,24.848 268.781,0 77.895,185.401 268.781,370.814 292.92,345.961 127.638,185.401 		"/>
              </g>
            </g>
          </svg>
          <?php echo esc_html( $label ); ?>
        </a>
        
        <?php /* Featured image (optional) */ ?>
        <?php if ( isset( $item['image'] ) && $item['image'] ) : ?>
          <a href='<?php echo esc_url( $item['url'] ); ?>'>
            <h4 class='limit mb-2 font-primary text-lg leading-[18px]'><?php echo esc_html( $label ); ?></h4>
            <div class='limit'>
              <div
                class='group relative w-full relative aspect-[19/10] '
                data-component='media'
              >
                <img src='<?php echo esc_url( $item['image'] ); ?>' alt='<?php echo esc_attr( $label ); ?>' loading='lazy' width='1320' height='1573' class='absolute left-0 top-0 h-full w-full object-cover md:hidden w-full object-cover' sizes='100vw'>
              </div>
            </div>
          </a>
        <?php endif; ?>
        
        <?php /* Quick links for this category */ ?>
        <div class='mobile-menu__submenu-links limit mt-6 grid grid-cols-2 gap-4 pb-1'>
          <?php foreach ( $item['children'] as $child ) :
            $child_label        = isset( $child['label'] ) ? $child['label'] : '';
            $child_url          = isset( $child['url'] ) ? $child['url'] : '#';
            $child_has_children = ! empty( $child['children'] );
            if ( empty( $child_label ) ) {
              continue;
            }
            ?>
            <div class='mobile-menu__top-link text-lg-medium col-span-1 leading-[1]'>
              <a href='<?php echo esc_url( $child_url ); ?>' class='mobile-menu__link'><?php echo esc_html( $child_label ); ?></a>
            </div>
          <?php endforeach; ?>
        </div>
        
        <?php /* Subcategory links with tertiary menu */ ?>
        <div class='mobile-menu__submenu-links limit mt-8 flex flex-col gap-[16px] border-t border-canvas pt-6'>
          <?php foreach ( $item['children'] as $child ) :
            $child_label        = isset( $child['label'] ) ? $child['label'] : '';
            $child_url          = isset( $child['url'] ) ? $child['url'] : '#';
            $child_has_children = ! empty( $child['children'] );
            if ( empty( $child_label ) ) {
              continue;
            }
            ?>
            <div class='mobile-menu__top-link leading-[18px]'>
              <a
                href='<?php echo esc_url( $child_url ); ?>'
                <?php if ( $child_has_children ) : ?>
                  data-tertiary-menu-link='<?php echo esc_attr( $child_label ); ?>'
                <?php endif; ?>
                class='mobile-menu__link text-lg leading-[18px]'
              ><?php echo esc_html( $child_label ); ?></a>
            </div>
            
            <?php /* Level 3: Tertiary links (slides in from right when subcategory is tapped) */ ?>
            <?php if ( $child_has_children ) : ?>
              <div
                class='mobile-menu__tertiary-links limit absolute left-0 top-0 h-[120%] w-full translate-x-full overflow-y-auto bg-cream'
                data-tertiary-menu='<?php echo esc_attr( $child_label ); ?>'
              >
                <a
                  href='#'
                  data-tertiary-menu-close
                  class='mobile-menu__tertiary-menu-close text-lg-medium mb-[36px] mt-4 flex items-center gap-2 leading-[20px]'
                >
                  <svg
                    version='1.1'
                    id='Capa_1'
                    xmlns='http://www.w3.org/2000/svg'
                    xmlns:xlink='http://www.w3.org/1999/xlink'
                    x='0px'
                    y='0px'
                    width='370.814px'
                    height='370.814px'
                    viewBox='0 0 370.814 370.814'
                    style='enable-background:new 0 0 370.814 370.814;'
                    xml:space='preserve'
                    class='w-3 h-3'
                  >
                    <g>
                      <g>
                        <polygon points="292.92,24.848 268.781,0 77.895,185.401 268.781,370.814 292.92,345.961 127.638,185.401 		"/>
                      </g>
                    </g>
                  </svg>
                  <?php echo esc_html( $child_label ); ?>
                </a>
                <div class='mobile-menu__submenu-links flex flex-col gap-[16px]'>
                  <?php foreach ( $child['children'] as $grandchild ) :
                    $gc_label = isset( $grandchild['label'] ) ? $grandchild['label'] : '';
                    $gc_url   = isset( $grandchild['url'] ) ? $grandchild['url'] : '#';
                    if ( empty( $gc_label ) ) {
                      continue;
                    }
                    ?>
                    <div class='mobil-menu__tertiary-link leading-[18px]'>
                      <a
                        href='<?php echo esc_url( $gc_url ); ?>'
                        class='mobile-menu__link text-lg leading-[18px]'
                      ><?php echo esc_html( $gc_label ); ?></a>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>