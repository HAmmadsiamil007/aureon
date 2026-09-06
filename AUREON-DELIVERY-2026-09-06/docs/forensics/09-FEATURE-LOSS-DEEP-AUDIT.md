# 09 — JavaScript, CSS, Assets, Security, A11y, Responsive, Cache, Demo, Feature Loss (Phases 15–23)

Read-only. Code-level evidence; runtime/browser behavior marked UNPROVEN where applicable.

## Phase 15 — JavaScript

Load graph (pack, from manifest `assets.js` priority "before" + deps):

```
jquery (pack js/jquery.min.js — version unverified) ← jquery-validate
photoswipe.umd → photoswipe-lightbox.umd
drift.min / nouislider.min / zoom.js / infinityslide.js / shop.js   (no inter-deps declared)
wp-localized: vineta_bridge (ajax_url, nonce) + VinetaPageData (shims handle)
footer: vineta-data-shims.js, vineta-path-bridge.js, composer bridges (search/auth/jquery)
```

Findings (documented, not fixed):

| # | Finding | Risk |
|---|---|---|
| J1 | **jQuery duplication possible:** pack ships `js/jquery.min.js`; WP also enqueues jQuery in admin bar / WC fragments contexts. Version and noConflict state unverified. | MEDIUM — double-load or version conflict |
| J2 | **Triple path-rewrite redundancy:** server regex (ferm-page.php) + inline MutationObserver script + `vineta-path-bridge.js`. Any future HTML change can be caught by 0–3 of them depending on timing. | HIGH (silent divergence) |
| J3 | **Double VinetaPageData injection** (`wp_localize_script` on shims + inline `<script>` echo). Later one wins; ordering differs between complete-page and standalone routes. | MEDIUM |
| J4 | Bridges rely on **class/id hooks** in frozen HTML (`.cart-count`, `#customer_login`, `#submit-login`, `.header__logo`, `#mobileMenuBtn`). No contract test exists that these selectors still exist. | HIGH (redesign trap) |
| J5 | `vineta_search_bridge`/`vineta_auth_bridge` are printed raw in wp_footer (heredoc-style echo). CSP (`aether-security.php` sends CSP) could block inline scripts → needs nonce-application verification on complete-page routes. | MEDIUM (functional + security) |
| J6 | No error boundaries around JSON hydration: if VinetaPageData missing/malformed, pack JS may throw before UI init (null-failure class). Selector mismatch class defects likely present but UNPROVEN without browser run. | MEDIUM |
| J7 | Dead handlers: wish-list/compare pages exist as frozen HTML; wishlist AJAX endpoints exist in theme; wiring between them NOT FOUND in shims — dead feature or unwired endpoints. | LOW-MEDIUM |
| J8 | `model-viewer.min.js` + 3D product template present; only served for product-3d template which is not in the route map — dead asset on default routes. | LOW |

## Phase 16 — CSS

- **Token system:** pack `css/styles.css` consumes `--vineta-*` variables + `--font-heading`/`--font-body` bridge tokens; composer `vineta_emit_customizer_css()` prints `:root{--vineta-…}` at wp_head 20; pack `tokens.php` provides defaults via `aureon_option_defaults` (priority 20).
- **Bootstrap duplication:** pack enqueues its own `css/bootstrap.min.css`; AETHER platform Bootstrap is dequeued on complete-page routes (suppression list) — OK today, but both exist in tree.
- **Font Awesome:** theme FA 4.7 dequeued; pack uses icon font (`fonts/font-icons.css`, icomoon) + possible FA 6 in shell — two icon systems coexist in tree.
- **WC CSS:** `woocommerce-general/layout/smallscreen`, `wc-blocks-style`, `select2` all dequeued on complete-page pages except checkout/account (skip-suppression). Checkout template re-includes pack CSS manually. Consequence: WC *core* styles absent on cart/shop pages; any WC component relying on them (e.g. quantity input styling) is styled only by pack CSS — visual regressions possible (UNPROVEN).
- **Inline CSS compensation:** `vineta_wc_page_inline_css` (priority 1001) injects page-specific CSS for cart/checkout/account — a fourth CSS source layer (pack css + customizer css + inline wc css + WC native where allowed).
- **Responsive:** pack `styles.css` + animate.css; breakpoints are pack-internal (Bootstrap grid + custom). Platform responsive.css dequeued. Responsive behavior itself UNPROVEN (no browser run here); responsive *mechanism* is coherent.
- **Dead rules:** platform `frontend/assets/css/*.css` (style/motion/responsive/a11y/pages — luxury-era) unused under vineta; kept in tree.
- Verdict: Customizer colors **can** visibly reach the UI (vars path verified in code); platform layout controls **cannot**.

## Phase 17 — Assets

| Class | Items | Notes |
|---|---|---|
| REQUIRED | pack css/js/fonts, favicon set (hardcoded), demo JSON (while demo mode on) | enqueued or referenced by runtime |
| USED | frozen HTML imagery in `images/`, product imagery (local pack copies) | served via pack URL rewrite |
| UNUSED/DEAD | luxury-era platform assets (`frontend/assets/css|js`), `product-3d` template + model-viewer on default routes, `view-cart.html`, `cart-empty.html`, `checkout.html` pack files, `shop-left-sidebar|infinity-scroll|filter-drawer` (not in route map) | bloat + confusion |
| EXTERNAL | struct.com CDN rewrites (ferm-era), jsdelivr/unpkg CDNs (luxury-era, dequeued), fermliving.com rewriter for legacy pack | legacy external deps remain in code paths |
| BROKEN RISK | `.gitignore` exempts only `frontend/designs/fermliving/cdn/` from ignoring `cdn/` dirs — vineta pack `images/` tracked, but any vineta `cdn/` folder would be ignored → deploy hole (UNPROVEN whether pack uses `cdn/`) | deploy risk |
| DUPLICATE | ferm-image-urls.json (root) vs fermliving pack; fonts duplicated across root frontend/ and pack | LOW |

## Phase 18 — Security

| Area | Evidence | Assessment |
|---|---|---|
| AJAX nonces | `check_ajax_referer('vineta_cart_nonce')` ×4 in composer; `aether_nonce` for aether AJAX | GOOD baseline; capability checks on privileged ops need review |
| REST | `aether_register_newsletter_rest` — permission_callback must be verified (code read shows route registered; callback depth not audited line-by-line) | NEEDS_HARDENING check |
| Input handling | `absint` on ids/qty; `sanitize_text_field` on auth param; searches via WP API | GOOD baseline |
| Output escaping | esc_attr/esc_url/esc_js on bridge attribute output; frozen HTML echoed raw (accepted, by-design, source-controlled) | ACCEPTABLE with contract tests |
| ferm-page regex rewrites | injects nonce input + rewrites form fields via regex — brittle but values are server-generated; no user input in patterns | LOW risk, HIGH fragility |
| Newsletter DB table | own table; SQL built with `$wpdb->prepare` (verify) | CHECK |
| Admin export (newsletter CSV) | capability check required — must verify `current_user_can` present | CHECK before production |
| Secrets | none found in tracked code (wp-config gitignored; no API keys in pack) | OK |
| CSP | aether-security sends CSP + nonces for enqueued scripts — inline pack scripts may violate CSP depending on mode | VERIFY on runtime |
| Root scripts | `enable_cod.php`/`update-contact.php` — server-mutating scripts in workspace root; if web-accessible on a misconfigured host = config exposure | PROCESS RISK (P0 hygiene) |
| ABSPATH guards | present in all audited PHP entry points | OK |

## Phase 19 — Accessibility (static review)

- Pack templates: semantic landmarks present in frozen HTML (header/nav/main/footer — Shopify-export quality); skip-link not observed in complete-page output (AETHER's skip-link component suppressed).
- Heading hierarchy inside frozen templates not machine-audited here (58 files); sample reading shows H1 per page with sections using H2/H3 — needs automated pass (test plan item).
- Forms (checkout/login/contact): label association comes from WC/pack markup — WC checkout labels are native and fine; frozen contact form labels need verification.
- Icon-only buttons: header icons have aria-label in AETHER shell fallback; frozen pack header icon buttons need per-button verification.
- Dialogs (search modal, cart drawer, popups): ARIA/dialog semantics UNPROVEN — pack JS modals typically lack focus traps.
- Verdict: a11y is UNPROVEN overall; mechanism exists but no evidence of testing. Full axe/keyboard pass required (test plan).

## Phase 20 — Responsive

- Viewport meta present; pack CSS responsive; mobile menu via `#mobileMenuBtn`; drawer/popups responsive in pack CSS.
- Required test viewports 1440/1024/768/390: **no responsive testing evidence in repo** (root `rendered-home.html` 0-byte suggests interrupted run). All responsive claims UNPROVEN. Test plan covers.

## Phase 21 — Cache / state

| Surface | Mechanism | Stale-state risk |
|---|---|---|
| Customizer values | options; CSS emitted per request | low (server-rendered) |
| Menus | spliced per request via output buffer | low; but page-cache plugins would cache frozen+spliced output per-URL — logged-in variance (auth bridge) must bypass cache | HIGH if page cache added |
| Cart badge/count | fragments + AJAX + VinetaPageData | MEDIUM — bfcache/back nav, fragment/AJAX race |
| Auth state | VinetaPageData.customer + isUserLoggedIn | HIGH under any page caching (logged-in flag cached) — cache exclusion rules unknown (no server config in repo) |
| Product data | injected per request | low |
| Demo/real switch | option + query filters | low; but `auto` stub makes behavior surprising (04 §F4) |

- **No server/caching config exists in the repo** (no nginx/apache/object-cache config). Production cache posture is UNKNOWN — blocker for auth correctness claims.

## Phase 22 — Demo / fallback (summary)

- Demo data: pack `demo/*.json` + baked-in frozen content; switch via `aether_demo_mode` (`auto|force_demo|disabled`), `vineta_use_demo_fallback()` master switch (default ON for fresh stores).
- Real data priority: real WC products/categories merge over demo when present (`vineta_has_real_products/categories`); query filters hide demo from real listings.
- Fallbacks: logo → frozen SVG; hero → frozen slides; product → `_generic` template + demo product; category → `collections.default`; 404 → **dead-wrong hardcoded fallback** (`pages/contact.html` nonexistent in pack — see 04 §F3); search empty state unproven.
- Default logo/site name: frozen Vineta branding — acceptable demo default, flagged as CLIENT_BRANDING risk if store goes live unswitched.

## Phase 23 — Feature loss (AUREON capability contract vs current frontend)

Contract source: `docs/architecture/CLIENT-FRONTEND-CAPABILITY-CONTRACT.md` (repo) + README feature claims. Losses/partial:

| Capability (platform) | Status under vineta complete-page |
|---|---|
| AETHER shell + design system (luxury) | LOST/BLOCKED by design (complete-page mode) — the 'luxury' engine is unreachable (C1/C2) |
| Platform Customizer (100+ controls) | STORED_NOT_CONSUMED for layout/typography/spacing |
| Platform sections (hero, features, stats, team, testimonials…) | NOT USED on complete-page routes (sections exist, registry loads them, composer blocked) |
| Newsletter platform UI | PARTIAL — backend live (AJAX+REST), pack form wired |
| Analytics (GA4 dataLayer) | PRESENT (aether-analytics hooks add_to_cart/purchase) — verify events fire on pack UI (UNPROVEN) |
| Wishlist/Compare/Quick view | ENDPOINTS EXIST, pack wiring UNPROVEN → feature-loss candidate |
| Menu Plus/sticky/off-canvas platform nav | REPLACED by pack nav |
| WC presentation module (platform) | SUPPRESSED |
| Accessibility helpers (a11y.css, skip link) | SUPPRESSED with shell — a11y now depends solely on pack markup |
| Performance module (HTML compression etc.) | PARTIAL — active but interaction with frozen HTML unproven |

Net: the current frontend deliberately trades platform breadth for a frozen premium design; the "losses" are architectural choices, but several (a11y helpers, analytics confirmation, wishlist wiring) must be consciously re-established or accepted as out-of-scope (→ QUESTIONS.md).
