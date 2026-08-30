# FRONTEND FAILURE MODE REPORT

> **Status:** BASELINE → **RESOLVED (Phases A/D implemented 2026-08-09)** · **Date:** 2026-08-08 (baseline)
> ⚠ Rows F1/F2/F3 (the critical ones) are **FIXED** in the working tree. Authoritative current state: `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md` §3.
> **Mission requirement (Rule 7):** content visible → animation enhances. **Never:** content hidden → library loads → content appears. Zero invisible content caused by animation failure.

---

## 1. Failure-mode matrix

| # | Failure | Current behavior | Result | Fix |
|---|---|---|---|---|
| F1 | **GSAP CDN blocked/fails while `animations.js` loads** | (historical) `has-motion` was added before the GSAP guard | ✅ **FIXED (2026-08-09)** — guard-first; missing libs → `disableMotion()` (content visible) |
| F2 | **ScrollTrigger blocked, GSAP loads** | same guard (`typeof ScrollTrigger === 'undefined'`) | ✅ **FIXED** — same guard-first path |
| F3 | **Runtime exception inside `init()`** | (historical) no try/catch | ✅ **FIXED** — try/catch/finally → `disableMotion()` |
| F4 | **JS disabled entirely** | no `has-motion` class; CSS keeps everything visible | ✅ visible | none |
| F5 | **Reduced motion (user pref)** | JS adds `no-motion`; CSS `@media (prefers-reduced-motion: reduce)` force-visible (`!important`) | ✅ visible | none |
| F6 | **Slow network (JS eventually loads)** | brief pre-init hidden state (footer scripts, after paint); ScrollTrigger `once:true` fires for in-viewport on init | ⚠️ acceptable transient; watchdog (Phase A) also covers timeout | optional |
| F7 | **Preloader JS fails** | `preloader` removed by its own JS (`main.js:742`); if JS disabled it persists | ⚠️ blocking overlay on no-JS | CSS `@media (scripting: none)` / noscript fallback |
| F8 | **Empty WC data (no products/categories)** | adapters fall back to demo tokens → sections render demo content | ⚠️ misleading in production | Phase D: `aether_demo_content` toggle (OFF in prod → sections skip) |
| F9 | **Product without image** | `wc_placeholder_img_src` / null checks | ✅ graceful | none |
| F10 | **Empty cart** | premium empty state | ✅ | none |
| F11 | **No menu assigned** | fallback menu (curated, <4 items → fallback) | ✅ | none |
| F12 | **No logo / brand** | brand falls back to `get_bloginfo('name')`; empty → safe default | ✅ | none |
| F13 | **WooCommerce not active** | guards (`function_exists`/`class_exists`) + `home_url('/shop/')` fallbacks | ⚠️ W1/W2 unguarded calls (Phase B) | guards |
| F14 | **Image load failure** | `loading="lazy"` + alt text; no JS dependency | ✅ | none |
| F15 | **Newsletter AJAX/REST fails** | `main.js` graceful simulated-success fallback | ✅ | none |
| F16 | **Search with no results** | blog-grid renders empty + pagination; newsletter remains | ✅ no blank page | none |
| F17 | **404 route** | AETHER error/404 component | ✅ | none |

## 2. Root cause detail (F1/F2) — AS FIXED 2026-08-09

`frontend/assets/js/animations.js` (current code — guard **before** hiding):

```js
function disableMotion() {
  document.documentElement.classList.remove('has-motion');
  document.documentElement.classList.add('no-motion');
}

if (reducedMotion) { disableMotion(); return; }

// Library gate FIRST — without GSAP/ScrollTrigger there is no reveal engine,
// so content must remain visible (has-motion is never applied).
if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
  console.warn('AETHER Motion: GSAP or ScrollTrigger not loaded — content left visible.');
  disableMotion();
  return;
}

var motionInitDone = false;
var motionWatchdog = setTimeout(function () {
  if (!motionInitDone) { disableMotion(); }
}, 2500);

document.documentElement.classList.add('has-motion'); // only after the gates pass
// ... init() wrapped in try/catch/finally; finally { motionInitDone = true; clearTimeout(motionWatchdog); }
```

`motion.css` still hides reveal-targets under `has-motion` — but `has-motion` can now only exist when the reveal engine is present and initialized. `no-motion` (and `@media (prefers-reduced-motion: reduce)`) force-visibles everything.

**Consequence:** CDN outage / ad-blocker / firewall → GSAP missing → `has-motion` never applied → content fully visible. Runtime exception → `disableMotion()`. Watchdog timeout → `disableMotion()`. Rule 7's acceptance test "blocked GSAP → content remains 100% visible" is met at the code level and automated in `specs/failure-injection.spec.js`.

## 3. Required hardening (Phase A) — IMPLEMENTED 2026-08-09

1. ✅ **Guard order** — GSAP/ScrollTrigger availability checked **before** `has-motion`; failure → `no-motion` + return.
2. ✅ **Try/catch around `init()`** — exception → `disableMotion()`.
3. ✅ **Watchdog timer** — 2.5 s; `has-motion` set but init incomplete → `no-motion`.
4. ✅ **CSS belt-and-braces** — `@media (prefers-reduced-motion: reduce)` visible rule; preloader `<noscript>` kill rule.
5. ✅ Happy path **identical** (guard/watchdog only change the failure path).

Acceptance is automated: `frontend/tests/specs/failure-injection.spec.js` (`@failure` tag) covers CDN-blocked, Swiper-blocked, injected init exception, and reduced-motion.

## 4. Acceptance tests (Phase E will automate)

| Scenario | Expected |
|---|---|
| Normal load | animations intact (unchanged) |
| DevTools: block cdnjs.cloudflare.com | all content visible, no motion |
| DevTools: block unpkg.com (lenis) | content visible (lenis only smooth-scroll) |
| DevTools: throttle to Slow 3G | content visible after watchdog timeout |
| JS disabled (noscript) | full layout visible |
| `prefers-reduced-motion: reduce` | content visible, no motion |
| Runtime error injected in init | content visible |
