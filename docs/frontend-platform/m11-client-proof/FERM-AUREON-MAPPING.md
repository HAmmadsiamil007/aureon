# FERM LIVING ↔ AUREON MAPPING SPECIFICATION

**Version:** 1.0  
**Date:** August 25, 2026  
**Purpose:** Complete mapping between Ferm Living frontend and AUREON/AETHER architecture

---

## 1. ARCHITECTURE OVERVIEW

### 1.1 Current Problem
```
CURRENT (BROKEN)
WordPress/WooCommerce
        ↓
AETHER adapters
        ↓
AETHER ViewModels
        ↓
AETHER-oriented composition
        ↓
Ferm CSS/components (patched)
```

**Result:** Ferm-colored but AETHER-shaped frontend

### 1.2 Correct Architecture
```
CORRECT
WordPress/WooCommerce/Reference Data
        ↓
Canonical Content Model
        ↓
Ferm Design Mapper
        ↓
Ferm Presentation Model
        ↓
Ferm Composer
        ↓
Ferm Sections/Components
        ↓
Ferm UI/UX
```

---

## 2. INTEGRATION POINTS

### 2.1 Design Pack Resolution
**File:** `frontend/views/design.php`

**Mechanism:**
```php
aether_resolve_design_path('sections/section-hero.php')
  → Check: frontend/designs/fermliving/sections/section-hero.php
  → If exists: return pack path
  → Otherwise: return base engine path
```

**Implication:** Pack files shadow base files without modification

### 2.2 Token Defaults
**File:** `frontend/designs/fermliving/tokens.php`

**Hook:** `aureon_option_defaults` filter (priority 20)

**Mechanism:**
```php
add_filter('aureon_option_defaults', function($defaults) {
    $defaults['aether_logo_svg'] = '...ferm living SVG...';
    $defaults['aether_announcement_items'] = [...];
    // ... more overrides
    return $defaults;
});
```

### 2.3 Homepage Composition
**File:** `frontend/designs/fermliving/composer.php`

**Hook:** `aether_frontpage_sections` filter

**Mechanism:**
```php
add_filter('aether_frontpage_sections', function($sections) {
    return [
        'hero',
        'categories',
        'editorial-split',
        'bestsellers',
        'room-grid',
        'newsletter',
    ];
});
```

### 2.4 Section Registration
**File:** `frontend/designs/fermliving/sections/*.php`

**Mechanism:**
```php
aether_register_section('ferm-editorial-split', [
    'template' => 'sections/section-editorial-split.php',
    'adapter'  => null, // uses token data
    'behavior' => [],
]);
```

### 2.5 Component Shadowing
**Mechanism:**
- Pack component at `components/cards/product.php` shadows base `components/cards/product.php`
- No manifest change required
- Pack file must accept same data shape as base

### 2.6 Adapter Data Filters
**File:** `frontend/designs/fermliving/composer.php`

**Available Filters:**
```php
aether_adapter_site_data
aether_adapter_header_data
aether_adapter_footer_data
aether_adapter_hero_data
aether_adapter_product_data
aether_adapter_wc_products_data
aether_adapter_wc_categories_data
aether_adapter_wc_filter_data
aether_adapter_blog_data
aether_adapter_about_data
aether_adapter_contact_data
aether_adapter_cart_data
aether_adapter_account_data
aether_adapter_search_data
```

---

## 3. DATA MAPPING

### 3.1 Product Data

| Ferm Field | AETHER Adapter Field | Source | Remap Required |
|------------|---------------------|--------|----------------|
| `name` | `name` | WC Product | No |
| `price` | `price` | WC Product | No |
| `image` | `image` | WC Product | No |
| `images` | `gallery` | WC Product | No |
| `url` | `url` | WC Product | No |
| `id` | `id` | WC Product | No |
| `colors` | `colors` | WC Product (meta) | **Yes** |
| `badges` | `badge` | Computed | **Yes** |
| `certified` | `tagline` | WC Product (meta) | **Yes** |
| `in_stock` | `in_stock` | WC Stock | No |

**Badge Logic (Ferm-specific):**
```php
// Current AETHER logic (wrong for Ferm):
$badge = $sale ? 'Sale' : ($new ? 'New' : ($featured ? 'Featured' : ''));

// Correct Ferm logic:
$badge = [];
if ($sale) $badge[] = 'Sale';
if ($certified) $badge[] = 'Certified';
if ($new) $badge[] = 'New';
```

### 3.2 Category Data

| Ferm Field | AETHER Adapter Field | Source | Remap Required |
|------------|---------------------|--------|----------------|
| `name` | `name` | WC Term | No |
| `url` | `url` | WC Term | No |
| `image` | `image` | WC Term (meta) | No |
| `count` | `count` | WC Term | No |
| `modifier` | `modifier` | Hardcoded | **Yes** |

### 3.3 Navigation Data

| Ferm Field | AETHER Adapter Field | Source | Remap Required |
|------------|---------------------|--------|----------------|
| `main_nav` | `items` | WP Menu | **Yes** (structure) |
| `mega_menu` | `children` | WP Menu | **Yes** (structure) |
| `quick_links` | N/A | Token | **Yes** (new) |
| `featured_image` | N/A | Token | **Yes** (new) |

### 3.4 Footer Data

| Ferm Field | AETHER Adapter Field | Source | Remap Required |
|------------|---------------------|--------|----------------|
| `usps` | `usp_items` | Token | **Yes** |
| `newsletter` | `newsletter.*` | Token | **Yes** |
| `columns` | `columns` | Token | **Yes** |
| `legal` | `legal` | Token | **Yes** |
| `company` | `company` | Token | **Yes** |
| `payment_icons` | N/A | Hardcoded | **Yes** |

### 3.5 Hero Data

| Ferm Field | AETHER Adapter Field | Source | Remap Required |
|------------|---------------------|--------|----------------|
| `slides` | `slides` | Token | **Yes** |
| `layout` | N/A | Hardcoded | **Yes** (2-up panels) |

---

## 4. TEMPLATE MAPPING

### 4.1 Base Templates → Ferm Overrides

| Base Template | Ferm Override | Status |
|---------------|---------------|--------|
| `sections/section-hero.php` | `sections/section-hero.php` | ✅ Override |
| `sections/section-bestsellers.php` | `sections/section-bestsellers.php` | ✅ Override |
| `sections/section-categories.php` | `sections/section-categories.php` | ✅ Override |
| `components/shell/header.php` | `components/shell/header.php` | ✅ Override |
| `components/shell/footer.php` | `components/shell/footer.php` | ✅ Override |
| `components/shell/mobile-chrome.php` | `components/shell/mobile-chrome.php` | ✅ Override |
| `components/cards/product.php` | `components/cards/product.php` | ✅ Override |
| `components/cards/category.php` | `components/cards/category.php` | ✅ Override |
| `components/product/gallery.php` | `components/product/gallery.php` | ✅ Override |
| `components/product/info.php` | `components/product/info.php` | ✅ Override |

### 4.2 New Ferm Sections

| Section | Purpose | Data Source |
|---------|---------|-------------|
| `section-editorial-split` | Text+image editorial bands | Token content |
| `section-room-grid` | Room-based shopping grid | Token `aether_room_items` |
| `section-secondary-products` | Secondary product row | WC Products |

### 4.3 Templates That Should NOT Be Overridden

| Template | Reason |
|----------|--------|
| `sections/section-cart.php` | Canonical WC cart logic |
| `sections/section-checkout.php` | Canonical WC checkout |
| `sections/section-order-confirmation.php` | Canonical WC order |
| `sections/section-auth.php` | Canonical WP auth |
| `components/product/breadcrumb.php` | Generic navigation |
| `components/cart/*.php` | Canonical WC cart |
| `components/checkout/*.php` | Canonical WC checkout |
| `components/account/*.php` | Canonical WC account |

---

## 5. TOKEN MAPPING

### 5.1 Visual Tokens

| AETHER Token | Ferm Value | CSS Variable |
|--------------|------------|--------------|
| `aether_color_primary` | `#383838` | `--color-primary` |
| `aether_color_secondary` | `#fffefa` | `--color-secondary` |
| `aether_color_accent` | `#383838` | `--color-accent` |
| `aether_color_background` | `#fffefa` | `--color-background` |
| `aether_color_text` | `#383838` | `--color-text` |
| `aether_font_heading` | `'Canela', serif` | `--font-heading` |
| `aether_font_body` | `'Teka', sans-serif` | `--font-body` |
| `aether_container_max` | `1920px` | `--container-max` |
| `aether_gutter` | `24px` | `--gutter` |
| `aether_section_spacing` | `100px` | `--section-spacing` |

### 5.2 Content Tokens

| Token | Ferm Value |
|-------|------------|
| `aether_logo_svg` | "ferm living" text SVG |
| `aether_announcement_items` | 4 USP items |
| `aether_hero_slides` | 2 collection slides |
| `aether_category_items` | 7 categories |
| `aether_product_items` | 8 demo products |
| `aether_room_items` | 6 room cards |
| `aether_newsletter_heading` | "Ferm Living news" |
| `aether_footer_columns` | 3 link columns |
| `aether_footer_usp_items` | 4 USP items |
| `aether_footer_company` | "Ferm Living ApS CVR..." |
| `aether_search_placeholder` | "Search Ferm Living..." |

---

## 6. COMPONENT MAPPING

### 6.1 Shell Components

| Component | Ferm Behavior | Data Source |
|-----------|---------------|-------------|
| `shell/announcement` | Rotating USP carousel | Token |
| `shell/header` | Logo left, nav center, icons right | `adapter-site` |
| `shell/mobile-chrome` | Slide-in menu, 3-level accordion | `adapter-site` |
| `shell/footer` | USP row, newsletter, 3 columns, legal | `adapter-footer` |
| `shell/preloader` | Ferm-specific animation | Token |

### 6.2 Card Components

| Component | Ferm Behavior | Data Source |
|-----------|---------------|-------------|
| `cards/product` | Image carousel, badges, heart, swatches, +Add | `adapter-wc-products` |
| `cards/category` | Overlay image card | `adapter-wc-categories` |
| `cards/blog` | Standard blog card | `adapter-blog` |

### 6.3 Product Components

| Component | Ferm Behavior | Data Source |
|-----------|---------------|-------------|
| `product/gallery` | Embla carousel, dots, prev/next | `adapter-product` |
| `product/info` | Title, price, swatches, qty, CTA | `adapter-product` |
| `product/sticky-bar` | Mobile sticky ATC | `adapter-product` |

### 6.4 Section Components

| Component | Ferm Behavior | Data Source |
|-----------|---------------|-------------|
| `section/accordion` | Expandable content | Token |
| `section/filter-bar` | Category filter buttons | `adapter-wc-filter` |
| `section/pagination` | Standard pagination | `adapter-wc-products` |

---

## 7. ADAPTER OVERRIDES

### 7.1 Available Adapter Filters

```php
// Site-wide
aether_adapter_site_data

// Header/Footer
aether_adapter_header_data
aether_adapter_footer_data

// Homepage
aether_adapter_hero_data
aether_adapter_wc_products_data
aether_adapter_wc_categories_data

// Shop
aether_adapter_shop_hero_data
aether_adapter_wc_filter_data

// Product
aether_adapter_product_data

// Content
aether_adapter_blog_data
aether_adapter_about_data
aether_adapter_contact_data

// Commerce
aether_adapter_cart_data
aether_adapter_account_data
aether_adapter_search_data
```

### 7.2 Filter Implementation Pattern

```php
// In composer.php or dedicated filter file
add_filter('aether_adapter_footer_data', function($data) {
    return [
        'usps' => [...],
        'newsletter' => [...],
        'columns' => [...],
        'legal' => [...],
        'company' => '...',
    ];
});
```

---

## 8. REFERENCE DATA ARCHITECTURE

### 8.1 Two-Mode System

```
REAL CLIENT MODE
WordPress/WooCommerce
        ↓
Canonical Adapters
        ↓
Ferm Presentation

REFERENCE MODE
Ferm Pack Reference Dataset
        ↓
Canonical Adapters (same)
        ↓
Ferm Presentation (same)
```

### 8.2 Reference Data Storage

```
frontend/designs/fermliving/
├── data/
│   ├── products.json      # 25+ demo products
│   ├── categories.json    # 7 categories
│   ├── pages.json         # Static page content
│   ├── navigation.json    # Menu structure
│   └── footer.json        # Footer content
```

### 8.3 Reference Data Activation

```php
// In composer.php
add_filter('aether_demo_content', function($flag) {
    return true; // Enable reference mode
});

// Adapters check this flag
if (aether_get_option('aether_demo_content')) {
    // Return reference data
} else {
    // Return real WC data
}
```

---

## 9. CSS/JS ISOLATION

### 9.1 CSS Scoping

**All Ferm CSS must be scoped to `design-fermliving` body class:**

```css
.design-fermliving .header { ... }
.design-fermliving .product-card { ... }
.design-fermliving .btn-primary { ... }
```

### 9.2 JS Isolation

**Ferm JS must not conflict with AETHER JS:**

```js
// Use IIFE or namespace
(function() {
    const FermLiving = { ... };
    window.FermLiving = Ferming;
})();
```

### 9.3 Asset Loading

**Via manifest.json:**
```json
{
    "assets": {
        "css": ["css/ferm.css", "css/fonts.css"],
        "js": ["js/ferm.js"]
    }
}
```

**Loading order:**
1. AETHER platform CSS
2. Ferm CSS
3. AETHER platform JS
4. Ferm JS

---

## 10. LUXURY ISOLATION

### 10.1 What Stays in AUREON

| Component | Reason |
|-----------|--------|
| Core renderer | Platform infrastructure |
| Adapter pattern | Data access abstraction |
| Component registry | Template resolution |
| Token system | Design token management |
| Asset pipeline | CSS/JS loading |
| WooCommerce integration | Business logic |
| Security | Nonces, escaping, capabilities |

### 10.2 What Moves to Ferm Pack

| Component | Reason |
|-----------|--------|
| Shell components | Brand identity |
| Card components | Visual presentation |
| Product components | Product display |
| Section templates | Page composition |
| Token values | Brand colors/fonts |
| Reference data | Demo content |
| CSS styles | Visual design |
| JS interactions | UI behavior |

### 10.3 What Must Never Be Touched

| Component | Reason |
|-----------|--------|
| WooCommerce core | Business logic |
| Cart/Checkout | Security critical |
| User authentication | Security critical |
| Payment processing | Security critical |
| Database schema | Data integrity |
| REST API | External integrations |

---

## 11. IMPLEMENTATION PLAN

### Phase 0: Freeze
- [ ] Create Git tag for current state
- [ ] Backup current Ferm pack as `fermliving-legacy`
- [ ] Document current integration points

### Phase 1: Standalone Ferm UI
- [ ] Build Ferm components from frozen reference
- [ ] Test components in isolation
- [ ] Verify visual parity against reference

### Phase 2: Reference Data
- [ ] Create `data/products.json` (25+ products)
- [ ] Create `data/categories.json` (7 categories)
- [ ] Create `data/pages.json` (5+ pages)
- [ ] Create `data/navigation.json`
- [ ] Create `data/footer.json`

### Phase 3: Dynamic Mapping
- [ ] Implement adapter filters
- [ ] Connect reference data to adapters
- [ ] Test data flow end-to-end

### Phase 4: WordPress Connection
- [ ] Map WP menus to Ferm navigation
- [ ] Map WP pages to Ferm pages
- [ ] Map WP posts to Ferm blog
- [ ] Test with real WP data

### Phase 5: WooCommerce Connection
- [ ] Map WC products to Ferm cards
- [ ] Map WC categories to Ferm categories
- [ ] Map WC cart to Ferm cart
- [ ] Test with real WC data

### Phase 6: Responsive
- [ ] Test mobile (< 768px)
- [ ] Test tablet (768px-1023px)
- [ ] Test desktop (1024px+)
- [ ] Test large desktop (1440px+)

### Phase 7: Interactions
- [ ] Test mega menu
- [ ] Test mobile menu
- [ ] Test carousels
- [ ] Test cart drawer
- [ ] Test product swatches

### Phase 8: Visual Regression
- [ ] Capture desktop screenshots
- [ ] Capture mobile screenshots
- [ ] Compare against reference
- [ ] Fix differences

### Phase 9: Functional Regression
- [ ] Test add to cart
- [ ] Test checkout flow
- [ ] Test account pages
- [ ] Test search

### Phase 10: Luxury Isolation
- [ ] Verify no AETHER leaks
- [ ] Verify WooCommerce untouched
- [ ] Verify security intact
- [ ] Verify performance

### Phase 11: Final Freeze
- [ ] Create final Git tag
- [ ] Archive `fermliving-legacy`
- [ ] Document final architecture

---

## 12. 100/100 ACCEPTANCE GATE

### 12.1 Route Parity
- [ ] `/` (homepage) matches reference
- [ ] `/shop/` matches reference
- [ ] `/shop/furniture/` matches reference
- [ ] `/shop/lighting/` matches reference
- [ ] `/shop/?p=1` (product) matches reference
- [ ] `/about/` matches reference
- [ ] `/blog/` matches reference
- [ ] `/contact/` matches reference
- [ ] `/cart/` matches reference
- [ ] `/my-account/` matches reference

### 12.2 DOM Parity
- [ ] Section order matches reference
- [ ] Component hierarchy matches reference
- [ ] Class names are semantic (not AETHER-specific)
- [ ] No AETHER leak classes (e.g., `aether-`)

### 12.3 Content Parity
- [ ] Homepage sections match reference
- [ ] Product cards show correct data
- [ ] Category cards show correct data
- [ ] Footer content matches reference
- [ ] Navigation content matches reference

### 12.4 Asset Parity
- [ ] Canela font loads correctly
- [ ] Teka font loads correctly
- [ ] Ferm images load correctly
- [ ] No 404 errors in console

### 12.5 CSS Parity
- [ ] Colors match reference
- [ ] Typography matches reference
- [ ] Spacing matches reference
- [ ] Layout matches reference
- [ ] Responsive breakpoints match reference

### 12.6 Interaction Parity
- [ ] Mega menu works correctly
- [ ] Mobile menu works correctly
- [ ] Carousels work correctly
- [ ] Cart drawer works correctly
- [ ] Product swatches work correctly
- [ ] Accordion works correctly

### 12.7 Accessibility
- [ ] Keyboard navigation works
- [ ] Screen reader compatible
- [ ] Color contrast meets WCAG AA
- [ ] Focus states visible

### 12.8 Performance
- [ ] Lighthouse score > 90
- [ ] First contentful paint < 1.5s
- [ ] Largest contentful paint < 2.5s
- [ ] No layout shift

### 12.9 Luxury Isolation
- [ ] Zero AETHER CSS classes in Ferm templates
- [ ] Zero AETHER JS calls in Ferm components
- [ ] WooCommerce untouched
- [ ] Security intact
- [ ] No business logic in presentation layer

---

## 13. FINAL OUTPUT

### 13.1 Deliverables

1. **FERM-DESIGN-SPEC.md** ✅
2. **FERM-AUREON-MAPPING.md** ✅ (this document)
3. New Ferm pack at `frontend/designs/fermliving/`
4. Reference data at `frontend/designs/fermliving/data/`
5. Visual regression screenshots
6. Functional regression tests

### 13.2 Success Criteria

The rebuild is complete when:
- 100/100 visual parity against frozen Ferm reference
- Zero AETHER leaks in Ferm templates
- WooCommerce fully functional
- All routes working
- All interactions working
- All responsive breakpoints working
- Performance meets targets
- Security intact

---

*End of FERM-AUREON-MAPPING.md*
