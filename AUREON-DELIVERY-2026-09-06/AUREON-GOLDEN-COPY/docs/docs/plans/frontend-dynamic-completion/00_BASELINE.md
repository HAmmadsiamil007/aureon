# Step 0 — Baseline: Frontend Dynamic Completion & Integration Hardening

> **Date:** 2026-08-13
> **Purpose:** Freeze the pre-mission state. No production code is modified by this step.
> **Source of truth:** live repo state + verified docs (STATUS.md, CHANGELOG, Phase 17 reports, G6 reports). Container `aureon_wp` is **not running** at audit time — live-only numbers are recorded from the last verified session (2026-08-09).

---

## 1. Git state

| Item | Value |
|---|---|
| Branch | `main` |
| HEAD | `88ab98a` — `feat(frontend): G6 hero slides repeater - schema-driven Customizer control, stable IDs, legacy migration, 41/41 hardening pass` |
| Tag | `v1.2.0-g6` |
| Working tree | **CLEAN** (0 modified/untracked tracked files) |
| Prior tags | `v1.1.0-aureon`, `v1.0.1-lumina`, `v1.0.0-lumina-pkg`, `v1.0.0-lumina` |

## 2. Versions

| Product | Display version | Internal constant |
|---|---|---|
| Aureon theme | `1.2.0` (`style.css`) | `AUREON_VERSION` = 3.6.1 |
| Aureon Studio plugin | `1.1.0` (`aureon-studio.php`) | `AUREON_STUDIO_VERSION` = 3.0.0 |
| AETHER engine | v1.1.0+ (26 sections / 23 adapters / 52 manifest entries, 53 component files) | — |

## 3. Quality gates (run 2026-08-13, local)

| Gate | Result |
|---|---|
| `php -l` (all tracked PHP: theme + plugin + frontend) | **337/337 clean** |
| `node --check` (all tracked JS excl. vendor/dist/minified) | **73/73 clean** |
| `frontend/tests/verify.sh` | **PASSED** — syntax, grep gate (0 WP/WC calls in components/), 23 adapters, tokens/manifest/renderer present |
| Git fingerprint scan (GeneratePress) | 0 hits outside intentional `license.txt` GPL attribution |

## 4. E2E suite (Playwright)

- Config: `frontend/tests/playwright.config.js` — Chrome channel, 2 projects:
  - `desktop` 1280×800
  - `mobile` 390×844
- Specs (5): `routes` (3), `interactions` (8), `failure-injection` (4), `a11y` (4), `visual` (2) = **21 test calls**
- **Recorded result (2026-08-09, per CHANGELOG): 69 passed / 1 skipped (desktop mobile-drawer) / 0 failed** — suite has evolved since (e.g. interactions 6→8, visual 1→2), so a fresh full run is required at M1.
- Harness bugs fixed historically (3): absolute-href product selector, FAQ live-locator, search overlay timeout.

## 5. Visual regression baseline

- 97 screenshots in `aureon-doc/screenshots/`: 17 route screens (screen-01…17), 12 section close-ups (sec-01…12), 5 stage proofs (stage3-home-*, stage4-*-*, stage5-*-*), 2 stage6-10, 1 stage13-home + historical marquee/bento/homepage series.
- Route screens correspond 1:1 to the 17-route live matrix (see §7).
- Baseline policy: any new visual diff must be classified EXPECTED or REGRESSION.

## 6. Browser / runtime matrix

| Axis | Baseline |
|---|---|
| Browsers | Chrome (Playwright channel), headless |
| Viewports | 1280×800 desktop, 390×844 mobile |
| Responsive targets (planned in STEP 15) | 375 / 390 / 768 / 820 / 1024 / 1280 / 1440 |
| Stack | WP 6.9.1 (container), WooCommerce 11.x (container, exact patch unverifiable offline), PHP 8.2 local / 8.3 container, Node 24 |

## 7. WooCommerce state (last verified 2026-08-07/09)

| Item | Value |
|---|---|
| Products | 6 (Void Jacket, Nebula Hoodie, Phantom Tee, Black Chino Pants, Aether Cap, Midnight Sneakers ID 33) |
| Categories | 1 (`Uncategorized`) — filter bar shows "All" only |
| Featured images | All 6 seeded (media ID 60 = sneaker cover as `_thumbnail_id`) |
| Sale products | 0 (Sale filter button correctly hidden) |
| New-Arrival badges | 4 products (<30 days old — real data, by design) |
| Reviews | Real review infrastructure present; product page currently renders demo fallback (4.8/128) |
| Permalinks | `/%postname%/` + flushed rewrites |
| Cart/Checkout | Empty-cart → `/checkout/` 302 → `/cart/` standard |
| Wishlist | AJAX toggle + count (nopriv → login redirect) |
| Newsletter | DB table `wp_aether_newsletter_subscribers`, 2 rows, REST + AJAX |

## 8. Content state

- 9 static pages (about 62, contact 63, team 64, faq 65, wishlist 66, login 67, register 68, coming-soon 69), blog index (70) via `page_for_posts`, sample post (71).
- FAQ / testimonials / team / hero render **demo fallbacks** from tokens (real CPTs empty).

## 9. Architecture contract (frozen — RULE 1)

```
WP/WC → Adapters (23, only WP/WC-touching layer) → ViewModel → Renderer (section registry,
per-call $data wins over registered adapter_args) → Components (52, zero WP/WC calls) → CSS/JS
```

- Frozen: kernel, registry, renderer, composer, manifest, adapters contract, tokens bucket (`aureon_settings`), Customizer contracts, motion contracts, WC bridge (theme `template_include`).
- Known engine fixes (durable gotchas): per-call data merge (renderer.php:79), adapter hyphen→underscore resolution, `esc_url_raw` must never receive unresolved relative paths, `wp_register_style('aether-tokens', false)` (enqueue path drops dependents).

## 10. Roadmap order (STEP 24 — evaluated from evidence)

The mission-spec order M1→M15 is sound with one dependency adjustment:

| Milestone | Evidence-based adjustment |
|---|---|
| M1 Final live verification | Blocked until container starts (docker unavailable at audit) — runs first once up |
| M2 WC Color Bridge | Highest-value, self-contained — start immediately |
| M3 G9 Typography | Independent of M2 (different token group) |
| M4 G11 Elements, M5 G12 Blog | Both read existing settings → low risk |
| M6 AJAX cart | Touches WC-native paths — sequence after M2 (color tokens shared) |
| M7 Checkout/Account | Wrap-only — after M6 |
| M8 CSP | Report-only audit — can run in parallel, enforce last |
| M9–M12 Demos | Largest scope — after all dynamic work stabilizes tokens |
| M13+ | Demo packs, commercial QA, release |

## 11. Known open items (pre-mission)

1. CSP is `Report-Only` (deferred enforcement) — M8.
2. Google OAuth dormant (empty client keys — site-owner config) — not a code gap.
3. Demo imagery reuses one sneaker photo (token/Customizer-level, M9 media fixes).
4. FAQ/testimonials/team real content absent (demo fallbacks visible) — content seeding, not code.
5. Plugin legacy dead endpoints (harmless, documented).
6. Docker container down — M1 must restart + re-verify before any live claims.

---

**Baseline recorded. No production code modified.**
