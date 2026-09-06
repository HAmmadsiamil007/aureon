# G6 — Hero Slides Repeater: Hardening & Verification Report

Status: **PASS (41/41 harness checks, DOM/viewport verified, regression clean)**
Date: 2026-08-09
Scope: schema-driven repeater Customizer control (`aether_hero_slides`), v1.2.0 freeze prep.

## 1. Issues found & fixed

| # | Issue | Severity | Root cause | Fix |
|---|-------|----------|-----------|-----|
| 1 | Legacy slide data destroyed on first v1.2 save | **Critical** — existing users lose hero content | `aureon_sanitize_repeater()` whitelisted schema keys only; legacy keys (`title`/`subtitle`/`cta`/`url`/`label`) were stripped on Save | Added `aureon_repeater_migrate_legacy()` — maps legacy keys onto the schema contract (`title→headline`, `subtitle→subline`, `cta`+`url→primary_cta`, `label→image_alt`), new-shape values win |
| 2 | Hex overlay colors silently rejected on frontend | High — overlay `#000` / `#00000099` never rendered as inline style | `aether_sanitize_overlay_color()` regex used `|` as delimiter while the pattern itself contains `|` alternation ⇒ `preg_match()` warning + always false | Rewrote regex with `~` delimiter and proper `^#(...)$` anchoring |
| 3 | 8-digit hex (alpha) overlay dropped by Customizer sanitizer | Medium | `aureon_sanitize_hex_color()` only accepted 3/6-digit hex; frontend contract accepts 3/4/6/8 | Extended regex to `3,4 \| 6 \| 8` digits |
| 4 | Hidden slide image preloaded (404 + wasted request) | Medium | `aether_preload_assets()` preloaded `$slides[0]['image']` raw — no visibility filter, no adapter | Now uses `aether_adapter_hero()` and preloads first **visible** slide's resolved image |

## 2. Verification harness (real API, WP-CLI-less)

`g6_hardening.php` — ran inside container against live WP/WooCommerce state via adapters + sanitizer only.

### 41/41 checks passed

- **Legacy → adapter** (pre-edit read path): 2 slides, title/subtitle preserved, CTA promoted. PASS
- **Legacy → sanitize → adapter** (what actually happens on Save): headline/subline/CTA survive round-trip. **PASS (was FAIL before fix #1)**
- **Mobile image mapping**: `frontend/…` relative paths resolve to `/wp-content/frontend/…` via `content_url()`; absent mobile image → `''`. PASS
- **Overlay**: `''`→`''`, `#000`, `#00000099`, `rgba(0,0,0,0.5)`, spaced rgba kept; `javascript:…`, named colors, bad hex → `''`; sanitizer strips `javascript:` and keeps hex8. **PASS (was FAIL before #2/#3)**
- **CTA (7 case table through sanitize + adapter)**: XSS in label neutralized (`<script>` stripped), relative `/product/x` kept absolute-ized, malformed URL escaped to `%20`-encoded safe string, external kept, empty label+url→ stays empty, empty url → shop fallback `wc_get_page_permalink('shop')`. PASS
- **CTA styles**: only secondary → renders `outline`; primary → `btn-primary`. PASS
- **Visibility**: `visible:false` dropped, `true` kept, missing key kept (legacy-lenient). PASS
- **Idempotence**: `sanitize(sanitize(x))` === `sanitize(x)` key-order-invariant, IDs stable across passes. PASS
- **No PHP warnings** during harness run (was: `preg_match Unknown modifier` from #2/#3). 

## 3. Customizer → DB → frontend round-trip (real browser, Playwright)

- Repeater panel renders both rows with `data-row-id` attributes and the healed field values.
- **Reorder**: drag `slide_0a1f2b3c` below `slide_4d5e6f70` via sortable `mouse` steps. Rows re-ordered in DOM, setting flushed (debounced 250 ms) with IDs **still bound to content**.
- **Publish** → DB JSON order changed, IDs + full content intact (`mobile_image`, `overlay #00000066/`, `image_alt`, CTAs).
- Frontend (desktop): only visible slide renders; hidden slide absent from DOM.
- Frontend (mobile 375×667): `<picture><source media="(max-width: 767px)">` swaps `currentSrc` to mobile image; desktop 1920px uses desktop image.
- DOM assertions: overlay inline style present, CTA primary/outline classes, alt text, badge, headline/accent.

## 4. Full regression (console error free)

| Page | Hero slides | Errors | Notes |
|------|-------------|--------|-------|
| `/` (front) | 3 (defaults, all visible) | 0 | preload = first *visible* slide |
| `/shop/` | 0 (hero is front-only) | 0 | products grid OK |
| `/product/…` | — | 0 | price/gallery OK |
| `/about/` | — | 0 | — |
| `/blog/` | — | 0 | — |

## 5. Files changed

- `aureon/theme/inc/customizer/helpers.php` — `aureon_repeater_migrate_legacy()`, hex 3/4/6/8 support
- `aureon/theme/inc/aether-performance.php` — visible-slide-aware hero image preload
- `frontend/views/viewmodel.php` — overlay color regex delimiter fix

## 6. Environment

- Container `aureon_wp`, site `http://localhost:8080`, DB round-trip verified via `update_option/get_option`.
- Harness artifact: `g6_hardening.php`, `g6_domstate.php`, `g6_debug.php`, `g6_restore.php`, `g6_peek.php` in temp.
- DB restored to schema defaults (3 stable-ID slides) after testing.