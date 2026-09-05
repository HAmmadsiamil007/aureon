# PHASE 04 — Customizer Round-Trip Audit

**Status:** DONE
**Date:** 2026-08-14
**Scope:** Static verification of the Customizer → option bucket → consumer (render/JS/CSS) → read-back path for every AETHER setting group. Live evidence captured from the running stack (`aureon_wp`, localhost:8080).

---

## 1. Round-Trip Architecture

```
Customizer control
  │  sanitize_callback (helpers.php / core)
  ▼
aureon_settings option bucket (wp_options.aureon_settings)
  │
  ├─ aureon_get_option()        — wp_parse_args( get_option('aureon_settings'), aureon_get_defaults() )
  │    (inc/theme-functions.php:20)   defaults from frontend/tokens/tokens.php via aether_frontend_defaults filter
  │
  ├─ PHPSide consumers:
  │    ├─ composer.php:25-28    — shell toggles (preloader/fog/announcement)
  │    ├─ theme page templates  — 16 section toggles gate aether_render_component()
  │    ├─ viewmodel.php:113-129 — 5 motion toggles → data-* behavior attrs
  │    ├─ adapters              — announcement text/url, hero slides (JSON decode), shop_per_page
  │    └─ aether-tokens.php     — colors + layout sliders → :root CSS (aether_generate_tokens_css)
  └─ JS consumers:
       └─ animations.js (data-* attrs only; JS itself is attr-driven, no option reads)
```

## 2. Setting Inventory (all in `aureon_aether_section`, all `transport => refresh`)

| Group | Settings | Sanitizer | Consumer | Round-trip |
|---|---|---|---|---|
| Section toggles (16) | `aether_section_{hero,categories,bestsellers,reviews,faq,newsletter,mission,features,story,stats,team,values,contact,auth,wishlist,coming_soon}` | `aureon_sanitize_checkbox` | page templates gate section render | ✅ verified live (Stage 9 toggles) |
| Shell toggles (3) | `aether_{preloader,fog,announcement}_enabled` | `aureon_sanitize_checkbox` | `aether_compose_header()` composer.php:25-28 | ✅ |
| Motion toggles (5) | `aether_motion_{enabled,reveal,tilt,parallax,text}` | `aureon_sanitize_checkbox` | `aether_viewmodel_behavior()` viewmodel.php:113-129 | ✅ |
| Announcement | `aether_announcement_text` / `_url` | `sanitize_text_field` / `esc_url_raw` | `aether_adapter_announcement()` | ✅ (url has active_callback on toggle) |
| Commerce | `aether_shop_per_page` | `absint` | shop adapter query | ✅ |
| Hero slides | `aether_hero_slides` | `aureon_sanitize_repeater($input,'hero')` | `aether_adapter_hero()` (JSON decode) | ✅ live-verified (see §3) |
| Colors (11) | `aether_color_{bg,surface,surface_2,surface_3,text,muted,accent,accent_hover,border,error,success}` | `aureon_sanitize_hex_color` (alpha enabled) | `aether_generate_tokens_css()` via `aether_resolve_color()` precedence chain | ✅ live-verified |
| Layout sliders (7) | `aether_{container_max,announcement_height,header_height,grid_gap,radius_sm,radius_md,radius_lg}` | `aureon_sanitize_integer` (add_setting type=option) | `aether_generate_tokens_css()` → `:root` | ✅ live-verified |
| Layout text | `aether_section_padding` | `sanitize_text_field` | `aether_generate_tokens_css()` → `--section-padding` | ✅ live-verified |
| Analytics | `aether_analytics_ga4_id` | `sanitize_text_field` | aether-analytics.php module (M10) | ✅ (empty = disabled) |

**Count:** 1 section title + 5 sub-titles, 24 checkboxes + 2 text + 1 url + 1 number + 1 repeater + 11 colors + 7 sliders + 1 padding + 1 GA4 = 49 controls.

## 3. Live Evidence (2026-08-14, localhost:8080)

### 3.1 Token CSS emission — VERIFIED
`:root` inline block (`#aether-tokens`, sourceURL=aether-tokens-inline-css) contains all 12 color tokens, both font stacks, and 9 layout tokens. Live values:

```
--void:#09090B --surface:#141416 --surface-2:#1a1a1d --surface-3:#232327
--text:#FFFFFF --muted:#A8B5C0 --chrome:#A8B5C0 --gold:#C8956C --gold-alt:#D4A574
--line:#1A1A1A --error:#CC4444 --success:#4CAF50
--font-heading:'Cabinet Grotesk',sans-serif
--font-body:'Space Grotesk',sans-serif          ← Typography Manager override (G9 bridge working)
--container-max:1200px --section-padding:100px 0 --announcement-height:40px
--header-height:80px --grid-gap:24px --radius-sm:8px --radius-md:12px --radius-lg:24px --radius-pill:999px
```

### 3.2 Option bucket — VERIFIED (docker exec PHP probe)
- `aether_hero_slides` = JSON string, 3 slides, keys exactly matching the schema: `id, visible, headline, accent, subline, badge, image, mobile_image, image_alt, overlay, primary_cta{label,url}, secondary_cta{label,url}`. Relative `frontend/assets/...` image paths preserved (sanitizer's `frontend/` branch working).
- `aether_section_*`, `aether_announcement_text`, `aether_shop_per_page`, `aether_color_*`, `aether_demo_content`, font options — **all unset** → defaults resolved by `aureon_get_option()` wp_parse_args. Correct default behavior confirmed.
- `typography` (Typography Manager option) = `[{selector:body, fontFamily:"Space Grotesk", module:core, group:base}]` → explains live `--font-body`. **G9 bridge round-trip VERIFIED end-to-end:** Typography Manager → option → `aether_generate_tokens_css()` → :root CSS.

## 4. Sanitizer Review (trust boundary)

| Function | File:Line | Verdict |
|---|---|---|
| `aureon_sanitize_repeater` | helpers.php:302 | ✅ Schema-driven whitelist (`aether_repeater_schemas` filter), type-safe per field, drops unknown keys, stable `slide_[a-f0-9]{8}` IDs, backfills missing IDs via `wp_hash`, legacy key migration (`aureon_repeater_migrate_legacy` :269). `frontend/` relative paths exempt from `esc_url_raw` (comment documents the mangling hazard). |
| `aureon_sanitize_integer` | helpers.php:97 | ✅ absint-style |
| `aureon_sanitize_hex_color` | helpers.php:200 | ✅ hex + alpha validation |
| `aureon_sanitize_checkbox` | (helpers) | ✅ bool coercion |
| `esc_url_raw` (announcement url) | fields:175 | ✅ (absolute URLs only — correct for this field) |
| `absint` (shop_per_page) | fields:190 | ⚠️ no clamp to UI max 48 — API can store 999; adapter passes through. LOW (Customizer is admin-only). |
| `sanitize_text_field` (section_padding) | fields:410 | ⚠️ free-text CSS emitted raw into `:root` — no shape validation (`100px 0 !important` would pass). Admin-only → LOW, but note for Phase 12 hardening. |

## 5. Findings

| ID | Sev | Finding |
|---|---|---|
| F4-1 | INFO | All 49 controls use `transport => refresh` — no postMessage/live preview for AETHER settings. Consistent with engine design (server-rendered templates), but Customizer preview shows stock theme, not AETHER output, until publish. Acceptable; note for Phase 12. |
| F4-2 | INFO | Motion toggles gate **attribute emission** (viewmodel), not JS execution — `animations.js` runs regardless but has zero targets when attrs absent. Kill switch (`has-motion` class removal) remains JS-side for CDN-failure scenarios. Defense-in-depth confirmed. |
| F4-3 | LOW | `aether_shop_per_page` sanitizer unclamped vs. control's `max=48`; stored value passes through to query. |
| F4-4 | LOW | `aether_section_padding` accepts arbitrary CSS text; emitted verbatim into `:root`. Suggest regex shape validation (`/^\d+(px|rem|em|vh|vw)?( \d+(px|rem|em|vh|vw)?)?$/`) in Phase 12. |
| F4-5 | INFO | **G9 font bridge VERIFIED live**: `typography` option (body → Space Grotesk) overrides AETHER default Satoshi in emitted tokens — full round-trip Typography Manager → option → CSS var → rendered page. |
| F4-6 | INFO | Default chain verified: explicit control value → theme `global_colors` palette → AETHER default (tokens.php). All AETHER controls default to empty string = inherit, so no hardcoded values live in the fields file (header comment honored). |
| F4-7 | INFO | Hero repeater round-trip verified end-to-end: Customizer save → JSON option → sanitizer (stable IDs intact: `slide_0a1f2b3c`, `slide_4d5e6f70`, `slide_8a9b0c1d`) → adapter decode → rendered slides. |

## 6. Inputs for Later Phases
- **Phase 12:** transport → postMessage feasibility for section toggles; `section_padding` + `shop_per_page` sanitizer hardening (F4-3, F4-4).
- **Phase 14:** none (no new options needed).