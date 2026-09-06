# PHASE 11 — A11Y SCAN

> **Phase:** 11 · **Date:** 2026-08-14 · **Method:** Playwright + `@axe-core/playwright` (WCAG 2 A/AA) — baseline suite (`specs/a11y.spec.js`, 12 tests desktop+mobile) plus an extended one-off scan over 9 additional surfaces with scroll-then-scan
> **Result:** baseline 12/12 green; extended scan exposed **1 CRITICAL accessibility defect** (every page with the newsletter section) and a scan-coverage gap in the baseline suite

---

## 1. Scans run

| Scan | Surfaces | Scroll before axe? | Result |
|---|---|---|---|
| Baseline `a11y.spec.js` (desktop+mobile) | `/`, `/contact/`, `/my-account/` + skip-link, landmarks, alt-attrs tests | **NO** (reveal-hidden sections skipped by axe) | 12/12 passed |
| Extended probe (desktop) | `/shop/`, product, `/cart/`, `/about/`, `/team/`, `/faq/`, `/wishlist/`, `/?s=void` | **YES** (scroll to bottom, 800ms settle) | 1 passed (coming-soon — no newsletter), 8 failed on a single rule |

## 2. Finding

| ID | Sev | Finding |
|---|---|---|
| F11-1 | **CRITICAL** | **Newsletter submit button has no accessible name.** Live markup: `<button type="submit" class="newsletter-btn"><span class="newsletter-btn-text"></span><i class="fas fa-arrow-right newsletter-btn-icon"></i></button>`. Root cause — three-layer default shadowing: `sections/section-newsletter.php:26` passes no `button_text` → `components/section/newsletter.php:46` always sets `button_text => ''` (isset-default) → `components/forms/newsletter.php:31`'s fallback `__( 'Subscribe', 'aureon' )` is unreachable (`isset()` true for `''`). Every page rendering the newsletter section ships an unnamed submit button (axe `button-name`, impact **critical**). JS never populates the span (main.js only handles AJAX submit). **One-line fix candidate (Phase 12-15):** `section/newsletter.php:46` default `''` → `__( 'Subscribe', 'aureon' )` (or forward only when non-empty). |
| F11-2 | MED | **Baseline a11y suite has a scan-coverage gap.** axe runs at ~1.5s after load without scrolling; reveal-animated sections sit at `opacity:0; visibility:hidden` (inline style pre-reveal) and axe excludes `visibility:hidden` from the accessibility tree — so below-the-fold violations (like F11-1) are invisible to the baseline suite. Verified: `/contact/` without scroll → `button-name` nodes = none; `/shop/` after scroll → node reported. **Fix candidate (Phase 12-15):** scroll-to-bottom + settle before axe in `a11y.spec.js`, and add `id="newsletter"` (shop, cart, product, about, team, faq, wishlist, search) to `A11Y_PAGES`. |

## 3. Non-issues re-verified

- Skip link `#main` is the first focusable element (desktop + mobile) ✅
- `main#swup` + header/footer landmarks present ✅
- All `main img` have `alt` ✅
- Contrast: `--muted #A8B5C0` on `--void #09090B` ≈ 6.9:1 (AA) — no color-contrast violations reported on any scanned page ✅

## 4. Verdict

One critical defect (F11-1) with a one-line root cause and a clear fix, plus a suite coverage gap (F11-2). Both feed the Phase 12-15 change gate. No immediate change (read-only phase).