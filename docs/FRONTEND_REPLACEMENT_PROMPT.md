# FRONTEND REPLACEMENT PROMPT

> Use this prompt to instruct an AI or developer to replace the frontend of the AUREON/AETHER WordPress theme with a new design.
> **Version:** 2.0.0 · **Date:** 2026-08-29

---

## The Prompt

```
You are working on a WordPress theme called AUREON with an integrated frontend engine called AETHER.

## CRITICAL FRONTEND RULE (READ BEFORE ANYTHING)

AUREON supports TWO frontend modes. DETERMINE THE MODE FIRST.

### MODE A — COMPONENT MODE

Use when:
- No complete premium frontend exists, OR
- The client explicitly wants AUREON to compose the UI from sections/components.

Architecture:
```
AUREON
→ adapters
→ ViewModels/data
→ sections/components
→ rendered page
```

In this mode:
- AUREON controls presentation composition.
- Design packs shadow base templates via filesystem.
- tokens.php, composer.php, component/section conventions are used.
- Platform CDN dependencies (Bootstrap, Swiper, GSAP) may be loaded.

### MODE B — COMPLETE-PAGE MODE

Use when:
- The client provides a complete HTML/CSS/JS premium frontend.
- The client wants that frontend preserved as-is.

Architecture:
```
COMPLETE CLIENT FRONTEND
→ generic complete-page host
→ thin integration bridge
→ AUREON/WP/WooCommerce
```

In this mode:
- Client HTML/CSS/JS remains presentation source of truth.
- AUREON provides routing, canonical data, business logic, security, endpoints, WooCommerce integration.
- Bridge translates between the two systems.
- tokens.php is OPTIONAL (client owns its own design system).
- Sections/components are NOT used (client has its own presentation).

**NEVER convert a COMPLETE-PAGE frontend into COMPONENT MODE merely because AUREON's component architecture already exists.**

## DEFAULT CORE EDIT RULE

Do not modify `aureon/theme/**` or `aureon/plugin/**` by default.
Generic, reusable core changes are allowed ONLY when:
1. A missing reusable platform capability is proven necessary.
2. The change is isolated behind a generic contract/flag (e.g., `complete_page=true`).
3. The change is regression-tested.
4. A client-specific frontend must not introduce client-specific business logic into AUREON core.

## YOUR TASK

Determine the frontend mode FIRST, then follow the appropriate workflow below.

---
```

---

## STEP 1: DETERMINE FRONTEND MODE

Ask this question:

> **Does the client provide a complete HTML/CSS/JS premium frontend?**

```
YES → Follow MODE B: Complete-Page Workflow
NO  → Follow MODE A: Component Mode Workflow
```

---

## MODE A: COMPONENT MODE WORKFLOW

> Use when no complete premium frontend exists, or client wants AUREON to compose the UI.

### Step A1: Study the Existing Architecture

Read these files first:
- `docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md` — Authoritative guide
- `docs/TEMPLATE_REQUIREMENTS_FOR_CORE_THEME.md` — Template requirements (Component Mode Reference section)
- `docs/forensics/CORE-THEME-AUDIT.md` — What's safe to touch
- `frontend/manifest/components.php` — All 53 component registrations
- `frontend/views/registry.php` — All 26 section registrations

### Step A2: Study the Data Contracts

Every component receives pre-normalized data from adapters:

**Product Card** (from `adapter-wc-products.php`):
- `id`, `name`, `price`, `image`, `url`, `badge`, `variants`, `rating`, `review_count`

**Product Page** (from `adapter-product.php`):
- `id`, `name`, `price`, `description`, `short_description`, `images`, `variants`, `attributes`, `add_to_cart_url`, `gallery`, `reviews`, `rating`, `review_count`, `related`

**Cart** (from `adapter-cart.php`):
- `items[]` (key, id, name, price, image, quantity, subtotal, variants, remove_url)
- `subtotal`, `shipping`, `total`, `count`, `is_empty`

**Shell/Header** (from `adapter-shell.php`):
- `logo`, `nav_items[]`, `footer_links[]`, `cart_count`, `search_url`, `account_url`, `announcement`

### Step A3: Create Your Pack Directory

```bash
mkdir -p frontend/designs/{your-pack}/css
mkdir -p frontend/designs/{your-pack}/js
mkdir -p frontend/designs/{your-pack}/components/shell
mkdir -p frontend/designs/{your-pack}/components/cards
mkdir -p frontend/designs/{your-pack}/components/product
mkdir -p frontend/designs/{your-pack}/sections
mkdir -p frontend/designs/{your-pack}/assets/fonts
mkdir -p frontend/designs/{your-pack}/assets/images
```

### Step A4: Create manifest.json

```json
{
  "name": "{your-pack}",
  "version": "1.0.0",
  "description": "Your custom design pack",
  "author": "Your Name",
  "assets": {
    "css": ["css/{pack}.css"],
    "js": ["js/{pack}.js"]
  }
}
```

### Step A5: Create tokens.php

```php
<?php
return [
    'colors' => [
        'primary'    => '#000000',
        'secondary'  => '#ffffff',
        'accent'     => '#c8a97e',
        'background' => '#f8f6f3',
        'text'       => '#1a1a1a',
        'muted'      => '#6b7280',
    ],
    'typography' => [
        'heading_font' => '"Your Heading Font", serif',
        'body_font'    => '"Your Body Font", sans-serif',
        'base_size'    => '16px',
        'scale'        => '1.25',
    ],
    'spacing' => [
        'section_gap' => '6rem',
        'container_width' => '1280px',
    ],
];
```

### Step A6: Create composer.php

```php
<?php
// Control section ordering on homepage
add_filter('aether_frontpage_sections', function($sections) {
    return ['hero', 'categories', 'bestsellers', 'newsletter'];
});
```

### Step A7: Shadow Components

Copy base templates to your pack and modify:

```bash
# Example: override product card
cp frontend/components/cards/product.php frontend/designs/{your-pack}/components/cards/product.php
# Edit the copy — base remains untouched
```

### Step A8: Create Your CSS

```css
@import url('fonts.css');

:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
}

/* Your styles here */
```

### Step A9: Create Your JS

```javascript
(function() {
    'use strict';
    // Your pack JavaScript
    // Safe: DOM manipulation, animations, UI interactions
    // Forbidden: calling WP/WC functions, modifying AETHER globals
})();
```

### Step A10: Activate Your Pack

Set the `aether_active_design` option to your pack slug, or use WordPress Customizer -> AETHER Design.

### Component Mode Template Pattern

Every component follows this pattern:

```php
<?php
// $data is the normalized data array from the adapter
// NEVER call WP/WC functions here
// ONLY use $data fields and escape output

$name  = esc_html($data['name'] ?? '');
$price = $data['price'] ?? '';
$image = esc_url($data['image'] ?? '');
$url   = esc_url($data['url'] ?? '');
?>

<div class="product-card">
    <?php if ($image): ?>
        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" loading="lazy">
    <?php endif; ?>
    <h3 class="product-card__title">
        <a href="<?php echo $url; ?>"><?php echo $name; ?></a>
    </h3>
    <div class="product-card__price"><?php echo $price; ?></div>
</div>
```

### Component Mode — What You Can Do

| Operation | Safe? | Where |
|-----------|-------|-------|
| Override component templates | YES | `pack/components/**/*.php` |
| Override section templates | YES | `pack/sections/**/*.php` |
| Modify CSS | YES | `pack/css/*.css` |
| Modify JS | YES | `pack/js/*.js` |
| Change fonts | YES | `pack/css/fonts.css` + `pack/assets/fonts/` |
| Change colors | YES | `pack/tokens.php` or CSS custom properties |
| Reorder sections | YES | `pack/composer.php` |
| Add new sections | YES | Create section + adapter, register in composer |
| Change logo | YES | `pack/components/shell/header.php` |
| Modify product card | YES | `pack/components/cards/product.php` |

### Component Mode — What You Cannot Do

| Operation | Forbidden? | Why |
|-----------|------------|-----|
| Edit `aureon/theme/**` | YES | Theme updates overwrite changes |
| Edit `aureon/plugin/**` | YES | Plugin updates overwrite changes |
| Edit `frontend/views/**` | YES | Engine kernel — shared infrastructure |
| Edit `frontend/adapters/**` | CAUTION | Data contract boundary — breaks all components |
| Call WP/WC functions in templates | YES | Components receive normalized data only |
| Modify AETHER globals | YES | Breaks engine behavior |
| Override platform JS (GSAP, Swiper) | YES | Platform dependency |

### Component Mode Testing Checklist

- [ ] Homepage renders at 1440px, 1024px, 768px, 390px
- [ ] Shop page renders at all breakpoints
- [ ] Product page renders at all breakpoints
- [ ] Cart page renders at all breakpoints
- [ ] Add to cart works (simple + variable products)
- [ ] Cart update/remove works
- [ ] Checkout flow completes
- [ ] My Account login works
- [ ] Wishlist add/remove works
- [ ] All images load correctly
- [ ] All fonts load correctly
- [ ] No CSS conflicts
- [ ] No JS errors in console
- [ ] Mobile menu opens/closes
- [ ] Mobile cart count updates

---
```

---

## MODE B: COMPLETE-PAGE WORKFLOW

> Use when the client provides a complete HTML/CSS/JS premium frontend.
> **This is the preferred mode for premium client frontends (e.g., Ferm Living).**

### Step B1: Forensic Audit

Before doing anything, audit the client frontend:

```
FORENSIC AUDIT CHECKLIST:
├── Capture standalone HTML snapshot at 1440px, 1024px, 768px, 390px
├── Capture all CSS files and their dependencies
├── Capture all JS files and their dependencies
├── Capture all vendor libraries and versions
├── Identify Shopify-specific code (Shopify routes, liquid tags, Shopify API)
├── Identify WordPress-compatible code (WP functions, WC integration)
├── Document all data fields used in the HTML
├── Document all commerce actions (add-to-cart, checkout, wishlist)
├── Document all interactive elements (menus, modals, sliders)
└── Document all animation/motion systems
```

### Step B2: Immutable Original Source

Create an immutable copy of the client frontend:

```
frontend/designs/{your-pack}/
  original/                    ← IMMUTABLE — never edit
    index.html
    css/
    js/
    assets/
    images/
    fonts/
  build/                       ← Working copy — edits go here
    index.html
    css/
    js/
    assets/
    images/
    fonts/
```

**Rule:** The `original/` directory is the forensic baseline. Never modify it. All edits go to `build/`.

### Step B3: Dependency Classification

Classify every dependency:

```
DEPENDENCY CLASSIFICATION:
├── BUSINESS-REQUIRED (must keep):
│   ├── WooCommerce cart.js (bridged)
│   ├── jQuery (WP core)
│   └── WP admin bar
│
├── CLIENT-PRESENTATION (keep — client owns):
│   ├── Client CSS (design system)
│   ├── Client JS (presentation logic)
│   ├── Client vendor libraries
│   └── Client animations
│
├── SHOPIFY-SPECIFIC (remove or bridge):
│   ├── Shopify liquid tags → replace with PHP/JS
│   ├── Shopify Cart API → bridge to WC
│   ├── Shopify routes → map to WP routes
│   └── Shopify customer → bridge to WC customer
│
└── AUREON-CONTAMINATION (remove):
    ├── AUREON presentation CSS
    ├── AUREON presentation JS
    ├── Bootstrap (if client has its own grid)
    ├── Swiper (if client has its own carousel)
    └── GSAP (if client has its own animations)
```

### Step B4: Third-Party Cleanup

Remove Shopify-specific code:

```
CLEANUP CHECKLIST:
├── Remove {{ shop.xxx }} liquid tags → replace with PHP/JS
├── Remove {% for %} liquid loops → replace with PHP loops
├── Remove Shopify analytics → replace with GA4 dataLayer
├── Remove Shopify customer → bridge to WC customer
├── Remove Shopify cart → bridge to WC cart
├── Remove Shopify routes → map to WP routes
└── Test: standalone HTML renders correctly after cleanup
```

### Step B5: Asset Normalization

Normalize assets for WordPress:

```
ASSET NORMALIZATION:
├── Rename files to match WP conventions
├── Update paths from relative to absolute
├── Add WordPress-safe prefixes to JS functions
├── Ensure jQuery compatibility (noConflict)
├── Add proper enqueuing in bridge.php
└── Test: all assets load correctly in WordPress
```

### Step B6: Template Contract

Document the data contract between AUREON and the client frontend:

```markdown
# TEMPLATE CONTRACT

## Data Fields

### Product
- `product.id` — WooCommerce product ID
- `product.name` — Product title
- `product.price` — Formatted price string
- `product.images[]` — Array of image URLs
- `product.description` — Product description HTML
- `product.variants[]` — Array of variant objects
- `product.attributes[]` — Array of attribute objects
- `product.add_to_cart_url` — WC add-to-cart URL
- `product.rating` — Average rating (0-5)
- `product.review_count` — Number of reviews
- `product.related[]` — Array of related product IDs

### Collection
- `collection.id` — Category/term ID
- `collection.name` — Category name
- `collection.products[]` — Array of product objects
- `collection.count` — Total product count

### Navigation
- `nav.items[]` — Array of menu items
- `nav.logo` — Logo URL
- `nav.cart_count` — Current cart count
- `nav.search_url` — Search page URL
- `nav.account_url` — Account page URL

### Cart
- `cart.items[]` — Array of cart item objects
- `cart.subtotal` — Formatted subtotal
- `cart.total` — Formatted total
- `cart.count` — Item count

### Customer
- `customer.logged_in` — Boolean
- `customer.name` — Customer name
- `customer.email` — Customer email
```

### Step B7: Assets Manifest

Document all assets:

```json
{
  "css": [
    "css/main.css",
    "css/responsive.css"
  ],
  "js": [
    "js/main.js",
    "js/animations.js"
  ],
  "vendor": [
    "vendor/lottie.min.js"
  ],
  "fonts": [
    "fonts/HeadingFont.woff2",
    "fonts/BodyFont.woff2"
  ],
  "images": [
    "images/logo.svg",
    "images/hero-bg.jpg"
  ]
}
```

### Step B8: JS Compatibility Map

Document JS runtime dependencies:

```markdown
# JS COMPATIBILITY MAP

## Client JS
- main.js — Requires: window, document, fetch
- animations.js — Requires: IntersectionObserver, requestAnimationFrame

## Vendor JS
- lottie.min.js — Self-contained, no dependencies

## Bridge JS
- bridge.js — Requires: jQuery (WP), fetch API

## Conflicts
- None identified

## WordPress Compatibility
- jQuery loaded in noConflict mode
- WP admin bar: 32px height (desktop), 0px (mobile)
```

### Step B9: Create manifest.json

```json
{
  "name": "{your-pack}",
  "version": "1.0.0",
  "description": "Client premium frontend — complete-page mode",
  "author": "Your Name",
  "complete_page": true,
  "client_frontend": {
    "source": "original/index.html",
    "build": "build/index.html",
    "css": ["build/css/main.css"],
    "js": ["build/js/main.js"],
    "vendor": ["build/vendor/lottie.min.js"]
  },
  "assets": {
    "css": [],
    "js": ["js/bridge.js"]
  }
}
```

### Step B10: Create Complete-Page Host

If not already present, create the complete-page host:

```
frontend/views/complete-page.php
```

This file:
1. Reads the frozen HTML file
2. Extracts body content between `<body>` and `</body>`
3. Wraps with `wp_head()` / `wp_footer()`
4. Outputs the complete page

### Step B11: Create Thin Data/Business Bridge

Create the bridge that connects AUREON/WP/WooCommerce to the client frontend:

```
frontend/designs/{your-pack}/bridge.php
```

The bridge provides:
- Product data mapping (WC → client format)
- Collection/category data mapping
- Navigation data (WP menus → client format)
- Cart bridge (WC cart → client cart)
- Customer state (WC session → client format)
- Search bridge (WC search → client format)
- Form handling (contact, newsletter)
- Route mapping (WP routes → client pages)
- Runtime configuration

### Step B12: Create bridge.js

```javascript
(function() {
    'use strict';

    // Cart count sync
    // Wishlist sync
    // Commerce action handlers
    // Runtime configuration injection

    // DO NOT:
    // - Recreate the client DOM
    // - Rewrite presentation JS
    // - Modify client CSS
    // - Split pages into sections
})();
```

### Step B13: Real Browser Verification

Test the connected frontend:

```
VERIFICATION CHECKLIST:
├── Standalone client frontend renders correctly
├── WordPress-connected frontend renders identically
├── No AUREON presentation contamination
├── No duplicate libraries
├── No Shopify/runtime dependency errors
├── No asset 404s
├── Real dynamic data displayed
├── Real commerce actions working
├── No console errors
└── All viewports render correctly (1440, 1024, 768, 390)
```

### Complete-Page Mode — What You Can Do

| Operation | Safe? | Where |
|-----------|-------|-------|
| Edit client CSS | YES | `build/css/*.css` |
| Edit client JS | YES | `build/js/*.js` |
| Edit client HTML | YES | `build/*.html` |
| Create bridge.php | YES | `pack/bridge.php` |
| Create bridge.js | YES | `pack/js/bridge.js` |
| Map data fields | YES | `pack/bridge.php` |
| Bridge cart API | YES | `pack/bridge.php` + `pack/js/bridge.js` |
| Suppress AUREON tokens | YES | `manifest.json` → `complete_page: true` |
| Suppress AUREON assets | YES | `manifest.json` → `complete_page: true` |

### Complete-Page Mode — What You Cannot Do

| Operation | Forbidden? | Why |
|-----------|------------|-----|
| Split client HTML into sections | YES | Client owns presentation |
| Rebuild client DOM with AUREON components | YES | Client owns presentation |
| Recreate client CSS | YES | Client owns design system |
| Rewrite presentation JS | YES | Client owns interactions |
| Add AUREON sections/components | YES | Not used in complete-page mode |
| Add platform CDN dependencies | CAUTION | Client may already have its own |
| Introduce client-specific logic into AUREON core | YES | Core must remain generic |

### Complete-Page Mode Testing Checklist

```
COMMON TESTS:
├── Source standalone vs WordPress connected
├── No AUREON presentation contamination
├── No duplicate libraries
├── No Shopify/runtime dependency errors
├── No asset 404s
├── Real dynamic data displayed
├── Real commerce actions working
└── Isolation proven (no visual interference)

VIEWPORT TESTS:
├── 1440px — desktop
├── 1024px — tablet landscape
├── 768px — tablet portrait
├── 390px — mobile

COMMERCE TESTS:
├── Product data displays correctly
├── Add to cart works (simple + variable)
├── Cart update/remove works
├── Checkout flow completes
├── Customer login works
├── Wishlist add/remove works
└── Search works

ISOLATION TESTS:
├── No AUREON CSS loaded
├── No AUREON JS loaded
├── No Bootstrap loaded (if client has own grid)
├── No Swiper loaded (if client has own carousel)
├── No GSAP loaded (if client has own animations)
├── No duplicate jQuery
├── No Shopify runtime errors
└── WP admin bar works correctly
```

### Complete-Page Acceptance Gate

```
STANDALONE CLIENT FRONTEND:
├── Renders correctly at all viewports
├── All assets load
├── All interactions work
└── No console errors

WORDPRESS-CONNECTED FRONTEND:
├── Renders identically to standalone
├── Dynamic data displays correctly
├── Commerce actions work
├── No presentation contamination
└── No console errors

PASS CRITERIA:
├── Zero prohibited third-party runtime errors
├── Zero presentation asset contamination
├── Zero duplicate libraries
├── Zero source DOM reconstruction
├── Dynamic data proven separately
├── Business actions proven separately
└── Isolation proven separately
```

---
```

---

## ROLLBACK

Every change is in git. To rollback:

```bash
# Rollback specific file
git checkout HEAD -- frontend/designs/{your-pack}/css/{pack}.css

# Rollback last commit
git revert HEAD

# Switch packs instantly
# Change aether_active_design option → clear cache → done
```

## REFERENCE FILES

- `docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md` — Full guide
- `docs/TEMPLATE_REQUIREMENTS_FOR_CORE_THEME.md` — Template requirements (both modes documented)
- `docs/forensics/CORE-TO-FERM-INTEGRATION-MAP.md` — Data flow
- `docs/FRONTEND_DATA_CONTRACT.md` — Adapter data contracts
- `frontend/manifest/components.php` — Component registry (Component Mode)
- `frontend/views/registry.php` — Section registry (Component Mode)
```

---

## How to Use This Prompt

1. **Copy the prompt** inside the code block above
2. **Determine frontend mode** — does the client have a complete HTML/CSS/JS frontend?
3. **Follow the appropriate workflow** — MODE A or MODE B
4. **Replace `{your-pack}`** with your pack name (e.g., `fermliving`)
5. **Replace `{pack}`** with your pack shortname (e.g., `ferm`)
6. **Paste into your AI assistant** or give to your developer
7. **Follow the step-by-step process** in order
8. **Test everything** using the appropriate checklist before deploying

## Customization Points

### Component Mode

| What to Change | Where to Edit |
|----------------|---------------|
| Colors | `tokens.php` or CSS `:root` variables |
| Fonts | `css/fonts.css` + `assets/fonts/` |
| Layout | Component/section templates + CSS |
| Section order | `composer.php` filter |
| Logo | `components/shell/header.php` |
| Navigation | WP Admin → Appearance → Menus |
| Product card | `components/cards/product.php` |
| Cart page | `sections/section-cart.php` |
| Checkout | WC native (wrapped in shell) |
| Footer | `components/shell/footer.php` |
| Mobile menu | `components/shell/mobile-chrome.php` |

### Complete-Page Mode

| What to Change | Where to Edit |
|----------------|---------------|
| Client CSS | `build/css/*.css` |
| Client JS | `build/js/*.js` |
| Client HTML | `build/*.html` |
| Data mapping | `bridge.php` |
| Cart bridge | `bridge.php` + `js/bridge.js` |
| Asset loading | `manifest.json` |
| Route mapping | `bridge.php` |
| Customer state | `bridge.php` |
