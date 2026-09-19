# AUREON Golden Copy — Deep Forensic Theme Audit & Phase B 100/100 Plan

**Audit target:** `AUREON-GOLDEN-COPY.zip`

**Assessment date:** 2026-09-12

**Audit method:** filesystem/static forensic inspection of the supplied archive + review of bundled Serena memories and project reports + inspection of bundled screenshot/QA evidence. Runtime claims from older reports are treated as historical evidence and are not accepted as current proof unless the current archive supports them.

---

## 1. Executive Verdict

### Verdict: **NOT 100/100. Not release-ready as a “golden” master yet.**

The archive contains a serious amount of working architecture and a credible premium-theme foundation, but it is **not internally consistent enough to be called production-finished**. The strongest problems are architectural consistency and verification integrity rather than basic PHP syntax.

The most important finding is this:

> **The project currently has more than one source of truth for routing, templates, assets, dynamic rendering, and historical documentation.**

That is the root cause behind several apparently unrelated issues.

The current tree statically validates well at a basic language level:

- PHP lint: **0 syntax errors** across the inspected theme/plugin/frontend PHP set.
- Non-minified JS syntax check: **0 syntax errors** in the inspected source set.
- The current Vineta manifest files mostly resolve to real files.
- Core AJAX/REST handlers generally include nonce/capability controls.

However, syntax correctness is only the floor. The current archive still contains:

1. **A duplicated theme tree** with six divergent files.
2. **A duplicated plugin/source arrangement** that materially increases maintenance ambiguity.
3. A generic complete-page router whose **fallback routes belong to an older Ferm-era structure**.
4. Current manifest routes that are **not all consumed before the legacy fallback is reached**.
5. A concrete **search/404 routing failure path** in the current resolver logic.
6. Multiple **relative/static HTML + manifest + WordPress enqueue + JS shim** asset/data authorities.
7. A Vineta data bridge that is much larger and more invasive than a “thin adapter”.
8. Search suggestions and several content values still hardcoded.
9. A screenshot QA set that visually represents an older AETHER-style surface while the current active design pack is Vineta — so the screenshot set is **not a trustworthy current visual regression baseline**.
10. A bundled runtime report with **48 pages showing unimplemented tracking functions** and **2 account pages reporting `jQuery is not defined`**.
11. Missing optional/demo assets such as the documented `cursor-close.svg`, plus additional 3D/video/group-page asset gaps found by the archive scan.
12. Legacy naming/comment drift (`Ferm`, `AETHER`) inside the AUREON runtime, increasing cognitive load and upgrade risk.
13. `eval()` in premium plugin hook/element execution paths. This can be intentional for a trusted admin extensibility feature, but it materially raises the security and review burden and must never be treated as harmless.

### Current engineering score

**Overall forensic score: 71/100**

This is deliberately lower than several bundled historical reports claiming roughly 99.6% verification. Those reports are useful evidence of past work, but the current archive contains contradictions that make the historical score non-portable to this copy.

### Release-readiness score

**6.0/10**

The master should remain in audit/remediation until the route, asset, dynamic-data, duplicated-tree, and evidence-baseline problems are closed and verified in a fresh clean runtime.

---

# 2. What the Archive Actually Contains

## 2.1 High-level inventory

The current archive contains these major layers:

- `themes/aureon/` — WordPress theme.
- `themes/aureon/theme/` — nested duplicate/legacy theme tree.
- `plugins/aureon-studio/` — AUREON Studio plugin and premium modules.
- `frontend/` — frontend engine, adapters, views, components, design packs.
- `frontend/designs/vineta/` — current complete-page Vineta design pack.
- `docs/docs/` — architecture, forensic, QA, phase, customizer and feature reports.
- `.serena/memories/` — Serena project memories.
- `wordpress-qa/` — screenshot/runtime QA evidence.
- `mu-plugins/` — environment/runtime support.

Approximate archive-wide file inventory from the supplied ZIP:

| Type | Approx. count |
|---|---:|
| PHP | 603 |
| JPG | 292 |
| CSS | 220 |
| JS | 192 |
| JSON | 82 |
| PNG | 74 |
| HTML | 46 |
| Other | remainder |

The project is therefore not a small “theme”; it is a **theme + companion plugin + frontend engine + design pack + QA/reporting ecosystem**.

---

# 3. Evidence Integrity — Critical Finding

## 3.1 Serena memory is not the current filesystem truth

The bundled `.serena/memories/AUREON-GOLDEN-COPY.md` describes a different/older structure and state than the ZIP actually contains.

Examples of divergence include:

- memory references an older root arrangement rather than the current `themes/aureon/`, `plugins/aureon-studio/`, `frontend/` structure.
- memory describes a different default design state.
- current `frontend/views/design.php` defaults to **`vineta`** when no option is set.
- current filesystem includes the Vineta complete-page pack and Vineta-specific routing/data code.

### Consequence

The Serena memory must be treated as **historical architectural evidence**, not a release manifest.

### Required correction

Create one machine-verifiable **CURRENT_MASTER_BASELINE.md** generated from the actual checked-in filesystem. Serena memory should summarize this baseline, not replace it.

---

# 4. Current Architecture Assessment

## 4.1 Architecture strengths

The architecture has several strong ideas:

- AUREON separates theme shell, plugin capabilities, frontend engine, and design pack.
- Active design resolution is centralized.
- Design packs have manifests.
- There is a complete-page design mode.
- WooCommerce integration is treated explicitly rather than mixed blindly into static HTML.
- Dynamic data has a dedicated composer/bridge layer.
- WordPress Customizer settings are bridged into frontend CSS/data.
- AJAX and REST APIs exist for stateful features.
- Security headers, nonces, sanitization and capability checks are present in substantial parts of the system.

The problem is not that these pieces do not exist. The problem is that **too many of them exist in parallel**.

---

# 5. P0/P1/P2/P3 Finding Matrix

## P0 — Must resolve before calling the master release-ready

### P0-01 — Source-of-truth conflict between current pack, routing, and historical evidence

**Impact:** extremely high.

The current code is Vineta-first, while several fallback route rules are Ferm/Aether-era. The QA screenshot pack is AETHER-branded/legacy-looking while the current complete-page pack is Vineta.

This prevents reliable reasoning about whether a bug is fixed or merely fixed in an earlier generation of the system.

**Fix:** establish one current baseline and regenerate screenshots/reports from that exact commit/archive.

---

### P0-02 — Current route resolver contains dead/legacy fallback mappings

`themes/aureon/ferm-page.php` contains a manifest-first resolver followed by a backward-compatibility map.

The fallback includes mappings such as:

- `collections/furniture.html`
- `pages/contact.html`
- `pages/about.html`
- `pages/store-locator.html`
- `blogs/stories.html`
- search → `blogs/stories.html`
- 404 → `pages/contact.html`

Those are not the current Vineta canonical file structure.

The current Vineta manifest instead points at:

- `shop-default.html`
- `about-us.html`
- `contact-us.html`
- `store-location.html`
- `blog-grid-01.html`
- `shop-default.html` for search
- no canonical dedicated 404 page in the manifest.

Because the current resolver does not consume every manifest route before entering the legacy fallback, the system can resolve a valid WordPress route to a **nonexistent legacy HTML file and then silently fall back to `index.html`**.

### Why this is severe

This is the classic failure mode that makes a storefront “load” while showing the wrong page. HTTP 200 does not prove the page is correct.

**Fix:** eliminate legacy route fallback entirely or move it behind an explicit migration compatibility layer that is disabled by default. Every route must resolve through the manifest and a typed route policy.

---

### P0-03 — Search and 404 are not safely represented in the current route contract

The current manifest has a `search` page mapping, but the resolver's manifest block does not consume a dedicated `is_search()` branch before the legacy fallback.

The legacy fallback instead points search to `blogs/stories.html`.

The 404 fallback points to `pages/contact.html`.

This is a direct current-code correctness problem.

**Fix:** add explicit route classes:

- `home`
- `shop`
- `category`
- `product`
- `search`
- `blog_archive`
- `blog_single`
- `static`
- `cart`
- `checkout`
- `account`
- `404`

Then require every class to resolve through a manifest entry or a controlled typed fallback. A 404 may never fall back to contact or homepage.

---

## P1 — High-priority production issues

### P1-01 — Duplicated theme tree with drift

`themes/aureon/` and `themes/aureon/theme/` contain a large overlapping codebase.

Six current files differ:

- `cart.php`
- `ferm-page.php`
- `inc/frontend.php`
- `inc/aether-security.php`
- `checkout/form-checkout.php`
- `myaccount/my-account.php`

The root versions have newer Vineta standalone behavior while the nested copies preserve older AETHER composition behavior in several places.

### Risk

A future patch can be applied to the wrong copy, a ZIP/package can ship the wrong copy, or a maintainer can inspect an obsolete file and believe it is authoritative.

**Fix:** one executable theme tree. Archive/remove nested mirror. If the nested copy is required for provenance, move it to a non-executable `legacy/` or `archive/` directory outside WordPress runtime.

---

### P1-02 — Plugin/source duplication also increases maintenance risk

The plugin has a substantial nested `plugin/` source tree inside `plugins/aureon-studio/` while the archive root also contains plugin-level resources.

This is less obviously dangerous than the theme duplicate, but the packaging model is still harder to reason about than a single canonical plugin root.

**Fix:** define one runtime root and one source-of-truth mapping. Add CI validation that no runtime file exists in two authoritative paths.

---

### P1-03 — Complete-page host uses regex parsing of full HTML documents

`aureon_ferm_extract_body()` parses `<body>` with a regex.

This is fragile for unusual markup and makes the system dependent on assumptions about document structure.

**Fix:** use a proper HTML5-aware parser where feasible, or stop treating full HTML documents as runtime template containers and instead normalize design packs into a controlled partial/document format during build time.

---

### P1-04 — Client-side path rewriting remains part of the architecture

The complete-page host and Vineta pack use path bridges and browser-side DOM/link rewriting.

This is useful as a migration tactic, but it is not a clean production architecture because:

- crawlers can see the wrong original URL,
- users can experience flashes of incorrect links,
- accessibility tools can inspect pre-rewrite state,
- JS failure leaves broken navigation,
- debugging requires tracing server HTML + JS transformations.

**Fix:** rewrite internal URLs server-side at render/build time. Client-side path bridging should be a temporary compatibility layer only.

---

### P1-05 — Two asset authorities exist

The system has at least these asset authorities:

1. frozen HTML `<link>/<script>` tags,
2. design `manifest.json`,
3. WordPress enqueue logic,
4. JS compatibility shims.

This increases duplicate-loading and execution-order risk.

**Fix:** the manifest must be the single runtime dependency graph. Frozen HTML should contain only non-runtime-safe references or be normalized at build time.

---

### P1-06 — Vineta “composer” is not actually thin

The current composer is doing much more than pure data transformation.

It includes or participates in:

- DOM/data injection setup,
- AJAX endpoint wiring,
- cart behavior,
- authentication bridge behavior,
- navigation HTML splicing,
- customizer CSS injection,
- search suggestions,
- demo mode policy,
- route/page-data construction.

This increases coupling between frontend markup, business rules and WordPress.

**Fix:** split into:

- `vineta-data.php` — pure data mapping
- `vineta-routes.php` — route mapping only
- `vineta-assets.php` — manifest/enqueue only
- `vineta-cart.php` — commerce actions
- `vineta-auth.php` — account/auth bridge
- `vineta-customizer.php` — presentation settings
- `vineta-render-adapter.php` — final slot contract only

Then define strict interfaces.

---

### P1-07 — Search suggestions are hardcoded

Current composer data uses:

`Fashion`, `Electronics`, `Jewelry`, `Skincare`, `Furniture`.

This is not store-aware behavior.

**Fix:** use WooCommerce product categories/search suggestions with configurable limits and relevance; optionally allow curated suggestions in Customizer.

---

### P1-08 — Authentication/account stack is still fragile

The bundled screenshot QA reports:

- My Account desktop: `ReferenceError: jQuery is not defined`
- My Account mobile: `ReferenceError: jQuery is not defined`

The current root `myaccount/my-account.php` has custom standalone behavior and explicitly prints Woo account JS.

The Vineta composer also contains a jQuery compatibility bridge because the frozen pack can load its own jQuery.

This strongly suggests the project still has load-order/library ownership issues.

**Fix:** never ship a second jQuery instance from the design pack. Use WordPress's registered jQuery only. Replace old plugins that assume `$` with wrappers or noConflict-safe modules.

---

### P1-09 — `eval()` exists in the premium plugin

Detected in:

- `plugins/aureon-studio/plugin/hooks/functions/hooks.php`
- `plugins/aureon-studio/plugin/elements/class-hooks.php`

The feature appears intended to execute admin-defined PHP hook code.

This is a major security boundary.

**Fix options:**

1. Best: remove arbitrary PHP execution and replace with typed hooks/configuration.
2. If retained: make it an explicitly privileged developer-only feature, disabled by default, with capability checks, import warnings, audit logging, validation, and hard documentation that imported configurations are executable code.

Do not count this feature as safe merely because it is inside an admin UI.

---

### P1-10 — Screenshot baseline does not match the current Vineta pack

The QA screenshots are strongly AETHER-branded/dark-gold in appearance, while current Vineta pages are branded as “Vineta - Multipurpose eCommerce” in their HTML titles.

Therefore the screenshots are not a reliable current visual baseline for the current archive.

**Fix:** regenerate all screenshots after the current active pack is resolved in a clean environment.

---

### P1-11 — Third-party tracker placeholders throw errors on most QA pages

The current `wordpress-qa/screenshot-report.json` has 50 route/viewport records.

There are:

- 48 occurrences of `Error: Function rdt not implemented.`
- 48 occurrences of `Error: Function snaptr not implemented.`
- 2 occurrences of `ReferenceError: jQuery is not defined`
- 2 occurrences of a failed resource request associated with the 404 captures.

Even if the tracker functions are test-environment stubs, a clean theme runtime should not produce red console errors.

**Fix:** provide no-op stubs only in the test harness, not production; or load test harness adapters before scripts execute. Production should either load real trackers conditionally or not load them.

---

### P1-12 — Missing/optional Vineta assets remain

The archive scan found references to files that are not present for several demo pages/features, including:

- `cursor-close.svg` (also documented in earlier reports)
- product-group `fs-1.jpg` through `fs-5.jpg`
- product 3D `.glb` asset reference
- product video `.mp4` asset reference

Some may be optional demo content, but the theme should either ship the assets or remove/disable the corresponding links/pages.

**Fix:** run an HTML asset closure test on every design page and require zero missing local assets, except an explicit allowlist.

---

# 6. Current Data/Dynamic-System Assessment

## What is working conceptually

The system has real dynamic plumbing for:

- product names/prices/links/images,
- WooCommerce cart state,
- customer/account state,
- newsletter subscription,
- navigation menus,
- product categories,
- customizer settings,
- page data injection,
- product detail state.

This is the right direction.

## What prevents 100/100

The dynamic model is still **DOM-patching oriented**.

The Vineta JS shims contain substantial operations such as:

- replacing cart markup with `innerHTML`,
- replacing navigation markup,
- replacing footer data,
- DOM query/replace cycles,
- fetch-driven cart updates.

This means the system can look correct only after JavaScript finishes executing.

### Risks

- broken/slow JS creates broken UX,
- crawler/SEO value is lower than server-rendered data,
- accessibility state can be wrong before hydration,
- third-party script failures can break the bridge chain,
- markup changes in the upstream design can break selectors silently.

### 100/100 target

For a premium WordPress theme master, use:

**server-rendered canonical data first + JS enhancement second.**

JavaScript should update interaction state, not reconstruct the entire commerce surface from scratch.

---

# 7. WooCommerce Assessment

## Strong areas

The current root templates have clearly moved toward standalone Vineta-aware cart/checkout/account templates. That is a meaningful architectural improvement over older shell-only templates.

The checkout template includes empty-cart redirection logic before output, which is important because headers cannot be sent after body output.

AJAX handlers sanitize product IDs and use Woo APIs rather than direct SQL for the main cart/product operations.

## Weak areas

### 7.1 Cart/checkout routing is duplicated across multiple filters/templates

The code has:

- Woo page template routing,
- complete-page router exceptions,
- standalone cart template,
- standalone checkout template,
- Vineta page data paths,
- JS cart shims.

This is too many layers for one route.

### 7.2 Cart/checkout complete-page manifest routes can become misleading

The Vineta manifest defines `cart`, `checkout` and `account` page files, but the higher-priority routing intentionally bypasses some complete-page routes and sends them through dedicated WordPress/Woo templates.

That creates a contract problem:

> A route present in `manifest.json` is not necessarily the file actually served.

The manifest must declare the true route authority, not only an aspirational page inventory.

### 7.3 Account is especially sensitive to jQuery ownership

The QA error confirms this is not merely theoretical.

---

# 8. Customizer Assessment

The archive contains a meaningful Customizer bridge and Vineta-native color controls.

The current composer includes canonical readers for settings and CSS variable output.

That is good.

However:

- some historical reports still describe controls as `STORED_NOT_CONSUMED` even though the current composer now reads several of them;
- this means the reports are not synchronized with code;
- the system has multiple naming generations (`aether_*`, AUREON, older PHANTOM/Ferm terminology).

### Required change

Produce an automated **Customizer Contract Matrix**:

`setting -> control -> stored key -> reader -> output target -> rendered selector/variable -> screenshot test`

No setting is “verified” until the full chain is proven.

---

# 9. Routing Matrix — Current Risk

| Route | Current intended source | Risk |
|---|---|---|
| Home | `index.html` | Low |
| Shop | `shop-default.html` | Low |
| Product | `product-detail.html` | Medium |
| Category | `shop-default.html` | Medium |
| Search | `shop-default.html` in manifest | **High: resolver branch missing** |
| Blog | `blog-grid-01.html` | Medium |
| Single post | `blog-single.html` | Medium/high, needs explicit resolver proof |
| About | `about-us.html` | Low for matching slug |
| Contact | `contact-us.html` | Low for matching slug |
| Store locator | `store-location.html` | Medium due naming aliases |
| Cart | `view-cart.html` in manifest, but native template routing can bypass it | High contract ambiguity |
| Checkout | `checkout.html` in manifest, but native template routing can bypass it | High contract ambiguity |
| Account | `account-page.html` in manifest, but standalone PHP template can bypass it | High contract ambiguity |
| 404 | No canonical manifest mapping | **Critical** |

---

# 10. Visual/UX Assessment From Included Screenshots

The included screenshot corpus is broad and covers approximately 25 route families at desktop/mobile widths.

Visually, the older screenshots show a polished premium e-commerce direction:

- strong typography,
- consistent dark premium surfaces,
- large editorial imagery,
- clear product grid hierarchy,
- coherent footer/newsletter treatment,
- usable commerce layouts.

That is a good visual foundation.

But the screenshots also show:

- significant vertical space on some utility/error/search pages,
- some content that reads as demo/boilerplate rather than store-native content,
- repeated visual system branding that does not match current Vineta source files,
- multiple screenshot generations/baselines,
- state quality that cannot be trusted against the current code.

### Verdict

**Visual quality: ~8/10 as a design artifact.**

**Visual verification quality: ~4/10 for the current codebase**, because the screenshots are not aligned tightly enough with the current active pack.

---

# 11. Accessibility Assessment

Positive:

- WordPress shell contains skip-link and ARIA patterns.
- Search and navigation controls include labels in the base theme.
- Form controls are broadly structured.

Risks:

- client-side DOM replacement can destroy focus/interaction state;
- dynamic markup replacement can alter accessible names unexpectedly;
- image/interactive states in the frozen HTML need a dedicated axe-based audit;
- the project has not demonstrated a current zero-violation accessibility baseline.

### Required gate

For every canonical route:

- axe-core automated scan,
- keyboard traversal,
- focus-visible verification,
- modal escape behavior,
- screen-reader landmarks and accessible names,
- reduced-motion behavior.

---

# 12. Security Assessment

## Good

- WordPress escaping/sanitization is used throughout large portions of the code.
- AJAX endpoints generally verify nonces.
- REST settings operations use `manage_options`.
- Contact and newsletter flows include rate-limiting attempts.

## High-risk

### Arbitrary PHP execution through `eval()`

This is the highest security concern found statically.

It must be classified as an intentional privileged execution subsystem, not ordinary theme behavior.

### Additional required checks

- CSP should move from report-only to enforceable after full dependency closure.
- External fonts/CDNs should be minimized or made configurable/self-hostable.
- Import/export must validate allowed option keys and reject executable payloads.
- File upload/import code must be reviewed for path traversal and MIME confusion.
- Admin-only operations require current capability checks everywhere.

---

# 13. Performance Assessment

The project has enough assets that performance will never be “automatic”.

### Main performance risks

- duplicated libraries or asset authority,
- frozen design pack CSS/JS plus WordPress plugins,
- jQuery duplication,
- page-independent loading of large scripts,
- DOM replacement after initial paint,
- multiple frontend subsystems loaded into the same request.

### Recommended standard

Set budgets for:

- initial JS,
- CSS,
- fonts,
- image bytes,
- long tasks,
- LCP,
- CLS,
- INP.

Run Lighthouse/Playwright on representative routes at mobile and desktop.

---

# 14. Code Quality Assessment

## Strong

- file naming is largely systematic,
- WordPress APIs are used extensively,
- PHP syntax is clean,
- the code has meaningful docblocks,
- systems are separated into logical subsystems.

## Weak

- legacy naming persists,
- duplicate trees persist,
- large multifunction files are doing too much,
- historical report claims have not been kept synchronized,
- the route system contains compatibility leftovers that now behave like runtime logic,
- frontend JS is tightly coupled to selectors and specific DOM structures.

### Code-quality verdict: **7/10**

---

# 15. Documentation / Serena Assessment

The project has unusually extensive documentation and audit artifacts.

The problem is **documentation drift**.

The archive contains reports that say things such as “all green”, “99.6%”, “release gate complete”, etc., while the current source has changed and still contains known structural problems.

### Rule going forward

No report is allowed to say “verified” unless it contains:

1. commit/archive identifier,
2. exact environment,
3. exact route/test matrix,
4. timestamp,
5. pass/fail output,
6. artifact hashes where appropriate.

Otherwise label it **historical / snapshot**.

---

# 16. Scorecard

| Domain | Score | Reason |
|---|---:|---|
| Architecture foundation | 12/15 | Strong separation ideas, but competing authorities remain |
| Routing correctness | 7/12 | Legacy fallback creates real current risk |
| WooCommerce integration | 10/13 | Good API integration, but route/template duplication |
| Dynamic data fidelity | 8/13 | Real bridges exist, but JS-heavy and some hardcoded data |
| Customizer/editorial system | 7/10 | Broad and increasingly connected, evidence not fully current |
| Visual quality | 8/10 | Strong design artifact, current baseline mismatch |
| JS/runtime reliability | 6/10 | jQuery and bridge complexity + QA errors |
| Security | 6/8 | Good baseline, but `eval()` is a serious concern |
| Performance | 4/5 | Good potential, not enough current closure proof |
| Testing/release hygiene | 3/4 | Many tests/docs exist, but baseline drift lowers confidence |
| **TOTAL** | **71/100** | |

---

# 17. What “100/100” Should Mean

AUREON should not be called 100/100 because it has “many settings” or because 99% of a historical script passed.

The master should reach 100/100 only when all of these are true:

### Architecture

- exactly one runtime theme tree;
- exactly one runtime plugin source of truth;
- exactly one design resolver;
- exactly one route contract;
- exactly one asset dependency authority.

### Dynamic system

- every visible dynamic value has a source;
- no production hardcoded demo business data unless explicitly documented as a fallback;
- server-rendered first, JS enhancement second;
- no selector-dependent reconstruction of entire commerce surfaces.

### Commerce

- simple products;
- variable products;
- stock;
- price/sale price;
- gallery;
- variations;
- cart add/update/remove;
- mini-cart/count;
- checkout;
- payment;
- order-pay/order-received;
- account/login/register/lost-password;
- customer orders/account details;
- wishlist where supported;
- empty/error/success states.

### Frontend

- all routes have canonical pages;
- 404 is a real 404 design;
- search has a real search state;
- no broken local assets;
- no console errors.

### Verification

- clean environment;
- current commit only;
- automated route matrix;
- screenshot matrix;
- axe;
- console/network errors;
- Lighthouse budgets;
- PHPStan/PHPCS or equivalent quality gates;
- theme/plugin packaging validation.

---

# 18. Phase B — 100/100 Remediation Program

## Phase B0 — Baseline Freeze

### B0.1 — Create current master manifest

Create a machine-generated file:

`docs/current-master-baseline.json`

Include:

- git commit/hash,
- theme version,
- plugin version,
- active design,
- file counts,
- file hashes,
- manifest hash,
- PHP/JS test results,
- route matrix version,
- screenshot matrix version.

### B0.2 — Update Serena memory

Replace historical claims with:

- CURRENT STATE,
- KNOWN FAILURES,
- VERIFIED CAPABILITIES,
- PENDING TASKS.

### B0.3 — Freeze evidence

Mark all older reports `historical` unless regenerated from the current baseline.

**Gate:** one unambiguous source of truth.

---

# Phase B1 — Remove Structural Duplication

### B1.1 — Delete or archive `themes/aureon/theme/`

Before removal:

- diff every duplicate,
- migrate any unique changes,
- run tests,
- package theme.

### B1.2 — Normalize plugin runtime root

Define one canonical runtime source under `plugins/aureon-studio/plugin/` or the intended root; do not keep two authoritative copies.

### B1.3 — Add duplicate-path CI

Fail CI if two runtime paths contain equivalent executable theme/plugin files.

**Gate:** one executable source tree.

---

# Phase B2 — Route Contract Rewrite

### B2.1 — Define typed route classes

Implement a route resolver returning:

```text
RouteResult
- route_type
- canonical_slug
- template_file
- http_status
- data_context
- required_assets
```

### B2.2 — Remove Ferm compatibility fallback

Do not delete history; remove it from runtime.

### B2.3 — Add explicit search route

Consume manifest `pages.search` before any fallback.

### B2.4 — Add explicit 404 route

Require `pages.404`.

### B2.5 — Validate every manifest route file exists

Build-time failure if missing.

### B2.6 — Validate every known WordPress route maps to exactly one template

No route may resolve to homepage by accident.

### B2.7 — Add status validation

404 must return HTTP 404 even when rendered through a complete-page pack.

**Gate:** 0 wrong-template route cases.

---

# Phase B3 — Asset System Unification

### B3.1 — Make `manifest.json` the only dependency authority

### B3.2 — Remove/normalize duplicate `<script>` and `<link>` tags from frozen HTML

### B3.3 — Add `type: module` support to manifest schema

Fields:

```json
{
  "file": "...",
  "type": "module",
  "deps": [],
  "page": ["product"],
  "priority": "before"
}
```

### B3.4 — Page-aware loading

Do not ship product-only JS to home/shop.

### B3.5 — Remove second jQuery

Use WP registered jQuery only.

### B3.6 — Asset closure test

Every local reference must exist or be allowlisted.

**Gate:** zero duplicate runtime libraries, zero missing required assets.

---

# Phase B4 — Dynamic Data Contract

### B4.1 — Define slot contracts

For each slot:

- source,
- type,
- fallback,
- sanitizer,
- render target,
- screenshot test.

### B4.2 — Replace hardcoded search suggestions

### B4.3 — Replace demo rating fallbacks with real values / explicit empty state

### B4.4 — Replace hardcoded legal/footer links with settings or real WP/Woo URLs

### B4.5 — Replace hardcoded shipping labels with WC settings/data

### B4.6 — Replace static product-color maps with attribute-aware mapping/configuration

### B4.7 — Separate demo mode from live mode

Demo content must never silently win over valid store data.

### B4.8 — Server-render critical product/cart/account data

JS enhances; it does not recreate the primary document.

**Gate:** 100% dynamic slot coverage on the route matrix.

---

# Phase B5 — Split Vineta Composer

Refactor large responsibilities into small modules.

### B5.1
`vineta-routes.php`

### B5.2
`vineta-assets.php`

### B5.3
`vineta-product-data.php`

### B5.4
`vineta-cart.php`

### B5.5
`vineta-auth.php`

### B5.6
`vineta-customizer.php`

### B5.7
`vineta-slot-renderer.php`

### B5.8
`vineta-demo.php`

**Gate:** each subsystem can be unit-tested independently.

---

# Phase B6 — WooCommerce Full Closure

### B6.1 — Products

- simple,
- variable,
- sale,
- out of stock,
- backorder,
- external,
- downloadable where applicable.

### B6.2 — Cart

- add,
- quantity change,
- remove,
- empty,
- coupon,
- totals,
- shipping,
- fragments.

### B6.3 — Checkout

- guest,
- logged-in,
- validation,
- gateway,
- failed payment,
- success,
- order received.

### B6.4 — Account

- login,
- register,
- lost password,
- dashboard,
- orders,
- order view,
- downloads if enabled,
- addresses,
- account details,
- logout.

### B6.5 — Endpoints

Test all WC endpoints with complete-page design enabled and disabled.

**Gate:** zero critical commerce regressions.

---

# Phase B7 — JavaScript Reliability

### B7.1 — Remove duplicated jQuery

### B7.2 — Enforce noConflict-safe modules

### B7.3 — Convert DOM rewriting into targeted state updates

### B7.4 — Add defensive selectors with contract tests

### B7.5 — Add error boundaries for fetch/AJAX responses

### B7.6 — Remove console errors

### B7.7 — Make tracking optional and environment-safe

**Gate:** 0 console errors on all route/viewport combinations.

---

# Phase B8 — Security Hardening

### B8.1 — Review `eval()` execution paths

Preferred: remove.

### B8.2 — If retained, require:

- `manage_options`,
- explicit feature toggle,
- import confirmation,
- audit log,
- code execution warning.

### B8.3 — Review all import/export handlers

### B8.4 — Review REST permission callbacks

### B8.5 — Review file upload and font library paths

### B8.6 — CSP enforcement

Move from report-only to enforce once dependencies are clean.

**Gate:** security review signed off.

---

# Phase B9 — Customizer 100% Contract Verification

For every setting:

`UI -> setting -> storage -> reader -> CSS/data -> DOM -> screenshot`

### Required output

Generate:

`docs/customizer-contract-matrix.csv`

Columns:

- setting,
- control,
- default,
- storage key,
- runtime reader,
- output,
- route coverage,
- screenshot proof,
- status.

**Gate:** no `STORED_NOT_CONSUMED`, `UNVERIFIED`, or `PARTIAL` settings in release scope.

---

# Phase B10 — Visual System Closure

### B10.1 — Regenerate every screenshot from current master

### B10.2 — Desktop

- 1440
- 1280
- 1024

### B10.3 — Mobile

- 768
- 390
- 360

### B10.4 — Route set

At minimum:

Home, Shop, Product, Category, Search, Cart, Checkout, Account, Blog, Single Post, all static pages, 404, coming-soon, empty states, logged-in states, invalid states.

### B10.5 — Pixel/semantic assertions

- header visible,
- hero visible,
- product count correct,
- cart count correct,
- totals correct,
- footer visible,
- no horizontal overflow,
- no broken images,
- no wrong route content.

**Gate:** current screenshots match current code and baseline.

---

# Phase B11 — Accessibility Closure

### B11.1
axe-core on every canonical route.

### B11.2
Keyboard-only navigation.

### B11.3
Focus trap for dialogs.

### B11.4
Escape closes overlays.

### B11.5
Reduced motion.

### B11.6
Form labels/error messages.

### B11.7
Alt text and decorative image semantics.

**Gate:** no critical/serious axe violations.

---

# Phase B12 — Performance Closure

Create page budgets.

Example master limits:

| Metric | Budget |
|---|---:|
| LCP | <= 2.5s |
| CLS | <= 0.1 |
| INP | <= 200ms |
| Initial JS | <= 250 KB compressed where practical |
| Critical CSS | <= 100 KB |
| Console errors | 0 |
| Failed local requests | 0 |

Use representative routes rather than only home.

---

# Phase B13 — Documentation Closure

### B13.1
Mark old reports historical.

### B13.2
Generate current architecture diagram.

### B13.3
Generate route matrix.

### B13.4
Generate asset matrix.

### B13.5
Generate Customizer matrix.

### B13.6
Generate security matrix.

### B13.7
Generate release checklist.

**Gate:** documentation describes the exact checked-in code.

---

# Phase B14 — Final Release Gate

The release gate should fail if any of these are true:

- duplicate runtime source tree exists,
- legacy route is executed,
- search wrong template,
- 404 not real 404,
- missing local asset,
- console error,
- broken image,
- jQuery duplicate,
- unhandled AJAX error,
- dynamic setting is disconnected,
- commerce state is wrong,
- screenshot mismatch is unexplained,
- PHP/JS lint failure,
- security gate failure.

Only after all pass should the master be tagged as:

`AUREON MASTER V1 — RELEASED`

---

# 19. Recommended Task Order

Do **not** start by adding more sections or more Customizer settings.

The correct order is:

1. **Baseline/evidence freeze**
2. **Remove duplicate trees**
3. **Fix route contract**
4. **Unify asset loading**
5. **Eliminate jQuery duplication**
6. **Close dynamic data contracts**
7. **Split Vineta composer**
8. **Close WooCommerce states**
9. **Security hardening**
10. **Customizer contract verification**
11. **Visual regression regeneration**
12. **Accessibility**
13. **Performance**
14. **Final release gate**

This ordering is important because visual polish before architecture cleanup creates expensive rework.

---

# 20. Highest-Value Immediate Fixes

If the team can only do ten changes first, do these:

### 1. Delete/archive the nested theme mirror

### 2. Replace the route resolver with one typed manifest-first resolver

### 3. Add explicit search and 404 routes

### 4. Remove the Ferm fallback paths from runtime

### 5. Make manifest the only asset dependency source

### 6. Remove pack-owned jQuery

### 7. Fix account runtime JS load order

### 8. Replace hardcoded search/footer/legal data

### 9. Regenerate screenshots from the current Vineta baseline

### 10. Re-run the complete route matrix with zero console errors

These ten changes will improve reliability much more than adding another 50 theme settings.

---

# 21. Final Professional Assessment

AUREON is **not a failed project**. The foundation is significant and technically ambitious. The problem is that the project has reached the point where continued feature growth without consolidation will make it less reliable, not more capable.

The correct next stage is therefore not “add more features”. It is:

> **Consolidate → normalize contracts → eliminate drift → verify current runtime → then expand.**

The current archive should be considered a **strong pre-release master candidate**, not a finished 100/100 golden master.

The most dangerous misconception would be to trust the historical “99.6% complete” documentation without reconciling it against the current filesystem. The current source shows real progress, but also shows that several older fixes/reports have not been integrated into one clean source of truth.

### Target after Phase B

A credible AUREON 100/100 master should have:

- one theme tree,
- one plugin runtime tree,
- one route resolver,
- one manifest/asset graph,
- one dynamic-data contract,
- server-rendered critical commerce data,
- zero legacy runtime fallback routes,
- zero console errors,
- zero missing local assets,
- zero disconnected Customizer controls,
- complete WooCommerce endpoint coverage,
- current screenshot proof,
- current Serena memory and reports generated from the same baseline.

That is the point at which the project can safely become the protected master/starter for client-specific design packs.

---

## Appendix A — Concrete Evidence Collected From This Archive

### Static syntax

- PHP lint: **0 errors** in the inspected theme/plugin/frontend set.
- JS syntax checks: **0 errors** in the inspected non-minified source set.

### Duplicate tree evidence

Theme duplicate tree contains six divergent files, including the current standalone Cart, Checkout, Account and frontend router files.

### Current active design

`frontend/views/design.php` defaults to `vineta`.

### Current Vineta manifest

Declares home/shop/product/collection/blog/search/cart/checkout/account/static mappings and asset lists.

### Current route resolver

Manifest-first resolver is followed by a legacy Ferm/Aether route map with stale file names.

### QA screenshot report

50 route/viewport records:

- 48 × `rdt not implemented`
- 48 × `snaptr not implemented`
- 2 × `jQuery is not defined`
- 2 × failed resource errors on the 404 captures

### Security scan

Two `eval()` execution paths were found in premium plugin hook/element execution systems.

---

# Final Score

## **71 / 100 — PRE-RELEASE / PHASE B REQUIRED**

### Current status

**Architecture:** strong but fragmented

**Visual:** strong but evidence is stale/misaligned

**Commerce:** substantial but not fully contract-closed

**Dynamic system:** capable but over-JS-driven

**Security:** reasonable baseline with one serious privileged execution issue

**Maintainability:** below premium-master standard due duplicated trees and legacy terminology

**Verification:** extensive historical coverage, insufficiently synchronized with the current archive

### Release decision

**DO NOT freeze this archive as the final 100/100 master yet.**

Proceed with Phase B in the order above, then regenerate the complete evidence suite from the cleaned master.
