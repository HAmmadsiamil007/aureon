# 07 — Customizer Deep Audit

**Method:** registered controls enumerated from code only (`inc/customizer.php` + pack composer `customize_register` callbacks). No live Customizer instance available — runtime behavior (preview refresh, saved-value round-trip) is UNPROVEN.

## Two Customizer systems coexist

| System | Where | Storage | Reader | Consumed by current frontend? |
|---|---|---|---|---|
| Platform (AUREON/GeneratePress-derived) | `inc/customizer.php` (1,575 lines) + `inc/customizer/` fields/controls | theme mods + `aureon_settings` option bucket | `aureon_get_option()` | Mostly **no** on complete-page routes (presentation suppressed); some values bridge via tokens |
| Pack (Vineta) | `designs/vineta/composer.php` — `vineta_customize_register_hero_banner` (priority 30), `vineta_customize_register_colors` (32) + CSS emitter `vineta_emit_customizer_css` (wp_head 20) | `aureon_settings` option (via `vineta_get_customizer_value`) | same | **Yes** — this is the one that reaches Vineta UI |

## Pack-registered controls (verified in code)

### Hero banner (section: added to panel; priority 30)

Registered controls (IDs from composer):
- hero banner enable/heading/subtitle group (repeater schema extended via `aether_repeater_schemas` filter — `vineta_extend_hero_schema`)
- slide image / heading / link per repeat (repeater control)

- **Storage:** `aureon_settings` option array
- **Reader:** `vineta_get_customizer_value( $key, $default )`
- **DOM target:** pack hero slider markup via VinetaPageData + JS; CSS emission for images
- **Fallback:** frozen demo slides in index.html
- **Status:** IMPLEMENTED — runtime UNPROVEN (no live preview test possible here)

### Colors (priority 32)

- Registered color controls (pack palette: primary/accent/background/text families — exact IDs enumerated in composer lines ~1386–1443)
- **CSS path:** `vineta_emit_customizer_css()` prints `:root { --vineta-*: value }` at wp_head 20 — this *does* reach the pack CSS (`styles.css` consumes `--vineta-*` variables).
- **Status:** WORKING_NEEDS_HARDENING (sanitize_hex_color verification required per control)

## Platform Customizer → current UI reachability

Platform controls (site identity, typography manager, colors, spacing, woocommerce presentation, menu-plus, page-header, …) were built for the AETHER shell + `luxury` design. With vineta complete-page active:

- Header/footer/typography/spacing platform controls → **STORED_NOT_CONSUMED** (their CSS never renders; shell suppressed).
- Site Identity (logo/favicon/title) → logo **CONSUMED** via JS logo bridge in ferm-page.php; favicon **CONSUMED** via `aureon_aether_favicons()` (hardcoded `frontend/assets/images/favicon/*` files — NOT the Customizer site-icon; separate paths).
- Typography manager → partially consumed: `aureon-google-fonts` enqueue deliberately kept, bridged into `--font-heading`/`--font-body` tokens (comment in suppression function). Pack CSS variable consumption of those tokens UNPROVEN.
- Menu locations (primary/footer) → CONSUMED via pack server-side splicing.
- Newsletter/social/etc → consumed only if pack markup matches; social links in VinetaPageData `site` — UNPROVEN.

## Control audit table (consolidated)

| Area | Control | Storage | Reader | Bridge | Reaches Vineta UI? | Status |
|---|---|---|---|---|---|---|
| Logo | custom_logo | theme mod | get_theme_mod | ferm-page.php JS swap | YES | WORKING_NEEDS_HARDENING |
| Favicon | WP site icon + hardcoded pack favicons | option | — | aureon_aether_favicons (hardcoded files) | PARTIAL (hardcoded pack icons win) | WRONG_STORAGE (two favicon systems) |
| Hero slides | pack repeater | aureon_settings | vineta_get_customizer_value | VinetaPageData | YES | UNPROVEN |
| Hero heading/sub | pack controls | aureon_settings | same | VinetaPageData | YES | UNPROVEN |
| Colors | pack color controls | aureon_settings | same | `--vineta-*` CSS vars | YES | WORKING_NEEDS_HARDENING |
| Typography | platform typography manager | aureon_settings/theme mods | aureon_get_option | font enqueue + token bridge | PARTIAL | UNPROVEN |
| Header layout/platform | platform header controls | aureon_settings | aureon_get_option | none (shell suppressed) | NO | STORED_NOT_CONSUMED |
| Footer layout/platform | platform | aureon_settings | aureon_get_option | none | NO | STORED_NOT_CONSUMED |
| Spacing | platform spacing module | aureon_settings | aureon_get_option | none | NO | STORED_NOT_CONSUMED |
| WC presentation | platform woocommerce module | aureon_settings | aureon_get_option | none on complete-page | NO | STORED_NOT_CONSUMED |
| Menus | nav menus (primary, footer) | WP terms | wp_get_nav_menu_items / wp_nav_menu | pack splicing | YES | WORKING_NEEDS_HARDENING |
| Demo mode | aether_demo_mode | aureon_settings | vineta_get_demo_mode | composer switching | YES | IMPLEMENTED (auto-mode stub — see 04 §F4) |
| Announcement | VinetaPageData announcement | aureon_settings | composer | page data | YES | UNPROVEN |
| Newsletter/social | aether options + pack | aureon_settings | composer | page data / aether AJAX | PARTIAL | UNPROVEN |

## Reset behavior

- Platform defaults: `aureon_option_defaults` filter (+ pack defaults at priority 20 via `aether_design_defaults` → pack `tokens.php`).
- Pack defaults: `designs/vineta/tokens.php` (607-line token sheet feeding `--vineta-*` and layout defaults).
- No observed reset-all mechanism for pack controls; Customizer core handles per-control defaults.

## Findings

| # | Finding | Severity |
|---|---|---|
| CU1 | Two parallel Customizer systems; the platform one (1,575 lines + module controls) is largely dead weight for the current design | HIGH (maintenance + user confusion: admin edits controls that do nothing) |
| CU2 | Hardcoded favicon files bypass WP site icon | MEDIUM |
| CU3 | Pack color CSS emitter runs at wp_head 20 on every complete-page route; per-control sanitization must be verified before production | MEDIUM (security) |
| CU4 | `aether_demo_mode` `auto` semantics stubbed (`return true`) — Customizer "auto" toggle misleads | MEDIUM |
| CU5 | No Customizer preview integration for VinetaPageData-driven sections (selective refresh impossible on frozen HTML) | LOW/known-limit |
