# Phase 2 — Version Analysis

**Audit:** GeneratePress 3.6.1 (theme) + GP Premium 2.5.6 (plugin)
**Date:** 2026-08-02
**Re-verified:** 2026-08-03 (fresh header/constant scan — byte-consistent)

---

## 2.1 Theme Version Metadata

Source: `generatepress/generatepress/style.css` (lines 7–17) + `readme.txt`

| Field | Value |
|-------|-------|
| Theme Name | GeneratePress |
| **Version** | **3.6.1** |
| Requires at least (WP) | 6.5 |
| Tested up to (WP) | 6.9 |
| Requires PHP | 7.4 |
| License | GPLv2+ |
| Text Domain | generatepress |
| Author | Tom Usborne / EDGE22 Studios LTD. |
| Stable tag (readme.txt) | 3.6.1 |
| Code constant | `GENERATE_VERSION = '3.6.1'` (functions.php:18) — **matches** style.css |

## 2.2 Plugin Version Metadata

Source: `gp-premium/gp-premium.php` (lines 1–17) + `readme.txt`

| Field | Value |
|-------|-------|
| Plugin Name | GP Premium |
| **Version** | **2.5.6** |
| Requires at least (WP) | 6.1 |
| Requires PHP | 7.2 |
| Tested up to (WP) | 6.8 (readme.txt) |
| License | GPLv2+ |
| Text Domain | gp-premium |
| Author | Tom Usborne |
| Stable tag (readme.txt) | 2.5.6 |
| Code constant | `GP_PREMIUM_VERSION = '2.5.6'` (gp-premium.php:17) — **matches** |

## 2.3 Release Timeline (verified via web research)

| Release | Date | Status |
|---------|------|--------|
| GeneratePress 3.6.1 | Dec 1, 2025 | **Current stable** on WordPress.org |
| GP Premium 2.5.6 | May 29, 2026 | **Current stable** (emergency security release for Font Library upload CVE) |

Both installed copies are the **latest** public releases as of the audit date (2026-08-02).

## 2.4 PHP Compatibility

- Theme requires PHP **7.4+**; code reviewed under PHP **8.2.31** (audit environment): all 209 PHP files **lint clean** (Phase 7). No deprecated PHP-8 functions in use (verified: no `create_function`, no curly-brace string offsets, no `each()`).
- Plugin requires PHP **7.2+**; `version_compare( PHP_VERSION, '5.4', '>=' )` gates Site Library (line 147) — vestigial but harmless.
- PHP max compatibility: no hard caps found; both packages are PHP 8.x-safe.

## 2.5 WordPress Compatibility

- Theme: Requires 6.5, Tested up to 6.9. Uses only standard APIs (`wp_enqueue_*`, Customizer, `register_nav_menus`, block editor `add_theme_support`).
- Plugin: Requires 6.1, Tested up to 6.8. Uses REST API, Customizer, Gutenberg (dist/ bundles), block editor assets.

## 2.6 WooCommerce Compatibility

- Theme: `add_theme_support( 'woocommerce' )` in functions.php:32; dedicated WC integration in `inc/plugin-compat.php` (removes default wrappers, re-wraps with GP structure, adds WC CSS at priority 100).
- Plugin: `woocommerce/woocommerce.php` module **only loads when WooCommerce plugin is active** (`is_plugin_active` check, gp-premium.php:121-125) — no forced dependency.
- No `woocommerce.php` template overrides shipped in theme (WC handled via hooks, not template copy — the officially recommended pattern).

## 2.7 Dependencies

| Type | Theme | Plugin |
|------|-------|--------|
| Required | WordPress 6.5+, PHP 7.4+ | WordPress 6.1+, PHP 7.2+ |
| Optional | — | GeneratePress theme (all modules assume it) |
| Optional | — | WooCommerce (WC module) |
| Optional | — | GenerateBlocks (site-library filters reference it) |
| Third-party bundled | FontAwesome 4.7 (local), selectWoo (local), select2 (local), alpha-color-picker (local), infinite-scroll pkgd (local), WXR importer (local, site-library) | Same libraries as needed |

**All third-party assets are bundled locally** — no runtime CDN dependencies except Google Fonts (user-optional) and the official generatepress.com update/license API.

## 2.8 Deprecated / Legacy Code

- Theme ships `inc/deprecated.php` (23 KB) — legacy function aliases preserved for backward compatibility (all guarded by `function_exists`).
- Plugin ships `inc/deprecated.php`, `inc/deprecated-admin.php`, `inc/legacy/`, and deprecated modules (`hooks`, `page-header`, `sections`) gated behind option checks — intentional backward-compat layers, not bugs.
- `hooks/functions/hooks.php:22` — legacy GP Hooks module uses `eval()` (see Phase 6; capability-gated).

## 2.9 Future-Compatibility Risks

| Risk | Severity | Detail |
|------|----------|--------|
| PHP 9 / WP 7 breaking changes | Low | Both actively maintained; no experimental APIs |
| `eval()` in 2 legacy modules | Medium | Deprecated features; may be removed; monitor updates |
| Font Library REST hardening | None | Already patched in 2.5.6 (manage_options gate + MIME whitelist) |
| Theme 4.0 refactor | Informational | GP historically migrates major versions carefully (3.0→3.1 colors/typography gating logic proves this) |

## 2.10 Verdict

**PASS (9/10).** Version metadata is consistent across style.css / plugin header / readme.txt / code constants. Both are current stable releases. PHP 7.4+ / WP 6.5+ requirements are modest and future-safe.
