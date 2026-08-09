# CUSTOMIZER → FRONTEND BINDING MATRIX

> **Status:** COMPLETE (baseline) · **Date:** 2026-08-08 · **Closure:** 2026-08-09 (G1/G4/G5 resolved)
> **Source:** `aureon/theme/inc/customizer/fields/frontend.php` (AETHER Frontend section, priority 120) + `frontend/tokens/tokens.php` defaults + `frontend/adapters/*` consumers.
> **Round-trip:** Customizer control → `aureon_settings[...]` → `aureon_get_option()` → token resolver / adapter → component → frontend property.
> **Transport:** refresh (full reload) for all AETHER controls; live-preview for theme-native panels (colors/fonts via `aether-tokens.php` inline `:root`).
> ⚠ **Note:** the customizer field file lives in the **theme** (read-only per mission). Any new control that must be added there = stop-condition report. Frontend-only bindings are preferred.

---

## 1. Section visibility (16 checkboxes)

| Setting (`aureon_settings[...]`) | Default | Component/section gated | Frontend effect | Verified |
|---|---|---|---|---|
| `aether_section_hero` | true | front-page → hero | hero slider on/off | ✅ |
| `aether_section_categories` | true | front-page → categories | category grid on/off | ✅ |
| `aether_section_bestsellers` | true | front-page → bestsellers | top-sellers grid on/off | ✅ |
| `aether_section_reviews` | true | front-page → reviews | reviews swiper on/off | ✅ |
| `aether_section_faq` | true | front-page + page-faq | FAQ accordion on/off | ✅ |
| `aether_section_newsletter` | true | all pages | newsletter on/off | ✅ |
| `aether_section_mission` | true | page-about | mission on/off | ✅ (live toggle-tested Stage 9) |
| `aether_section_features` | true | page-about | features on/off | ✅ |
| `aether_section_story` | true | page-about | story on/off | ✅ |
| `aether_section_stats` | true | page-about | stats on/off | ✅ |
| `aether_section_team` | true | page-about + page-team | team grid on/off | ✅ |
| `aether_section_values` | true | page-team | values on/off | ✅ |
| `aether_section_contact` | true | page-contact | contact on/off | ✅ |
| `aether_section_auth` | true | page-login/register | auth on/off | ✅ |
| `aether_section_wishlist` | true | page-wishlist | wishlist on/off | ✅ |
| `aether_section_coming_soon` | true | page-coming-soon | countdown on/off | ✅ |

## 2. Shell & motion (8 checkboxes)

| Setting | Default | Consumer | Effect |
|---|---|---|---|
| `aether_preloader_enabled` | true | `aether_compose_header()` | preloader render |
| `aether_fog_enabled` | true | `aether_compose_header()` | fog render |
| `aether_announcement_enabled` | true | `aether_compose_header()` | marquee render |
| `aether_motion_enabled` | false→true | `aether_viewmodel_behavior()` | kills all behavior attrs when off |
| `aether_motion_reveal` | true | viewmodel behavior | `data-reveal*` attrs |
| `aether_motion_tilt` | true | viewmodel behavior | `data-tilt` attrs |
| `aether_motion_parallax` | true | viewmodel behavior | `data-parallax*` attrs |
| `aether_motion_text` | true | viewmodel behavior | `data-motion-text` attrs |

## 3. Announcement & commerce (3)

| Setting | Default | Consumer | Effect | Status |
|---|---|---|---|---|
| `aether_announcement_text` | 'Free shipping on orders over $150…' | `adapter-shell` → `aether_adapter_announcement()` (G1 closed) | single-item marquee when no repeater items | ✅ |
| `aether_announcement_url` | '' | — | — | ✅ (primary source = `aether_announcement_items` repeater) |
| `aether_shop_per_page` | 9 | `archive-product.php` → shop-grid args | products per page | ✅ |

## 4. Design — colors (11)

| Setting | Default (tokens) | Resolver | Token → CSS var | Verified |
|---|---|---|---|---|
| `aether_color_bg` | #09090B | `aether_resolve_color` | `--void` | ✅ customizer-aware `:root` |
| `aether_color_surface` | #141416 | same | `--surface` | ✅ |
| `aether_color_surface_2` | #1a1a1d | same | `--surface-2` | ✅ |
| `aether_color_surface_3` | #232327 | same | `--surface-3` | ✅ |
| `aether_color_text` | #FFFFFF | same | `--text` | ✅ |
| `aether_color_muted` | #A8B5C0 | same | `--muted`, `--chrome` | ✅ |
| `aether_color_accent` | #C8956C | same | `--gold` | ✅ |
| `aether_color_accent_hover` | #D4A574 | same | `--gold-alt` | ✅ |
| `aether_color_border` | #1A1A1A | same | `--line` | ✅ |
| `aether_color_error` | #CC4444 | same | `--error` | ✅ |
| `aether_color_success` | #4CAF50 | same | `--success` | ✅ |

Empty value = inherit theme palette / React `global_colors` (slug-mapped) / AETHER default.

## 5. Design — layout (7 sliders + 1 text)

| Setting | Default | Token | CSS consumers | Verified |
|---|---|---|---|---|
| `aether_container_max` | 1200px | `--container-max` | `.container` | ✅ |
| `aether_announcement_height` | 40px | `--announcement-height` | `.announcement-bar` | ✅ |
| `aether_header_height` | 80px | `--header-height` | `.header` | ✅ |
| `aether_grid_gap` | 24px | `--grid-gap` | `.products-grid`, `.category-grid` | ✅ |
| `aether_radius_sm` | 8px | `--radius-sm` | small cards/inputs | ✅ |
| `aether_radius_md` | 12px | `--radius-md` | cards | ✅ |
| `aether_radius_lg` | 24px | `--radius-lg` | hero/modals | ✅ |
| `aether_section_padding` | '100px 0' | `--section-padding` | sections | ✅ |

## 6. Theme-native panels that feed the frontend (via bridges)

| Panel | Controls | Frontend bridge | Status |
|---|---|---|---|
| Colors → Global Colors (React) | palette slugs | `aether_resolve_color` → palette map | ✅ |
| Typography → Typography Manager | body / all-headings | `aether_font_for()` | ✅ |
| Typography → classic fonts | font_body / font_heading_1 | classic path (non-dynamic) | ✅ |
| WooCommerce Colors/Layout | WC-specific | theme/plugin WC module (read-only) | ✅ |

---

## 7. Binding gaps (Phase C candidates — all fixable frontend-side)

| Gap | Setting missing / unbound | Status (2026-08-09) |
|---|---|---|
| G1 | `aether_announcement_items` / text / url not consumed | ✅ **CLOSED** — `adapter-shell.php` consumes `aether_announcement_items` (repeater JSON/array) with `aether_announcement_text` fallback |
| G3 | `aether_categories_label/title/subtitle` no controls | ⚠️ partial — values flow via tokens/settings; no dedicated controls (low priority, deferred) |
| G4 | Footer columns no controls | ✅ **CLOSED** — `adapter-site.php` reads `aether_footer_columns` (default-URL resolution) |
| G5 | Contact info no controls | ✅ **CLOSED** — `adapter-contact.php` reads `aether_contact_address`/`aether_contact_hours` |
| G6 | Hero slides no repeater control | ⏸ **DEFERRED** — needs a theme-side control (stop-condition); settable via settings bucket / tokens |
| — | Live preview (postMessage) for AETHER controls | refresh transport is functional; postMessage hardening optional |

## 8. Live-preview test results (documented runtime, Stage 9 + 12)

| Test | Result |
|---|---|
| Toggle `aether_section_mission` off → `/about/` drops mission | ✅ (curl-verified, restored) |
| Change accent color → `:root` token updates | ✅ (customizer preview renders live tokens) |
| Toggle `aether_announcement_enabled` | ✅ (marquee removes/adds) |
| `aether_shop_per_page` 9 → 12 | ✅ (grid shows 12 on next refresh) |
