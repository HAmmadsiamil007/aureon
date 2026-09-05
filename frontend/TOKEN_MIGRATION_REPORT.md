# TOKEN_MIGRATION_REPORT

**Phase:** 17 — Frontend Integration Framework (Step 5: Tokenize Design)
**Date:** 2026-08-06
**Status:** Complete — token map for Customizer migration

---

## 1. Source Token Landscape

The AETHER frontend defines **6 palette tokens** + 4 typography/spacing tokens in `:root` (style.css):

```css
:root {
  --void: #09090B;      /* primary background */
  --surface: #141416;   /* elevated surface (cards, sections) */
  --chrome: #A8B5C0;    /* muted text, borders, chrome UI */
  --gold: #C8956C;      /* accent (CTA, highlights, ratings) */
  --white: #FFFFFF;
  --black: #000000;
  --font-heading: 'Cabinet Grotesk', sans-serif;
  --font-body: 'Satoshi', sans-serif;
  --section-padding: 100px 0;
  --container-max: 1200px;
  --announcement-height: 40px;
  --header-height: 80px;
}
```

Additional hardcoded values throughout `style.css` (4,550 lines):

| Value Type | Estimated Count | Example |
|---|---|---|
| Hex colors (un-tokenized) | ~80 | `#1a1a1d`, `#232327`, `#d4af37` (legacy gold), `#e5e5e5` |
| `rgba(…)` colors | ~40 | `rgba(9, 9, 11, 0.9)` (void @ alpha) |
| Gradients | ~6 | hero overlay, button gold gradient |
| px radius | ~15 | 8px, 12px, 24px, 999px |
| px spacing | ~60 | 24px, 32px, 60px, 100px |
| px shadows | ~12 | drop shadows on cards, floating elements |
| z-index values | ~10 | 999, 998, 1200, 9999 |
| font sizes | ~25 | clamp(2.5rem…), 14px, 16px |

---

## 2. Target: Aureon Customizer Token System

The theme already ships a Customizer field system (`inc/customizer/fields/*.php`, `class-customize-field.php`) with per-section defaults (`aureon_get_option`, `aureon_color_option_defaults`, `aureon_font_option_defaults`). **No new infrastructure required** — the framework adds a new "Frontend" section group with the AETHER tokens as **defaults**, not hardcoded values.

### 2.1 New token families (Customizer "Frontend" panel)

| Family | Customizer IDs (default = AETHER) | Consumers |
|---|---|---|
| Color | `frontend_color_bg` = `#09090B`, `frontend_color_surface` = `#141416`, `frontend_color_text` = `#FFFFFF`, `frontend_color_muted` = `#A8B5C0`, `frontend_color_accent` = `#C8956C`, `frontend_color_accent_hover`, `frontend_color_border`, `frontend_color_error`/`success`/`info` | all components |
| Font | `frontend_font_heading` = `Cabinet Grotesk`, `frontend_font_body` = `Satoshi` (register via `font-library` module + `aureon_font_option_defaults`) | headings, body |
| Layout | `frontend_container_max` = `1200px`, `frontend_section_padding` = `100px 0`, `frontend_grid_gap` = `24px` | sections |
| Header | `frontend_announcement_height` = `40px`, `frontend_header_height` = `80px` | shell/header |
| Radius | `frontend_radius_sm` = `8px`, `frontend_radius_md` = `12px`, `frontend_radius_lg` = `24px`, `frontend_radius_pill` = `999px` | cards, buttons, inputs |
| Elevation | `frontend_shadow_sm`, `frontend_shadow_md`, `frontend_shadow_lg` (AETHER defaults) | cards, floating |
| Motion | `frontend_motion_reveal` = on, `frontend_motion_tilt` = on, `frontend_motion_parallax` = on, `frontend_motion_text` = on | fx/* (reduces-power fallback) |

---

## 3. Migration Mechanics

### 3.1 Tokenization pipeline

```
style.css (AETHER)  →  extract hardcoded values  →  map to token semantic names
                    →  emit tokenized bundle  frontend/assets/css/frontend.css
                    →  values reference CSS vars  var(--aureon-frontend-accent)
                    →  vars emitted by Aureon css-output.php from Customizer
```

### 3.2 CSS variable naming

- Namespaced to avoid collision: `--aureon-frontend-*`
- Semantic (use-based), not value-based: `--aureon-frontend-surface-1` vs `--aureon-frontend-141416`
- Alpha variants derived: `color-mix(in srgb, var(--aureon-frontend-bg) 90%, transparent)` (modern browsers; fallback = solid)

### 3.3 Output wiring (existing system)

- `inc/class-css.php` + `inc/css-output.php` already generate `:root` vars from options — extend with frontend tokens (same pattern as `aureon_get_option`).
- Inline critical header vars printed once, component CSS uses `var()` exclusively.

### 3.4 Legacy gold discrepancy

The template mixes `#C8956C` (modern `--gold`) and `#d4af37` (legacy gold, ~12 occurrences). **Migration decision:** normalize all to `--aureon-frontend-accent` = `#C8956C`; keep `#d4af37` only in vendor CSS (excluded) — document as intentional.

---

## 4. Token Mapping Table (source → target)

| Source Value | Frequency | Target Token | Notes |
|---|---|---|---|
| `#09090B` / `--void` | high | `--aureon-frontend-bg` | body background |
| `#141416` / `--surface` | high | `--aureon-frontend-surface` | cards, sections |
| `#A8B5C0` / `--chrome` | med | `--aureon-frontend-muted` | secondary text, borders |
| `#C8956C` / `--gold` | med | `--aureon-frontend-accent` | CTAs, active states |
| `#FFFFFF` | high | `--aureon-frontend-text` (on dark) | headings, body |
| `#1a1a1d`, `#232327` | med | `--aureon-frontend-surface-2`, `-3` | nested surfaces |
| `rgba(9,9,11,.9)` | med | `color-mix` on `--bg` | overlays, sticky header |
| `#d4af37` | low (12) | `--aureon-frontend-accent` (normalized) | legacy gold |
| `Cabinet Grotesk` | n/a | `aureon_font_heading` default | font-library |
| `Satoshi` | n/a | `aureon_font_body` default | font-library |
| `1200px` | n/a | `--aureon-frontend-container` | container-max |
| `100px 0` | n/a | `--aureon-frontend-section-padding` | sections |
| `40px / 80px` | n/a | `--aureon-frontend-announcement-h`, `--aureon-frontend-header-h` | header |
| `8/12/24/999px` radii | n/a | `--aureon-frontend-radius-{sm,md,lg,pill}` | cards, buttons |

---

## 5. Dark/Light Strategy

The template is dark-only. The `phantom-dark-mode.js` (dead) suggests a planned light mode. **Decision:** ship dark-only in v1 (default), keep token structure light-ready (each color family has dark/light pair defaults in Customizer; `body.is-light-mode` override block optional via Customizer toggle, default off).

---

## 6. Verification Checklist

- [ ] `rg --color=never "#[0-9a-fA-F]{3,8}" frontend/assets/css/frontend.css` → 0 hardcoded hex (except vendor/font files)
- [ ] All `var()` references resolve to Customizer defaults
- [ ] Compare screenshot of static page vs WP render (same palette)
- [ ] Change accent in Customizer → re-render matches new color (visual smoke test)
- [ ] WC module colors continue to work via `aureon_color_option_defaults` (existing bridge)