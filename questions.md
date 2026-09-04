# QUESTIONS.md — CONSOLIDATED DECISION GATE (ROUND 2)
## Date: 2026-09-04 · Stage: STAGE A COMPLETE — WAITING FOR ONE CONSOLIDATED DECISION
## Audit basis: docs/forensics corpus (2026-09-04 11:16–11:26) + `docs/forensics/P0-REVALIDATION.md` (this round, fresh runtime evidence)
## Prior round archive: `docs/forensics/QUESTIONS-2026-09-02-ARCHIVE.md`

> Per the master prompt, this file holds every question/blocker/decision for implementation.
> The user is asked **ONE consolidated decision request** in chat (see bottom).
> Everything below is the supporting detail; recommended defaults are stated so an unanswered
> question never blocks progress.

---

## 0. REVALIDATED STATE (30-second summary)

The five alleged P0s from the previous forensic round were re-proven against the live runtime:

1. **Core file version drift → NOT PRESENT** (canonical tree == mirrors == Docker runtime; one
   `index.html` revision behind in mirrors — P2 re-sync).
2. **Vineta asset path / 37 image 404s → RESOLVED** (all URLs now HTTP 200; stale console log).
3. **shop.js null-dataset crash → FIXED** (null guard + dedupe; clean console).
4. **ES-module `export` error → STILL LIVE, root cause corrected**: `js/model-viewer.min.js`
   (ES-module) loads as a classic script on every page. Not shims.js.
5. **Bootstrap/jQuery `$().modal` mismatch → NOT CURRENTLY BROKEN** (Bootstrap 5.3.2
   jQuery-compat plugin present), fragile architecture only.

Everything the old corpus blamed on "shims.js broken" must be treated as **UNPROVEN**
(verify, don't assume) — re-classified in `test-results/FULL-FORENSIC-AUDIT-MATRIX.json`.

Environment (live facts): Docker WordPress healthy on localhost:8080 · theme `aureon` ·
active design `vineta` · `aether_demo_mode=auto` · real Sole Origine products in DB · menus
Primary(78)→`primary` + Footer(79)→`footer` assigned · active plugins: `aureon-studio`,
`woocommerce` (+ mu-plugin `aureon-fix-wc-session`) · Customizer colors/fonts/heading/search
options populated · checkout/cart/account serve **frozen Vineta templates** (complete-page).

---

## 1. P0 BLOCKERS

### Q1 — The one live P0: model-viewer ES-module load
- **Why it matters:** every page logs `SyntaxError: Unexpected token 'export'` and downloads
  936 KB of a script that can never execute; any 3D-viewer feature is dead.
- **Current evidence:** script-mode parse fails ONLY for `js/model-viewer.min.js`; it is enqueued
  unconditionally by `manifest.json`; assets pipeline already supports `"page"` gating.
- **Options:**
  - A. Gate pack assets by page in `manifest.json` (`model-viewer` → `page: "product"`-ish only;
    also gate `shop.js` to shop/collection routes; drop the unresolvable `aether-bootstrap-js`
    dep on `main.js`). Client-pack only.
  - B. Also remove model-viewer entirely until a product actually uses a 3D viewer.
  - C. Load it as `type="module"` only where needed (requires bridge support for module enqueue).
- **Recommended:** A (with B's removal as an acceptable stronger variant).
- **Consequence:** A/B are client-pack edits, zero Golden Core change, zero other-pack impact;
  C touches the bridge/Core asset loader (needs CORE-CHANGE-REQUEST.md if Core is edited).
- **Default if unanswered:** A.

---

## 2. P1 DECISIONS

### Q2 — Stage B breadth for this round
- **Why:** determines gate order and how long the session runs.
- **Options:**
  - A. **P0 + full P1 (recommended):** model-viewer/manifest fix, then Customizer mappings +
    dynamic consumers (hero, shop/category, product, search, cart, auth/account, menus, blog/404)
    with targeted tests after each group.
  - B. P0 only + the P1s that block visual acceptance (Customizer + shop/category/product).
  - C. P0 only, then stop for a fresh decision.
- **Recommended:** A, executed sequentially by this single agent (no parallel writes).
- **Default if unanswered:** B (smallest coherent unit after P0).

### Q3 — Search results page
- **Why:** `/?s=` returns 200 but renders the shop template; search semantics unproven.
- **Options:** build a dedicated Vineta search results state/template (bridge + pack), or verify
  the shop-template search rendering is acceptable for this client.
- **Recommended:** verify current behavior first; build dedicated results only if it misleads.
- **Default:** verify-and-report, fix if broken.

### Q4 — Checkout / account fidelity
- **Why:** frozen Vineta checkout.html and account templates are served; WC content injection
  (form fields, payment methods, order review) is unproven at runtime.
- **Options:** (a) full functional checkout/account acceptance with test order; (b) visual-only
  alignment + WC-native fallback where the frozen template can't host WC forms.
- **Recommended:** (a) — real order via COD/test gateway, then cleanup.
- **Default:** (a).

### Q5 — Product/bridge data gaps carried from the corpus (SKU, stock, variant JS)
- **Why:** SKU/stock never read by adapter; real-time variation JS unconfirmed.
- **Recommended:** add SKU+stock to adapter output only if the Vineta product template displays
  them; add variation JS only if swatch UI exists on product page.
- **Default:** skip what the approved visual doesn't show.

---

## 3. ARCHITECTURE QUESTIONS

### Q6 — Duplicate script loading (7–8 libs ×2; jQuery ×2)
- **Why:** 53 script tags/page, double-init risk (shop.js needed its own dedupe guard).
- **Options:** (a) stop WP-enqueueing manifest assets that the frozen HTML already self-loads
  (manifest is the single source of truth → remove frozen `<script>` tags or prune manifest);
  (b) keep frozen HTML as-is and add dedupe guards; (c) defer.
- **Recommended:** prune the manifest to scripts the frozen pages do NOT self-load and add page
  gating (Q1) — one manifest edit solves R1+R2+R5 together.
- **Default:** (a) via manifest, verified by script census before/after.

### Q7 — Dual jQuery architecture
- **Why:** P0-5 works by luck of load order; WC AJAX fragments bind a different jQuery.
- **Recommended:** document + regression-test the save/restore bridge; do NOT re-platform during
  this round unless a concrete break appears.
- **Default:** document + test only.

### Q8 — Golden Core boundary
- **Why:** master prompt forbids Core edits without proof + CORE-CHANGE-REQUEST.md.
- **Current expectation:** NO Core change needed for any P0/P1 above (all client-pack/bridge).
  If one becomes necessary: STOP, file CORE-CHANGE-REQUEST.md, ask.
- **Default:** frozen Core.

---

## 4. CUSTOMIZER QUESTIONS

### Q9 — Customizer acceptance depth
- **Why:** options exist in DB (colors, fonts, heading, search placeholder, logo 544, menus).
- **Recommended:** full default→set→save→reload→computed-style→reset cycle for the ~9 implemented
  controls; add NEW controls (newsletter copy, announcement bar) only if Stage B reaches P2 and
  the approved visual needs them.
- **Default:** verify existing 9; no new controls this round unless visual requires it.

---

## 5. WOOCOMMERCE QUESTIONS

### Q10 — Test data policy
- **Why:** DB already contains real-looking Sole Origine products; earlier rounds used VTS/VTV QA
  products and created orders/users (see test-results/QA-DATA-IDS.json).
- **Recommended:** reuse existing published products for visual/dynamic acceptance; create
  temporary QA products/orders/users ONLY for mutation tests and clean them up (QA cleanup gate).
- **Default:** reuse; temp data for mutations; full cleanup after.

---

## 6. PLUGIN QUESTIONS

### Q11 — Plugin scope
- **Why:** active plugins = aureon-studio + woocommerce; aureon-studio is large/unanalyzed.
- **Recommended:** verify WC + aureon-studio don't break Vineta pages (already true on 7 routes);
  treat deeper aureon-studio module work as P2+; don't activate anything new.
- **Default:** verification only.

---

## 7. DEPLOYMENT / PRODUCTION QUESTIONS

### Q12 — Mirror sync & git state
- **Why:** mirrors lag `index.html`; both mirrors carry uncommitted Ferm-removal changes; git HEAD
  `a79ce16` predates today's corpus.
- **Recommended:** after Stage B fixes: re-sync `AUREON-WORDPRESS-DEPLOY/` + `AUREON-GOLDEN-COPY/`
  from canonical, remove `.bak-phase3` files, commit forensic docs + fixes as discrete commits.
- **Default:** sync + commit at end of Stage B (no push).

### Q13 — Live/production site (InfinityFree)
- **Why:** earlier INFINITYFREE-* reports describe a live deployment; this pass could not verify it
  (no URL/credentials). Production claim must not be made without it.
- **Options:** (a) include live-site verification in Stage B if credentials exist; (b) this round
  is local-runtime only, and live verification is scheduled separately.
- **Default:** (b) unless the user provides live access.

---

## 8. QUESTIONS → ONE CONSOLIDATED DECISION REQUEST

Everything above reduces to one decision with clear defaults:

**Authorize STAGE B implementation now, on the local Docker runtime (localhost:8080), executed
sequentially by this single agent, starting with the one live P0 (model-viewer/manifest page
gating, client-pack only — Golden Core frozen), then the agreed P1 gate set with targeted tests
after every fix, QA data cleaned up, mirrors re-synced, and forensic docs committed — with NO push
and NO changes to production?**

- **If answered YES with the recommended options** → implement P0 first, then P1 in gate order
  (Customizer → dynamic consumers → search/cart/auth/account → menus → blog/404), testing each.
- **If answered PARTIAL (P0 only)** → implement P0 + re-verify clean console on all routes, then
  stop for the next decision.
- **If the user has different priorities** → those override, stated in chat.

**Status: `AUDIT_COMPLETE_WAITING_FOR_DECISION`** — Stage B has NOT started. No source was
modified in Stage A (only the forensic docs above were created/updated).
