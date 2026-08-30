# ANIMATION_INTEGRATION_REPORT

**Phase:** 17 — Frontend Integration Framework (Step 6: Animation Bridge)
**Date:** 2026-08-06
**Status:** Complete — bridge spec from JS audit

---

## 1. Animation Stack Inventory

| Library | Version | Load Source (static) | Active? | WP Strategy |
|---|---|---|---|---|
| GSAP | 3.12.5 | CDN `<script>` | Yes — ScrollTrigger + text reveals | Bundle locally (global vendor has 3.15.0 — **pin decision required** below) |
| ScrollTrigger | 3.12.5 | CDN (gsap plugin) | Yes | bundled with GSAP |
| Lenis | 1.1.18 | CDN | Yes — `lenis-scroll.js` | Bundle locally |
| Swiper | 11 | CDN | Yes — hero + product sliders | Bundle locally |
| jQuery | 3.7.1 | CDN (bootstrap deps) | Bootstrap-only | Keep bundled (WP core + bootstrap need it) |
| Bootstrap | 5.3.3 | CDN | Yes — accordion, dropdowns, offcanvas | Bundle locally |

**Dead JS (exclude):** `effects.js`, `phantom-dark-mode.js`, `three-scenes.js`, all `vendor/*` (19).

---

## 2. Behavior Hook (data-attribute) Census

| Attribute | Count | Owned By | Function |
|---|---|---|---|
| `data-motion-text="words\|lines"` | 35 | animations.js | split/word or line reveal on scroll |
| `data-tilt` | 38 | animations.js | 3D hover tilt |
| `data-reveal-item` | 20 | animations.js | stagger reveal in flex/grid |
| `data-reveal-group` | 8 | animations.js | parent group stagger |
| `data-reveal` (attr) | ~applied broadly | animations.js | generic reveal (.section-header, .card, etc.) |
| `data-parallax-section` | 16 | animations.js | section bg parallax |
| `data-parallax` | 3 | animations.js | element parallax |
| `data-image-zoom` | 27 | main.js | product gallery zoom |
| `data-mouse-parallax` | 1 | animations.js | hero mouse parallax |
| `data-phantom` (contract) | 62 keys | phantom-bridge | content binding |
| `data-swiper` (implied by `.hero-swiper` / `.productsSwiper`) | — | main.js | Swiper init |

---

## 3. Animations.js engine shape (from audit)

- Wrapped in single IIFE, `document` auto-init on `DOMContentLoaded`.
- Guarded: reduced-motion detection + `gsap`/`ScrollTrigger` presence check.
- Text split functions (`splitWords`, `splitLines`) inject `.motion-word`/`.motion-line` spans.
- Auto-reveal via `autoAssignReveals()` — applies default reveal to known selectors (`.section-header`, `.section-label`, `.product-card`, `.blog-card`, etc.) — **this couples JS to specific class names** (framework must keep class names or map them).
- Preset-based `data-reveal` presets (fade-up, fade-left, etc).

---

## 4. Animation Bridge Architecture

The bridge is **declarative**: WP templates set `data-*` attributes; the bridge JS reads them. No imperative per-component animation JS in PHP.

### 4.1 Bridge file layout

```
frontend/assets/js/aether-animations.js   (renamed/refactored animations.js)
frontend/assets/js/aether-core.js           (main.js → decoupled DOM-ready handlers)
frontend/assets/js/aether-lenis.js          (lenis-scroll.js)
frontend/assets/js/aether-forms.js          (form handlers: newsletter/contact/notify)
frontend/assets/js/aether-cart.js           (mini-cart + add-to-cart AJAX + quantity)
frontend/assets/js/aether-gallery.js        (product gallery + data-image-zoom)
frontend/assets/js/aether-auth.js           (firebase-auth fix, module)
vendor bundle: gsap.min.js + ScrollTrigger.min.js + lenis.min.js + swiper-bundle.min.js + bootstrap.bundle.min.js + jquery.min.js
```

### 4.2 Declarative bridge = attribute contract (pass-through to components)

Components output `data-reveal-item`, `data-tilt`, `data-motion-text` attributes declared in `$componentData['behavior']`. The bridge auto-initializes all registered attributes on one pass.

### 4.3 Reduced-motion / performance flags

- Respect `prefers-reduced-motion: reduce` (already in engine).
- Customizer `frontend_motion_*` toggles (from TOKEN report) can gate each fx before enqueue:
  - `motion_parallax` off → strip `data-parallax-*` attrs at render time (PHP-side), don't enqueue parallax module.
  - Note: template's `motion.css` shows parallax defaults + `.has-javascript` gating.

### 4.4 Preloader / fog

- `#preloader` + `#fog` in shell components; bridge handles fade-out after `window.load` + optional brand delay.
- Reduced-motion: skip preloader (show content immediately).

---

## 5. GSAP Version Pin Decision

- Template pins CDN **3.12.5**; global vendor lib has **3.15.0**. For reproducibility:
  - **Decision: pin GSAP 3.12.5 + ScrollTrigger 3.12.5 locally** (match template exactly, no risk of API drift in `animations.js` code).
  - Same for Lenis **1.1.18** (template) — global is 1.3.26. Verify `raf` API compatibility; if the small API delta is moot, note pin vs latest tradeoff.
  - Swiper **11** stable pin.
- Enqueue via `wp_enqueue_script` with `defer`, versioned, `integrity` only for local (no CDN deps — faster, offline-capable, and Customizer-friendly with cache-busting).

---

## 6. Init Order (target)

```
window load
 ├── preloader/fog fade
 ├── lenis (smooth scroll, if enabled)
 ├── header/mobile-menu handlers
 ├── swiper instances (hero/sliders)
 ├── annimations engine (scrolltrigger.refresh after swoipr init!)
```

- Order concern: `ScrollTrigger.refresh()` must run **after** images/sliders settle — delay via `window.load` + `Swiper.on('init')` callback (same caveat as static template).
- In dynamic WP with lazy-loaded content (`WP_Query` AJAX), re-init via `window.dispatchEvent(new CustomEvent('aether:content-updated'))` — bridge listens and re-scans `[data-*]`.

---

## 7. Accessibility & Reduced-Motion Compliance

- Standard: `<a11y.css>` provides focus rings; `.skip-to-content` present (see COMPONENT_INVENTORY).
- `prefers-reduced-motion: reduce` → no loops/no scroll reveals (existing behavior).
- Swiper: add `pagination: { clickable: true }`, ARIA slides, `aria-live="polite"` on autoplay.
- Lenis: cap smooth-scroll duration for users who prefer reduced motion? (leave optional in config).
- Keyboard: `data-tilt` must not trap provide on-focus-jump; prefer transform on hover only for persists; disable tilt on `@media (hover: none)`.

---

## 8. Test Matrix (for Step 9 regression)

| Test | Selector/Page | Expect |
|---|---|---|
| motion-text splits | home `.section-title`, product `.pd-hero h1` | words animate with GSAP on scroll |
| reveal stagger | `.shop-grid-section .product-card` | rotation? no — stagger fade-up |
| hero slider | `/` `.hero-swiper` | autoplay + fade, sync with `ScrollTrigger` |
| product zoom | product gallery `[data-image-zoom]` | zoom overlay on hover |
| mobile menu | < 992px `.mobile-header` | overlay opens, body scroll-locked |
| header hide | scroll down 100px | `.header` hides/condenses, reappears up |
| tilt | product/category cards hover | 3D transform lerp |
| fog/preloader | page load | fade out ≤ 1.2s, fog drifts |
| reduced-motion | browser OS reduced ⇒ Verify | all motion skipped, content visible without animation |
| AJAX re-init | shop filter (if lazy cards) | `aether:content-updated` re-runs `autoAssignReveals`.