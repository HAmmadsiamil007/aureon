# VINETA FEATURE CAPABILITY MATRIX

**Date:** 2026-09-01
**Source:** Vineta HTML Package

---

## Status Legend

| Status | Meaning |
|--------|---------|
| ✅ SUPPORTED | Feature exists in Vineta and is preserved |
| 🔧 PLATFORM_PROVIDED | Feature provided by WordPress/WooCommerce/AUREON |
| 🔗 BRIDGE_REQUIRED | Feature needs explicit bridge mapping |
| ⚪ OPTIONAL | Feature exists but not critical for MVP |
| ❌ EXCLUDED | Feature intentionally not included |
| 🚫 BLOCKED | Feature cannot work without platform change |

---

## CAPABILITY MATRIX

### Homepage

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Hero slideshow | ✅ SUPPORTED | `tf-slideshow` in all 30 homepages | 30 design variants preserved |
| Product grid | ✅ SUPPORTED | `.card-product` in homepages | Static product cards |
| Featured categories | ✅ SUPPORTED | Category sections in homepages | Static category cards |
| Banner sections | ✅ SUPPORTED | Banner sections in homepages | Static banners |
| Testimonials | ✅ SUPPORTED | Testimonial sections in homepages | Static testimonials |
| Instagram feed | ✅ SUPPORTED | Instagram section in homepages | Static images |
| Newsletter | ✅ SUPPORTED | Newsletter popup/form | Form preserved |
| Blog preview | ✅ SUPPORTED | Blog sections in homepages | Static blog posts |
| Brand logos | ✅ SUPPORTED | Brand carousel in homepages | Static brand logos |
| Countdown timer | ✅ SUPPORTED | Timer sections in homepages | JS countdown preserved |
| Parallax effects | ✅ SUPPORTED | Parallax sections in homepages | JS parallax preserved |
| Customizer | 🔧 PLATFORM_PROVIDED | WordPress Customizer | Logo, site name, announcement, social |
| Dynamic products | 🔧 PLATFORM_PROVIDED | WooCommerce product query | Products from WC |
| Dynamic categories | 🔧 PLATFORM_PROVIDED | WooCommerce category query | Categories from WC |
| Demo content | 🔧 PLATFORM_PROVIDED | AUREON demo system | Demo products/categories |

### Shop

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Product grid | ✅ SUPPORTED | All shop pages | Grid layout preserved |
| Product cards | ✅ SUPPORTED | `.card-product` | Image, title, price, actions |
| Add to cart | ✅ SUPPORTED | `.list-product-btn` | AJAX add-to-cart UI |
| Wishlist button | ✅ SUPPORTED | `.wishlist` in product cards | UI preserved, bridge required |
| Compare button | ✅ SUPPORTED | `.compare` in product cards | UI preserved, bridge required |
| Quick view | ✅ SUPPORTED | `#quickView` modal | Modal preserved |
| Sorting | ✅ SUPPORTED | Sort dropdown in shop pages | JS sorting preserved |
| Price filter | ✅ SUPPORTED | `#price-value-range` | noUiSlider preserved |
| Category filter | ✅ SUPPORTED | Filter sidebar | Checkbox filter preserved |
| Color filter | ✅ SUPPORTED | Filter sidebar | Swatch filter preserved |
| Size filter | ✅ SUPPORTED | Filter sidebar | Button filter preserved |
| Brand filter | ✅ SUPPORTED | Filter sidebar | Checkbox filter preserved |
| Rating filter | ✅ SUPPORTED | Filter sidebar | Star filter preserved |
| Availability filter | ✅ SUPPORTED | Filter sidebar | Stock filter preserved |
| Pagination | ✅ SUPPORTED | Page numbers | Standard pagination preserved |
| Infinite scroll | ✅ SUPPORTED | `shop-infinity-scroll.html` | JS infinite scroll preserved |
| Load more | ✅ SUPPORTED | `shop-load-more-button.html` | JS load more preserved |
| Left sidebar | ✅ SUPPORTED | `shop-left-sidebar.html` | Layout variant preserved |
| Right sidebar | ✅ SUPPORTED | `shop-right-sidebar.html` | Layout variant preserved |
| Full width | ✅ SUPPORTED | `shop-fullwidth.html` | Layout variant preserved |
| Filter drawer | ✅ SUPPORTED | `shop-filter-drawer.html` | Drawer filter preserved |
| Filter hidden | ✅ SUPPORTED | `shop-filter-hidden.html` | Hidden filter preserved |
| Horizontal filter | ✅ SUPPORTED | `shop-horizontal-filter.html` | Horizontal filter preserved |
| 3-column grid | ✅ SUPPORTED | `shop-grid-3-columns.html` | Grid variant preserved |
| Collection list | ✅ SUPPORTED | `shop-collection-list.html` | Category listing preserved |
| Sub-collection | ✅ SUPPORTED | `shop-sub-collection*.html` | Sub-category preserved |
| Dynamic products | 🔧 PLATFORM_PROVIDED | WooCommerce product query | Products from WC |
| Dynamic filters | 🔧 PLATFORM_PROVIDED | WooCommerce attributes | Filter options from WC |
| Dynamic sorting | 🔧 PLATFORM_PROVIDED | WooCommerce sorting | Sort from WC |
| Dynamic pagination | 🔧 PLATFORM_PROVIDED | WooCommerce pagination | Pagination from WC |

### Product Detail

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Image gallery | ✅ SUPPORTED | Product gallery in all variants | Gallery preserved |
| Thumbnails | ✅ SUPPORTED | Bottom/right/stacked thumbnails | 3 positions preserved |
| Image zoom | ✅ SUPPORTED | 5 zoom variants | External, inner, circle, lightbox, none |
| Video | ✅ SUPPORTED | `product-video.html` | Video player preserved |
| 3D model | ✅ SUPPORTED | `product-3d.html` | Google model-viewer preserved |
| Product title | ✅ SUPPORTED | `.product-title` | Static title preserved |
| Price display | ✅ SUPPORTED | `.product-price` | Static price preserved |
| Sale price | ✅ SUPPORTED | Sale badge and price | Sale UI preserved |
| SKU display | ✅ SUPPORTED | `.product-sku` | Static SKU preserved |
| Stock status | ✅ SUPPORTED | `product-out-of-stock.html` | Out-of-stock UI preserved |
| Quantity selector | ✅ SUPPORTED | `.quantity` | Quantity input preserved |
| Add to cart | ✅ SUPPORTED | `.add-to-cart` | Add-to-cart button preserved |
| Variation swatches | ✅ SUPPORTED | 4 swatch variants | Dropdown, color, image, square |
| Description tabs | ✅ SUPPORTED | `product-description-tab.html` | Tabbed description preserved |
| Description accordions | ✅ SUPPORTED | `product-description-accordions.html` | Accordion description preserved |
| Side accordions | ✅ SUPPORTED | `product-description-side-accordions.html` | Side accordion preserved |
| Vertical description | ✅ SUPPORTED | `product-description-vertical.html` | Vertical description preserved |
| Drawer sidebar | ✅ SUPPORTED | `product-drawer-sidebar.html` | Drawer sidebar preserved |
| Related products | ✅ SUPPORTED | Related products section | Static related products |
| Wishlist | ✅ SUPPORTED | Wishlist button | UI preserved, bridge required |
| Compare | ✅ SUPPORTED | Compare button | UI preserved, bridge required |
| Share | ✅ SUPPORTED | Share buttons | Social share preserved |
| Pickup available | ✅ SUPPORTED | `product-pickup-available.html` | Pickup UI preserved |
| Affiliate link | ✅ SUPPORTED | `product-affiliate.html` | External link preserved |
| Product grid layout | ✅ SUPPORTED | `product-grid*.html` | Grid layout preserved |
| Product group | ✅ SUPPORTED | `product-group.html` | Grouped product UI preserved |
| Buy together | ✅ SUPPORTED | `product-together.html` | Bundle UI preserved |
| Volume discount | ✅ SUPPORTED | `product-volume-discount*.html` | Tiered pricing UI preserved |
| Buy X Get Y | ✅ SUPPORTED | `product-buyX-getY.html` | Promotion UI preserved |
| Countdown timer | ✅ SUPPORTED | `product-countdown-timer.html` | Timer UI preserved |
| Style variants | ✅ SUPPORTED | `product-style-01/02/03.html` | 3 style variants preserved |
| Dynamic product data | 🔧 PLATFORM_PROVIDED | WooCommerce product | Product data from WC |
| Dynamic variations | 🔧 PLATFORM_PROVIDED | WooCommerce variations | Variation data from WC |
| Dynamic gallery | 🔧 PLATFORM_PROVIDED | WooCommerce product images | Images from WC |
| Dynamic add to cart | 🔧 PLATFORM_PROVIDED | WooCommerce AJAX cart | Cart from WC |
| Dynamic related | 🔧 PLATFORM_PROVIDED | WooCommerce related products | Related from WC |

### Cart

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Cart page | ✅ SUPPORTED | `view-cart.html` | Cart page preserved |
| Empty cart | ✅ SUPPORTED | `cart-empty.html` | Empty state preserved |
| Cart drawer | ✅ SUPPORTED | `cart-drawer-v2.html` | Slide-out drawer preserved |
| Quantity controls | ✅ SUPPORTED | `.quantity` in cart | +/- buttons preserved |
| Remove item | ✅ SUPPORTED | Delete button in cart | Remove button preserved |
| Subtotal | ✅ SUPPORTED | Cart totals section | Static subtotal preserved |
| Checkout CTA | ✅ SUPPORTED | Checkout button | Button preserved |
| Dynamic cart | 🔧 PLATFORM_PROVIDED | WooCommerce cart | Cart from WC |
| Dynamic totals | 🔧 PLATFORM_PROVIDED | WooCommerce cart totals | Totals from WC |
| Dynamic cart count | 🔧 PLATFORM_PROVIDED | WooCommerce AJAX fragments | Count from WC |

### Checkout

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Checkout form | ✅ SUPPORTED | `checkout.html` | Checkout form preserved |
| Customer info | ✅ SUPPORTED | Form fields | Name, email, phone fields preserved |
| Billing form | ✅ SUPPORTED | Billing section | Billing address fields preserved |
| Shipping form | ✅ SUPPORTED | Shipping section | Shipping address fields preserved |
| Order summary | ✅ SUPPORTED | Order review section | Static order summary preserved |
| Coupon field | ✅ SUPPORTED | Coupon input | Coupon UI preserved |
| Payment surface | ✅ SUPPORTED | Payment section | Payment method selection preserved |
| Form validation | ✅ SUPPORTED | `jquery-validate.js` | Client-side validation preserved |
| WooCommerce checkout | 🔧 PLATFORM_PROVIDED | WooCommerce checkout | Full checkout from WC |

### Account

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Account dashboard | ✅ SUPPORTED | `account-page.html` | Dashboard preserved |
| Account addresses | ✅ SUPPORTED | `account-addresses.html` | Address management preserved |
| Account details | ✅ SUPPORTED | `account-details.html` | Account editing preserved |
| Account orders | ✅ SUPPORTED | `account-orders.html` | Order list preserved |
| Login form | ✅ SUPPORTED | Login modal/section | Login UI preserved |
| Registration form | ✅ SUPPORTED | Registration modal/section | Registration UI preserved |
| Dynamic account | 🔧 PLATFORM_PROVIDED | WooCommerce customer | Customer data from WC |
| Dynamic orders | 🔧 PLATFORM_PROVIDED | WooCommerce orders | Orders from WC |
| Dynamic addresses | 🔧 PLATFORM_PROVIDED | WooCommerce addresses | Addresses from WC |

### Wishlist / Compare

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Wishlist page | ✅ SUPPORTED | `wish-list.html` | Wishlist UI preserved |
| Add to wishlist | ✅ SUPPORTED | Wishlist button in product cards | UI preserved |
| Compare page | ✅ SUPPORTED | `compare.html` | Compare UI preserved |
| Add to compare | ✅ SUPPORTED | Compare button in product cards | UI preserved |
| Wishlist data | 🔗 BRIDGE_REQUIRED | WC wishlist plugin | Needs YITH or similar |
| Compare data | 🔗 BRIDGE_REQUIRED | WC compare plugin | Needs YITH or similar |

### Blog

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Blog grid | ✅ SUPPORTED | `blog-grid-01/02.html` | 2 grid layouts preserved |
| Blog list | ✅ SUPPORTED | `blog-list-01/02.html` | 2 list layouts preserved |
| Single post | ✅ SUPPORTED | `blog-single.html` | Single post layout preserved |
| Dynamic posts | 🔧 PLATFORM_PROVIDED | WordPress WP_Query | Posts from WP |

### Static Pages

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| About us | ✅ SUPPORTED | `about-us.html` | Layout preserved |
| Contact us | ✅ SUPPORTED | `contact-us.html` | Layout + map preserved |
| FAQ | ✅ SUPPORTED | `faq.html` | Accordion FAQ preserved |
| Shipping | ✅ SUPPORTED | `shipping.html` | Layout preserved |
| Returns | ✅ SUPPORTED | `return-and-refund.html` | Layout preserved |
| Privacy | ✅ SUPPORTED | `privacy-policy.html` | Layout preserved |
| Terms | ✅ SUPPORTED | `term-and-condition.html` | Layout preserved |
| Store location | ✅ SUPPORTED | `store-location.html` | Layout + map preserved |
| Cookies | ✅ SUPPORTED | `cookies.html` | Layout preserved |
| Dynamic content | 🔧 PLATFORM_PROVIDED | WordPress pages | Content from WP |

### Navigation

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Header | ✅ SUPPORTED | All pages | Header preserved |
| Mega menu | ✅ SUPPORTED | `mega-home`, `mega-shop` | Mega menu preserved |
| Mobile menu | ✅ SUPPORTED | Mobile nav in all pages | Mobile nav preserved |
| Search | ✅ SUPPORTED | Search modal/overlay | Search UI preserved |
| Cart icon | ✅ SUPPORTED | Header cart icon | Cart icon preserved |
| Wishlist icon | ✅ SUPPORTED | Header wishlist icon | Wishlist icon preserved |
| Compare icon | ✅ SUPPORTED | Header compare icon | Compare icon preserved |
| Account icon | ✅ SUPPORTED | Header account icon | Account icon preserved |
| Breadcrumbs | ✅ SUPPORTED | Breadcrumb in inner pages | Breadcrumb preserved |
| Dynamic menus | 🔧 PLATFORM_PROVIDED | WordPress nav menus | Menus from WP |

### Footer

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Multi-column footer | ✅ SUPPORTED | All pages | Footer preserved |
| Footer links | ✅ SUPPORTED | Footer columns | Static links preserved |
| Newsletter | ✅ SUPPORTED | Footer newsletter form | Form preserved |
| Social links | ✅ SUPPORTED | Footer social icons | Icons preserved |
| Payment icons | ✅ SUPPORTED | Footer payment icons | Icons preserved |
| Copyright | ✅ SUPPORTED | Footer copyright | Text preserved |
| Dynamic footer | 🔧 PLATFORM_PROVIDED | WordPress menus + Customizer | Content from WP |

### Responsive

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Desktop (1440+) | ✅ SUPPORTED | CSS responsive rules | Full layout |
| Tablet (1024) | ✅ SUPPORTED | CSS responsive rules | Adapted layout |
| Mobile (768) | ✅ SUPPORTED | CSS responsive rules | Mobile layout |
| Small mobile (390) | ✅ SUPPORTED | CSS responsive rules | Compact layout |

### Accessibility

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Semantic HTML | ✅ SUPPORTED | Header/nav/main/footer | Semantic elements used |
| Alt text | ⚪ NEEDS REVIEW | Image tags | Many images lack alt text |
| Form labels | ⚪ NEEDS REVIEW | Form elements | Some labels missing |
| Keyboard navigation | ⚪ NEEDS REVIEW | Interactive elements | Needs testing |
| Focus management | ⚪ NEEDS REVIEW | Modals/drawers | Needs testing |
| ARIA attributes | ⚪ NEEDS REVIEW | Interactive elements | Some ARIA present |

### Performance

| Capability | Status | Evidence | Notes |
|------------|--------|----------|-------|
| Lazy loading | ✅ SUPPORTED | `lazysize.min.js` | Image lazy loading preserved |
| Image optimization | ⚪ NEEDS REVIEW | Image sizes | Some images large |
| CSS minification | ⚪ NEEDS REVIEW | CSS files | styles.css not minified |
| JS optimization | ⚪ NEEDS REVIEW | JS files | Some JS could be deferred |
| Preloader | ✅ SUPPORTED | `.preload` element | Loading animation preserved |

---

## SUMMARY

| Category | Supported | Platform Provided | Bridge Required | Optional | Needs Review |
|----------|-----------|-------------------|-----------------|----------|--------------|
| Homepage | 11 | 4 | 0 | 0 | 0 |
| Shop | 27 | 5 | 0 | 0 | 0 |
| Product | 30 | 5 | 0 | 0 | 0 |
| Cart | 6 | 3 | 0 | 0 | 0 |
| Checkout | 6 | 1 | 0 | 0 | 0 |
| Account | 6 | 3 | 0 | 0 | 0 |
| Wishlist/Compare | 4 | 0 | 2 | 0 | 0 |
| Blog | 4 | 1 | 0 | 0 | 0 |
| Static | 9 | 1 | 0 | 0 | 0 |
| Navigation | 9 | 1 | 0 | 0 | 0 |
| Footer | 6 | 1 | 0 | 0 | 0 |
| Responsive | 4 | 0 | 0 | 0 | 0 |
| Accessibility | 1 | 0 | 0 | 0 | 5 |
| Performance | 2 | 0 | 0 | 0 | 3 |
| **TOTAL** | **125** | **25** | **2** | **0** | **8** |

---

## BLOCKED CAPABILITIES: 0

No capabilities are blocked. All Vineta features can be preserved or connected to AUREON.

## BRIDGE-REQUIRED CAPABILITIES: 2

1. **Wishlist** — Requires WooCommerce wishlist plugin integration
2. **Compare** — Requires WooCommerce compare plugin integration
