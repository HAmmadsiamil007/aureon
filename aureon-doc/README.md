# Aureon — Documentation Index

**Aureon** is a lightweight, performance-first WordPress theme, and **Aureon Studio** is its companion premium-feature plugin. Together they form a complete site-building system. Both are a full rebrand of GeneratePress 3.6.1 + GP Premium 2.5.6 (GPL-2.0-or-later).

This documentation folder contains:

| File | Contents |
|---|---|
| [`README.md`](./README.md) | This index — product identity, folder map, quick start, licensing. |
| [`THEME.md`](./THEME.md) | Complete reference for the **Aureon theme** — architecture, every file, option system, Customizer framework (incl. React controls & JS globals), CSS pipeline, hooks, typography, block editor, meta boxes, features. |
| [`PLUGIN.md`](./PLUGIN.md) | Complete reference for the **Aureon Studio plugin** — module system, all 16 modules in detail, shared library, Dashboard, compiled assets, legacy code, known issues. |
| [`CHANGELOG.md`](./CHANGELOG.md) | Full change history — rebrand, fingerprint removal, i18n cleanup, collision fixes, deprecation fixes, WooCommerce session fix, verification, Ferm Living integration. |
| [`STATUS.md`](./STATUS.md) | Up-to-date status report: what is done, what is verified, what remains open — includes Ferm Living integration status. |
| [`AETHER-BRIDGE.md`](./AETHER-BRIDGE.md) | **How the AETHER frontend connects to the core theme** — boot sequence, output suppression, asset/CDN contract, `aetherAjax` context, WC template routing, all 8 `inc/aether-*.php` feature bridges (SEO, security, analytics, newsletter, AJAX, cart, tokens, performance), design resolution. |
| [`FRONTEND-OPERATIONS.md`](./FRONTEND-OPERATIONS.md) | **How to operate the AETHER frontend** — edit styling/data/markup/JS, add components, replace a frontend via design packs, create a dynamic client frontend (M11 pipeline + core-freeze rules), testing harness. |
| [`../docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md`](../docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md) | **Authoritative guide for replacing/editing the frontend** — architecture, golden rule, safe zones, forbidden zones, pack creation, component/section editing, fonts, CSS, JS, cart bridge, complete-page mode, data flow, testing, rollback, cookbook. |
| [`../docs/TEMPLATE_REQUIREMENTS_FOR_CORE_THEME.md`](../docs/TEMPLATE_REQUIREMENTS_FOR_CORE_THEME.md) | **Complete guide for creating templates** — two-mode architecture (Component Mode + Complete-Page Mode), feature map, connection points, template types, cloning, creation, data flow, components, sections, assets, tokens, adapters, security, performance, accessibility, testing, deployment, feature integration checklist. |
| [`../docs/FRONTEND_REPLACEMENT_PROMPT.md`](../docs/FRONTEND_REPLACEMENT_PROMPT.md) | **Copy-paste prompt for AI/developer** — two-mode workflow (Component Mode + Complete-Page Mode), architecture overview, step-by-step replacement process, forensic audit, dependency classification, template contract, component patterns, data contracts, testing checklists. |
| `../Report/DETECTION.md` | GeneratePress fingerprint inventory + copyright/trademark analysis (now **RESOLVED**, see §10 addendum). |
| `../Report/COMPARISON_AUREON_VS_GENERATEPRESS.md` | Pro audit: Aureon vs GeneratePress feature parity + bug report. |

---

## Product identity

| | Aureon (theme) | Aureon Studio (plugin) |
|---|---|---|
| Version (display) | 1.0.0 | 1.0.0 |
| Version (internal constant) | `AUREON_VERSION` = `3.6.1` | `AUREON_STUDIO_VERSION` = `3.0.0` |
| Text domain | `aureon` | `aureon-studio` |
| Requires WP | 6.0+ | 6.1+ |
| Requires PHP | 7.4 | 7.2 |
| License | GPL-2.0-or-later | GPL-2.0-or-later |
| Derived from | GeneratePress 3.6.1 | GP Premium 2.5.6 |

**AETHER frontend engine** (repo root `frontend/`, deployed to `wp-content/frontend/`): a self-contained, data-driven presentation engine — design packs (luxury = engine default, lumen = shipped pack) on top of one core. See [`AETHER-BRIDGE.md`](./AETHER-BRIDGE.md) + [`FRONTEND-OPERATIONS.md`](./FRONTEND-OPERATIONS.md).

> **Why internal constants differ from the displayed version?** The plugin gates features on theme/plugin version numbers. Keeping internal constants at upstream-compatible values (3.6.1 / 3.0.0) guarantees every feature gate resolves correctly while the product displays "1.0.0". Do **not** lower these constants.

## Folder map (this repo)

```
wordpress/
├── aureon/                  ← Aureon product (this documentation covers it)
│   ├── theme/               ← Aureon theme (activate as "Aureon") — includes the
│   │                           AETHER bridge (inc/frontend.php + 8 inc/aether-*.php)
│   └── plugin/              ← Aureon Studio plugin (activate as "Aureon Studio")
├── frontend/                ← AETHER frontend engine (kernel, 23 adapters, 54 components,
│                               26 sections, design packs in designs/, tokens, manifest, assets)
├── generatepress/           ← upstream reference (GeneratePress 3.6.1 + GP Premium 2.5.6) — gitignored
├── Report/                  ← engineering & audit reports
├── docs/                    ← engineering & audit reports (forensics, phase reports, M11 plans)
├── aureon-doc/              ← this documentation
└── .serena/memories/        ← persistent project memory
```

## Quick start

1. Copy `aureon/theme/` into `wp-content/themes/aureon/` and activate **Aureon**.
2. Copy `aureon/plugin/` into `wp-content/plugins/aureon-studio/` and activate **Aureon Studio**.
3. Copy `frontend/` into `wp-content/frontend/` (**omit `source/`**) — this is the AETHER engine; the theme boots it via `inc/frontend.php`.
4. Go to **Appearance → Aureon** (Dashboard) to enable modules, then **Appearance → Customize** to configure.
5. All options live in the Customizer; per-page options live in the page/post editor meta boxes.
6. Switch designs by setting the `aether_active_design` option (`''`/`'luxury'` = engine default, `'lumen'` = shipped design pack) — see [`FRONTEND-OPERATIONS.md`](./FRONTEND-OPERATIONS.md) §4.

## Licensing note

Aureon derives from GeneratePress / GP Premium (GPL-2.0-or-later, © EDGE22 Studios / Tom Usborne). As a derivative it remains GPL-2.0-or-later, © Aureon Studio. Both products ship the full GPL license text **plus the upstream attribution** in `license.txt` — this attribution is legally required and intentional; it is the only place the GeneratePress name appears, and it must stay.

## Repo conventions (read before editing)

- **Sentinel words — do not rename:** `GenerateBlocks`/`generateblocks` (live third-party plugin integration, ~519 refs), `regenerate`/`generated`/`generates` (English words), `gpDynamicTextType`/`gpDynamicDisplayType` (DB schema).
- **JS globals are paired writer/reader** — PHP `wp_localize_script()` writes them, JS bundles consume them. Renaming must keep each pair in sync (see THEME.md §15 / PLUGIN.md §9).
- **Enqueue handles are paired** — an `wp_enqueue_script()`/`wp_localize_script()` pair must share the identical handle string.
