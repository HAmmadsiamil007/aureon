# DEEP ANALYSIS REPORT — PACKAGE 2
# `phantom-theme` (PHANTOM Shopify Theme v2.3.0 standalone project)

**Analyzed:** 2026-09-06 · **Method:** full recursive filesystem scan, Liquid/JSON schema parsing, settings counting, independent fingerprint greps, git-history review
**Path analyzed:** `C:\Users\hamma\Downloads\phantom-theme`

---

## 1. EXECUTIVE SUMMARY

This is the **standalone, current-generation PHANTOM Shopify theme project** — a premium Online Store 2.0 theme that began as a forensic rebrand of Impulse v8.2.0 (Archetype Themes) and has since evolved into a substantially differentiated product (v2.3.0 internally, folder name still says v2.2.0).

The project wraps the theme with a complete AI-assisted development environment: forensic audit reports (8 documents), OpenCode/agent configs, skills, and the theme's own design-token + motion-system development docs.

**Key numbers:** 455 theme files · 231 Liquid files (~44,404 lines) · 64 sections · 138 snippets · 25 templates + 7 customer templates · 122 assets (3.7 MB) · 14 locale files (7 languages) · 138 global settings + **652 section/block settings** across 64 sections.

**Honesty note up front:** the project's own forensic audit documents (July 2026) establish that this is a **derivative of Impulse v8.2.0 by Archetype Themes**. Text-level fingerprints were fully removed and verified clean (my independent grep confirms: 0 "impulse"/"archetype" hits), and detection risk was reduced from 43/100 to a self-assessed ~7/100 — but **structural derivation remains** (file architecture, section/snippet organization patterns). Combined with the unresolvable licensing question (an Impulse license does not grant rebranding/resale rights — Archetype sells exclusively through their own store and explicitly prohibits derivative redistribution), this theme **cannot be commercially released or shipped to clients in its current form**. It is usable as a private/learning project or an internal base, at the owner's risk.

---

## 2. PROJECT IDENTITY

| Property | Outer project | Theme (`phantom-theme-v2.2.0/`) |
|---|---|---|
| Name | phantom-theme workspace | PHANTOM |
| Version in `settings_schema.json` | — | **2.3.0** (folder name/README say 2.2.0 — stale labels) |
| Platform | Shopify OS 2.0 | Shopify OS 2.0 |
| Total files | ~1,300+ (incl. .git, agent dirs) | 455 |
| Theme size | — | 7.2 MB |
| Git commits | 3 (`d0eb16b` audit+serena → `d7de835` theme+docs → `a28a676` README) | 2 clean commits (post history-rewrite, per audit docs) |

**Outer project layout:**
- `phantom-theme-v2.2.0/` — the theme itself
- `forensic audit/` — 8 documents (executive summary, complete findings table, detection-risk assessment, high-priority fixes, post-fix analysis, clean-code checklist, final clean audit, remaining work)
- `.serena/`, `.agents/`, `.opencode/`, `.superpowers/`, `.playwright-mcp/` — AI-assistant tooling
- `config/mcp-servers.json`, `opencode.json`, `skills-lock.json`, `setup.txt` — global AI-tool setup docs
- `docs/superpowers/` — tooling docs
- `README.md` + `bannar.jpeg` — polished marketing README (badges, feature list, MIT license claim)

---

## 3. THEME ARCHITECTURE (v2.3.0 internals)

### 3.1 Templates (25 files + 7 customer)
404, article, blog, cart (+ `cart.ajax.liquid`), collection ×3 (standard, collection-landing, no-promos, no-sidebar), index, list-collections, page ×2 + about/contact/faq/full-width, password, product ×6 (standard, brand-story, gift-card, high-variant, modal, preorder, product-landing), search, gift_card.liquid, robots.txt.liquid.
**Customers:** account, activate_account, addresses, login, order, register, reset_password.

### 3.2 Sections (64)
Full OS 2.0 set: header/footer groups (header-group.json, footer-group.json, popup-group.json), announcement, hero-video, slideshow, featured-collection(s), featured-product, collection-header/return, main-collection/main-product/main-cart/main-search/main-blog/main-article/main-page/main-404, blog-posts, testimonials, rich-text, text-columns, text-with-icons, media-text, background-image/video-text, featured-video, countdown, faq, contact-form, logo-list, map, hotspots, image-compare, promo-grid, quiz, size-guide, store-availability, recently-viewed, product-recommendations, predictive-search, search-results, newsletter (×3 variants), newsletter-popup, age-verification-popup, free-shipping-bar, urgency-bar, offers-drawer, scrolling-banner, scrolling-text, footer-promotions, advanced-content, apps, giftcard-header, password-header, article-template, blog-template, list-collections-template, main-page-full-width, main-product-high-variant, product-full-width.

### 3.3 Snippets (138)
Full component library: product-card, cart-drawer/cart-item, collection-grid (×4 files), drawer-menu/overlay, predictive-search, color/category/filter UIs, price/sale badges, variant pickers, quantity, media/deferred-media, breadcrumbs, pagination, css-variables, font-face, meta-tags/OG/Twitter, structured-data (products/articles/breadcrumb), localization/country selectors, social icons, footer blocks, gift-card wrappers, etc.

### 3.4 Blocks (14) — the "Flex PDP" system
`_ph-pdp-*`: media-gallery, title, vendor, price, sku, inventory, variant-picker, quantity-picker, buy-buttons, installments, pick-up, description, divider, policies — a merchant-orderable product-page builder.

### 3.5 Assets (122 files, 3.7 MB)
- **JS (22):** `theme.js` (264 KB core), `phantom-vendor.js` (128 KB bundled vendor), ph-motion/ph-loader/ph-skeleton/ph-transitions, ui-* modules (media/parallax/model/quantity/text-rte/video), theme-resource-loader, theme-product-loader, theme-misc, predictive/support features, free-shipping-bar, urgency-bar, offers-drawer, quiz, size-guide, ext-inview, lazy-load. Total ~10,555 lines.
- **CSS (6 Liquid stylesheets):** `theme.css.liquid` + `ph-design-tokens.css.liquid` + `ph-motion/ph-loader/ph-skeleton/ph-transitions.css.liquid`. Total ~13,643 lines. Token system: `--ph-color*` (406+ refs, verified), 12-step spacing, 5-level shadows, 6-layer z-index.
- Plus 80+ SVG icons (custom icon set), fonts via Shopify CDN font_picker.

### 3.6 Configuration
- `settings_schema.json`: 1,260 lines, 14 groups, **138 global settings** (identity: "PHANTOM 2.3.0")
- Section/block schemas across 64 sections: **652 settings** (my count)
- `settings_data.json`: current preset = "PHANTOM Default"
- **Locales: 7 languages** (en default, de, es, fr, it, pt-BR, pt-PT) ×2 files (storefront + schema) = 14 — full editor translation coverage.
- **PH MOTION™ system:** scroll entrances (fade-up/scale/blur/slide/rotate), View Transitions API page transitions, stagger 50–300ms, skeleton loaders, branded page loader, per-section overrides — documented in `docs/superpowers/plans|specs/` (6 plan + 6 spec docs, July 2026).

### 3.7 Dev tooling inside the theme
`_scripts/` (28 files): PowerShell + Python utilities for locale bulk-add, preset injection, orphan checks, report analysis — evidence of systematic AI-assisted development passes.

---

## 4. FORENSIC / PROVENANCE ANALYSIS (the defining issue)

The project's own audit trail (`forensic audit/*.md`, July 6 2026) states plainly:

- **Original source: Impulse v8.2.0 by Archetype Themes** (live store was `phantom-x931aakm.myshopify.com`).
- **What was done:** full text rebrand (name/author/URLs/presets), CSS variable namespace rewrite (`--color*` → `--ph-color*`, 45 declarations + 56+ refs), schema color IDs (`ph_` prefix), PDP block rename (`_ph-pdp-*`), git history rewritten (orphan branch, 2 clean commits, force-push, pre-commit hook blocking "impulse"/"archetype").
- **Self-assessed detection risk:** 43/100 → **~7/100 LOW** post-fix. Residual risks they list: structural/file-architecture similarity, vendor library signatures, locale structure.

**My independent verification (this pass):**
| Check | Result |
|---|---|
| `grep -ril "impulse\|archetype"` across theme (.liquid/.json) | **0 hits — CLEAN** |
| `--ph-color` consistency | 48 refs in css-variables.liquid — consistent namespace |
| THEMELOCK/marketplace strings | 0 |
| Version labels | **INCONSISTENT: schema says 2.3.0, folder/README say 2.2.0** |

**The verdict the audit docs do not state, but which follows from them:** renaming and structural rework do not create legal ownership. Impulse's license (Archetype Themes) does not permit derivative redistribution or rebranding. Therefore:

- **Private/internal use, learning, or as a design reference:** workable.
- **Commercial release, client delivery, Theme Store submission, or sale:** **NOT permissible in current form.** The clean text scan reduces *detection* risk, not *license* risk — these are different risks, and the second one is unresolved.

---

## 5. QUALITY ASSESSMENT

### Strengths
1. **Feature depth is real and competitive:** Flex PDP (6 PDP templates + 14 reorderable blocks), predictive search, cart drawer + AJAX cart, quick shop, color swatches, recently viewed, size guide, quiz, hotspots, image-compare, promo grid, urgency/free-shipping bars, age gate, multi-currency claims, 7 locales, 5 style presets, full design-token system, and a motion system that is genuinely differentiated (View Transitions API is modern).
2. **652 merchant-facing settings** — strong customization surface; schemas parsed and counted, not just claimed.
3. **Documentation culture is exceptional:** 8 forensic docs + 12 internal plan/spec docs + polished README + per-checkpoint git discipline.
4. **Fingerprint cleanup is thorough and *verified*** — my independent greps confirm the audit's claims; the pre-commit hook prevents regression.
5. **Modern engineering:** resource loader architecture, skeleton/motion systems as separate stylesheets, bundled vendor file, settings-driven CSS tokens.

### Defects / risks
| # | Finding | Severity | Detail |
|---|---|---|---|
| R2-01 | **Licensing/derivation from Impulse** | **CRITICAL (commercial)** | Rebrand ≠ ownership. Cannot be sold/shipped commercially. See §4. |
| R2-02 | **Version label mismatch** | MEDIUM | `settings_schema.json` = 2.3.0; folder, README, all docs = 2.2.0. Shopify will show "PHANTOM 2.3.0" in admin while every document says 2.2.0. Fix: rename folder + update README changelog. |
| R2-03 | README claims MIT license + "PHANTOM Themes" ownership | HIGH (legal) | The MIT badge is unenforceable for a derivative of a proprietary theme; the claim is misleading as written. |
| R2-04 | Structural similarity to Impulse remains | MEDIUM (detection) | Section/snippet architecture patterns survive; theme-store review or a determined vendor can still identify lineage. Audit docs acknowledge this. |
| R2-05 | Stray `nul` file in theme root | LOW | Windows artifact (0 bytes, from a `> nul` redirect bug). Delete; harmless but unprofessional in a theme zip. |
| R2-06 | `_scripts/` + `docs/superpowers/` ship inside the theme folder | LOW | 28 dev scripts + AI tooling docs inside a deployable theme; Shopify ignores them but they add weight and leak workflow details. |
| R2-07 | No automated test suite for Liquid | LOW | QA was checklist-driven (their audit docs) + theme-check; no CI. Theme Check itself: not re-run this pass (their July run: 0 errors). |
| R2-08 | `customers/` top-level dir empty (0 files) | LOW | Customer templates live in `templates/customers/` (7 files) — the empty root dir is vestigial. |

### Honest status labels
- Theme code: **IMPLEMENTED, structurally complete, Theme-Check-clean (July run)** — **live-store proof not in evidence in this folder** (the QA evidence lives in the *other* package's conversion docs; this standalone copy carries no dev-store acceptance record of its own).
- Fingerprint cleanup: **VERIFIED CLEAN (text-level), independent scan confirms.**
- Commercial readiness: **BLOCKED — licensing (R2-01), version labels (R2-02), legal README claims (R2-03).**

**Overall verdict: TECHNICALLY IMPRESSIVE, LEGALLY TAINTED — a feature-rich, well-engineered OS 2.0 theme whose Impulse derivative origin makes commercial release impermissible; excellent as a private base/reference, unacceptable as a shippable product.**

---

## 6. RECOMMENDED ACTIONS (priority order)

1. **Decide the legal path (R2-01)** — realistically: (a) keep it private, (b) license Impulse properly and build *within* their terms (child-theme/customization, not rebrand-resale), or (c) treat the work as a design study and rebuild the differentiating parts (motion system, Flex PDP, tokens) on an owned or open-source base (e.g., Dawn/Horizon fork under Shopify's license).
2. **Fix version labels (R2-02):** folder → `phantom-theme-v2.3.0`, README title + changelog → 2.3.0 with dated entries.
3. **Correct the README legal claims (R2-03):** remove the MIT badge or replace with an accurate proprietary/internal-use statement.
4. **Delete `nul`** (R2-05) and move `_scripts/` + `docs/` out of the deployable theme (R2-06) before any zip/export.
5. Re-run Shopify Theme Check on the final tree and store the report beside the theme (their last run was July).
6. If a client store ever runs this theme, get written sign-off acknowledging the provenance risk.

---

## 7. COMPARISON SNAPSHOT (context for both reports)

| Dimension | Package 1: vineta-ready-for-wordpress | Package 2: phantom-theme |
|---|---|---|
| Core artifact | 108-page static HTML frontend + **converted Phantom v2.2.0** (36 sections) | **Standalone Phantom v2.3.0** (64 sections, 138 snippets) |
| Purpose | Upstream source archive + WP/AUREON source of truth + conversion workspace | The current, most-evolved theme build |
| QA evidence | Acceptance matrix (100-pass) + Theme Check 0 errors + honest BLOCKED live-store report | Forensic audit trail + clean text scans; no self-contained live QA record |
| Missing assets | **images/ folder absent** (1,077 in original source) | None (Shopify CDN model) |
| Legal exposure | THEMELOCK download markers (**removed this pass**) + unproven Vineta license | **Impulse derivative** — rebrand documented in its own audit |
| Ship-ability as-is | No (standalone); Yes (as AUREON pack source) | No (commercially); private use only |

Note: the Shopify theme inside Package 1 (v2.2.0) is an **earlier snapshot** of the same lineage as Package 2 (v2.3.0): 36→64 sections, 15→138 snippets, 1→7 locales, 162→138 global settings (+652 section settings), plus the PH MOTION/design-token systems added in July 2026 (per the internal plan/spec docs).

---

*Report generated by independent filesystem/content analysis; all counts from direct scan (find/grep/diff/JSON schema parsing); fingerprint checks re-executed rather than trusted from docs.*
