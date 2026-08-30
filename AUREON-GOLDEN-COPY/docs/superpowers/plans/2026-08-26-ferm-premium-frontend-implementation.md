# Ferm Premium Frontend — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Port the frozen Ferm Living frontend into the Ferm design pack as a premium frontend with a thin dynamic bridge to WordPress/WooCommerce/AUREON.

**Architecture:** Frozen Ferm source provides the presentation contract (HTML/CSS/JS). PHP server-renders the Ferm DOM and injects `window.FermPageData` from AUREON canonical data via the Ferm mapper. Ferm JS enhances the server-rendered DOM. Shopify business logic replaced by WooCommerce/AUREON contracts.

**Tech Stack:** WordPress, WooCommerce, AUREON, PHP, JavaScript (GSAP, Lenis, Swiper/Embla), compiled Tailwind CSS (from frozen source).

## Global Constraints

- **Frozen source:** `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com` — immutable `FERM_REFERENCE_V1`
- **Project root:** `C:\Users\hamma\Downloads\phantom\wordpress`
- **Pack path:** `frontend/designs/fermliving/`
- **Legacy pack:** `frontend/designs/fermliving-legacy/` — never active, never deleted until acceptance
- **AUREON core:** No Ferm-specific changes. Generic extensions allowed.
- **WooCommerce:** Business logic untouched. Database never replaced with Ferm reference data.
- **CSS:** Frozen compiled CSS is source of truth. No Tailwind rebuild.
- **JS:** Frozen JS behavior is source of truth. Shopify code removed/replaced.
- **Data:** `window.FermPageData` — page-scoped, minimal, public-safe JSON.
- **Nonces:** Reuse existing AUREON/WP localized nonce/endpoint bridge. No second auth system.
- **Routes:** REFERENCE ROUTE → PAGE FAMILY → WORDPRESS TARGET ROUTE. Do not mimic Shopify URLs.
- **Max-width:** Inspect frozen compiled CSS. Do NOT hardcode 1440.
- **Critical CSS:** Minimal above-the-fold subset inline only. Full Ferm CSS as self-hosted asset.
- **Assets:** Generate `assets-manifest.json`. Verify HTTP 200 + identity (hash).
- **Release gate:** Tests + Screenshots + Routes + Content + Assets + Isolation + Core Integrity = RELEASE.

---

## Phase 0 — Freeze / Baseline

**Objective:** Create a clean rollback point before any implementation.

**Depends on:** Nothing.

### Task 0.1: Record Current State

- [ ] **Step 1: Record current git HEAD**
  ```bash
  git rev-parse HEAD
  ```
  Record the hash. This is the rollback point.

- [ ] **Step 2: Record working tree status**
  ```bash
  git status --short
  ```
  Record any uncommitted changes.

- [ ] **Step 3: Record current branch**
  ```bash
  git branch --show-current
  ```

- [ ] **Step 4: Verify protected layers are intact**
  ```bash
  # AUREON core
  ls theme/aureon/
  ls aureon/plugin/
  ls aureon/theme/
  
  # Luxury pack
  ls frontend/designs/luxury/
  
  # Legacy Ferm
  ls frontend/designs/fermliving-legacy/
  
  # Design resolver
  ls frontend/views/design.php
  ```
  All must exist and be unchanged.

- [ ] **Step 5: Create work branch**
  ```bash
  git checkout -b feature/ferm-premium-frontend
  ```

- [ ] **Step 6: Commit baseline**
  ```bash
  git add -A
  git commit -m "chore(frontend): freeze ferm reference rebuild baseline"
  ```
  Record the commit hash: `____________________`

**Rollback point:** This commit hash.

**Acceptance:** Git repo is on `feature/ferm-premium-frontend` branch with a clean baseline commit. All protected layers verified intact.

---

## Phase 1 — Global Shell

**Objective:** Port announcement, header, mega menu, search overlay, mobile nav, footer from frozen source into Ferm pack PHP templates with matching DOM.

**Depends on:** Phase 0 (baseline commit).

### Task 1.1: Source Audit — Extract Frozen Shell DOM

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\index.html` (first 500 lines for header/footer structure)
- Read: `C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\components\shell\header.php`
- Read: `C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\components\shell\footer.php`
- Read: `C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\components\shell\announcement.php`
- Read: `C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\components\shell\mobile-chrome.php`

- [ ] **Step 1: Extract frozen source announcement bar DOM**
  Open frozen `index.html`, locate the announcement/USP bar section. Copy the exact HTML structure, classes, and data attributes.

- [ ] **Step 2: Extract frozen source header DOM**
  Locate `<section data-section-id="header">` in frozen source. Copy exact DOM including all `data-*` attributes, classes, nested structure.

- [ ] **Step 3: Extract frozen source mega menu DOM**
  Locate mega menu container in frozen source. Copy exact structure with `data-megamenu`, `data-megamenu-menu-point`, `data-dynamic-menu-wrapper`, etc.

- [ ] **Step 4: Extract frozen source search overlay DOM**
  Locate search overlay in frozen source. Copy exact structure.

- [ ] **Step 5: Extract frozen source mobile nav DOM**
  Locate mobile navigation drawer in frozen source. Copy exact structure.

- [ ] **Step 6: Extract frozen source footer DOM**
  Locate footer in frozen source. Copy exact structure including newsletter, columns, legal, payment icons.

- [ ] **Step 7: Document extracted DOM patterns**
  For each component, record:
  - Exact HTML structure
  - All CSS classes used
  - All `data-*` attributes
  - Dynamic data placeholders (menu items, cart count, brand name, URLs)

### Task 1.2: CSS Extraction — Shell Dependencies

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\` (CSS files)
- Create: `frontend/designs/fermliving/css/ferm-shell.css`

- [ ] **Step 1: Identify shell CSS dependencies**
  From frozen source, identify which CSS rules apply to:
  - Announcement bar
  - Header (`.header`, `.header--transparent`, `.header--solid`, `.header--scrolled`)
  - Mega menu (`.megamenu`, `.megamenu-wrapper`, `.megamenu__inner`)
  - Search overlay (`.search-overlay`, `.search-input`)
  - Mobile nav
  - Footer

- [ ] **Step 2: Extract shell CSS rules**
  Copy the exact compiled CSS rules for shell components from frozen source CSS files. Do not modify selectors or values.

- [ ] **Step 3: Handle global resets**
  Classify global rules (`*`, `html`, `body`, `button`, `a`, `img`) as platform-safe reset vs Ferm-specific. Only include Ferm-specific ones.

- [ ] **Step 4: Verify responsive rules**
  Extract `@media` queries that affect shell components. Preserve exact breakpoint values.

- [ ] **Step 5: Write ferm-shell.css**
  Combine extracted shell CSS into a single file. Scope if necessary for `.design-fermliving`.

### Task 1.3: PHP Templates — Shell Components

**Files:**
- Modify: `frontend/designs/fermliving/components/shell/announcement.php`
- Modify: `frontend/designs/fermliving/components/shell/header.php`
- Modify: `frontend/designs/fermliving/components/shell/footer.php`
- Modify: `frontend/designs/fermliving/components/shell/mobile-chrome.php`

- [ ] **Step 1: Rewrite announcement.php**
  Replace current content with frozen source DOM. Use PHP variables for dynamic data:
  ```php
  <?php
  // Data from adapter: $usps (array of USP strings)
  ?>
  <div class="announcement-bar ...">
    <?php foreach ($usps as $usp): ?>
      <span class="announcement-bar__item"><?= esc_html($usp) ?></span>
    <?php endforeach; ?>
  </div>
  ```
  Preserve exact frozen source classes and structure.

- [ ] **Step 2: Rewrite header.php**
  Replace current content with frozen source DOM. Use PHP variables for:
  - `$brand` — site name
  - `$brand_url` — home URL
  - `$menu` — navigation menu items (with children/grandchildren)
  - `$cart_count` — WooCommerce cart item count
  - `$customer` — minimal customer state (isLoggedIn)
  - Icon SVGs (search, wishlist, cart, account, hamburger, close)
  Preserve all `data-*` attributes exactly.

- [ ] **Step 3: Rewrite footer.php**
  Replace current content with frozen source DOM. Use PHP variables for:
  - `$footer_columns` — link columns
  - `$newsletter` — newsletter section
  - `$legal` — legal links
  - `$payments` — payment icons

- [ ] **Step 4: Rewrite mobile-chrome.php**
  Replace current content with frozen source mobile nav DOM.

- [ ] **Step 5: Verify all data-* attributes preserved**
  Grep for `data-` in each template and verify they match frozen source.

### Task 1.4: FermPageData — Shell Schema

**Files:**
- Modify: `frontend/designs/fermliving/composer.php` (or mapper)

- [ ] **Step 1: Define shell FermPageData fields**
  ```php
  // In FermPageData output for all pages:
  'navigation' => [
      'menu' => [...], // from wp_nav_menu or AUREON menu adapter
      'currentPath' => $_SERVER['REQUEST_URI'],
  ],
  'cart' => [
      'itemCount' => WC()->cart->get_cart_contents_count(),
      'total' => [
          'amount' => (float) WC()->cart->get_total('edit'),
          'formatted' => WC()->cart->get_total('formatted'),
          'currency' => get_woocommerce_currency(),
      ],
  ],
  'customer' => [
      'isLoggedIn' => is_user_logged_in(),
  ],
  ```

- [ ] **Step 2: Verify nonce/endpoint mechanism**
  Confirm AUREON/WP runtime already localizes endpoint URLs and nonces via `wp_localize_script` or equivalent. Do NOT add a second system.

- [ ] **Step 3: Test FermPageData output**
  Load a page in browser, inspect `window.FermPageData` in console. Verify structure matches spec.

### Task 1.5: JS — Shell Behavior

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\` (JS files)
- Create: `frontend/designs/fermliving/js/ferm-shell.js`

- [ ] **Step 1: Inventory frozen source JS files**
  For each JS file in frozen source, record:
  ```
  FILE: [filename]
  BEHAVIOR: [what it does]
  DEPENDENCIES: [what it requires]
  SHOPIFY DEPENDENCY: [yes/no]
  ACTION: [keep/adapt/remove]
  TARGET: [ferm pack filename]
  ```

- [ ] **Step 2: Extract shell-relevant JS behavior**
  From the inventory, extract behaviors for:
  - Header hide/show on scroll
  - Mega menu open/close
  - Mobile nav toggle
  - Search overlay open/close
  - Cart count updates

- [ ] **Step 3: Remove Shopify-specific code**
  Strip: Shopify cart API calls, Liquid JSON parsing, Clerk.io, Shopify customer endpoints.

- [ ] **Step 4: Replace data sources**
  Replace Shopify data reads with `window.FermPageData` reads.

- [ ] **Step 5: Write ferm-shell.js**
  Combine adapted shell JS behaviors into a single file.

- [ ] **Step 6: Library deduplication check**
  Check if AUREON already provides GSAP, Lenis, Swiper/Embla, Three.js. If compatible version exists, use it. If not, load from Ferm pack.

### Task 1.6: Asset Extraction — Shell Assets

**Files:**
- Create: `frontend/designs/fermliving/assets/` (subdirectories)
- Create: `frontend/designs/fermliving/assets-manifest.json`

- [ ] **Step 1: Identify shell assets needed**
  - Logo image
  - Font files (from frozen source `fonts.fd2d67c5ce.css`)
  - Icon SVGs (search, wishlist, cart, account, hamburger, close)
  - Payment icons (for footer)

- [ ] **Step 2: Copy assets from frozen source**
  Copy from `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\` to `frontend/designs/fermliving/assets/`.

- [ ] **Step 3: Preserve content-hashed filenames**
  Keep original filenames with hashes for cache-busting.

- [ ] **Step 4: Generate assets-manifest.json entries**
  For each asset:
  ```json
  {
    "referencePath": "cdn/shop/t/164/assets/logo.svg",
    "localPath": "assets/logo.svg",
    "hash": "sha256:...",
    "type": "image",
    "usedBy": ["shell:header"]
  }
  ```

- [ ] **Step 5: Verify all assets resolve**
  For each asset in manifest, verify HTTP 200 and correct content.

### Task 1.7: Shell Visual Validation

- [ ] **Step 1: Load homepage in browser**
  Navigate to WordPress homepage with Ferm pack active.

- [ ] **Step 2: Verify route correctness**
  ```
  REFERENCE: /
  FAMILY: Homepage
  WP TARGET: [actual WordPress URL]
  ```
  Must resolve to same page family.

- [ ] **Step 3: Verify Ferm DOM present**
  Inspect DOM. Verify frozen source classes, data attributes, structure are present.

- [ ] **Step 4: Verify FermPageData populated**
  In console: `window.FermPageData` — verify navigation, cart, customer fields.

- [ ] **Step 5: Verify Ferm CSS loaded**
  Check network tab for ferm-shell.css. Verify no missing classes.

- [ ] **Step 6: Verify Ferm JS initialized**
  Check console for errors. Verify mega menu, search overlay, mobile nav work.

- [ ] **Step 7: Capture 1440px screenshot**
  ```
  Screenshot: screenshots/ferm-shell-1440.png
  Reference: C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\index.html (header/footer)
  ```

- [ ] **Step 8: Capture 390px screenshot**
  ```
  Screenshot: screenshots/ferm-shell-390.png
  ```

- [ ] **Step 9: Run 16-point gate**
  All 16 checks must pass.

- [ ] **Step 10: Commit**
  ```bash
  git add frontend/designs/fermliving/
  git commit -m "ferm: port shell (header, footer, mega menu, search, mobile nav)"
  ```
  Record commit hash: `____________________`

**Rollback point:** Phase 0 baseline or this commit.

---

## Phase 2 — Homepage

**Objective:** Port hero, categories, editorial, products, rooms sections from frozen source.

**Depends on:** Phase 1 (shell complete).

### Task 2.1: Source Audit — Extract Frozen Homepage DOM

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\index.html` (main content sections)

- [ ] **Step 1: Extract hero section DOM**
  Locate hero section in frozen source. Copy exact structure, classes, image ratios, text layout.

- [ ] **Step 2: Extract categories section DOM**
  Locate category grid. Copy exact grid structure, card classes, image ratios.

- [ ] **Step 3: Extract editorial sections DOM**
  Locate editorial split sections. Copy exact layout, text/image positioning.

- [ ] **Step 4: Extract product grid DOM**
  Locate product grid sections. Copy exact card structure, carousel behavior.

- [ ] **Step 5: Extract rooms section DOM**
  Locate room grid. Copy exact structure.

- [ ] **Step 6: Extract newsletter section DOM**
  Locate newsletter signup. Copy exact form structure.

- [ ] **Step 7: Document all section patterns**
  For each section, record: exact HTML, classes, data attributes, dynamic placeholders.

### Task 2.2: CSS Extraction — Homepage Dependencies

**Files:**
- Create: `frontend/designs/fermliving/css/ferm-homepage.css`

- [ ] **Step 1: Identify homepage CSS dependencies**
  From frozen source CSS, identify rules for: hero, categories, editorial, products, rooms, newsletter.

- [ ] **Step 2: Extract homepage CSS rules**
  Copy exact compiled CSS rules. Do not modify.

- [ ] **Step 3: Write ferm-homepage.css**
  Combine into single file. Scope if needed.

### Task 2.3: PHP Templates — Homepage Sections

**Files:**
- Modify: `frontend/designs/fermliving/sections/section-hero.php`
- Modify: `frontend/designs/fermliving/sections/section-categories.php`
- Modify: `frontend/designs/fermliving/sections/section-editorial-split.php`
- Modify: `frontend/designs/fermliving/sections/section-bestsellers.php`
- Modify: `frontend/designs/fermliving/sections/section-room-grid.php`
- Create: `frontend/designs/fermliving/sections/section-newsletter.php`

- [ ] **Step 1: Rewrite section-hero.php**
  Use frozen source DOM. Dynamic: `$hero['title']`, `$hero['image']`, `$hero['link']`.

- [ ] **Step 2: Rewrite section-categories.php**
  Use frozen source DOM. Dynamic: `$categories` array (title, image, URL).

- [ ] **Step 3: Rewrite section-editorial-split.php**
  Use frozen source DOM. Dynamic: `$editorial` array (title, text, image, link).

- [ ] **Step 4: Rewrite section-bestsellers.php**
  Use frozen source DOM. Dynamic: `$products` array (title, price, image, URL, badge).

- [ ] **Step 5: Rewrite section-room-grid.php**
  Use frozen source DOM. Dynamic: `$rooms` array (title, image, link).

- [ ] **Step 6: Create section-newsletter.php**
  Use frozen source DOM. Form action points to WordPress/AUREON newsletter endpoint.

- [ ] **Step 7: Wire homepage template**
  Ensure WordPress homepage template loads all sections in correct order.

### Task 2.4: FermPageData — Homepage Schema

**Files:**
- Modify: `frontend/designs/fermliving/composer.php` (or mapper)

- [ ] **Step 1: Define homepage FermPageData fields**
  ```php
  'hero' => ['title' => '...', 'image' => '...', 'link' => '...'],
  'categories' => [...],
  'products' => ['bestsellers' => [...], 'newArrivals' => [...]],
  'editorial' => [...],
  'rooms' => [...],
  ```

- [ ] **Step 2: Map to AUREON adapter data**
  Connect each field to existing adapter outputs (adapter-hero, adapter-wc-products, adapter-wc-categories, etc.).

- [ ] **Step 3: Test FermPageData output**
  Load homepage, verify `window.FermPageData` contains all required fields.

### Task 2.5: JS — Homepage Behavior

**Files:**
- Create: `frontend/designs/fermliving/js/ferm-homepage.js`

- [ ] **Step 1: Extract homepage JS behavior**
  From frozen source JS, extract: hero carousel/transitions, product carousels, scroll animations, editorial reveals.

- [ ] **Step 2: Remove Shopify dependencies**
  Strip Shopify product JSON, collection data, Clerk.io.

- [ ] **Step 3: Replace data sources**
  Read from `window.FermPageData` instead of Liquid JSON.

- [ ] **Step 4: Write ferm-homepage.js**

### Task 2.6: Asset Extraction — Homepage Assets

**Files:**
- Create: `frontend/designs/fermliving/assets/hero/`, `categories/`, `editorial/`, `rooms/`

- [ ] **Step 1: Identify homepage assets needed**
  Hero images, category images, editorial images, room images.

- [ ] **Step 2: Copy from frozen source**

- [ ] **Step 3: Add to assets-manifest.json**

- [ ] **Step 4: Verify all resolve**

### Task 2.7: Homepage Visual Validation

- [ ] **Step 1: Load homepage**
- [ ] **Step 2: Verify route correctness**
- [ ] **Step 3: Verify Ferm DOM**
- [ ] **Step 4: Verify FermPageData**
- [ ] **Step 5: Verify CSS/JS**
- [ ] **Step 6: Capture 1440px screenshot**
- [ ] **Step 7: Capture 390px screenshot**
- [ ] **Step 8: Run 16-point gate**
- [ ] **Step 9: Commit**
  ```bash
  git commit -m "ferm: port homepage (hero, categories, editorial, products, rooms)"
  ```
  Record commit hash: `____________________`

**Rollback point:** Phase 1 commit or this commit.

---

## Phase 3 — Archive / PLP

**Objective:** Port collection/archive pages with product grid, filters, sorting, pagination.

**Depends on:** Phase 2 (homepage complete).

### Task 3.1: Source Audit — Extract Frozen Archive DOM

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\collections\*.html`

- [ ] **Step 1: Extract archive page DOM**
  Locate collection page structure in frozen source. Copy: product grid, filter sidebar, sort dropdown, pagination, active filter chips.

- [ ] **Step 2: Extract product card DOM**
  Copy exact product card structure (from `components/cards/product.php` — verify against frozen source).

- [ ] **Step 3: Extract filter UI DOM**
  Copy exact filter structure (checkboxes, radio buttons, price range, color swatches).

- [ ] **Step 4: Document all patterns**

### Task 3.2: CSS — Archive Dependencies

**Files:**
- Create: `frontend/designs/fermliving/css/ferm-archive.css`

- [ ] **Step 1: Identify archive CSS**
  Grid, filters, sorting, pagination, product card styles.

- [ ] **Step 2: Extract rules**
- [ ] **Step 3: Write ferm-archive.css**

### Task 3.3: PHP Templates — Archive

**Files:**
- Create: `frontend/designs/fermliving/templates/archive-product.php`
- Modify: `frontend/designs/fermliving/components/cards/product.php`

- [ ] **Step 1: Create archive template**
  Use frozen source DOM. Dynamic: `$products`, `$filters`, `$pagination`, `$sort`.

- [ ] **Step 2: Rewrite product card**
  Use frozen source DOM. Dynamic: `$product['title']`, `$product['price']`, `$product['image']`, `$product['url']`, `$product['badge']`.

- [ ] **Step 3: Connect to WooCommerce product query**
  Use existing AUREON adapter (adapter-wc-products, adapter-wc-filter).

### Task 3.4: FermPageData — Archive Schema

- [ ] **Step 1: Define archive FermPageData fields**
  ```php
  'collection' => ['title' => '...', 'description' => '...', 'image' => '...'],
  'products' => [...],
  'filters' => ['available' => [...], 'active' => [...]],
  'pagination' => ['currentPage' => 1, 'totalPages' => 12, 'totalProducts' => 240],
  'sort' => ['current' => 'bestselling', 'options' => [...]],
  ```

- [ ] **Step 2: Map to AUREON adapters**
- [ ] **Step 3: Test output**

### Task 3.5: JS — Archive Behavior

**Files:**
- Create: `frontend/designs/fermliving/js/ferm-archive.js`

- [ ] **Step 1: Extract filter/sort/pagination JS**
- [ ] **Step 2: Remove Shopify filter API**
- [ ] **Step 3: Replace with WooCommerce/AUREON AJAX**
- [ ] **Step 4: Write ferm-archive.js**

### Task 3.6: Asset Extraction — Archive Assets

- [ ] **Step 1: Copy product images from frozen source**
- [ ] **Step 2: Add to manifest**
- [ ] **Step 3: Verify**

### Task 3.7: Archive Visual Validation

- [ ] **Step 1: Load collection page**
  Route: `/collections/furniture` (reference) → WP equivalent
- [ ] **Step 2–8: Standard validation (DOM, FermPageData, CSS, JS, 1440, 390, 16-point gate)**
- [ ] **Step 9: Commit**
  ```bash
  git commit -m "ferm: port archive (grid, filters, sorting, pagination)"
  ```
  Record commit hash: `____________________`

**Rollback point:** Phase 2 commit or this commit.

---

## Phase 4 — Product / PDP

**Objective:** Port product detail page with gallery, info, variants, ATC, accordions, recommendations.

**Depends on:** Phase 3 (archive complete).

### Task 4.1: Source Audit — Extract Frozen Product DOM

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\products\*.html`

- [ ] **Step 1: Extract product gallery DOM**
  Copy: main image container, thumbnail strip, image transitions.

- [ ] **Step 2: Extract product info DOM**
  Copy: title, price, variant selector, ATC button, description, accordion sections.

- [ ] **Step 3: Extract recommendations DOM**
  Copy: related products grid.

- [ ] **Step 4: Extract mobile product DOM**
  Verify mobile-specific layout changes.

- [ ] **Step 5: Document all patterns**

### Task 4.2: CSS — Product Dependencies

**Files:**
- Create: `frontend/designs/fermliving/css/ferm-product.css`

- [ ] **Step 1: Identify product CSS**
  Gallery, info layout, variant selector, accordion, recommendations.

- [ ] **Step 2: Extract rules**
- [ ] **Step 3: Write ferm-product.css**

### Task 4.3: PHP Templates — Product

**Files:**
- Modify: `frontend/designs/fermliving/components/product/gallery.php`
- Modify: `frontend/designs/fermliving/components/product/info.php`
- Create: `frontend/designs/fermliving/components/product/accordion.php`
- Create: `frontend/designs/fermliving/components/product/recommendations.php`

- [ ] **Step 1: Rewrite gallery.php**
  Use frozen source DOM. Dynamic: `$product['images']` array.

- [ ] **Step 2: Rewrite info.php**
  Use frozen source DOM. Dynamic: `$product['title']`, `$product['price']`, `$product['variants']`, `$product['options']`, `$product['stock']`.

- [ ] **Step 3: Create accordion.php**
  Use frozen source DOM. Dynamic: `$product['description']`, spec details.

- [ ] **Step 4: Create recommendations.php**
  Use frozen source DOM. Dynamic: `$recommendations` array.

- [ ] **Step 5: Connect to WooCommerce**
  Product data from WooCommerce product post. Variations from WC variations. ATC via WC cart AJAX.

### Task 4.4: FermPageData — Product Schema

- [ ] **Step 1: Define product FermPageData fields**
  ```php
  'product' => [
      'title' => '...',
      'price' => ['amount' => 1299, 'formatted' => '1,299.00 kr', 'currency' => 'DKK'],
      'compareAtPrice' => null,
      'description' => '...',
      'images' => [...],
      'variants' => [...],
      'options' => [...],
      'stock' => ['available' => true],
      'vendor' => 'Ferm Living',
      'tags' => [...],
      'sku' => 'FL-123',
  ],
  'recommendations' => [...],
  'breadcrumbs' => [...],
  ```

- [ ] **Step 2: Map to WooCommerce product data**
- [ ] **Step 3: Verify structured money format**
- [ ] **Step 4: Test output**

### Task 4.5: JS — Product Behavior

**Files:**
- Create: `frontend/designs/fermliving/js/ferm-product.js`

- [ ] **Step 1: Extract gallery JS**
  Image switching, zoom, thumbnail navigation.

- [ ] **Step 2: Extract variant selector JS**
  Option selection, price update, stock update, image per variant.

- [ ] **Step 3: Extract ATC JS**
  Remove Shopify `/cart/add.js`. Replace with WooCommerce AJAX add-to-cart.

- [ ] **Step 4: Extract accordion JS**
- [ ] **Step 5: Write ferm-product.js**

### Task 4.6: Asset Extraction — Product Assets

- [ ] **Step 1: Copy product images from frozen source**
- [ ] **Step 2: Add to manifest**
- [ ] **Step 3: Verify**

### Task 4.7: Product Visual Validation

- [ ] **Step 1: Load product page**
  Route: `/products/[sample]` (reference) → WP equivalent
- [ ] **Step 2–8: Standard validation**
- [ ] **Step 9: Commit**
  ```bash
  git commit -m "ferm: port product (gallery, info, variants, ATC, recommendations)"
  ```
  Record commit hash: `____________________`

**Rollback point:** Phase 3 commit or this commit.

---

## Phase 5 — Content

**Objective:** Port about, blog, article, contact pages.

**Depends on:** Phase 4 (product complete).

### Task 5.1: Source Audit — Content Pages

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\pages\about*.html`
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\blogs\*.html`
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\pages\contact*.html`

- [ ] **Step 1: Extract about page DOM**
- [ ] **Step 2: Extract blog listing DOM**
- [ ] **Step 3: Extract article page DOM**
- [ ] **Step 4: Extract contact page DOM**
- [ ] **Step 5: Document all patterns**

### Task 5.2: CSS — Content Dependencies

**Files:**
- Create: `frontend/designs/fermliving/css/ferm-content.css`

- [ ] **Step 1: Identify content CSS**
- [ ] **Step 2: Extract rules**
- [ ] **Step 3: Write ferm-content.css**

### Task 5.3: PHP Templates — Content

**Files:**
- Create: `frontend/designs/fermliving/templates/page-about.php`
- Create: `frontend/designs/fermliving/templates/page-blog.php`
- Create: `frontend/designs/fermliving/templates/page-article.php`
- Create: `frontend/designs/fermliving/templates/page-contact.php`

- [ ] **Step 1: Create about template**
  Use frozen source DOM. Dynamic: WordPress page content.

- [ ] **Step 2: Create blog template**
  Use frozen source DOM. Dynamic: WordPress post loop.

- [ ] **Step 3: Create article template**
  Use frozen source DOM. Dynamic: WordPress single post content, author bio.

- [ ] **Step 4: Create contact template**
  Use frozen source DOM. Dynamic: Contact form, office info.

### Task 5.4: FermPageData — Content Schema

- [ ] **Step 1: Define content FermPageData fields**
  Page-specific data from WordPress post/page queries.

- [ ] **Step 2: Map to WordPress data**
- [ ] **Step 3: Test output**

### Task 5.5: JS — Content Behavior

**Files:**
- Create: `frontend/designs/fermliving/js/ferm-content.js`

- [ ] **Step 1: Extract content JS behavior**
  Blog carousel, article reading progress, scroll animations.

- [ ] **Step 2: Write ferm-content.js**

### Task 5.6: Asset Extraction — Content Assets

- [ ] **Step 1: Copy blog/editorial images**
- [ ] **Step 2: Add to manifest**
- [ ] **Step 3: Verify**

### Task 5.7: Content Visual Validation

- [ ] **Step 1–8: Standard validation for each content page**
- [ ] **Step 9: Commit**
  ```bash
  git commit -m "ferm: port content (about, blog, article, contact)"
  ```
  Record commit hash: `____________________`

**Rollback point:** Phase 4 commit or this commit.

---

## Phase 6 — Commerce

**Objective:** Port cart, checkout, account, search, 404 pages.

**Depends on:** Phase 5 (content complete).

### Task 6.1: Source Audit — Commerce Pages

**Files:**
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cart.html`
- Read: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\account\*.html`

- [ ] **Step 1: Extract cart page DOM**
- [ ] **Step 2: Extract account page DOM**
- [ ] **Step 3: Extract search page DOM**
- [ ] **Step 4: Extract 404 page DOM**
- [ ] **Step 5: Document all patterns**

### Task 6.2: CSS — Commerce Dependencies

**Files:**
- Create: `frontend/designs/fermliving/css/ferm-commerce.css`

- [ ] **Step 1: Identify commerce CSS**
- [ ] **Step 2: Extract rules**
- [ ] **Step 3: Write ferm-commerce.css**

### Task 6.3: PHP Templates — Commerce

**Files:**
- Create: `frontend/designs/fermliving/templates/page-cart.php`
- Create: `frontend/designs/fermliving/templates/page-checkout.php`
- Create: `frontend/designs/fermliving/templates/page-account.php`
- Create: `frontend/designs/fermliving/templates/page-search.php`
- Create: `frontend/designs/fermliving/templates/page-404.php`

- [ ] **Step 1: Create cart template**
  Use frozen source DOM. Dynamic: WooCommerce cart data.

- [ ] **Step 2: Create checkout template**
  Use frozen source DOM. Dynamic: WooCommerce checkout form.

- [ ] **Step 3: Create account template**
  Use frozen source DOM. Dynamic: WordPress user data (minimal).

- [ ] **Step 4: Create search template**
  Use frozen source DOM. Dynamic: WordPress search results.

- [ ] **Step 5: Create 404 template**
  Use frozen source DOM. Static content.

### Task 6.4: FermPageData — Commerce Schema

- [ ] **Step 1: Define cart FermPageData fields**
  ```php
  'cart' => [
      'items' => [...],
      'total' => ['amount' => ..., 'formatted' => '...', 'currency' => '...'],
      'shipping' => '...',
      'discounts' => [...],
  ],
  ```

- [ ] **Step 2: Define account FermPageData fields**
  ```php
  'customer' => [
      'isLoggedIn' => true,
      'displayName' => '...',
  ],
  ```

- [ ] **Step 3: Define search FermPageData fields**
  ```php
  'search' => [
      'query' => '...',
      'results' => [...],
      'totalResults' => 24,
  ],
  ```

- [ ] **Step 4: Verify no private customer data exposed**
  Confirm: no passwords, no full order history, no addresses in FermPageData.

- [ ] **Step 5: Test output**

### Task 6.5: JS — Commerce Behavior

**Files:**
- Create: `frontend/designs/fermliving/js/ferm-commerce.js`

- [ ] **Step 1: Extract cart JS**
  Remove Shopify `/cart.js`, `/cart/add.js`, `/cart/update.js`. Replace with WooCommerce AJAX.

- [ ] **Step 2: Extract search JS**
  Remove Shopify predictive search. Replace with WordPress/Woo REST endpoint.

- [ ] **Step 3: Extract account JS**
  Remove Shopify customer API. Replace with WordPress auth.

- [ ] **Step 4: Write ferm-commerce.js**

### Task 6.6: Asset Extraction — Commerce Assets

- [ ] **Step 1: Copy commerce-specific assets**
- [ ] **Step 2: Add to manifest**
- [ ] **Step 3: Verify**

### Task 6.7: Commerce Visual Validation

- [ ] **Step 1–8: Standard validation for each commerce page**
- [ ] **Step 9: Commit**
  ```bash
  git commit -m "ferm: port commerce (cart, checkout, account, search, 404)"
  ```
  Record commit hash: `____________________`

**Rollback point:** Phase 5 commit or this commit.

---

## Phase 7 — Full Visual Regression

**Objective:** Compare all page families at all widths against frozen source.

**Depends on:** Phase 6 (all families complete).

### Task 7.1: Screenshot Capture — All Routes

- [ ] **Step 1: Define route matrix**
  ```
  REFERENCE ROUTE         PAGE FAMILY      WP TARGET ROUTE
  /                       Homepage         /
  /collections/all        Archive/PLP      /product-category/all/
  /collections/furniture  Archive/PLP      /product-category/furniture/
  /products/[sample]      Product/PDP      /product/[sample]/
  /blogs/journal          Blog             /blog/
  /blogs/journal/[slug]   Article          /blog/[slug]/
  /pages/about            About            /about/
  /pages/contact          Contact          /contact/
  /cart                   Cart             /cart/
  /account                Account          /account/
  /search?q=table         Search           /?s=table
  /nonexistent            404              /nonexistent-page
  ```

- [ ] **Step 2: Capture 1440px screenshots for all routes**
- [ ] **Step 3: Capture 1024px screenshots for all routes**
- [ ] **Step 4: Capture 768px screenshots for all routes**
- [ ] **Step 5: Capture 390px screenshots for all routes**

### Task 7.2: Visual Comparison

- [ ] **Step 1: Compare each screenshot against frozen source**
  For each route + width combination:
  - Same DOM structure
  - Same layout
  - Same typography
  - Same spacing
  - Same images
  - Same colors
  - Same responsive behavior

- [ ] **Step 2: Document any differences**
  Acceptable: backend implementation differences with same DOM output, minor animation timing (±50ms), WordPress URLs.
  Not acceptable: missing sections, wrong layout, wrong typography, missing images, broken responsive, AETHER classes, Shopify markup.

- [ ] **Step 3: Fix any failures**
  Return to the failed phase, fix, re-capture, re-compare.

- [ ] **Step 4: Commit regression results**
  ```bash
  git commit -m "test: complete ferm visual regression"
  ```
  Record commit hash: `____________________`

---

## Phase 8 — Luxury Isolation

**Objective:** Verify Ferm and Luxury packs are fully isolated.

**Depends on:** Phase 7 (visual regression complete).

### Task 8.1: Luxury Isolation Test

- [ ] **Step 1: Switch to Luxury pack**
  Activate Luxury design pack in WordPress.

- [ ] **Step 2: Load same routes**
  Navigate to each route with Luxury active.

- [ ] **Step 3: Verify zero Ferm artifacts**
  ```
  No Ferm CSS loaded
  No Ferm JS loaded
  No Ferm assets loaded
  No Ferm HTML rendered
  No FermPageData present
  ```

- [ ] **Step 4: Verify Luxury renders correctly**
  AETHER components work normally.

- [ ] **Step 5: Switch back to Ferm**
  Reactivate Ferm pack.

- [ ] **Step 6: Verify Ferm returns correctly**
  All Ferm behavior restored.

- [ ] **Step 7: Commit isolation results**
  ```bash
  git commit -m "test: ferm luxury isolation verified"
  ```

---

## Phase 9 — Final 100/100 Acceptance

**Objective:** Final verification of all acceptance criteria.

**Depends on:** Phase 8 (isolation complete).

### Task 9.1: Final Gate Checklist

- [ ] **VISUAL REGRESSION:**
  - [ ] All families — 1440px match
  - [ ] All families — 390px match
  - [ ] All families — 1024px match
  - [ ] All families — 768px match

- [ ] **FUNCTIONAL:**
  - [ ] Product navigation works
  - [ ] Collection navigation works
  - [ ] Add to cart works
  - [ ] Cart operations work
  - [ ] Search works
  - [ ] Filters work
  - [ ] Sorting works
  - [ ] Pagination works
  - [ ] Mobile nav works
  - [ ] Mega menu works
  - [ ] Search overlay works
  - [ ] Account works

- [ ] **ASSETS:**
  - [ ] All fonts load
  - [ ] All images load
  - [ ] All CSS loads
  - [ ] All JS loads
  - [ ] No 404 errors
  - [ ] No mixed content
  - [ ] assets-manifest.json complete and verified

- [ ] **PERFORMANCE:**
  - [ ] No unnecessary render-blocking resources
  - [ ] Critical CSS handled correctly
  - [ ] No FOIT
  - [ ] Acceptable LCP/CLS

- [ ] **ISOLATION:**
  - [ ] Ferm active → only Ferm assets
  - [ ] Luxury active → zero Ferm artifacts
  - [ ] Switch back → Ferm returns correctly

- [ ] **CORE INTEGRITY:**
  - [ ] AUREON core untouched (no Ferm-specific changes)
  - [ ] WooCommerce logic untouched
  - [ ] No Shopify API calls
  - [ ] No Shopify markup
  - [ ] No Clerk.io references
  - [ ] No legacy Ferm contamination
  - [ ] Security nonces present for mutations
  - [ ] No database migration with reference data

### Task 9.2: Final Release Decision

- [ ] **Step 1: All checks pass?**
  If YES → proceed to merge.
  If NO → document failure, return to failed phase, fix, re-verify.

- [ ] **Step 2: Merge to main**
  ```bash
  git checkout main
  git merge feature/ferm-premium-frontend
  ```

- [ ] **Step 3: Final commit**
  ```bash
  git commit -m "feat(ferm): premium frontend port — 100/100 acceptance"
  ```

---

## Appendix A: File Map

### New Files Created

```
frontend/designs/fermliving/
├── css/
│   ├── ferm-shell.css
│   ├── ferm-homepage.css
│   ├── ferm-archive.css
│   ├── ferm-product.css
│   ├── ferm-content.css
│   └── ferm-commerce.css
├── js/
│   ├── ferm-shell.js
│   ├── ferm-homepage.js
│   ├── ferm-archive.js
│   ├── ferm-product.js
│   ├── ferm-content.js
│   └── ferm-commerce.js
├── assets/
│   ├── fonts/
│   ├── hero/
│   ├── categories/
│   ├── editorial/
│   ├── rooms/
│   ├── products/
│   └── ...
├── assets-manifest.json
├── templates/
│   ├── archive-product.php
│   ├── page-about.php
│   ├── page-blog.php
│   ├── page-article.php
│   ├── page-contact.php
│   ├── page-cart.php
│   ├── page-checkout.php
│   ├── page-account.php
│   ├── page-search.php
│   └── page-404.php
└── sections/
    ├── section-hero.php
    ├── section-categories.php
    ├── section-editorial-split.php
    ├── section-bestsellers.php
    ├── section-room-grid.php
    └── section-newsletter.php
```

### Modified Files

```
frontend/designs/fermliving/
├── components/
│   ├── shell/
│   │   ├── announcement.php
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── mobile-chrome.php
│   ├── cards/
│   │   └── product.php
│   └── product/
│       ├── gallery.php
│       ├── info.php
│       ├── accordion.php (new)
│       └── recommendations.php (new)
├── composer.php
└── manifest.json
```

### Frozen Source Reference

```
C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\
├── index.html
├── collections\
├── products\
├── blogs\
├── pages\
├── cart.html
├── account\
└── cdn\shop\t\164\assets\
```

## Appendix B: Rollback Points

| Phase | Commit | Rollback To |
|-------|--------|-------------|
| Phase 0 | baseline | Previous HEAD |
| Phase 1 | shell | Phase 0 |
| Phase 2 | homepage | Phase 1 |
| Phase 3 | archive | Phase 2 |
| Phase 4 | product | Phase 3 |
| Phase 5 | content | Phase 4 |
| Phase 6 | commerce | Phase 5 |
| Phase 7 | regression | Phase 6 |
| Phase 8 | isolation | Phase 7 |
| Phase 9 | acceptance | Phase 8 |

## Appendix C: Core Modification Protocol

If any phase requires an AUREON core change:

```
1. STOP implementation
2. Document: why pack-only cannot solve it
3. Document: exact generic change required
4. Document: affected files
5. Document: regression risk
6. Wait for explicit user approval
7. Only then implement the minimal generic change
8. Re-run full isolation test
```

No silent core changes.

---

## END OF PLAN
