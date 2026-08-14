# MASTER FRONTEND REPLACEMENT PLAN — AUREON Client Frontend Integration Framework

> **Date:** 2026-08-14
> **Status:** FORENSIC PLAN — approved for planning only. No implementation until this plan is accepted.
> **Frozen baseline:** `main` @ `de52eaf` @ tag `v1.2.1-audit` — working tree clean, origin in sync (verified 2026-08-14).
> **Mission:** A professional, reusable frontend-replacement architecture: take an arbitrary external HTML/CSS/JS frontend, transform its UI/UX into an AETHER-compatible presentation layer, and connect it to the existing Aureon Theme + Studio Plugin + Customizer + WooCommerce ecosystem — **without breaking the core or losing dynamic functionality.**
> **This is NOT:** a multi-demo theme, a demo importer, a frontend redesign, or a theme fork generator.

---

## 1. Executive summary

The AUREON platform already implements ~90% of the required separation by construction: the **adapter layer** (23 files) is the only code allowed to call WordPress/WooCommerce, **components** (52 manifest entries) are verified pure (grep gate in CI), **ViewModels** are documented (`docs/FRONTEND_DATA_CONTRACT.md`), and the **Customizer** drives everything through one settings bucket (`aureon_settings`) with token emission (`aether-tokens.php`). The audit (16 phases) proved the current design works end-to-end with zero core regressions.

**The gap is not the data layer. The gap is the presentation layer.** Today the engine tree (`frontend/`) is one monolithic "Luxury" design: component templates carry design-specific markup/classes, `assets/css/style.css` is the Luxury design system, `assets/js/main.js` attaches behaviors to design-specific class names, and the 26 section templates compose Luxury components. A second client design currently requires **editing that same tree** — which violates the frozen-baseline rule.

**The framework to build:** introduce the concept of an isolated **Design Pack** — a self-contained presentation layer (`assets/`, `components/`, `sections/`, `tokens` defaults, `manifest` overrides, JS behaviors, descriptor) that plugs into the unchanged kernel (loader → registry → renderer → viewmodel → composer) and the unchanged bridge (theme `inc/frontend.php`). The kernel, adapters, ViewModels, token *mechanism*, Customizer controls, WC bridge, hardening layer, and test gates remain frozen platform code. Client identity lives in the pack; platform contracts live in the core.

**Smallest platform work (recommended roadmap M1–M12)** — contract documentation, three ViewModel inconsistencies normalized, design-pack envelope (directory + descriptor + enqueue + registry override), CSS/JS isolation rules, intake tooling, and a proof-of-concept second pack.

---

## 2. Current state (evidence, 2026-08-14)

| Layer | Files | Status | Verified by |
|---|---|---|---|
| Kernel | `frontend/views/` (loader, registry, renderer, viewmodel, composer) | ✅ frozen, design-agnostic | Stage 2–14 + audit |
| Adapters | `frontend/adapters/` (23 files) | ✅ only WP/WC-touching layer | adapter inventory + grep gate |
| ViewModels | documented in `docs/FRONTEND_DATA_CONTRACT.md` (239 lines) | ✅ | audit Phases 2–8 |
| Components | `frontend/manifest/components.php` — 52 entries | ✅ pure (zero WP/WC calls) | `verify.sh` grep gate |
| Sections | `frontend/sections/` — 26 self-registering templates | ✅ | route suite 16/16 ×2 |
| Tokens | `frontend/tokens/tokens.php` (607 lines) → `aureon_settings` bucket → `:root` emission | ✅ | audit Phase 4/7 |
| Bridge | `aureon/theme/inc/frontend.php` (227 lines) | ✅ | live |
| Hardening | `inc/aether-{tokens,security,seo,newsletter,ajax,cart,analytics,performance}.php` | ✅ | audit Phase 12–15 |
| Customizer | 49 controls incl. G6 hero repeater, 15 section toggles | ✅ | audit Phase 4 |
| WC routing | `template_include` bridge + theme cart/checkout/account templates | ✅ | routes + live-gaps 6/6 |
| Tests | Playwright 6 specs × 2 projects, `verify.sh`, CI | ✅ | full suite green 2026-08-14 |
| Docs | 8 forensic + 16 audit docs in `docs/` | ✅ | — |

**Quality gates today:** `php -l` (0/330+), `node --check` (0), `verify.sh` (manifest resolve, grep gate, adapter count), Playwright (routes 16/16 ×2, live-gaps 6/6, interactions 5+1skip / 6, failure-injection 4/4, a11y 14/14 ×2, visual 3/3 zero-delta). CI runs the static job on push/PR.

---

## 3. Architecture diagram (target)

```text
                    AUREON PLATFORM
                         │
        ┌────────────────┼─────────────────┐
        ▼                ▼                 ▼
   WordPress       Aureon Studio      WooCommerce
        │                │                 │
        └────────────────┼─────────────────┘
                         ▼
                  DATA / SETTINGS
              (aureon_settings bucket, Customizer)
                         │
                         ▼
                 AUREON ADAPTERS  (frozen — 23 files, only WP/WC layer)
                         │
                         ▼
                    VIEW MODELS  (frozen contract)
                         │
                         ▼
                AETHER COMPONENT API
              (kernel: registry → renderer → manifest)
                         │
                         ▼
             ┌───────────────────────────────┐
             │   DESIGN PACK (presentation)  │
             │  ┌─────────┬─────────┬──────┐ │
             │  │ Luxury  │ ClientB │ ...  │ │
             │  │ pack    │ pack    │      │ │
             │  └─────────┴─────────┴──────┘ │
             │  assets/ components/ sections/│
             │  tokens manifest behaviors    │
             └───────────────────────────────┘
```

Dependency direction is **one-way**: Platform → Adapters → ViewModels → Component API → Design Pack. Never reversed.

---

## 4. Core / API boundary (what stays frozen platform code)

Protected files — **never modified for a client design**:

| Path | Responsibility |
|---|---|
| `frontend/views/*.php` | loader, registry, renderer, viewmodel, composer (kernel) |
| `frontend/manifest/components.php` | base component map (id → template path) — pack may extend/override, not edit |
| `frontend/adapters/*.php` | the only WP/WC-touching layer |
| `frontend/tokens/tokens.php` | option-defaults registration *mechanism* (values are defaults; packs register their own) |
| `aureon/theme/inc/frontend.php` | bridge: boot, enqueue, suppression, WC routing, `aetherAjax` localize |
| `aureon/theme/inc/aether-*.php` | tokens emission, security, SEO, newsletter, AJAX, cart, analytics, performance |
| `aureon/theme/inc/customizer/fields/frontend.php` | platform Customizer controls (49) |
| `aureon/plugin/woocommerce/` | WC module + gate |
| `frontend/tests/`, `.github/workflows/ci.yml` | verification gates |

**Rule:** if a client design requires a change inside these paths, that change must be (a) justified as a platform improvement benefiting all clients, (b) planned, (c) tested on the full suite, (d) committed separately from client work.

---

## 5. AETHER boundary (reusable architecture, not design)

- Kernel + contracts (this plan documents them formally — M1/M2)
- Behavior attribute whitelist (`aether_behavior_attrs`: reveal, reveal-group, tilt, parallax, parallax-section, zoom, motion-text)
- AJAX contract (`aetherAjax`: ajaxUrl, nonce, restUrl, isUserLoggedIn, shopUrl, searchUrl, wcAjaxUrl)
- Newsletter / contact / wishlist / cart-count handlers (`aether-ajax.php`, `aether-newsletter.php`, `aether-cart.php`)
- Motion guard-first infrastructure (`animations.js` watchdog + `@media (scripting:none)`)
- Empty-state, error/404, pagination, filter-bar, rating, countdown — **reusable components already design-neutral in data, design-bound in markup** (see §7)
- Visual regression harness + failure-injection suite

## 6. Frontend (design pack) boundary

Everything visual per client:

- Component templates (markup per design)
- CSS/SCSS → tokenized custom properties
- JS behaviors (delegated, see §16)
- Images, fonts, icons, videos
- Section composition (which sections, in which order, which components)
- Token *values* (design palette/typography/spacing defaults)

The pack is a directory + one descriptor file. It does NOT contain WP/WC calls, options logic, or Customizer controls.

---

## 7. Component contract (formalized — M2)

Every component gets a documented contract entry (existing per-file docblocks are the source; the matrix doc `docs/FRONTEND_COMPONENT_DYNAMICITY_MATRIX.md` exists and is extended):

| Field | Meaning |
|---|---|
| `id` | manifest key, e.g. `card/product` |
| `purpose` | one-line role |
| `input` | ViewModel fields consumed (all optional at render; safe defaults) |
| `variants` | e.g. `card/product` layout `shop` vs `default` |
| `slots` | child/structural hooks (mostly none — flat props) |
| `states` | loading / added / active / empty / error |
| `responsive` | breakpoint rules |
| `tokens` | CSS custom properties consumed |
| `js` | behaviors that attach (class hooks documented) |
| `a11y` | roles/landmarks/alt requirements |
| `data` | which adapter feeds it |

**Audit findings to normalize (M2, LOW risk):**
1. Blog `paged{current,total,base}` vs wc-products `pagination{current,total}` — same concept, two keys. Decide one (alias the other in renderer or document both).
2. Cart `crumbs` vs product `breadcrumb` — same concept, two keys.
3. `stats` shape divergence: account `[{number,label}]` vs about `{items:[...]}` — normalize to one shape in the renderer (compat wrapper).
4. `adapter-shell.php` exports 3 functions (announcement/header/mobile) and has no `aether_adapter_shell` — registry lookup for slug `shell` silently no-ops. Document as intended (composer-only).
5. `adapter-menu.php` signature is `$location` (string), `adapter-options.php` is `$keys` (map) — document both as non-registry adapters.
6. `adapter-testimonials` contains the only raw `$wpdb` query — acceptable (adapter layer), but flag for review.

**Component replacement rule:** a new design maps its UI to existing ids first. If a design needs a component that does not exist: **reuse > extend > add new reusable AETHER component**. One-off client markup goes in the client pack, never into shared component templates.

---

## 8. ViewModel contracts (frozen — M1)

Existing source of truth: `docs/FRONTEND_DATA_CONTRACT.md` (239 lines, per-adapter ViewModel tables) + adapter inventory. The formal contract freeze:

- Freeze adapter output keys per adapter (inventory table in this plan's appendix is the working copy).
- **HeaderViewModel** = adapter-header output (brand, brand_url, menu, icons, cart_count) — already matches the user-level contract.
- **HeroViewModel** = adapter-hero slides (id, visible, headline, accent, subline, badge, image, mobile_image, image_alt, overlay, primary_cta, secondary_cta) + behavior — G6 repeater schema IS the contract.
- **ProductViewModel** = adapter-product output (24 keys: breadcrumb, gallery, badge, title, id, product_type, price, price_plain, old_price_plain, rating, rating_text, description, colors, sizes, quantity, add_to_cart_url, add_to_cart_label, trust, specs, size_table, reviews_score, reviews_count, reviews_bars, reviews_items).
- **PostViewModel** = adapter-blog item (title, excerpt, date, category, author, image, alt, url, read_more, behavior) + paged.
- **CartViewModel** = adapter-cart (context, is_empty, items, subtotal, shipping, tax, total, cart_url, shop_url, checkout_url, title, crumbs).
- **AccountViewModel** = adapter-account (name, email, initial, stats, menu, logout_label, dashboard_url, shop_url) + adapter_account_orders (orders, empty_text, shop_url, logout_url).

**Rule:** adapters are the contract's implementation — a pack never changes what an adapter returns. If a new design needs different data, the adapter is extended (platform improvement) with defaults, never forked.

---

## 9. Template / slot contract (M3)

Composer already defines the shell slots implicitly. Formalized:

| Slot | Provider | Contents |
|---|---|---|
| `shell.preloader` | `aether_compose_header` | preloader (toggle) |
| `shell.fog` | " | fog (toggle) |
| `shell.skip-link` | " | skip link (always) |
| `shell.mobile-chrome` | " | mobile header + drawer |
| `shell.announcement` | " | announcement bar (toggle) |
| `shell.header` | " | desktop header |
| `main#swup` | theme templates | page content |
| `shell.footer` | `aether_compose_footer` | footer |

Theme templates (26 sections + front-page/cart/product/blog/static templates) are **composition only**: they call `aether_render_section()` with section ids. A design pack changes composition by overriding **section templates** (same registry id → pack template path) and/or **component templates** (same manifest id → pack path). Section ids are stable platform identifiers.

**Pack template override mechanics (proposed, smallest change):**
- Registry: section template path resolves pack-first (`design_dir/sections/section-X.php` if exists, else base).
- Manifest: component template path resolves pack-first (`design_dir/components/...` if exists, else base).
- Both via a single filter in the kernel (`aether_design_template`), defaulting to base — kernel change is additive and tiny.

---

## 10. Token architecture (M4)

Current: `tokens.php` registers defaults on `aureon_settings`; `aether-tokens.php` emits `:root` custom properties from saved option values; Customizer controls write the same bucket. The client identity must be a **token/presentation layer**, not a core fork:

- **Platform tokens (frozen):** the emission mechanism, the WC color bridge (`--aether-wc-*`), the typography bridge, radii/layout sliders, section-padding sanitizer, motion toggles.
- **Design tokens (per pack):** pack registers its own default values under **namespaced option keys** (`aether_design_<pack>_*` or a pack-level defaults array) — Customizer values still win when set; pack defaults apply when untouched. No new duplicate token systems unless the design genuinely requires isolation.
- **Hardening:** components currently consume `var(--gold)` etc. — keep the generic names (`--gold`, `--surface`, ...) as the *contract* surface; packs map their identity onto them (Luxury maps its palette; ClientB maps its palette onto the same names). This is the key trick that keeps 52 components + all CSS working across designs.

**Verification rule:** no hardcoded hexes in components (already enforced by convention; add grep gate in `verify.sh`).

---

## 11. Customizer mapping

Data flow stays: `Customizer → aureon_settings → token/ViewModel → pack`. The framework does **not** add controls per client. The existing 49 controls (colors, typography, spacing, shell, motion, announcement, shop per-page, section toggles, hero repeater, GA4) are the platform surface. If a client design needs a control that cannot map naturally → produce a mapping spec, not a new control class (G6 repeater is the pattern for genuinely reusable controls).

---

## 12. WordPress integration

The template hierarchy remains the router: `front-page.php`, `home.php`, `single.php`, `archive-product.php`, `single-product.php`, `cart.php`, `checkout/form-checkout.php`, `myaccount/my-account.php`, `woocommerce/checkout/thankyou.php`, 9 static templates + `template_include` bridge. A pack never re-implements routing; it supplies presentation for the resolved template. The 404/search/blog/archives/account surfaces already have pack-replaceable section/component templates.

---

## 13. WooCommerce integration

Business logic stays 100% WC-native (cart, checkout, order-pay, account endpoints, HPOS, coupons, shipping, payments, notices). The platform already routes WC pages into AETHER templates (bridge §4). Packs render **ViewModels only** (`adapter-cart`, `adapter-order`, `adapter-account`, `adapter-wc-products`, `adapter-product`, `adapter-wishlist`). Live-gaps spec (6/6) is the WC regression gate and must pass for every pack. The WC color bridge (`--aether-wc-*`) already lets a merchant's WC palette flow into any pack.

---

## 14. Plugin integration

Aureon Studio modules keep working because the pack never replaces options logic. Third-party bridge audit list (per mission §14): ACF, Rank Math, Yoast, WPML, Polylang, forms plugins, BuddyPress, bbPress, LearnDash, Events Calendar — none are currently touched by the engine; SEO is handled by `aether-seo.php` (OG/JSON-LD/canonical). Pack-specific risk: third-party frontend CSS/JS conflicts (see §17/§16). Guarded plugin bridges (`aureon_*` functions) remain the integration contract.

---

## 15. CSS isolation (M7)

Client CSS is the biggest risk. Rules:

1. **Enqueue isolation:** pack styles are enqueued only via the bridge on front-end surfaces (`wp_enqueue_scripts`), never in admin/Customizer/Gutenberg (current behavior already correct — add a regression assertion).
2. **Scope contract:** pack CSS must not assume it owns `body`, `html`, or global resets that clash with WP admin/editor/plugin UI. Resets allowed, but the pack descriptor declares them; admin surfaces are excluded by construction.
3. **Token conversion:** client palette/typography/spacing → pack token defaults → generic contract names (`--gold`, `--surface`, `--font-heading`...) so components/JS keep working.
4. **Coexistence test:** both packs enqueue simultaneously never happens (one active pack), but the *test* must prove the inactive pack's CSS has zero effect (enqueue gate + visual regression).
5. **Class collision:** `.container`, `.row`, `.card`, `.btn` — the active pack's CSS wins by enqueue order; document that pack CSS must be written against the pack's own classes and contract names.

## 16. JS behavior mapping (M8)

For every client JS behavior, classify: **KEEP / ADAPT / REPLACE / MOVE INTO AETHER / REMOVE**.

- Platform JS (contract, keep): AJAX add-to-cart, cart-count fragment, contact, newsletter, wishlist, search, preloader, motion watchdog, `aetherAjax` consumers.
- Design JS (pack, adapted): swipers, marquees, accordions, tabs, filters, counters, sticky elements, gallery — behaviors attach via **delegated class hooks** namespaced to the pack (`data-*` attributes are preferred over class-name scraping; the `aether_behavior_attrs` whitelist is the platform mechanism).
- Never ship the client's entire JS bundle blindly (duplicates, conflicts, licensing).

## 17. Asset pipeline

Per pack: `assets/` with fonts (self-hosted, licensed), images (optimized, responsive variants), icons (FA 6.5.1 contract or pack icons), favicons. Enqueued via the bridge with `filemtime` versioning (existing pattern). No scattered `<script>/<link>` tags in templates.

## 18. Performance

Budgets (current Luxury baseline): engine CSS ~100KB, vendor pinned CDNs, local JS deferred by enqueue order, no render-blocking fonts (font-display swap), images preloaded for hero. Every pack must hold: same Lighthouse budget class, no new blocking requests, asset count cap (audit tool row in the intake checklist), HTML compression still applies.

## 19. Accessibility

axe suite (14/14 ×2) is per-design — each pack gets the same a11y gate (scroll-then-axe, 11 surfaces). Skip-link/main landmark/alt rules are platform (composer) — packs must preserve `#main`, `main#swup`, headings hierarchy, focus order.

## 20. Security

- No new unsafe calls: grep gate (no WP/WC in components) + no raw SQL outside adapters + escaping conventions (renderer is the single escape boundary).
- CSP stays report-only (site-owner decision) — packs must not require inline scripts that would break a future strict CSP (prefer nonce-able enqueues).
- Hostile-input injection test (audit Phase 10/15) re-run per pack.

## 21. Visual regression (M9)

Per pack, committed baselines:
- Viewports: 1440, 1280, 1024, 390, 375
- Routes: home, shop, product, cart, checkout, account, blog, single, search, 404, pack-specific pages
- Compare: **client design reference vs AUREON integration** (fidelity gate) — and zero-delta vs the pack's own committed snapshots (regression gate). Harness: extend `visual.spec.js` with pack-scoped snapshot dirs.

## 22. Client intake (M5 + §26)

Repeatable intake process per client frontend package:

1. **Forensic intake** → `CLIENT_FRONTEND_FORENSIC_REPORT.md` (pages, shared components, layouts, nav, forms, product UI, cart/checkout/account, blog, animations, responsive, assets, fonts, icons, dependencies).
2. **Tier classification:**
   - **Tier A — Directly compatible:** maps cleanly to existing ids/tokens.
   - **Tier B — Adaptable:** requires component/token/JS adaptation (normal work).
   - **Tier C — Architectural mismatch:** requires significant reconstruction — scope explicitly, client approves before work.
   - **Tier D — Unsafe:** licensing/security/performance conflict — **reject** with documented reason.
3. Component extraction → mapping to manifest ids (reuse > extend > new reusable).
4. Data-dependency analysis → ViewModel contract check.
5. Token mapping → contract names.
6. Template mapping → section ids + pack section overrides.
7. JS mapping → KEEP/ADAPT/REPLACE/MOVE/REMOVE.
8. Implementation → pack build.
9. Browser regression + core regression + a11y + performance.
10. Client acceptance → release.

## 23. Client branching

- `main` = frozen platform (v1.2.1-audit and forward).
- Every integration: `client/<name>` branch from the latest release tag.
- Pack code lives on the client branch; platform improvements found during integration are cherry-picked/committed to `main` separately (never through the client branch).
- Every client integration independently revertible (pack removal = one directory + descriptor deactivation).

## 24. Replacement workflow (14 phases)

```
P0 intake → P1 forensic audit → P2 component inventory → P3 data contract mapping →
P4 customizer mapping → P5 woo mapping → P6 token mapping → P7 css/js analysis →
P8 component implementation → P9 template implementation → P10 dynamic integration →
P11 browser regression → P12 performance/a11y/security → P13 client acceptance → P14 production release
```

No "HTML → WordPress" copy-paste anywhere.

## 25. Success definition

A pack is complete only when: design visually faithful · core unchanged (except justified platform improvements) · plugin functional · Customizer functional · WC functional · WP functional · bridges functional · all required data dynamic · zero forbidden WP/WC calls in pure components · CSS isolated · JS lifecycle stable · assets integrated · responsive works · a11y passes · performance passes · security passes · existing tests green · client code isolated · rollback possible · documentation exists.

---

## 26. Compatibility tiers

| Tier | Meaning | Action |
|---|---|---|
| A | Directly compatible | Map onto existing ids/tokens; enqueue pack |
| B | Adaptable | Component/token/JS adaptation within pack |
| C | Architectural mismatch | Reconstruction required — client-scoped plan + approval |
| D | Unsafe (licensing/security/perf) | **Reject** — documented reason, no promise |

Classify at intake; never promise literal support for every frontend.

## 27. Client design preservation

The client keeps its identity (colors, typography, spacing, imagery, animations, component shapes, layouts, navigation, cards, marketing sections). Underneath: AUREON data + AETHER contracts + client presentation. The client never sees the data system.

## 28. Required documentation (M12)

```
docs/frontend-platform/FRONTEND_REPLACEMENT_ARCHITECTURE.md   (this plan's distilled reference)
docs/frontend-platform/CLIENT_FRONTEND_INTAKE.md              (intake process + report template)
docs/frontend-platform/COMPONENT_CONTRACT.md                  (52-component matrix)
docs/frontend-platform/DATA_CONTRACT.md                       (frozen ViewModels; supersets FRONTEND_DATA_CONTRACT.md)
docs/frontend-platform/CUSTOMIZER_MAPPING.md                  (49 controls → token/VM mapping)
docs/frontend-platform/WOO_MAPPING.md                         (commerce surfaces → ViewModels)
docs/frontend-platform/TOKEN_MAPPING.md                       (design token → contract name conversion)
docs/frontend-platform/CLIENT_INTEGRATION_CHECKLIST.md        (acceptance checklist per §25)
docs/frontend-platform/REGRESSION_GATE.md                     (full gate inventory)
docs/frontend-platform/CLIENT_BRANCH_STRATEGY.md              (branching + revert rules)
docs/frontend-platform/KNOWN_LIMITATIONS.md                   (gaps, deferred, rejection cases)
```

## 29. What is already implemented vs missing (forensic answer)

**Already implemented (reusable as-is):**
- Full adapter/ViewModel/renderer/component pipeline with purity enforcement
- 52 components, 26 sections, documented data contract
- Customizer-driven tokens + G6 repeater + WC color bridge + typography bridge
- Shell composer with stable slots
- WC routing, AJAX contract, newsletter/contact/wishlist/cart handlers
- Motion guard-first, a11y, security, SEO, performance layers
- Playwright + axe + visual + failure-injection suite, CI

**Missing (must build):**
1. Design-pack envelope: pack directory convention + descriptor + activation (M10 first proof)
2. Template resolution override (pack-first) in registry/manifest — small kernel filter
3. Namespaced design token defaults registration (pack-level) — additive
4. Pack-scoped enqueue (bridge reads active pack descriptor) — additive
5. Intake tooling + forensic report template (forensic.cjs exists — formalize)
6. Mapping manifest schema (§ client_frontend manifest) + docs
7. ViewModel normalization fixes (3 inconsistencies) — LOW
8. Multi-pack visual regression harness + coexistence test
9. Docs (12 files)

**Duplicated/risky (flag):**
- `main.js` design-class hooks (pack-ify via delegation)
- `style.css` single-tree design system (moves into pack)
- `fonts.css` Luxury fonts (pack)
- `data-phantom` attribute system tied to Luxury source (pack-scope or archive)

## 30. Implementation roadmap (smallest platform work)

| M | Workstream | Deliverable | Risk | Depends |
|---|---|---|---|---|
| M1 | Contract freeze | DATA_CONTRACT + freeze rules doc | LOW | — |
| M2 | Component contract audit | COMPONENT_CONTRACT.md + 3 VM normalizations (paged/pagination, crumbs/breadcrumb, stats) | LOW | M1 |
| M3 | Template/slot contract | slot doc + pack override filter in registry/manifest | LOW-MED | M1 |
| M4 | Token boundary hardening | namespaced pack defaults + hex grep gate in verify.sh | LOW | M1 |
| M5 | Intake tooling | CLIENT_FRONTEND_INTAKE.md + forensic report template | LOW | — |
| M6 | Mapping manifest | `design-manifest.json` schema + loader | MED | M3, M4 |
| M7 | CSS isolation | pack enqueue isolation + coexistence regression test | MED | M3 |
| M8 | JS behavior adapter | behavior classification doc + delegated hooks | MED | M3 |
| M9 | Visual regression harness | multi-viewport/multi-pack baselines | MED | M7 |
| M10 | **First real client integration (proof)** | one Tier-A/B pack end-to-end on `client/<name>` | HIGH (proof) | M1–M9 |
| M11 | Platform hardening | fixes surfaced by M10 | MED | M10 |
| M12 | Documentation/finalization | remaining 11 docs + acceptance checklist | LOW | M10, M11 |

**Priority order rationale (from evidence):** the kernel is already clean — M1–M4 are documentation + tiny additive kernel changes with LOW risk; M5–M9 enable the pack lifecycle; M10 is the architectural proof that decides M11 scope. No core rewrite is recommended anywhere in this plan.

---

## 31. Risk register

| # | Risk | Severity | Mitigation |
|---|---|---|---|
| R1 | main.js class hooks design-coupled | MED | M8 delegated hooks; pack-scoped class names |
| R2 | CSS leakage between pack and WP admin/editor | MED | M7 enqueue isolation + assertion test |
| R3 | Token collision across packs | MED | M4 namespaced defaults; generic contract names stable |
| R4 | Registry/manifest override breaks section ids | MED | M3 pack-first resolution with fallback + route suite per pack |
| R5 | Section templates hardcode component ids | LOW | section templates are pack-owned (they live in the pack) |
| R6 | Asset duplication (fonts/images) | LOW | M9 pipeline; shared assets stay in platform tree |
| R7 | Client JS conflicts with platform JS | MED | M8 classification + failure-injection suite |
| R8 | Quality tooling not installed (phpcs/PHPStan/Psalm/ESLint) | LOW | keep php -l + node --check + verify.sh as gates |
| R9 | Pack activation during WP admin breaks admin | LOW | enqueue guard by request context |
| R10 | Promise of "any frontend" | MED | Tier A–D classification at intake (§26) |

## 32. Failure modes & rollback

- Pack activation fails (fatal) → bridge wraps pack include in `function_exists`/try-catch; on failure, active pack falls back to Luxury pack (default) — site never blanks.
- Pack visual regression fails → block merge on `client/<name>`; revert = remove pack dir + descriptor.
- Platform regression from kernel change → kernel changes are committed alone, gated by the full suite before pack work starts.
- **Rollback point for any pack:** `main` @ tag (v1.2.1-audit) + pack removal.

## 33. Testing strategy (per pack + platform)

- Platform (frozen): full suite as today (routes, interactions, live-gaps, a11y, visual, failure-injection, verify.sh, CI).
- Per pack: pack route suite (16 routes), pack a11y (11 surfaces ×2 viewports), pack visual (multi-viewport baselines + fidelity comparison), pack live-gaps (commerce flows), coexistence test (inactive pack CSS/JS zero-effect), hostile-input injection.

## 34. THE CRITICAL QUESTION — answers (mission §32)

**A. Can the current AUREON core safely support replacement frontends?**
Yes. The adapter/ViewModel/component pipeline already enforces the boundary; the Customizer + token mechanism is design-agnostic. The only missing piece is a presentation-layer envelope (pack) with pack-first template resolution.

**B. What parts of AETHER are already reusable?**
Kernel (5 views files), manifest mechanism, 23 adapters + all ViewModels, behavior whitelist, AJAX/contact/newsletter/wishlist/cart handlers, motion guard, a11y shell, WC routing, tokens mechanism, WC color + typography bridges, test harness. **Components are reusable in *data* but design-bound in *markup* — a pack re-implements markup, not data.**

**C. What is currently coupled to the existing visual design?**
Component templates' markup/classes, `assets/css/*` (Luxury design system + fonts), `main.js` behaviors' class hooks, `data-phantom` attrs, section compositions, token default *values* (announcement copy, hero slides, footer columns, imagery).

**D. What must become contracts?**
Adapter output shapes (freeze now — mostly done), component contracts (purpose/input/variants/states/tokens/js/a11y), slot contract (composer shell), token contract names (`--gold`, `--surface`, `--font-*`, `--aether-wc-*`), AJAX contract (`aetherAjax`), behavior whitelist.

**E. What must remain in core?**
Everything in §4 protected list. The core never changes for a client.

**F. What must remain in AETHER?**
The kernel, contracts, platform JS behaviors, adapters, token mechanism, hardening, tests. Anything useful for many clients.

**G. What must be client-specific?**
Pack directory contents: markup, CSS, design JS behaviors, assets, fonts, imagery, token default values, section composition. Anything useful for 1 client.

**H. How can a developer integrate a new HTML/CSS/JS frontend without breaking core?**
Intake → tier classification → component/VM/token mapping → build a pack (assets + component templates + section overrides + token defaults + delegated JS) → activate via descriptor → run pack gates (routes/a11y/visual/live-gaps) + platform regression. Zero core edits.

**I. Exact steps?** The 14-phase workflow (§24) + M1–M10 framework build first.

**J. Smallest platform work before accepting the first real client frontend?**
M1–M4 (contract freeze + pack-first template resolution + namespaced token defaults + hex gate) + M5 (intake) + M7 (CSS isolation) + M9 (visual harness). Estimated: 6 small additive changes + docs. No kernel rewrite.

**K. When should a frontend be rejected or rebuilt?**
Tier D (licensing/security/perf) → reject. Tier C (architectural mismatch: e.g. SPA-only, non-HTML runtime, shadow-DOM app, dependency on removed Site Library/agency APIs) → rebuild-or-scope decision with the client before any work. Also reject: unlicensed assets/fonts, inline-script-heavy designs (CSP), >1 design that cannot map to existing WC flows without forking WC templates.

**L. Protected files?** §4 table. Additionally: `frontend/views/`, `manifest/components.php`, all `adapters/`, `tokens/tokens.php` mechanism, `theme/inc/frontend.php`, `theme/inc/aether-*.php`, `customizer/fields/frontend.php`, `plugin/woocommerce/`, `frontend/tests/`, CI.

**M. Tests that must pass before a pack reaches production?**
verify.sh + php -l + node --check (platform) · pack routes 16/16 ×2 · pack live-gaps 6/6 · pack a11y 14/14 ×2 · pack visual zero-delta + fidelity comparison · failure-injection 4/4 · coexistence test · hostile-input injection · Customizer round-trip spot check · WC flow spot check (add-to-cart AJAX + no-JS + cart + checkout redirect).

**N. Recommended next milestone?**
**M1–M5 (contract freeze + pack envelope + intake tooling) as one "Framework Foundation" milestone**, then M10 (first real client pack) as the architectural proof. Do not build multiple packs before the first proof passes.

---

## 35. Final safety rule

**DO NOT MAKE THE CORE FOLLOW THE CLIENT FRONTEND.** The client frontend adapts to AUREON contracts:

```text
AUREON CORE → DATA → CONTRACT → AETHER → CLIENT PRESENTATION
```

NOT:

```text
CLIENT HTML → modify AUREON → break Customizer/WC/plugins
```

If a design requires modifying the frozen core, STOP and produce a mapping specification before any code.

---

## Appendix A — Adapter inventory (23, working copy of the frozen contract)

| Slug | Function | Args | Top-level output keys |
|---|---|---|---|
| about | `aether_adapter_about` | `$args` | label, title, subtitle, mission, features, story, values, stats (demo-gated) |
| account | `aether_adapter_account` (+`aether_adapter_account_orders`) | none | name, email, initial, stats, menu, logout_label, dashboard_url, shop_url / orders, empty_text, shop_url, logout_url |
| article | `aether_adapter_article` | `post_id` | category, title, image, alt, author, author_bio, date, read_time, content, avatar, behavior |
| auth | `aether_adapter_auth` | `$args` | brand, forgot, login, register, redirect, show_register |
| blog | `aether_adapter_blog` | whitelisted query args | items, paged{current,total,base} |
| cart | `aether_adapter_cart` | `context` | context, cart_url, shop_url, checkout_url, is_empty, items, subtotal, shipping, tax, total, title, crumbs |
| coming-soon | `aether_adapter_coming_soon` | `$args` | brand, title, subtitle, target, socials |
| contact | `aether_adapter_contact` | none | fields, action, nonce, info, socials |
| faq | `aether_adapter_faq` | none | items (demo-gated) |
| hero | `aether_adapter_hero` | none | slides, behavior |
| menu | `aether_adapter_menu` | `$location` (string) | menu tree {label, url, active, children} |
| options | `aether_adapter_options` | `$keys` (map) | passthrough option values |
| order | `aether_adapter_order` | `$args` | title, subtitle, order_number, email_note, delivery_note, shop_url, track_url |
| product | `aether_adapter_product` | none | 24 keys (see §8) |
| shell | (no `aether_adapter_shell`) | — | announcement/header/mobile functions, composer-only |
| shop-hero | `aether_adapter_shop_hero` | none | label, title, subtitle |
| site | `aether_adapter_site` (+`aether_adapter_footer`) | none | name, brand, tagline, logo, url / footer keys |
| team | `aether_adapter_team` | none | items (demo-gated) |
| testimonials | `aether_adapter_testimonials` | none | items, score, count (demo-gated; real WC aggregate wins) |
| wc-categories | `aether_adapter_wc_categories` | `$args` | items, has_more, total_categories, all_categories_url, label/title/subtitle (demo-gated) |
| wc-filter | `aether_adapter_wc_filter` | none | buttons |
| wc-products | `aether_adapter_wc_products` | whitelisted query args | items, pagination{current,total}, cta_label, cta_url (demo-gated) |
| wishlist | `aether_adapter_wishlist` | none | items, status, count, shop_url, account_url |

**Shared shapes:** `items` (8 adapters), `brand` (6), `title` (6), `subtitle` (4), `shop_url` (4), `socials` (4).
**To normalize (M2):** blog `paged` ↔ wc-products `pagination`; cart `crumbs` ↔ product `breadcrumb`; `stats` inner shape (account vs about).

---

*End of master plan. Implementation begins only after acceptance of this plan. Rollback point: `v1.2.1-audit`.*