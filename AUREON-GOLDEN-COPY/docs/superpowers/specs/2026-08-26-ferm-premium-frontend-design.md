# Ferm Premium Frontend Port — Design Specification

**Date:** 2026-08-26
**Status:** Approved with 7 corrections — Ready for implementation plan
**Source:** Frozen Ferm Living frontend (SiteOne-Crawler)
**Target:** WordPress/AUREON Ferm design pack

---

## 1. Objective

Port the frozen Ferm Living frontend into the Ferm design pack as a **premium frontend application layer** with a thin dynamic bridge to WordPress/WooCommerce/AUREON.

**Core principle:**
```
PHP = server-side rendering + canonical→Ferm data bridge
HTML/CSS/JS = Ferm presentation (copied from frozen source)
```

PHP is NOT the visual design authoring mechanism. The frozen Ferm frontend IS the visual source of truth.

---

## 2. Architecture

### 2.1 Data Flow

```
WordPress / WooCommerce
        ↓
AUREON canonical data (adapters)
        ↓
Ferm presentation mapper
        ↓
┌───────┴───────┐
↓               ↓
Server-rendered    window.FermPageData
Ferm HTML          (page-scoped JSON)
↓               ↓
└───────┬───────┘
        ↓
Ferm JS (enhances/interacts)
        ↓
AUREON/WooCommerce mutations
        ↓
CLIENT UI/UX
```

### 2.2 Integration Boundary

```
Shopify-specific behavior → REMOVED → WordPress/AUREON equivalent

Shopify cart API         → WooCommerce/AUREON cart contract
Shopify search           → WordPress/Woo/AUREON search contract
Shopify customer/account → WordPress/Woo customer contract
Clerk.io recommendations → Reference/demo provider or AUREON recommendation contract
Liquid JSON              → window.FermPageData
```

### 2.3 Rules

- **PHP:** Server-rendered semantic HTML + canonical→Ferm data bridge. NOT visual authoring.
- **HTML/CSS/JS:** Preserve the frozen Ferm presentation contract exactly; adapt only integration boundaries. Replace Shopify/Liquid/business-specific dependencies.
- **AUREON core:** No Ferm-specific changes. Generic, minimal, reusable extensions allowed.
- **WooCommerce:** Business logic untouched. Product, cart, checkout, customer, stock, pricing remain platform responsibilities.
- **Legacy Ferm pack:** Preserved until all tests pass. Never active/resolvable by production design resolver.
- **Frozen source:** `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com` treated as immutable `FERM_REFERENCE_V1`.
- **Database:** WooCommerce database never replaced with Ferm reference data.

---

## 3. Page Families

### 3.1 Global Shell (shared by all routes)

```
announcement bar
header (logo left, nav center, icons right)
mega menu
search overlay
mobile navigation drawer
footer (newsletter, columns, legal)
```

### 3.2 Page Families

| # | Family | Frozen Source | Dynamic Data |
|---|--------|--------------|--------------|
| 1 | Homepage | `index.html` main content | Hero, categories, products, editorial, rooms |
| 2 | Archive/PLP | `collections/*.html` | Product grid, filters, sorting, pagination |
| 3 | Product/PDP | `products/*.html` | Title, price, gallery, variants, stock, reviews, related |
| 4 | Blog | `blogs/*.html` | Post list, categories, pagination |
| 5 | Article | `blogs/*/articles/*.html` | Article content, author, related |
| 6 | About | `pages/about*.html` | Page content, team, values |
| 7 | Contact | `pages/contact*.html` | Form, office info |
| 8 | Cart/Checkout | `cart.html` | Cart items, totals, shipping |
| 9 | Account | `account/*.html` | Customer data, orders |
| 10 | Search/404 | Search, 404 pages | Search results |

### 3.3 Porting Rule Per Family

```
1. Extract exact DOM from frozen source
2. Map DOM to PHP template structure
3. Identify dynamic data fields
4. Map fields to FermPageData schema
5. Connect to AUREON adapter data
6. Validate at 1440px + 390px
```

### 3.4 What Stays Identical

- DOM structure (semantic/visual/interaction contract nodes)
- CSS classes (where frozen CSS depends on them)
- Layout, grid, spacing, typography
- Responsive breakpoints and behavior
- Animation hooks and interaction states
- Required data attributes for JS behavior

### 3.5 What Gets Replaced

- Shopify Liquid → WordPress PHP
- Shopify product queries → WooCommerce product queries
- Shopify cart → WooCommerce cart
- Shopify checkout → WooCommerce checkout
- Shopify customer API → WordPress user
- Shopify URLs → WordPress permalinks
- Shopify-specific data state → FermPageData
- Clerk.io → AUREON or reference data

---

## 4. CSS Strategy

### 4.1 Source

Frozen compiled CSS from `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\`.

**No Tailwind rebuild. No manual CSS expansion.**

### 4.2 Extraction Process

```
Frozen source CSS files
        ↓
Identify required CSS by page family
        ↓
Extract compiled utility classes + component styles
        ↓
Scope to .design-fermliving where needed
        ↓
Preserve exact responsive breakpoints
        ↓
Self-host in ferm pack css/ directory
```

### 4.3 CSS Classification

| Category | Action |
|----------|--------|
| Tailwind utilities (`fixed`, `z-[12]`, `w-full`, `flex`, `tab_l:grid-12`) | Copy verbatim |
| Component styles (header, mega menu, product card, gallery, footer) | Copy verbatim |
| Typography/font declarations (`@font-face`) | Copy verbatim, self-host fonts |
| Responsive rules (`@media` queries) | Copy verbatim |
| CSS custom properties (`--site-max-width`, tokens) | Copy verbatim |
| Shopify-specific selectors | Exclude |
| Third-party embedded (Clerk.io, analytics CSS) | Exclude |

### 4.4 Scoping Rules

- Scope utility classes to `.design-fermliving` where the generated DOM is inside that scope
- Verify specificity, cascade order, pseudo-elements, `@keyframes`, `@font-face`, `@supports`, CSS variables, `@media`, `@container` are not naively prefixed
- Handle global CSS/reset/base styles carefully — classify as platform-safe reset vs Ferm-specific presentation
- Do not create conflicting reset layers between Ferm and AUREON

### 4.5 Dependency Map

```
page family → component → required classes → required CSS

Homepage → hero, categories, editorial, products, rooms
Product → gallery, info, variants, recommendations
Archive → grid, filters, sorting, pagination
Shell → header, mega menu, search, mobile nav, footer
```

Only copy CSS actually required by implemented page families.

---

## 5. JS Strategy

### 5.1 Source

Frozen JS from `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\`.

### 5.2 Extraction Process

```
Frozen source JS files
        ↓
Inventory each file → behavior mapping
        ↓
Classify: keep / adapt / remove
        ↓
Remove Shopify API calls
        ↓
Replace with AUREON/WooCommerce contracts
        ↓
Self-host in ferm pack js/ directory
```

### 5.3 JS File Inventory Template

```
FILE: [filename]
BEHAVIOR: [what it does]
DEPENDENCIES: [what it requires]
SHOPIFY DEPENDENCY: [yes/no — which APIs]
ACTION: [keep / adapt / remove]
TARGET: [ferm pack filename]
```

### 5.4 Behavior Classification

| Category | Behaviors | Shopify Dependency | Action |
|----------|-----------|-------------------|--------|
| Shell/Chrome | Header hide/show, mega menu, mobile nav, search overlay | Liquid menu data, cart count | Replace data source → FermPageData |
| Carousel/Gallery | Embla/Swiper init, product gallery, image transitions | None | Copy verbatim |
| Animation | GSAP ScrollTrigger, reveal animations, hover effects | None | Copy verbatim |
| Scroll | Lenis smooth scroll, parallax, sticky behavior | None | Copy verbatim |
| Product | Variant selector, ATC, accordion, size guide | Shopify cart/add.js, product JSON | Replace with WooCommerce AJAX |
| Cart | Cart drawer, line item updates, quantity changes | Shopify cart API | Replace with WooCommerce cart |
| Search | Predictive search, results rendering | Shopify predictive search | Replace with WordPress/Woo search |
| Account | Login, register, order history | Shopify customer API | Replace with WordPress auth |
| Recommendations | Product recommendations | Clerk.io | Replace with AUREON or reference data |

### 5.5 JS Classification per File

```
PURE PRESENTATION — copy as-is
PLATFORM ADAPTER — adapt to AUREON/WooCommerce
SHOPIFY BUSINESS LOGIC — remove
THIRD-PARTY APP — remove/replace
```

### 5.6 Integration Boundary

```
Animation/motion     → No platform dependency, copy as-is
UI state             → No platform dependency, copy as-is
DOM interaction      → No platform dependency, copy as-is
Data rendering       → Read from FermPageData
Cart operations      → Call AUREON/Woo AJAX
Search operations    → Call WordPress/Woo REST
Account operations   → Call WordPress auth
Recommendations      → Call AUREON/references
```

### 5.7 Library Deduplication

Before loading any JS library, check AUREON compatibility:

| Library | Check | Rule |
|---------|-------|------|
| GSAP | Version, API surface, plugins | Use AUREON version if API-compatible |
| Lenis | Version, initialization | Use AUREON version if compatible |
| Swiper/Embla | Version, modules | Use AUREON version if compatible |
| Three.js | Version, features | Use AUREON version if compatible |

**Rule:** No duplicate library when compatible. No forced reuse when incompatible. Test version + API + initialization + CSS dependency before reusing.

### 5.8 Server-Side Rendering Rule

PHP renders the initial HTML. Ferm JS enhances it.

```
PHP renders:
  - Full semantic HTML structure
  - Correct DOM elements with classes
  - Meta tags, structured data
  - Font declarations
  - Critical above-the-fold CSS inline (minimal subset only — NOT full Ferm CSS)
  - Full compiled Ferm CSS loaded as self-hosted pack asset

FermPageData provides:
  - Dynamic values for JS hydration
  - State for interactive components
  - Data for client-side filtering/sorting

Ferm JS enhances:
  - Animations
  - Interactions
  - State transitions
  - Async updates (cart, search, filters)
```

JS must NOT rebuild the entire initial DOM from JSON.

---

## 6. Data Bridge (FermPageData)

### 6.1 Structure

Page-scoped, minimal, public-safe. One payload per page load.

```json
{
  "version": 1,
  "schema": "fermliving-page",
  "design": "fermliving",
  "page": {
    "type": "product|collection|homepage|blog|article|page|cart|account|search|404",
    "id": 123,
    "slug": "product-name",
    "url": "/products/product-name"
  },
  "settings": {
    "currency": "DKK",
    "currencySymbol": "kr",
    "siteMaxWidth": "INspect frozen compiled CSS for actual value — do NOT hardcode 1440 (viewport ≠ content max-width)",
    "locale": "en"
  },
  "navigation": {
    "menu": [...],
    "currentPath": "/furniture"
  },
  "cart": {
    "itemCount": 3,
    "total": { "amount": 2598, "formatted": "2,598.00 kr", "currency": "DKK" },
    "items": [...]
  },
  "customer": {
    "isLoggedIn": false
  }
}
```

### 6.2 Per-Page-Type Payloads

**Homepage:**
```json
{
  "page": { "type": "homepage" },
  "hero": { "title": "...", "image": "...", "link": "..." },
  "categories": [...],
  "products": { "bestsellers": [...], "newArrivals": [...] },
  "editorial": [...],
  "rooms": [...]
}
```

**Collection/Archive:**
```json
{
  "page": { "type": "collection", "slug": "furniture" },
  "collection": { "title": "Furniture", "description": "...", "image": "..." },
  "products": [...],
  "filters": { "available": [...], "active": [...] },
  "pagination": { "currentPage": 1, "totalPages": 12, "totalProducts": 240 },
  "sort": { "current": "bestselling", "options": [...] }
}
```

**Product/PDP:**
```json
{
  "page": { "type": "product", "id": 123 },
  "product": {
    "title": "Tray Table",
    "price": { "amount": 1299, "formatted": "1,299.00 kr", "currency": "DKK" },
    "compareAtPrice": null,
    "description": "...",
    "images": [...],
    "variants": [...],
    "options": [...],
    "stock": { "available": true },
    "vendor": "Ferm Living",
    "tags": [...],
    "sku": "FL-123"
  },
  "recommendations": [...],
  "breadcrumbs": [...]
}
```

**Cart:**
```json
{
  "page": { "type": "cart" },
  "cart": {
    "items": [
      {
        "id": 123,
        "title": "Tray Table",
        "price": { "amount": 1299, "formatted": "1,299.00 kr", "currency": "DKK" },
        "quantity": 2,
        "image": "...",
        "variant": "Black",
        "url": "/products/tray-table"
      }
    ],
    "total": { "amount": 2598, "formatted": "2,598.00 kr", "currency": "DKK" },
    "shipping": "Free",
    "discounts": [...]
  }
}
```

**Account:**
```json
{
  "page": { "type": "account" },
  "customer": {
    "isLoggedIn": true,
    "displayName": "..."
  }
}
```

**Search:**
```json
{
  "page": { "type": "search" },
  "search": {
    "query": "tray table",
    "results": [...],
    "totalResults": 24
  }
}
```

### 6.3 Shopify → FermPageData Mapping

| Frozen JS Expectation | Shopify Source | AUREON Source | FermPageData Field |
|----------------------|---------------|---------------|-------------------|
| `product.title` | `product.title` | WooCommerce product post title | `product.title` |
| `product.price` | `product.price` | WooCommerce price | `product.price.amount` / `product.price.formatted` |
| `product.images` | `product.images` | WooCommerce gallery | `product.images` |
| `product.variants` | `product.variants` | WooCommerce variations | `product.variants` |
| `product.options` | `product.options` | WooCommerce attributes | `product.options` |
| `cart.item_count` | `cart.item_count` | WooCommerce cart count | `cart.itemCount` |
| `cart.total_price` | `cart.total_price` | WooCommerce cart total | `cart.total` |
| `cart.items` | `cart.items` | WooCommerce cart items | `cart.items` |
| `collection.products` | `collection.products` | WooCommerce product query | `products` |
| `collection.filters` | `collection.filters` | WooCommerce filter adapter | `filters` |
| `customer` | `customer` | WordPress user | `customer` |
| `navigation` | Liquid menu | `wp_nav_menu` / AUREON menu | `navigation.menu` |
| `predictive_search` | Shopify predictive search | WordPress/Woo search | `search.results` |

### 6.4 Security Boundary

**In FermPageData (public):**
- Product data (title, price, images, description)
- Navigation structure
- Cart state (item count, totals)
- Page settings (currency, locale)
- Search results

**NOT in FermPageData:**
- Passwords
- Security nonces (use AUREON/Woo nonces separately)
- Payment information
- Internal API secrets
- Server credentials
- Full order history, addresses, or broad customer data (keep in authenticated WordPress/WooCommerce endpoint context only)

**Customer data rule:**
- All pages: `customer.isLoggedIn` only (and `customer.id` if required by UI)
- Account page: minimum fields actually rendered (e.g., `displayName`). Orders/addresses retrieved via authenticated WordPress endpoint, NOT exposed in public FermPageData.

**For mutations:** Use existing AUREON/WooCommerce AJAX/REST contracts with proper nonces.

**Nonce/endpoint mechanism:**
```
FermPageData          = content/state (public, read-only)
AUREON/WP runtime     = endpoint URLs + mutation nonce/config (already localized by platform)
```
Do NOT create a second authentication/data system for Ferm. Reuse the platform's existing localized nonce/endpoint bridge (e.g., `wp_localize_script` or equivalent AUREON mechanism).

### 6.5 Mapper Responsibility

The Ferm mapper is the ONLY layer that converts canonical AUREON/WooCommerce data into the Ferm presentation schema. JS must never need to understand WooCommerce's internal object structure.

---

## 7. Asset Strategy

### 7.1 Source

Frozen assets from `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\`.

### 7.2 Target

Self-host in `frontend/designs/fermliving/assets/`.

### 7.3 Asset Classification

| Category | Copy | Exclude |
|----------|------|---------|
| Core design (fonts, global CSS, global JS, logos, icons) | Yes | |
| Reference/demo content (hero, category, editorial, room, product images) | Yes | |
| Shopify-only runtime (cart scripts, Clerk.io, customer APIs, Liquid-only) | | Yes |
| Third-party app assets (analytics, tracking) | | Yes |
| Unrelated crawl artifacts | | Yes |

### 7.4 Rules

- Preserve content-hashed filenames for cache-busting
- Use AUREON pack URL mechanism for asset resolution
- No browser dependency on `https://fermliving.com/`
- No CORS dependency, no external CDN availability dependency
- Generate `assets-manifest.json` build artifact (see 7.5)
- Verify every referenced asset resolves (HTTP 200 + correct identity)
- Use design pack version for WordPress asset URLs

### 7.5 Asset Manifest

Generate `assets-manifest.json` at build time containing:

```json
{
  "referencePath": "cdn/shop/t/164/assets/image.jpg",
  "localPath": "assets/image.jpg",
  "hash": "sha256:...",
  "type": "image|font|css|js",
  "usedBy": ["homepage:hero", "shell:header"]
}
```

This provides a deterministic answer when visual comparison says an image is wrong. Every critical asset (hero images, category images, editorial images, product images, fonts) must be traceable from reference path → local path → page/component usage.

---

## 8. Implementation Order

### 8.1 Phase Sequence

```
Phase 0: Git checkpoint (freeze state, baseline commit)
Phase 1: Global shell (announcement, header, mega menu, search, mobile nav, footer)
  → 16-point gate → 1440 + 390 screenshots
Phase 2: Homepage (hero, categories, editorial, products, rooms)
  → 16-point gate → 1440 + 390 screenshots
Phase 3: Archive/PLP (grid, filters, sorting, pagination)
  → 16-point gate → 1440 + 390 screenshots
Phase 4: Product/PDP (gallery, info, variants, ATC, related)
  → 16-point gate → 1440 + 390 screenshots
Phase 5: Content (about, blog, article, contact)
  → 16-point gate → 1440 + 390 screenshots
Phase 6: Commerce (cart, checkout, account, search, 404)
  → 16-point gate → functional test
Phase 7: Full visual regression (all families, all widths)
Phase 8: Isolation testing (Ferm ↔ Luxury)
Phase 9: Final 100/100 acceptance
```

### 8.2 Per-Phase Gate (16 checks, all required)

```
1.  Correct route loads
2.  Correct page family/template renders
3.  Correct Ferm DOM/visual structure
4.  Required Ferm CSS applied (no missing classes)
5.  Ferm JS initialized (no console errors)
6.  Correct FermPageData schema/content
7.  1440px visual comparison — PASS
8.  390px visual comparison — PASS
9.  No AETHER class leakage
10. No Shopify runtime/API/markup dependency
11. No legacy Ferm pack assets/scripts
12. No duplicate JS library initialization
13. Server-rendered HTML works before JS enhancement
14. Fonts/CSS/assets load correctly
15. Critical assets are correct reference assets (identity verified)
16. No unexpected console/network errors
```

### 8.3 Three-Layer Proof

Every page family requires:

```
LAYER 1: Route + page family correctness
LAYER 2: DOM + data + asset correctness
LAYER 3: Screenshot visual parity
```

All three must pass.

### 8.4 Visual Comparison Criteria

**EXACT MATCH:**
- Same semantic/visual structure
- Same layout (grid, positioning)
- Same typography (font, size, weight)
- Same spacing (margins, paddings, gaps)
- Same colors (background, text, accent)
- Same responsive breakpoints
- Same interactive states

**ACCEPTABLE DIFFERENCES:**
- Backend implementation differences with same DOM output
- PHP-rendered vs Liquid-rendered (same result)
- Minor animation timing (±50ms)
- Shopify URLs replaced with WordPress equivalents

**NOT ACCEPTABLE:**
- Missing sections
- Wrong layout
- Wrong typography
- Missing images
- Broken responsive behavior
- AETHER classes appearing
- Shopify markup leaking

### 8.5 Route Correctness Gate

Before any screenshot is scored:

```
REFERENCE ROUTE (frozen Shopify URL)
        ↓
PAGE FAMILY (which template renders)
        ↓
WORDPRESS TARGET ROUTE (actual permalink)
```

Do NOT require WordPress to mimic Shopify URLs unless permalink configuration is deliberately designed that way. The page family determines the template; the WordPress permalink determines the URL.

Example:
```
REF: /collections/furniture
FAMILY: Archive/PLP
WP: /product-category/furniture/ (or whatever WP permalink config produces)
```

If reference URL and target URL resolve to different page families, stop. Do not score the screenshot.

---

## 9. Testing

### 9.1 Functional Tests

| Test | Verification |
|------|-------------|
| Product navigation | Product card → correct PDP |
| Collection navigation | Category → correct archive |
| Add to cart | ATC → cart updates → FermPageData updates |
| Cart operations | Update quantity, remove item, totals recalculate |
| Search | Input → results page → correct results |
| Filters | Apply filter → product grid updates |
| Sorting | Change sort → products reorder |
| Pagination | Navigate pages → correct products |
| Mobile nav | Hamburger → drawer opens → navigation works |
| Mega menu | Hover nav item → mega menu appears |
| Search overlay | Click search → overlay opens → input works |
| Account | Login/logout → state updates |

### 9.2 Isolation Testing

**Ferm active:**
- Ferm CSS loaded
- Ferm JS loaded
- Ferm assets loaded
- Ferm DOM rendered
- No AETHER component classes
- No Luxury CSS/JS

**Luxury active:**
- No Ferm CSS/JS/assets/HTML
- Luxury CSS/JS loaded
- AETHER components render normally

Switch packs and verify both directions.

### 9.3 Shopify Dependency Scan

Per phase, scan source/runtime for:

```
Shopify, ShopifyAPI, Liquid
predictive-search Shopify endpoint
/cart.js, /cart/add.js, /cart/update.js
customer/account Shopify endpoints
Clerk.io
Shopify section IDs
```

No Shopify runtime dependency allowed.

### 9.4 Legacy Contamination Check

Verify in active Ferm runtime:

```
no fermliving-legacy CSS
no fermliving-legacy JS
no fermliving-legacy components
no fermliving-legacy assets
```

### 9.5 Runtime Identity Gate

Before every phase:

```
ACTIVE DESIGN = fermliving
ACTIVE PACK = new fermliving
MANIFEST VERSION = expected
PACK PATH = expected
NO legacy pack active
```

### 9.6 Final 100/100 Acceptance

```
VISUAL REGRESSION:
  [ ] All families — 1440px match
  [ ] All families — 390px match
  [ ] All families — 1024px match
  [ ] All families — 768px match

FUNCTIONAL:
  [ ] All 12 functional tests pass

ASSETS:
  [ ] All fonts load
  [ ] All images load
  [ ] All CSS loads
  [ ] All JS loads
  [ ] No 404 errors
  [ ] No mixed content

PERFORMANCE:
  [ ] No unnecessary render-blocking resources
  [ ] Critical CSS handled correctly
  [ ] No FOIT
  [ ] Acceptable LCP/CLS

ISOLATION:
  [ ] Ferm active → only Ferm assets
  [ ] Luxury active → zero Ferm artifacts
  [ ] Switch back → Ferm returns correctly

CORE INTEGRITY:
  [ ] AUREON core untouched (no Ferm-specific changes)
  [ ] WooCommerce logic untouched
  [ ] No Shopify API calls
  [ ] No Shopify markup
  [ ] No Clerk.io references
  [ ] No legacy Ferm contamination
  [ ] Security nonces present for mutations
  [ ] No database migration with reference data

RELEASE GATE:
  TESTS PASS + SCREENSHOTS PASS + ROUTE PASS + CONTENT PASS + ASSET PASS = RELEASE
```

---

## 10. Safety

### 10.1 Freeze Protocol

Before any implementation:

```bash
git rev-parse HEAD
git status --short
```

Record commit hash, file status, branch name. This is the rollback point.

### 10.2 What Must Not Change

| Layer | Rule |
|-------|------|
| AUREON core | No Ferm-specific changes. Generic extensions allowed. |
| WooCommerce | Business logic untouched |
| Luxury pack | Zero changes |
| Legacy Ferm | Zero changes until full acceptance |
| Design resolver | No changes unless generic capability needed |
| Adapter architecture | No structural changes |
| Security | No weakening |
| WooCommerce database | Never replaced with Ferm reference data |
| Frozen source | Immutable FERM_REFERENCE_V1 |

### 10.3 Core Modification Gate

If a core modification appears necessary:

```
1. STOP
2. Document why pack-only cannot solve it
3. Document exact generic change required
4. Document regression impact
5. Wait for explicit approval
6. Only then implement
```

### 10.4 Git Strategy

```
main (stable)
        ↓
feature/ferm-premium-frontend (work branch)
        ↓
baseline: chore(frontend): freeze ferm reference rebuild baseline
Phase 1: ferm: port shell
Phase 2: ferm: port homepage
Phase 3: ferm: port archive
Phase 4: ferm: port product
Phase 5: ferm: port content
Phase 6: ferm: port commerce
Phase 7: test: complete ferm visual regression
        ↓
Final acceptance → merge to main
```

### 10.5 Rollback

If a phase fails irrecoverably:

```
1. Identify last clean checkpoint
2. git reset --hard [checkpoint-hash]
3. Verify AUREON/WooCommerce/Luxury/legacy intact
4. Reassess approach
5. New branch from clean state
6. Retry with revised strategy
```

### 10.6 Risk Register

| Risk | Mitigation |
|------|-----------|
| Wrong asset path | Verify all asset URLs resolve before checkpoint |
| Wrong dynamic binding | FermPageData schema validation per page type |
| Wrong route | Route-correctness gate before visual comparison |
| CSS conflict | Scope Ferm CSS, isolation test after each phase |
| JS dependency conflict | Library deduplication check, version compatibility |
| Duplicate library | Inventory before loading, AUREON compatibility check |
| Shopify markup leaking | Shopify dependency scan per phase |
| AETHER class leakage | Explicit AETHER check in 16-point gate |
| Cache/stale runtime | Cache-busting via pack version |
| Legacy contamination | Explicit legacy check in 16-point gate |
| Performance regression | Monitor critical resources |
| Breaking Luxury | Isolation test after every Ferm change |

### 10.7 Communication Protocol

**Phase complete:**
```
PHASE [N] COMPLETE
  Route: [url]
  Widths: 1440 ✓ | 1024 ✓ | 768 ✓ | 390 ✓
  Gate: 16/16 pass
  Commit: [hash]
  Notes: [caveats]
```

**Phase blocked:**
```
PHASE [N] BLOCKED
  Failed check: [name]
  Root cause: [description]
  Screenshot: [path]
  Proposed fix: [approach]
  Waiting for: [decision]
```

---

## 11. Acceptance Criteria

The frontend passes 100/100 when ALL of:

```
visual regression ✅
functional tests ✅
responsive (1440, 1024, 768, 390) ✅
assets (fonts, images, CSS, JS) ✅
interactions ✅
AETHER leaks 0 ✅
Luxury unchanged ✅
core integrity ✅
no Shopify dependency ✅
no legacy contamination ✅
server-rendered HTML works ✅
FermPageData correct ✅
```

**No "green tests, visually wrong" acceptance.**

Tests pass + Screenshots pass + Route pass + Content pass + Asset pass = RELEASE.

---

## END OF SPEC
