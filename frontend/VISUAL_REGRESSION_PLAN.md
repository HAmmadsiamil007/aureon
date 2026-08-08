# VISUAL_REGRESSION_PLAN

**Phase:** 17 — Frontend Integration Framework (Step 9: Quality Verification)
**Date:** 2026-08-06
**Status:** Plan complete — ready for Step 9 execution

---

## 1. Goal

Prove the WordPress-rendered Aureon+AETHER output is **visually equivalent** to the static frontend reference before any template is considered done. Static pages in `frontend/source/` are the golden reference; WP pages are the candidates.

---

## 2. Tooling Baseline

- **Browser:** Playwright (available via `playwright-mcp` + `webapp-testing` skill) with Chrome.
- **Reference screenshots:** captured from `frontend/source/*.html` served locally (may need a tiny static server since `asset paths are relative`).
- **Candidate screenshots:** from the WP/Local site (dev URL, e.g. `http://aureon.test` or the active install URL).
- **Diff tooling:** Playwright `toHaveScreenshot` with viewport set, or pixelmatch on exported PNGs.
- **Golden artifacts:** `frontend/regression/golden/*.png`, `frontend/regression/candidate/*.png`, `frontend/regression/diff/*.png`.

---

## 3. Test Matrix (per page type)

### 3.1 Shell (all pages)
| Check | Method |
|---|---|
| Header renders logo + menu + search + cart | screenshot top 800px |
| Footer (widgets, newsletter, social, copyright) | full-page screenshot bottom |
| Preloader fades; fog layers present | capture at 0ms, 600ms, 1500ms after nav |
| Announcement bar text | top strip text assert |
| Back-to-top presence | JS assert element exists |

### 3.2 Page-by-page (from TEMPLATE_MAPPING)

| Page | Golden | Candidate URL | Focus |
|---|---|---|---|
| Home | index.html | `/` | hero slider (slide 1), hero text breaks, categories, bestsellers, reviews, faq, newsletter |
| Shop | shop.html | `/shop/` | filter bar, product grid count/match, pagination |
| Product | product-detail.html | `/product/` (seed product) | gallery, meta, tabs, related slider |
| Cart | cart.html | `/cart/` (with session) | table, qty, totals |
| Checkout | checkout.html | `/checkout/` | form layout, summary, payment |
| Blog | blog.html | `/blog/` (or posts page) | card grid, categories, pagination |
| Single post | single-blog.html | `/post-slug/` | hero meta, article body, quote, author box, related |
| FAQ | faq.html | `/faq/ (page)` | accordion open state ~closed |
| Testimonials | testimonials.html | `/testimonials/` | card grid |
| Team | team.html | `/team/` | cards |
| Contact | contact.html | `/contact/` | form |
| Login/Register | login.html, join-now.html | `/my-account/` | forms |
| Thank-you | thank-you.html | `/checkout/order-received/` | confirmation |
| 404 | 404.html | `/~nonexistent` | layout |
| Legal | policy pages | `/privacy-policy/` | content layout |

### 3.3 Thematic (across pages, using emulation profile)

- **Reduced motion** (`prefers-reduced-motion: reduce`): all GSAP/Swiper/Lenis disabled → content visible, no hidden-by-animation.
- **Mobile widths**: 375px, 768px: mobile menu works, grid collapses, header hides/reveals; fog clipped.
- **Touch device** (`hover: none`): tilt off, no hover-revealed actions left unreachable.
- **`admin-bar` overlay** (logged-in): header doesn't collide.
- **Dark-only contrast** baseline passes AA for text/surface (AETHER palette).

### 3.4 Data-slots (phantom keys) presence

For each page, assert the server-injected keys exist in the candidate DOM and are non-empty (drive the adapter to emit them): `di nodel list` spot-check 10 keys/page (e.g. `section_label`, `page_title`, `product_price`, `blog_title`, `error_code`).

---

## 4. Execution Workflow

1. Serve static (`npx serve frontend/source` on port 8080) — capture all 22 golden files at fixed viewport 1440×900 + 375×812.
2. On WP, set Customizer tokens to = AETHER defaults (default) so the CSS vars match.
3. Capture candidate pages via Playwright DAP navigation scripted in `webapp-testing`.
4. Diff per page with pixelmatch (threshold ~0.1%, ignore known fonts/metrics deltas).
5. Review each diff manifest; classify: **pass** / **known-diff** (documented, acceptable: e.g. dynamic dates, WP admin bar, lazy images) / **fail** (investigate + reopen todo item).

---

## 5. Known-Accept Differences (protect list)

| Diff | Root cause | Policy |
|---|---|---|
| Font metrics | WP-local @font-face vs CDN fonts load timing | re-capture after `document.fonts.ready` |
| WooCommerce default filters bits | WC core hooks bunny during commerce override staging | review during WC report step |
| Lazy images | `loading="lazy"` in WP, eager in static | use `load: 'eager'` in capture |
| Admin bar | WP toolbar | disable for candidate shots (`show_admin_bar(false)` via `show_admin_bar` filter) |
| Newsletter instances on home (2× vs footer) | Section toggle | check only the intended one (toggle off the duplicate in Customizer) |

### Known mismatches to FIX, not accept:
- `a11y.css` broken link → ensure loaded in WP build (never regressed)
- Font mismatch (Satoshi/Cabinet vs system) — must match via `font-library` registration
- Gold `#d4af37` vs `#C8956C` — fix at token layer, exclude vendor CSS
- `single-blog.html` page chrome > article (nav etc.) duplicate classes — WP body classes cleaner

---

## 7. Runbook & Grading

- `frontend/regression/run.sh` or `npm run regress` script (Playwright) — runs full suite; writes `results.json` + `results.md`.
- Grade: `A` (all pass or documented), `B` (only known diffs), `C` (fail — gate blocking).
- Definitions: **pass** if diff mask `<=0.5%` pixel area; **warning** if <=10% and all diffs are in documented list; else **fail**.
- Escalate any fail to a git issue / opens an investigation todo.

---

## 8. Success Criteria (acceptance bar)

- 100% of pages captured in golden + candidate.
- Every page graded A or B by end of Step 9.
- No known-diff outside the protect list.
- All 62 phantom server keys verified present on the responsible page.
- a11y.css loaded; reduced-motion variant at baseline contrast AA.