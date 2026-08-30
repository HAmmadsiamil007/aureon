# Phase 5 — Compatibility Audit

**Audit:** GeneratePress 3.6.1 ↔ GP Premium 2.5.6
**Date:** 2026-08-02
**Re-verified:** 2026-08-03 (compat checks: class/symbol separation — byte-consistent)

---

## 5.1 Theme ↔ Plugin Compatibility

| Check | Result |
|-------|--------|
| Plugin Requires WP 6.1 / theme Requires WP 6.5 | ✓ Theme requirement (6.5) ≥ plugin (6.1) |
| Plugin Requires PHP 7.2 / theme Requires PHP 7.4 | ✓ Theme stricter (7.4) — both satisfied on any modern host |
| Plugin module gating vs. theme version | ✓ typography/colors modules auto-disable on theme ≥3.1 |
| WC module vs. WC plugin presence | ✓ Only loads when WooCommerce active |
| Text domains | Distinct (`generatepress` / `gp-premium`) — no conflicts |

## 5.2 Symbol Collision Scan (functions & classes)

PHP token-level scan across both packages (209 PHP files) identified shared symbol names. **Every collision is guarded:**

### Shared function names — ALL guarded by `if ( ! function_exists() )` on one or both sides
| Function | Theme defines in | Plugin defines in | Guard |
|----------|------------------|-------------------|-------|
| generate_enqueue_color_palettes | inc/deprecated.php | colors module | ✓ both |
| generate_typography_default_fonts | inc/deprecated.php | typography module | ✓ both |
| generate_typography_convert_values | inc/deprecated.php | typography | ✓ both |
| generate_get_default_color_palettes | inc/deprecated.php | colors | ✓ both |
| generate_enqueue_google_fonts | inc/deprecated.php | typography | ✓ both |
| generate_get_all_google_fonts (+_ajax) | inc/typography.php:803 | typography/functions/functions.php:2657 | ✓ both |
| generate_get_google_font_variants | inc/typography.php | typography | ✓ both |
| generate_get_font_family_css | inc/typography.php | typography | ✓ both |
| generate_add_to_font_customizer_list | inc/typography.php | typography | ✓ both |
| generate_typography_set_font_data | inc/typography.php | typography | ✓ both |
| generate_font_css | inc/css-output.php:517 (**global fn**) | font-library (**static method** `GeneratePress_Pro_Font_Library::generate_font_css()`) | ✓ different scopes |

### Shared class names — ALL guarded by `if ( ! class_exists() )` on both sides
| Class | Notes |
|-------|-------|
| Generate_Hidden_Input_Control | theme customizer control + plugin legacy |
| Generate_Font_Weight_Custom_Control | theme + plugin |
| Generate_Text_Transform_Custom_Control | theme + plugin |
| GeneratePress_Rest (theme) vs **GeneratePress_Pro_Rest** (plugin) | **NO collision** — verified: theme declares `class GeneratePress_Rest` (inc/class-rest.php:15, namespace `generatepress/v1`); plugin declares `class GeneratePress_Pro_Rest` (inc/class-rest.php:16, namespace `generatepress-pro/v1`). Different names → no redeclaration risk. |

> **Note:** The plugin's `inc/class-rest.php` docblock misleadingly says `@package GenerateBlocks` / "Class GenerateBlocks_Rest" but the actual class name is `GeneratePress_Pro_Rest` — a cosmetic copy-paste from GenerateBlocks heritage. No functional impact.

## 5.3 Deprecated / Duplicate Functions

- Theme `inc/deprecated.php` (23 KB) — dozens of legacy aliases (e.g., `generate_get_option` variants), all `function_exists`-guarded.
- Plugin `inc/deprecated.php` + `inc/deprecated-admin.php` + `inc/legacy/` — legacy module stubs.
- Both themes' and plugin's deprecated layers are additive; no fatal redeclaration risk (the REST class names are distinct — see 5.2).

## 5.4 Hook Compatibility

- Theme emits `generate_*` hooks; plugin hooks into `generate_*` actions/filters (e.g., `generate_footer` → Elements Footer Hook, `generate_credits` → copyright module, `generate_dynamic_css_*` → external CSS file module).
- No hook-name collisions (theme: `generate_*`; plugin also `generate_*` but as **listeners**, not redeclarations — verified no plugin registers the same hook with a conflicting callback that would break theme output).
- `generate_dynamic_css_output` option shared intentionally (plugin external CSS uses the same cache key via filter).

## 5.5 Fatal Error Risk Register

| Risk | Severity | Status |
|------|----------|--------|
| Legacy eval() in hooks module | Medium | Capability-gated; only fires for admins using GP Hooks PHP boxes |
| Double Google Fonts enqueue | Low | Prevented by typography module gating |
| Double colors CSS | Low | Prevented by colors module gating |
| Widget text do_shortcode recursion | None | Standard pattern |
| `maybe_unserialize` in WXR importer (site-library) | Low | Only unserializes post meta from imported WXR files; `maybe_unserialize` is WP-safe |

## 5.6 Broken Includes / Circular Dependencies

- Theme functions.php: 17 requires + 9 structure requires — all files present (verified against file tree), no circular dependencies (structure files depend on functions loaded earlier).
- Plugin gp-premium.php: 20+ requires — all present.
- `include_once ABSPATH . 'wp-admin/includes/plugin.php'` (gp-premium.php:120) — standard pattern for `is_plugin_active()`.
- **No broken includes, no circular dependencies.**

## 5.7 WooCommerce Compatibility

- Theme WC support declared + hook-based wrapper replacement; plugin WC module adds product columns, quick-view bar, WC colors, WC typography — all filtered on top of theme hooks. No duplicate template overrides. ✓

## 5.8 Future Update Compatibility

- Both auto-disengage legacy modules against newer theme versions (colors/typography gating) — proven migration path.
- GenerateBlocks dynamic tags require GB; Elements degrade gracefully (filters reference `function_exists('generateblocks_pro')`).
- EDD updater passes `generatepress_version` to the license server — vendor-side compat checks supported.

## 5.9 Verdict

**PASS (9/10).** All function collisions guarded by `function_exists`; all class collisions guarded by `class_exists` or use distinct names (`GeneratePress_Rest` vs `GeneratePress_Pro_Rest` verified). Hooks, includes, WC, and future-update layers are clean. No unguarded redeclaration risk found. A runtime activation smoke-test is still recommended (see Phase 12) but no static blocker exists.
