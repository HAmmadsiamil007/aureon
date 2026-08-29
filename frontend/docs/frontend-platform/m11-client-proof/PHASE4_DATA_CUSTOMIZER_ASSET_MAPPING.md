# PHASE 4-6 — DATA / CUSTOMIZER / ASSET MAPPING

**Date:** 2026-08-21
**Status:** Complete

---

## PHASE 4 — DATA CONTRACT MAPPING

### 1. Data Flow Architecture

```
WordPress/WooCommerce Content
        ↓
AETHER Adapters (only layer touching WP APIs)
        ↓
ViewModels (normalized $componentData arrays)
        ↓
Renderer (aether_render_component / aether_render_section)
        ↓
Component Templates (PHP partials, no WP calls)
        ↓
HTML Output (escaped at render boundary)
```

### 2. Source → WordPress Data Mapping

| Ferm Source Data | WordPress Source | Adapter | ViewModel Key |
|-----------------|-----------------|---------|---------------|
| Product title | `WC_Product::get_name()` | `adapter-wc-products` | `name` |
| Product price | `WC_Product::get_price_html()` | `adapter-wc-products` | `price` |
| Product image | `wp_get_attachment_image_url()` | `adapter-wc-products` | `image` |
| Product URL | `get_permalink()` | `adapter-wc-products` | `url` |
| Product badge | Custom meta or tag | `adapter-wc-products` | `badge` |
| Color swatches | Product variations | `adapter-wc-products` | `swatches[]` |
| Category name | `WC_Product_Category::get_name()` | `adapter-wc-categories` | `name` |
| Category image | `WP_Term::thumbnail_id` | `adapter-wc-categories` | `image` |
| Category count | `WP_Term::count` | `adapter-wc-categories` | `count` |
| Blog title | `get_the_title()` | `adapter-blog` | `title` |
| Blog excerpt | `get_the_excerpt()` | `adapter-blog` | `excerpt` |
| Blog image | `get_the_post_thumbnail_url()` | `adapter-blog` | `image` |
| Page content | `get_the_content()` | `adapter-page` | `content` |
| Menu items | `wp_nav_menu_items` | `adapter-menu` | `menu[]` |
| Cart items | `WC()->cart->get_cart()` | `adapter-cart` | `items[]` |
| Cart total | `WC()->cart->get_cart_contents_total()` | `adapter-cart` | `total` |

### 3. Ferm-Specific Data Extensions

| Ferm Feature | Data Source | Adapter Extension |
|-------------|-------------|-------------------|
| Product color swatches | WC variations with `attribute_pa_color` | Add `swatches[]` to product adapter output |
| "New" badge | WC product tag `new` | Add badge detection to product adapter |
| "Certified" badge | WC product tag `certified` | Add badge detection to product adapter |
| Product "out of stock strategy" | WC stock status | Add stock strategy to product adapter |
| USP announcement items | Customizer repeater | Use existing `aether_announcement_items` option |
| Room/category landing images | WP page featured image + custom field | Use existing page adapter |
| Footer USP row | Customizer repeater | New option: `ferm_footer_usp_items` |

---

## PHASE 5 — CUSTOMIZER MAPPING

### 1. Complete Customizer Matrix

| Setting | Option Key | Default | Token | CSS Variable | Component | Responsive |
|---------|-----------|---------|-------|-------------|-----------|-----------|
| **Colors** ||||||
| Background | `aether_color_bg` | `#FFFEFA` | `--color-cream` | `var(--aureon-color-bg)` | global | No |
| Surface | `aether_color_surface` | `#FFFFFF` | `--color-white` | `var(--aureon-color-surface)` | global | No |
| Surface alt | `aether_color_surface_2` | `#F7F5EF` | `--color-canvas` | `var(--aureon-color-surface-2)` | global | No |
| Surface 3 | `aether_color_surface_3` | `#E3DAD1` | — | `var(--aureon-color-surface-3)` | global | No |
| Text | `aether_color_text` | `#383838` | `--color-black` | `var(--aureon-color-text)` | global | No |
| Muted | `aether_color_muted` | `#666666` | `--color-label` | `var(--aureon-color-muted)` | global | No |
| Accent | `aether_color_accent` | `#587664` | `--color-green` | `var(--aureon-color-accent)` | global | No |
| Accent hover | `aether_color_accent_hover` | `#48604F` | — | `var(--aureon-color-accent-hover)` | global | No |
| Border | `aether_color_border` | `#DCD3CB` | `--color-light-beige` | `var(--aureon-color-border)` | global | No |
| Error | `aether_color_error` | `#CC0000` | — | `var(--aureon-color-error)` | global | No |
| Success | `aether_color_success` | `#587664` | — | `var(--aureon-color-success)` | global | No |
| **Typography** ||||||
| Heading font | `aether_font_heading` | `CanelaText, Georgia, serif` | — | `var(--aureon-font-heading)` | global | No |
| Body font | `aether_font_body` | `KHTeka, system-ui, sans-serif` | — | `var(--aureon-font-body)` | global | No |
| **Layout** ||||||
| Container max | `aether_container_max` | `1920px` | `--site-max-width` | `var(--aureon-container-max)` | `.limit` | No |
| Grid gap | `aether_grid_gap` | `24px` | `--gutter-md` | `var(--aureon-grid-gap)` | grid | Yes |
| Section padding | `aether_section_padding` | `80px 0` | — | `var(--aureon-section-padding)` | sections | Yes |
| Header height | `aether_header_height` | `auto` | — | `var(--aureon-header-height)` | header | Yes |
| **Shell** ||||||
| Announcement enabled | `aether_announcement_enabled` | `true` | — | — | `shell/announcement` | No |
| Announcement items | `aether_announcement_items` | 4 USP items | — | — | `shell/announcement` | No |
| Preloader enabled | `aether_preloader_enabled` | `false` | — | — | `shell/preloader` | No |
| Fog enabled | `aether_fog_enabled` | `false` | — | — | `shell/fog` | No |
| **Homepage** ||||||
| Hero section visible | `aether_section_hero` | `true` | — | — | homepage sections | No |
| Categories visible | `aether_section_categories` | `true` | — | — | homepage sections | No |
| Bestsellers visible | `aether_section_bestsellers` | `true` | — | — | homepage sections | No |
| Newsletter visible | `aether_section_newsletter` | `true` | — | — | `section/newsletter` | No |
| **Footer** ||||||
| Footer columns | `aether_footer_columns` | 3 columns (Customer Service, Information, Professionals) | — | — | `shell/footer` | No |
| Newsletter text | `aether_newsletter_text` | `Stay updated` | — | — | `form/newsletter` | No |
| Newsletter subtitle | `aether_newsletter_subtitle` | `Sign up and get 10% off` | — | — | `form/newsletter` | No |
| **Motion** ||||||
| Motion enabled | `aether_motion_enabled` | `true` | — | — | global | No |
| Reduced motion | `aether_motion_reveal` | `true` | — | — | global | No |

### 2. Ferm-Specific Customizer Additions

| New Option Key | Type | Default | Purpose |
|---------------|------|---------|---------|
| `ferm_footer_usp_items` | Repeater | 4 USP items | Footer USP row (free shipping, sign up, help, fast delivery) |
| `ferm_announcement_speed` | Number | `4000` | USP rotation speed (ms) |
| `ferm_product_card_style` | Select | `carousel` | Product card image style: `carousel` or `static` |

### 3. Token → CSS Variable Mapping

```css
:root {
  /* Ferm Living Design Pack Tokens */
  --aureon-color-bg: #FFFEFA;
  --aureon-color-surface: #FFFFFF;
  --aureon-color-surface-2: #F7F5EF;
  --aureon-color-surface-3: #E3DAD1;
  --aureon-color-text: #383838;
  --aureon-color-muted: #666666;
  --aureon-color-accent: #587664;
  --aureon-color-accent-hover: #48604F;
  --aureon-color-border: #DCD3CB;
  --aureon-color-error: #CC0000;
  --aureon-color-success: #587664;
  --aureon-font-heading: 'CanelaText', Georgia, serif;
  --aureon-font-body: 'KHTeka', system-ui, sans-serif;
  --aureon-container-max: 1920px;
  --aureon-grid-gap: 24px;
  --aureon-section-padding: 80px 0;
}
```

---

## PHASE 6 — ASSET MANIFEST

### 1. Assets to Ship in Design Pack

```
frontend/designs/fermliving/
├── assets/
│   ├── fonts/
│   │   ├── CanelaText-Regular.woff2        (43KB) — heading
│   │   ├── KHTeka-Regular.woff2            (48KB) — body
│   │   ├── KHTeka-RegularItalic.woff2      (51KB) — body italic
│   │   ├── KHTeka-Medium.woff2             (48KB) — body medium
│   │   └── KHTeka-MediumItalic.woff2       (51KB) — body medium italic
│   ├── images/
│   │   ├── favicon.svg                     (1.3KB)
│   │   ├── favicon.ico                     (15KB)
│   │   ├── favicon-96x96.png               (2KB)
│   │   ├── apple-touch-icon.png            (2.2KB)
│   │   └── product-placeholder.webp        (5KB)
│   └── css/
│       └── (compiled from tokens — generated at build time)
```

**Total shipped assets:** ~290KB (fonts + favicons + placeholder)
**NOT shipped:** 7.38GB of crawled assets (product images served from WP media library)

### 2. Asset Classification

| Category | Files | Action |
|----------|-------|--------|
| **Ship (fonts)** | 5 WOFF2 files | Bundle in design pack |
| **Ship (favicons)** | 4 files | Bundle in design pack |
| **Ship (placeholder)** | 1 WebP | Bundle in design pack |
| **Reference only (CSS)** | `app.adf0bc36b7.css` | Use for token extraction, not shipped |
| **Reference only (JS)** | `app.1e7cf79a09.js`, `product.fa97565a5f.js` | Use for behavior analysis, not shipped |
| **Dynamic (product images)** | ~30,000+ files | Served from WP media library |
| **Exclude (Shopify CDN)** | `_cdn.shopify.com/**` | Shopify infrastructure |
| **Exclude (third-party)** | `_cdn.ablyft.com/**`, `_cdn.506.io/**`, etc. | Analytics, tracking, A/B testing |
| **Exclude (configurator)** | `_cdn.assets.struct.com/**` | Third-party app assets |
| **Exclude (apps)** | `_connect.getflowbox.com/**`, `_static.klaviyo.com/**` | Third-party app assets |

### 3. Font Loading Strategy

```php
// In fonts.php or functions.php of design pack:
wp_enqueue_style(
    'ferm-fonts',
    aether_active_design_dir() . 'assets/css/fonts.css',
    array(),
    '1.0.0'
);
```

Font CSS (`fonts.css`):
```css
@font-face {
  font-family: 'CanelaText';
  src: url('../assets/fonts/CanelaText-Regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'KHTeka';
  src: url('../assets/fonts/KHTeka-Regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'KHTeka';
  src: url('../assets/fonts/KHTeka-RegularItalic.woff2') format('woff2');
  font-weight: 400;
  font-style: italic;
  font-display: swap;
}
@font-face {
  font-family: 'KHTeka';
  src: url('../assets/fonts/KHTeka-Medium.woff2') format('woff2');
  font-weight: 500;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'KHTeka';
  src: url('../assets/fonts/KHTeka-MediumItalic.woff2') format('woff2');
  font-weight: 500;
  font-style: italic;
  font-display: swap;
}
```

---

## 4. Next Phase

→ [PHASE7_DESIGN_PACK.md](./PHASE7_DESIGN_PACK.md)
