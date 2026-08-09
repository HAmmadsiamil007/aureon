# FRONTEND DYNAMIC CONVERSION — IMPLEMENTATION PLAN (Phases A–F)

> **Version:** 1.0 · **Date:** 2026-08-08
> **Author:** Buffy (Freebuff) — verified against working tree, cross-checked with `aureon-doc/STATUS.md` (Phase 17, Stages 1–13) and the forensic baseline.
> **Purpose:** This is the execution plan for the **remaining** conversion work. The frontend is ~90% converted already; this plan closes the verified gaps. Every claim is reproducible — file paths and current code are quoted exactly so an independent reviewer (e.g. ChatGPT) can verify against the repo.

---

## 0. Context (what the verifier needs to know)

- **Repo layout:** `aureon/` (theme + plugin — **READ-ONLY per mission constraint**), `frontend/` (presentation engine — the ONLY layer we edit), `docs/` (this suite), `mu-plugins/`, `Report/`, `aureon-doc/`.
- **Mission:** convert the static premium frontend (`frontend/source/*.html`, design reference) into a fully data-driven AUREON frontend **without changing the design** and **without touching core theme/plugin**.
- **Verified architecture:** `WordPress/WC → aureon_get_option + tokens → 23 adapters → viewmodels → renderer (53-component manifest) → composer → 26 section templates → theme page templates`.
- **Audit output (already delivered):** `docs/FRONTEND_DYNAMIC_CONVERSION_BASELINE.md` (18 sections), plus 8 companion docs (contracts, matrices, reports). This plan is the **execution** layer on top of those.

### Verified non-goals (do NOT do)
- No edits to `aureon/theme/**` or `aureon/plugin/**` or `mu-plugins/**` — if a phase proves one is objectively required, produce a **stop-condition report** first (Problem / Evidence / Affected files / Options / Recommended / Risk / Core-change-required?).
- No redesign, no CSS restyle, no component-markup rewrites. Presentation files change **only** where the change is invisible in the happy path.
- No demo-data deletion (fallbacks are a feature for empty stores); we add a **master toggle** instead.
- No new WP/WC calls in components; no regex/HTML-string injection anywhere.

---

## 1. Execution summary (one table)

| Phase | Name | Risk | Files touched (all `frontend/`) | Commits |
|---|---|---|---|---|
| **A** | Animation hardening (Rule 7) | 🔴 HIGH | `assets/js/animations.js`, `assets/css/motion.css`, `components/shell/preloader.php` | A1–A2 |
| **B** | Data-source hardening | 🟠 MED | `adapters/adapter-wc-products.php`, `adapter-wc-filter.php`, `adapter-wc-categories.php`, `sections/section-shop-grid.php` | B1 |
| **C** | Customizer round-trip closure | 🟠 MED | `adapters/adapter-shell.php`, `adapter-site.php`, `adapter-contact.php`, `adapter-wc-categories.php`, `tokens/tokens.php` | C1–C3 |
| **D** | Demo-content policy | 🟡 LOW | `tokens/tokens.php`, `adapters/{adapter-wc-products,adapter-wc-categories,adapter-faq,adapter-testimonials,adapter-team,adapter-product,adapter-order}.php` | D1 |
| **E** | Verification suite (Playwright/a11y/perf/failure) | 🟡 LOW | `frontend/tests/**` (new), `tests/verify.sh` (extend) | E1–E3 |
| **F** | Styleguide + final reports | 🟡 LOW | `/styleguide/**` (new), `docs/**` | F1 |

Dependency graph: **A → B → C → D** (all independent of each other, but D must land after C so the demo toggle and settings bindings don't fight), **E** can start in parallel with B; **F** is last.

---

## 2. PHASE A — Animation hardening (Rule 7: animation must never control visibility)

### 2.1 The bug (verified, current code)

`frontend/assets/js/animations.js` (top of IIFE):

```js
var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (reducedMotion) {
  document.documentElement.classList.add('no-motion');
  return;
}

document.documentElement.classList.add('has-motion');

if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
  console.warn('AETHER Motion: GSAP or ScrollTrigger not loaded');
  return;
}
```

`frontend/assets/css/motion.css` (lines 8–16):

```css
html.has-motion [data-motion-text],
html.has-motion [data-reveal-item],
html.has-motion [data-image-reveal] { visibility: hidden; }
/* Use visibility:hidden to prevent layout shift and accidental clicks */
html.has-motion [data-reveal] { visibility: hidden; }
```

**Failure chain:** `animations.js` executes (JS on, reduced-motion off) → adds `has-motion` → CSS hides every `[data-reveal]`, `[data-reveal-item]`, `[data-motion-text]`, `[data-image-reveal]` → GSAP/ScrollTrigger CDN blocked (firewall/ad-blocker/CDN outage) → guard returns with only a `console.warn` → **nothing ever re-reveals the content**. Same failure for a runtime exception inside `init()` (no try/catch — elements after the throw point stay hidden).

This is the **single acceptance-criteria failure** in the baseline (mission §26: "Animation failure never hides content").

### 2.2 Target design

**Principle (progressive enhancement, mission §28 / Rule 7):** content is visible unless the full motion stack is present AND initialized successfully. Motion may only *enhance*, never gate.

1. **Check before hiding:** evaluate GSAP + ScrollTrigger availability *before* adding `has-motion`. If either is missing → add `no-motion`, return.
2. **Watchdog timer:** if `has-motion` was applied but init did not finish within a timeout (2.5 s), force `no-motion` (covers slow network where scripts arrive late, and deadlocks).
3. **Try/catch init:** wrap the entire `init()` body; on exception → `no-motion`.
4. **Idempotent kill-switch:** one helper `disableMotion()` that removes `has-motion` and adds `no-motion`; used by all fallbacks. `no-motion` CSS already force-visibles everything (`motion.css` lines 91+), so no new CSS needed for JS-side failures.
5. **Preloader no-JS edge:** `#preloader` is removed by `main.js` (line 742 `setTimeout(() => { preloader.remove(); }, 700)`). If JS is disabled the overlay persists → add a `noscript`-safe CSS kill inside the component (or `@media (scripting: none)`).
6. **Happy path untouched:** when GSAP + ScrollTrigger load and init succeeds, behavior is byte-identical to today (same presets, same triggers, same timings).

### 2.3 Exact change — `frontend/assets/js/animations.js`

Replace the opening block:

```js
// BEFORE
var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (reducedMotion) {
  document.documentElement.classList.add('no-motion');
  return;
}
document.documentElement.classList.add('has-motion');
if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
  console.warn('AETHER Motion: GSAP or ScrollTrigger not loaded');
  return;
}
```

```js
// AFTER — Rule 7: verify the motion stack BEFORE hiding any content.
'use strict';

var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
var MOTION_READY_TIMEOUT = 2500; // ms — if init hasn't finished by now, stop hiding content.

function disableMotion() {
  document.documentElement.classList.remove('has-motion');
  document.documentElement.classList.add('no-motion');
}

if (reducedMotion) {
  disableMotion();
  return;
}

// Library gate FIRST: without GSAP/ScrollTrigger there is no reveal engine,
// so content must remain visible (no has-motion class is applied).
if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
  console.warn('AETHER Motion: GSAP or ScrollTrigger not loaded — content left visible.');
  disableMotion();
  return;
}

var motionInitDone = false;
var motionWatchdog = setTimeout(function () {
  if (!motionInitDone) {
    console.warn('AETHER Motion: init timeout — content left visible.');
    disableMotion();
  }
}, MOTION_READY_TIMEOUT);

document.documentElement.classList.add('has-motion');
```

Then wrap `init()`:

```js
// AFTER — replace the bare init() call at the bottom of the IIFE:
function init() {
  try {
    initHeroEntrance();
    initTextReveals();
    initScrollReveals();
    initImageReveals();
    initSectionSnapping();
    initMagnetic();
    initImageParallax();
    initProgressBars();
    initTilt();
    initParallaxAttr();
    initImageZoom();
    initCountup();
    initScrubHorizontal();
    initStickyPin();
    initParallaxLayer();
    initButtonRipple();
    ScrollTrigger.refresh();
  } catch (err) {
    console.error('AETHER Motion: init failed — content left visible.', err);
    disableMotion();
  } finally {
    motionInitDone = true;
    clearTimeout(motionWatchdog);
  }
}
```

(Keep the existing DOMContentLoaded dispatch logic unchanged.)

**Rationale for reviewers:** (a) the `has-motion` CSS gate is the only hiding mechanism, so *not adding it* when the engine is absent is sufficient; (b) `no-motion` already forces `visibility: visible !important` via CSS, so `disableMotion()` is a complete recovery; (c) the watchdog converts "GSAP arrives late on slow networks" from a risk into a bounded, self-healing state.

### 2.4 Exact change — `frontend/assets/css/motion.css` (append)

```css
/* ─── Rule 7: never hide content when scripting is unavailable ─── */
@media (scripting: none) {
  html [data-motion-text],
  html [data-reveal],
  html [data-reveal-item],
  html [data-image-reveal],
  html .reveal-right-premium,
  html .reveal-fade-up,
  html .reveal-left,
  html .reveal-right,
  html .reveal-scale,
  html .reveal,
  html .footer-reveal,
  html .text-reveal {
    visibility: visible !important;
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
    clip-path: none !important;
  }
}
```

Note for reviewers: `has-motion` is only ever added by JS, so JS-disabled pages never hit the hidden state; this media query is belt-and-braces for browsers that run CSS without script permission contexts.

### 2.5 Exact change — `frontend/components/shell/preloader.php` (append inside the root `<div>`)

```html
<div id="preloader" aria-hidden="true">
	<noscript><style>#preloader{display:none!important}</style></noscript>
	<!-- ...existing inner markup unchanged... -->
</div>
```

### 2.6 Phase A acceptance criteria

| # | Scenario | Expected |
|---|---|---|
| A1 | Normal load (all CDNs OK) | animations byte-identical behavior; screenshots match baseline |
| A2 | DevTools: block `cdnjs.cloudflare.com` | all content visible, `no-motion` on `<html>`, console warn |
| A3 | DevTools: block `unpkg.com` (lenis only) | content visible (lenis is scroll-only) |
| A4 | Throttle Slow 3G | content visible after ≤2.5 s watchdog |
| A5 | JS disabled | full layout visible; preloader absent (noscript rule) |
| A6 | `prefers-reduced-motion: reduce` | content visible, no motion |
| A7 | Inject `throw` at top of `init()` | content visible (catch + disableMotion) |
| A8 | `node --check animations.js` | clean |

**Gate commands:** `node --check frontend/assets/js/animations.js` · `bash frontend/tests/verify.sh` · manual browser tests A2/A4/A7 via DevTools.

---

## 3. PHASE B — Data-source hardening

### 3.1 Unguarded WooCommerce calls (verified)

| File | Line (current) | Issue |
|---|---|---|
| `frontend/adapters/adapter-wc-products.php` | `$sale_ids = wc_get_product_ids_on_sale();` | fatal if WC inactive |
| `frontend/adapters/adapter-wc-products.php` | `$related_ids = wc_get_related_products( ... );` | fatal if WC inactive |
| `frontend/adapters/adapter-wc-filter.php` | `if ( ! empty( wc_get_product_ids_on_sale() ) )` | fatal if WC inactive |
| `frontend/adapters/adapter-wc-categories.php` | `$pid = wc_get_product_id_by_sku( $fb['sku'] );` (fallback fn) | fatal if WC inactive |

**Target pattern (repeat in all four sites):**

```php
// BEFORE (adapter-wc-products.php)
$sale_ids = wc_get_product_ids_on_sale();

// AFTER
$sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
```

```php
// BEFORE (adapter-wc-products.php)
$related_ids = wc_get_related_products( (int) $query_args['related_to'], (int) $query_args['posts_per_page'] );

// AFTER
$related_ids = function_exists( 'wc_get_related_products' )
    ? wc_get_related_products( (int) $query_args['related_to'], (int) $query_args['posts_per_page'] )
    : array();
```

```php
// BEFORE (adapter-wc-filter.php)
if ( ! empty( wc_get_product_ids_on_sale() ) ) {

// AFTER
$sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
if ( ! empty( $sale_ids ) ) {
```

```php
// BEFORE (adapter-wc-categories.php, in aether_get_fallback_categories())
$pid = wc_get_product_id_by_sku( $fb['sku'] );

// AFTER
$pid = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( $fb['sku'] ) : 0;
```

### 3.2 Pagination base hardening — `frontend/sections/section-shop-grid.php`

Current (line 30):

```php
$base = remove_query_arg( 'paged', home_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/' ) );
```

Target (same behavior, sanitized, WC-aware fallback):

```php
$base = remove_query_arg(
    'paged',
    esc_url_raw( home_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/' ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
);
```

(phpcs tag retained; optional further improvement: prefer `wc_get_page_permalink('shop')` when no query args — noted, not required.)

### 3.3 Phase B acceptance

- `php -l` clean on all 4 files.
- Simulated WC-inactive (probe: comment `class_exists('WooCommerce')` in theme or run adapters in isolation via `wp eval` in a non-WC context) → no fatal.
- `/shop/?paged=2` pagination still resolves correct URLs.
- Route sweep `/`, `/shop/`, `/product/*`, `/cart/` — 0 console errors.

---

## 4. PHASE C — Customizer round-trip closure

**Principle:** every user-facing copy block that is currently hardcoded in an adapter should resolve through `aureon_get_option()` with the **current strings as token defaults** — so default rendering is pixel-identical, but the values become editable. All changes below are adapter + token changes (frontend-only). No theme customizer control is added (that would need a stop-condition report); settings are stored in the standard `aureon_settings` bucket and can be set programmatically or via a future control.

### 4.1 C1 — Announcement (G1)

`frontend/adapters/adapter-shell.php` → `aether_adapter_announcement()`.

Current: hardcoded 4-item array, comment "Always use premium marquee items — Customizer DB values are legacy."

Target:

```php
function aether_adapter_announcement() {
    $items = aureon_get_option( 'aether_announcement_items', array() );

    // Repeater may be stored as JSON (Customizer) or PHP array (tokens default).
    if ( is_string( $items ) && '' !== trim( $items ) ) {
        $items = json_decode( $items, true );
    }

    // Single-text control (aether_announcement_text) as a one-item marquee fallback.
    if ( empty( $items ) || ! is_array( $items ) ) {
        $text = aureon_get_option( 'aether_announcement_text', '' );
        $items = $text ? array( array( 'text' => $text ) ) : array();
    }

    $normalized = array();
    foreach ( (array) $items as $item ) {
        if ( is_array( $item ) && ! empty( $item['text'] ) ) {
            $normalized[] = array( 'text' => sanitize_text_field( $item['text'] ) );
        } elseif ( is_string( $item ) && '' !== trim( $item ) ) {
            $normalized[] = array( 'text' => sanitize_text_field( $item ) );
        }
    }

    return array( 'items' => $normalized );
}
```

`frontend/tokens/tokens.php` already registers `aether_announcement_items` (4 marquee strings) and `aether_announcement_text` — so defaults are preserved with **zero token change**. `shell/mobile-chrome.php` consumes the same adapter output (already wired).

### 4.2 C2 — Footer columns (G4)

`frontend/adapters/adapter-site.php` → `aether_adapter_footer()`.

- Add tokens in `tokens.php`:

```php
'aether_footer_columns' => array(
    array(
        'heading' => 'Shop',
        'links'   => array(
            array( 'label' => 'Men', 'url' => '' ),
            array( 'label' => 'Women', 'url' => '' ),
            array( 'label' => 'Kids', 'url' => '' ),
            array( 'label' => 'New Arrivals', 'url' => '' ),
            array( 'label' => 'Bestsellers', 'url' => '' ),
        ),
    ),
    array(
        'heading' => 'Support',
        'links'   => array(
            array( 'label' => 'FAQ', 'url' => '' ),
            array( 'label' => 'Contact Us', 'url' => '' ),
            array( 'label' => 'Shipping Info', 'url' => '' ),
            array( 'label' => 'Returns & Exchanges', 'url' => '' ),
            array( 'label' => 'Size Guide', 'url' => '' ),
        ),
    ),
    array(
        'heading' => 'Company',
        'links'   => array(
            array( 'label' => 'About Us', 'url' => '' ),
            array( 'label' => 'Blog', 'url' => '' ),
            array( 'label' => 'Careers', 'url' => '' ),
            array( 'label' => 'Press', 'url' => '' ),
        ),
    ),
),
```

- Adapter resolves with URL fallback (empty `url` → keep the current `home_url('/shop/')` etc. so out-of-the-box behavior is unchanged):

```php
$columns = aureon_get_option( 'aether_footer_columns', array() );
if ( empty( $columns ) || ! is_array( $columns ) ) {
    $columns = array(); // fall through to current hardcoded defaults below
}
// …for each column/link: url = '' → keep the existing default url map…
```

Implementation detail: keep the current `$columns` array as the code-level fallback (unchanged markup), and when a saved setting exists, normalize it (resolve empty URLs via the same default map). This guarantees pixel-identical default output.

### 4.3 C3 — Contact info (G5) + categories copy (G3)

`frontend/adapters/adapter-contact.php`: add tokens `aether_contact_address` (2 lines), `aether_contact_hours` (string) with current values; adapter reads them with fallback to current literals. Email already reads `admin_email` (keep).

`frontend/adapters/adapter-wc-categories.php` + `frontend/sections/section-categories.php`: add tokens `aether_categories_label/title/subtitle` (current values); adapter resolves `aureon_get_option($key)` → falls back to the `$args` values passed by the section registration, so per-page overrides (if ever used) still win.

### 4.4 G6 — Hero slides repeater control

**Deferred + stop-condition note.** The data path is already dynamic (`aether_hero_slides` token default → adapter JSON-decode → slides). A Customizer *repeater control* would require a new control class in `aureon/theme/inc/customizer/...` — a theme-side change. Per the mission, we document ownership and defer unless the user explicitly requests a theme-side control (then a stop-condition report is produced first).

### 4.5 Phase C acceptance

- Default render after change == render before change (screenshot diff on `/`, `/about/`, `/contact/`, `/shop/`).
- `wp eval` update of `aureon_settings['aether_announcement_text']` → marquee changes on refresh.
- `aureon_get_option` returns token defaults when the bucket is empty.

---

## 5. PHASE D — Demo-content policy (master toggle)

### 5.1 Design

Add one token in `frontend/tokens/tokens.php`:

```php
// Master switch for demo fallback content. TRUE keeps the store visually
// populated before real products/CPTs exist. Set FALSE in production to
// show only real data (empty sections then render their graceful empty
// states, which already no-op via the section guards).
'aether_demo_content' => true,
```

**Recommendation: default `true`** (zero visual change out of the box; fresh stores remain pixel-visible), with a documented one-line production flip to `false`. Alternative (default `false`) is defensible for strict production hygiene but changes current empty-store visuals — a decision for the site owner. *(Flag for ChatGPT: this is the single behavior-vs-hygiene tradeoff in the plan.)*

### 5.2 Gate each fallback

Pattern (repeat in: `adapter-wc-products.php`, `adapter-wc-categories.php` (fallback categories), `adapter-faq.php`, `adapter-testimonials.php`, `adapter-team.php`, `adapter-product.php` (colors/sizes/specs/reviews/score/bars/trust/size_table), `adapter-order.php` (styleguide demo shape)):

```php
// BEFORE
if ( empty( $items ) ) {
    foreach ( (array) aureon_get_option( 'aether_product_items', array() ) as $demo ) { ... }
}

// AFTER
if ( empty( $items ) && aureon_get_option( 'aether_demo_content', true ) ) {
    foreach ( (array) aureon_get_option( 'aether_product_items', array() ) as $demo ) { ... }
}
```

### 5.3 Phase D acceptance

- Default: all routes render exactly as today (screenshot diff).
- `aether_demo_content=false` on an empty store → bestsellers/categories/faq/reviews/team sections skip (already guarded), product page falls back to **empty-safe** rendering (no blank page).
- Grep gate: every demo fallback loop in adapters is wrapped by the toggle (enforce in `tests/verify.sh`).

---

## 6. PHASE E — Verification suite (Playwright, a11y, perf, failure-injection)

### 6.1 Current state (verified)

- No `package.json`, no Playwright config anywhere in the repo.
- `frontend/tests/verify.sh` exists: PHP lint + `node --check` + grep gates (components must not call WP/WC) — extend, don't replace.

### 6.2 Deliverables (all new, under `frontend/tests/`)

1. `frontend/tests/package.json` — `devDependencies: @playwright/test` (pinned), scripts: `test`, `test:visual`.
2. `frontend/tests/playwright.config.js` —
   - `baseURL: process.env.WEB_BASE_URL || 'http://localhost:8080'` (env-driven; no hardcoded creds — Stage 13 lesson).
   - Projects: `desktop-chromium` (1280×800), `mobile-chromium` (390×844).
   - `outputDir`/`testDir` gitignored; append `?nocache` in tests where needed (Stage 2 caching lesson).
3. `frontend/tests/routes.spec.js` — visit `/`, `/shop/`, `/product/*` (real slug), `/cart/`, `/checkout/` (expect 302→cart when empty), `/my-account/`, `/blog/`, `/sample-post/`, `/about/`, `/contact/`, `/faq/`, `/team/`, `/wishlist/`, `/login/`, `/register/`, `/coming-soon/`, `/no-such-page/` (expect AETHER 404). Assert: `response.status() < 500`, expected section presence, zero console errors, zero failed image requests.
4. `frontend/tests/interactions.spec.js` — search overlay opens + Enter → `/?s=`, product-card click → product permalink, mobile hamburger opens drawer, FAQ accordion toggles, newsletter submit (graceful path), pagination click on a seeded >1-page shop.
5. `frontend/tests/failure-injection.spec.js` — `page.route('**cdnjs.cloudflare.com/**', route => route.abort())` → assert `html:not(.has-motion)` OR `html.no-motion` AND every `[data-reveal]` element is `visible` (computed). Same for `**unpkg.com/**`. Also a test injecting `document.querySelector('.hero-swiper')` removal before DOMContentLoaded (runtime-error proxy) → content visible.
6. `frontend/tests/visual.spec.js` — `toHaveScreenshot` with committed baselines for `/` (desktop + mobile). Baseline images committed to repo (no credentials involved).
7. `frontend/tests/a11y.spec.js` — `@axe-core/playwright` scan on `/`, `/shop/`, `/my-account/`, `/contact/`: no critical/serious violations; keyboard focus on header/menu; skip-link present; landmark count.
8. `frontend/tests/verify.sh` — add gates: (a) demo fallback loops must be inside `aether_demo_content` check; (b) no `wc_get_*` call without `function_exists` guard in adapters; (c) manifest/component cross-check (every `aether_render_component()` id resolves in `manifest/components.php`).

### 6.3 Phase E acceptance

- `npx playwright test` green on desktop+mobile against a running WP stack (`WEB_BASE_URL` env).
- `bash frontend/tests/verify.sh` passes (extended).
- Failure-injection suite proves Phase A fixes (content visible under blocked CDN).
- Perf smoke: record `window.performance` timings on `/` (LCP budget: document, no hard gate in phase E).

---

## 7. PHASE F — Styleguide + final reports

1. `/styleguide/index.php` — a single theme page reusing **only manifest components** (card/product, card/category, card/blog, form/*, section/accordion, commerce/rating, shell/skip-link, etc.). No demo-only duplicates (mission §16). Register a page or serve as a static template under `frontend/styleguide/` referenced by a hidden page — decision at implementation; presentation-only.
2. Final reports to `docs/`: `FRONTEND_VISUAL_REGRESSION_REPORT.md` (update with Phase E baselines), `FRONTEND_FAILURE_MODE_REPORT.md` (mark F1–F3 fixed), `FRONTEND_CONVERSION_REPORT.md` (flip acceptance scorecard), `FRONTEND_API_USAGE.md` (reflect Phase B/C/D changes).
3. Update `aureon-doc/CHANGELOG.md`, `.serena` memory.

---

## 8. Commit plan (small, testable commits)

| Order | Commit | Contents |
|---|---|---|
| 1 | `docs: frontend conversion baseline + contract suite` | (already staged in working tree) |
| 2 | `frontend: add implementation plan (phases A–F)` | this doc |
| 3 | `frontend: harden animation lifecycle (Rule 7)` | Phase A (animations.js, motion.css, preloader.php) |
| 4 | `frontend: gate WC calls + sanitize pagination base` | Phase B |
| 5 | `frontend: bind announcement/footer/contact/categories to settings` | Phase C |
| 6 | `frontend: add aether_demo_content master toggle` | Phase D |
| 7 | `test: add Playwright suite + extend verify gates` | Phase E |
| 8 | `docs: finalize visual regression + failure mode + conversion reports` | Phase F |

Each commit must pass `bash frontend/tests/verify.sh` (after Phase E: + Playwright) before push. Never batch hundreds of changes (mission §27).

---

## 9. Quality gates (per phase, mission §25)

| Gate | Command |
|---|---|
| PHP lint | `php -l` (all touched files; whole `frontend/` via verify.sh) |
| JS syntax | `node --check` (all touched JS) |
| Architecture grep | `bash frontend/tests/verify.sh` |
| Browser E2E | `npx playwright test` (Phase E+) |
| Visual diff | `toHaveScreenshot` baselines (Phase E+) |
| Security spot-check | `rg -n "eval\(|exec\(|system\(|shell_exec" frontend/` → 0 hits |
| Escaping spot-check | every new `echo` of dynamic data uses `esc_html/esc_attr/esc_url` |

---

## 10. Rollback plan

- Every phase is a small, self-contained commit → `git revert <commit>` per phase.
- Phase A specifically: revert of the single `animations.js` block restores current behavior; CSS addition is purely additive (new `@media` block); preloader change is a `<noscript>` addition.
- Visual regressions caught by Phase E baselines → fix forward, never redesign.

---

## 11. Risks & mitigations

| Risk | L | Mitigation |
|---|---|---|
| Rule-7 fix alters happy-path motion | M | Guard/wrap only; presets and triggers untouched; A1 screenshot diff |
| `aether_demo_content` default choice changes fresh-store visuals | M | Default `true` (pixel-identical); documented flip |
| New settings bindings diverge from current copy | M | Token defaults = current strings; default render screenshot-diffed |
| Playwright env/creds leakage | M | Env-driven baseURL, gitignored output, no credentials in tests (Stage 13 lesson) |
| Demo toggle gates a needed fallback (e.g. product attributes) | M | Gating is per-fallback; product page keeps graceful empty render |
| Verifier (ChatGPT) finds a plan/implementation mismatch | M | All claims quote exact file:line + current code in this doc; docs are the review contract |

---

## 12. Success criteria (final, mission §26 map)

After phases A–F: the single ❌ (animation failure hides content) → ✅; all ⚠️ → ✅ or explicitly documented as intentional (demo toggle OFF note, W3 native checkout forms, W4 card CTA routing, G6 hero repeater deferred). Zero core theme/plugin changes. Zero design changes.

---

## 13. Open questions for the site owner (needed before Phase D is finalized)

1. `aether_demo_content` default — `true` (current visual behavior) or `false` (strict production hygiene)?
2. Should `cards/product` "Add to Cart" become a real AJAX add-to-cart (feature, not bugfix), or stay as product-permalink navigation (current)?
3. Do you want the G6 hero-slides repeater in the Customizer (requires a theme-side control → stop-condition report + approval)?

---

*End of plan. This document is the review contract: any discrepancy between this plan and the repo should be raised before implementation proceeds.*
