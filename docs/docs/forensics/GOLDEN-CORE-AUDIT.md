# AUREON — GOLDEN CORE AUDIT
## Audit Date: 2026-09-04
## Auditor: Forensic Pass (Direct Code Reading)
## Status: COMPLETE

---

## 1. EXECUTIVE SUMMARY

The Golden Core is the AUREON frontend engine. Its single most important file is
`ferm-page.php` — the Complete-Page Template runtime. This file reads a frozen
HTML file from an active design pack (Vineta), re-assembles it as a valid
WordPress HTML document, injects Customizer CSS, rewrites all relative paths and
Shopify-style links to WordPress URLs via inline JavaScript, then calls
`wp_head()` / `wp_footer()` to insert admin bar, WooCommerce scripts, analytics,
etc.

---

## 2. FILE VERSION ANALYSIS

### CRITICAL FINDING: TWO ferm-page.php FILES WITH SAME CONTENT

| Location | Bytes | Lines |
|----------|-------|-------|
| `aureon/ferm-page.php` (source) | 34,987 | 900 |
| `theme/aureon/ferm-page.php` (deployed) | 34,987 | 900 |
| `theme/aureon/ferm-page.php.old` (backup) | 25,948 | ~700 |

**FINDING**: The source and deployed versions are IDENTICAL at 34,987 bytes.
The old discrepancy (previously reported as 25,062 bytes) referred to
`ferm-page.php.old` — a backup of an older deployed version.

**VERDICT**: No version discrepancy exists between current source and deployed.
The `.old` backup is evidence of a significant code change at some prior point
(~10,000 bytes / ~200 lines were added). This is SAFE to note but not a blocker.

---

## 3. ARCHITECTURE OF ferm-page.php

### 3.1 Purpose
Serves a complete standalone HTML page from the active design pack (Vineta).
Bypasses the AETHER shell (header.php / footer.php). Controlled by
`"complete_page": true` in manifest.json.

### 3.2 Boot Sequence (lines 1–289)

```
1. ABSPATH guard
2. Check aether_is_complete_page_design() → reads manifest.json["complete_page"]
3. Get pack directory via aether_active_design_dir()
4. Call aureon_ferm_resolve_page() → returns HTML filename relative to pack dir
5. file_get_contents($pack_dir . $file) → load frozen HTML
6. Extract <html> and <body> attributes from frozen HTML
7. Echo <!DOCTYPE html> + <html lang="...">
8. Echo <head> with:
   a. charset meta
   b. Favicon: WordPress site_icon OR pack favicon.svg
   c. wp_head() → all WP/WC scripts, styles, admin bar
   d. Dynamic CSS from Customizer (color vars + font vars)
9. aureon_ferm_extract_body($html) → extract <body> content
10. aureon_ferm_rewrite_paths($body_content, $pack_url) → server-side path rewrite
11. Echo <body> with original body attributes
12. Echo body content
13. Echo footer bridge scripts:
    a. JS path rewriter: img[src] cdn/ → absolute
    b. MutationObserver for dynamic images
    c. Nav link rewriter (_vm map: html filenames → WP URLs)
    d. External CDN rewriter (struct.com CDN)
    e. Account page: enable login submit + fix lost-password link
    f. Logo bridge: replace frozen SVG with WordPress custom_logo
14. wp_footer()
15. Echo </body></html>
16. exit
```

### 3.3 Route Resolution (aureon_ferm_resolve_page)

**Primary path: manifest.json["pages"] map**
```
is_front_page() / is_home()       → pages["home"]      → "index.html"
is_product()                       → pages["products"]["_generic"] → "product-detail.html"
is_post_type_archive("product")   → pages["collections"]["default"] → "shop-default.html"
is_page("shop")                   → pages["collections"]["default"] → "shop-default.html"
is_tax("product_cat")             → pages["collections"]["default"] → "shop-default.html"
is_page($slug)                    → pages["pages"][$slug] or pages["static"][$slug]
is_home() || blog archive         → pages["blog"]       → "blog-grid-01.html"
is_single()                       → pages["blog_single"] → "blog-single.html"
is_search()                       → pages["search"]     → "shop-default.html"  ← ISSUE
is_cart()                         → pages["cart"]       → "view-cart.html"
is_checkout()                     → pages["checkout"]   → "checkout.html"
is_account_page()                 → pages["account"]    → "account-page.html"
```

**Fallback path (backward compat, no manifest):**
```
Front page                         → "index.html"
Product (slug exists)              → "products/$slug.html"
Product (slug not found)           → first file in products/ dir
Shop/archive                       → "collections/furniture.html"  ← HARDCODED SLUG
Product category ($slug exists)    → "collections/$slug.html"
Product category (not found)       → "collections/furniture.html"  ← HARDCODED
Pages (contact/about/store-locator)→ hardcoded page_map
Blog                               → "blogs/stories.html"
Search                             → "blogs/stories.html"  ← USES BLOG TEMPLATE
404                                → "404.html" or "pages/contact.html"
```

### 3.4 Customizer CSS Injection (lines 82–108)

```php
$color_map = [
    'aether_color_bg'           => '--bg',
    'aether_color_surface'      => '--surface',
    'aether_color_text'         => '--text',
    'aether_color_muted'        => '--muted',
    'aether_color_accent'       => '--accent',
    'aether_color_accent_hover' => '--accent-hover',
    'aether_color_border'       => '--border',
];
// + 'aether_font_heading' → '--font-heading'
// + 'aether_font_body'    → '--font-body'
```

Uses `get_option()` — reads from wp_options table directly, NOT `get_theme_mod()`.
This is intentional (theme-agnostic options key scheme).

**FINDING**: Only 9 CSS variables are injected here. Vineta uses many more
CSS variables. The rest come from Vineta's own CSS files (not Customizer).

### 3.5 Server-Side Path Rewriter (aureon_ferm_rewrite_paths)

Runs **only** for the `fermliving` design slug. For Vineta, the server-side
regex rewriter is **skipped** (live_cdn = '' for non-fermliving).

**FINDING**: For Vineta, ALL path rewriting happens client-side via the
JavaScript injected in the footer (lines 131–231). This means path rewrites
have a brief flash-of-broken-images (FOBI) window before JS runs.

### 3.6 Logo Bridge

```javascript
var logos = document.querySelectorAll('.header__logo,[data-header-logo]');
```

If the Vineta HTML does not use `.header__logo` or `data-header-logo`, the
logo bridge silently fails. Status: NEEDS RUNTIME VERIFICATION.

### 3.7 Account Page Bridge

```javascript
var f = document.getElementById('customer_login');
```

WooCommerce's login form uses `id="customer_login"`. ferm-page.php enables
the submit button and fixes the lost-password link. This only runs on
`is_account_page()` routes.

---

## 4. HELPER FUNCTIONS

### aureon_ferm_extract_body (lines 483–488)
- Regex: `/\<body[^\>]*\>(.*)\<\/body\>/si`
- Returns inner body content (without body tags)
- GREEDY MATCH: if HTML has multiple `</body>` tags, captures everything including the first (PHP PCRE handles this with `s` flag — captures all content through LAST `</body>`)

### aureon_ferm_extract_body_attrs (lines 500–534)
- Extracts html[] and body[] attribute arrays
- Safe attributes whitelist: `['id', 'data-template', 'data-money-format', 'data-country', 'data-shop', 'class']` + all `data-*`

### aureon_ferm_render_attrs (lines 543–554)
- Renders attrs with esc_attr() on both name and value — CORRECT

### aureon_ferm_rewrite_paths (lines 567–703)
- CDN rewriting only for `fermliving` design
- For Vineta: Rewrites collections/.html → /product-category/X, products/.html → /product/X, etc.
- FINDING: The server-side rewrite also runs for Vineta for link href rewrites (lines 664–703), but NOT for image src. This means:
  - Link hrefs: rewritten server-side AND client-side (duplicate, harmless)
  - Image srcs: rewritten client-side ONLY → FOBI window

---

## 5. DESIGN RESOLUTION LAYER (design.php)

### Active Design Detection
```php
// Resolution order:
// AETHER_DESIGN constant > 'aether_active_design' option > 'vineta' (default)
$design = $design ? $design : 'vineta';  // default is 'vineta' (not 'luxury')
```

**FINDING**: Default design is hardcoded as `'vineta'` (line 51 of design.php).
If `AETHER_DESIGN` constant is not defined AND `aether_active_design` option is
not set in the database, Vineta is used. This is correct for the current setup.

### Pack URL Construction
```php
return trailingslashit( content_url() ) . 'frontend/designs/' . $design . '/';
```

This returns: `http://localhost:8080/wp-content/frontend/designs/vineta/`

**CRITICAL FINDING**: The pack is expected to be inside `WP_CONTENT_DIR/frontend/designs/vineta/`.
But the actual source is at `aureon/frontend/designs/vineta/` (not in wp-content!).
For this to work, the `frontend/` directory must be INSIDE `wp-content/`, meaning
it is either:
1. Symlinked from aureon/ into wp-content/
2. Deployed/copied into wp-content/
3. Docker-mounted into the container's wp-content/

This is the ROOT CAUSE of all the 404 image errors in console-errors.txt.
The images EXIST in the source (`aureon/frontend/designs/vineta/images/...`) but
the URL `wp-content/frontend/designs/vineta/images/...` only works if the
deploy/mount is correctly set up.

---

## 6. ASSET PIPELINE (assets.php)

### Complete-Page Design (Vineta) Asset Loading
```php
if ( aether_is_complete_page_design() ) {
    // Only enqueue pack assets from manifest — no platform contamination.
    foreach ( manifest['assets']['css'] ) { aether_enqueue_pack_asset(...) }
    foreach ( manifest['assets']['js'] ) { aether_enqueue_pack_asset(...) }
    return;  // STOP — no platform Bootstrap/GSAP/Swiper from CDN
}
```

**FINDING**: For Vineta (complete_page = true), NO platform CDN libraries are
loaded from WordPress. All CSS/JS comes from the pack itself. This is correct by
design — Vineta ships its own Bootstrap (bootstrap.min.js in js/ dir).

**CRITICAL FINDING**: The manifest lists 16 JS files:
- swiper-bundle.min.js, lazysize.min.js, wow.min.js, photoswipe*.js,
  drift.min.js, nouislider.min.js, jquery-validate.js, zoom.js,
  multiple-modal.js, model-viewer.min.js, carousel.js, count-down.js,
  infinityslide.js, **shop.js**, **main.js** (with deps: jquery, aether-bootstrap-js)

**PROBLEM**: main.js depends on `aether-bootstrap-js` which is only enqueued
for non-complete-page designs! This dependency is UNRESOLVABLE in complete-page
mode. The manifest also does NOT include `jquery` or `bootstrap` as entries —
they are in the design's own JS files but not registered as WordPress script
handles. `aether-bootstrap-js` will never be enqueued for Vineta, so `main.js`
has an unresolvable dependency in WordPress's script loader.

---

## 7. RISK CLASSIFICATION

| Component | Risk Level | Notes |
|-----------|------------|-------|
| aureon_ferm_resolve_page() | SAFE | Manifest-first, graceful fallbacks |
| aureon_ferm_extract_body() | SAFE | Regex is safe for single-doc HTML |
| aureon_ferm_rewrite_paths() | SAFE | Only runs for fermliving design |
| Customizer CSS injection | SAFE | Escaping correct (esc_js for JS) |
| Logo bridge JS | MEDIUM-RISK | CSS selector may not match Vineta |
| Account bridge JS | SAFE | WC uses standard form ID |
| Nav link rewriter (_vm) | SAFE | Rewrites to site_url, no user input |
| Path URL construction | CORE-RISK | Requires correct Docker/deploy mount |
| Asset dependency (aether-bootstrap-js) | CORE-RISK | Unresolvable in complete-page mode |
| Server-side image rewrite (Vineta) | NOTE | Not applicable — client-side only |

---

## 8. FUNCTIONS DEFINED IN ferm-page.php

| Function | Purpose | Risk |
|----------|---------|------|
| `aureon_ferm_resolve_page()` | Route → HTML file | SAFE |
| `aureon_ferm_extract_body()` | Extract body from HTML | SAFE |
| `aureon_ferm_extract_body_attrs()` | Extract html/body attrs | SAFE |
| `aureon_ferm_render_attrs()` | Render attr string | SAFE |
| `aureon_ferm_rewrite_paths()` | Server-side CDN rewrite | SAFE (Vineta: no-op) |

---

## 9. WORDPRESS HOOKS/FILTERS

No `add_action()` or `add_filter()` calls in ferm-page.php itself.
It is a template file, not a plugin, executed directly by WordPress's
template hierarchy. The hooks are registered in the supporting views/ files:

- `design.php`: `add_filter('aureon_option_defaults', ...)`, `add_filter('body_class', ...)`
- `assets.php`: `add_action('wp_enqueue_scripts', 'aether_design_enqueue_assets', 20)`
- `loader.php`: includes all adapter files, section files, etc.

---

## 10. DEPENDENCIES

| Dependency | Type | Status |
|------------|------|--------|
| `aether_is_complete_page_design()` | Core function | MUST be defined |
| `aether_active_design_dir()` | Core function | MUST return valid dir |
| `aether_design_manifest()` | Core function | MUST parse manifest.json |
| `aureon_ferm_resolve_page()` | Local function | Defined in same file |
| `aether_pack_url()` | Core function | Used for JS path bridge |
| WooCommerce `is_cart()`, `is_checkout()`, `is_account_page()` | WC functions | Optional, guarded |
| `get_theme_mod('custom_logo')` | WP Customizer | Logo bridge |
| `wp_lostpassword_url()` | WP function | Account bridge |

---

## 11. DISCREPANCIES: SOURCE VS DOCUMENTATION

| Claim | Evidence | Verdict |
|-------|----------|---------|
| "ferm-page.php version mismatch" | aureon/ and theme/aureon/ are both 34,987 bytes | FALSE — both identical |
| "fermliving CDN rewriting active" | Lines 573–577: only if design === 'fermliving' | CORRECT for Vineta — CDN rewrite SKIPPED |
| "search uses shop template" | manifest["pages"]["search"] = "shop-default.html" | CONFIRMED — search serves shop HTML |
| "aether_active_design defaults to 'luxury'" | design.php line 51: default is 'vineta' | WRONG — default is vineta |
