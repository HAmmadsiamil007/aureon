# AETHER-PRO-INTEGRATION-PLAN — COMPLETE + fully re-verified live (2026-08-08)

## Result: Plan DONE (100%). Independent re-verification confirms §8 claims. No gaps found beyond the documented deferred items.

## What the plan did (WS-1..WS-7, one pass)
Goal: AETHER frontend engine 100% Customizer-driven (was hardcoded; preview bailed).
- **WS-1 Tokenizer v2** — `aureon/theme/inc/aether-tokens.php` emits 23 tokens (12 colors incl. `--chrome` alias, 2 font stacks, 9 layout). `is_customize_preview()` bail REMOVED (preview now shows live tokens = G10).
- **WS-2 Typography bridge** — `frontend.php` suppress list keeps `'aureon-google-fonts'` (enqueues merged Google Fonts request via `Aureon_Typography::get_google_fonts_uri()`, `display=auto`); `'aureon-fonts'` stays suppressed. `aether_font_for($role,$saved)` precedence: aether_font_* → dynamic typography body/all-headings entries → classic font_* (only when `aureon_is_using_dynamic_typography()` false) → AETHER default (Cabinet Grotesk/Satoshi).
- **WS-3 Colors** — `customizer/fields/frontend.php` +11 `Aureon_Customize_Color_Control` (empty = inherit theme). `aether_resolve_color()` precedence in aether-tokens.php: explicit `aether_color_*` → customized `global_colors` palette (via `aether_get_custom_palette()`, only when differs from aureon_get_defaults() global_colors) → default.
- **WS-4 Layout** — 7 `Aureon_Range_Slider_Control` (container 960-1920, announcement 32-80, header 60-120, grid gap 8-48, radius sm 0-20/md 0-40/lg 0-60) + `aether_section_padding` text. style.css/pages.css/responsive.css literals → `var(--…)`; style.css `:root` declares same defaults (self-sufficient without bridge).
- **WS-5 Preview** — all new controls transport=refresh (no custom JS needed).
- **WS-6 Cleanup** — fix_*.php / _inspect_options.php / _enable_dyn_typo.php / frontend/test-categories.php all gone (verified local + container).
- **WS-7 Docs** — plan §8 + README note on theming AETHER from Customizer.

## Re-verification evidence (2026-08-08, container aureon_wp :8080)
- php -l theme/inc + frontend: 0 errors.
- Live head `?nocache=…`: `<style id='aether-tokens-inline-css'>` — 23 vars (`--void … --radius-pill`), `--font-heading: 'Cabinet Grotesk', sans-serif`, `--font-body: 'Space Grotesk', sans-serif`.
- Google Fonts link live: `fonts.googleapis.com/css?family=Space+Grotesk:300,regular,500,600,700&display=auto` + preconnect (single merged request).
- Customizer (Playwright, admin/admin123): 0 console errors (only the 2 documented WP-core warnings: sandbox iframe + tooltip prop). AETHER Frontend section: 16 section toggles, 8 shell/motion toggles, announcement text/url, shop_per_page=9, 11 color pickers, 7 sliders, section padding — ALL match fields/frontend.php.
- **Round-trip PROVEN**: radius-md 12→18 via Customizer → Publish → `--radius-md: 18px` in head; `.category-card` computed border-radius 18px; setting persisted (customizer reopened showed 18); reverted to 12 + published.
- Core theme surfaces (frontend active): Typography panel — Font Manager (Space Grotesk chip) + Typography Manager ("Body — Space Grotesk") + Google font-display = renders, 0 errors. Colors — Global Colors React (7 swatches: contrast/contrast-2/contrast-3/base/base-2/base-3/accent) + 10 legacy groups (Body…WooCommerce) = renders.
- Plugin surfaces: Customizer WooCommerce panel — Store Notice/Product Catalog/Product Images/Checkout/Colors sub-sections present; plugin dashboard themes.php?page=aureon-options — 10 modules activated, Import/Export/Reset, 0 errors.
- G10 preview: customizer iframe contains the token block + body computed "Space Grotesk".
- /shop/ final smoke: 9 product cards (shop_per_page), WC stylesheets loaded, 0 console errors/warnings.

## Gotchas worth remembering
- Token block id is `aether-tokens-inline-css` but WP prints it with DOUBLE quotes (`id="…"`) — regex must accept both quote styles.
- PowerShell → docker exec quoting: inner double quotes get stripped (glob expansion breaks find/curl). Use a .sh file + docker cp instead of inline `docker exec sh -c '…'` for anything non-trivial.
- Container shell is busybox ash (no python3); use php -r or files for parsing.
- WS pluan file AETHER-PRO-INTEGRATION-PLAN.md is untracked; 20+ theme/frontend files modified-but-uncommitted (git status M). Next recommended step: commit + push (repo main currently at a128205).

## Doc'd next steps (P2/P3, out of scope)
- G8 WC color bridge `--engine-wc-*` (WC plugin inline CSS already styles WC pages).
- G11 Elements / G12 Blog plugin settings — not integrated.
- G9 classic fonts (kept in fallback chain only when dynamic typography off).
- Environment: Action Scheduler shows 83 past-due actions (cron not running in container — pre-existing, non-plan issue).