# VINETA CLEANUP ANALYSIS

**Date:** 2026-09-01
**Source:** Vineta HTML Package (themesflat.com)

---

## PHASE 20 — SHOPIFY / FOREIGN BUSINESS LOGIC

### Findings

| Check | Result | Action |
|-------|--------|--------|
| Shopify API calls | ✅ NONE found in HTML/JS | No action needed |
| Shopify CDN references | ✅ NONE in source code | No action needed |
| Shopify checkout/cart | ✅ NONE | No action needed |
| Shopify customer API | ✅ NONE | No action needed |
| External commerce APIs | ✅ NONE | No action needed |
| Analytics/tracking scripts | ✅ NONE in source code | No action needed |
| Google Analytics | ⚠️ Only in privacy-policy.html template text | Replace text with WP-compatible |
| Facebook Pixel | ✅ NONE | No action needed |
| Hotjar/Mixpanel | ✅ NONE | No action needed |

### External URLs Found (All Presentational)

| URL Pattern | Purpose | Action |
|-------------|---------|--------|
| `facebook.com`, `instagram.com`, `x.com`, `snapchat.com` | Social media links in header/footer | KEEP — replace with dynamic Customizer values |
| `themeforest.net/item/vince-multipurpose...` | Template marketplace link in footer | REMOVE — replace with client content |
| `google.com/maps/embed` | Google Maps embed (contact page) | KEEP — replace with client location |
| `google.com/maps?q=...` | Store location links | REPLACE — dynamic WP data |
| `www.shopify.com/legal/privacy` | Template text in privacy policy | REPLACE — WP-compatible privacy text |

### PHP Files (Business Logic)

| File | Purpose | Action |
|------|---------|--------|
| `contact/contact-process.php` | Contact form handler | REMOVE — replaced by WP form handler |
| `mail/subscribe.php` | Newsletter subscription (CSV) | REMOVE — replaced by WC/WP newsletter |
| `mail/subscribe-mailchimp.php` | Mailchimp subscription | REMOVE — replaced by WC/WP newsletter |
| `mail/lib/` | Mail library | REMOVE — replaced by WP mail |

**Verdict:** ✅ CLEAN — No foreign commerce business logic in source code. Only template placeholder text needs updating.

---

## PHASE 21 — THIRD-PARTY VENDOR CLEANUP

### Required Libraries (KEEP ALL)

| Library | File(s) | Size | Purpose | Status |
|---------|---------|------|---------|--------|
| jQuery | `js/jquery.min.js` | 87K | Core dependency | REQUIRED |
| Bootstrap | `css/bootstrap.min.css` + `js/bootstrap.min.js` | 442K | Framework | REQUIRED |
| Swiper | `js/swiper-bundle.min.js` + `css/swiper-bundle.min.css` | 381K | Sliders/carousels | REQUIRED |
| PhotoSwipe | `js/photoswipe.umd.min.js` + `js/photoswipe-lightbox.umd.min.js` + `css/photoswipe.css` | 76K | Image gallery/lightbox | REQUIRED |
| Lazysize | `js/lazysize.min.js` | 17K | Lazy loading | REQUIRED |
| Animate.css | `css/animate.css` | 65K | CSS animations | KEEP |

### Optional Libraries (REVIEW)

| Library | File(s) | Size | Purpose | Status |
|---------|---------|------|---------|--------|
| Drift | `js/drift.min.js` + `css/drift-basic.min.css` | 18K | Product image zoom | KEEP — used in product pages |
| noUiSlider | `js/nouislider.min.js` | 40K | Price range filter | KEEP — used in shop filters |
| Bootstrap Select | `js/bootstrap-select.min.js` + `css/bootstrap-select.min.css` | 116K | Enhanced select dropdowns | KEEP — used in forms |
| WOW | `js/wow.min.js` | 8K | Scroll reveal | KEEP — used in animations |
| simpleParallax | `js/simpleParallaxVanilla.umd.js` | 13K | Parallax effects | KEEP — used in homepages |
| Image Compare Viewer | `js/image-compare-viewer.min.js` + `css/image-compare-viewer.min.css` | 41K | Before/after comparison | KEEP — product comparison |
| Fancybox | `css/jquery.fancybox.min.css` | 14K | Lightbox (may duplicate PhotoSwipe) | REVIEW — check if actually used |
| Model Viewer | `js/model-viewer.min.js` | 936K | 3D model viewer (Google) | KEEP — 3D product pages |

### Custom Vineta Scripts (KEEP ALL)

| Script | Size | Purpose | Status |
|--------|------|---------|--------|
| `js/main.js` | 52K | Main application logic | REQUIRED |
| `js/shop.js` | 23K | Shop filtering/sorting | REQUIRED |
| `js/carousel.js` | 8K | Carousel initialization | KEEP |
| `js/count-down.js` | 6K | Countdown timer | KEEP |
| `js/infinityslide.js` | 8K | Infinite scroll | KEEP |
| `js/jquery-validate.js` | 21K | Form validation | KEEP |
| `js/paralaxei.js` | 2K | Parallax | KEEP |
| `js/multiple-modal.js` | 2K | Multiple modal handler | KEEP |
| `js/zoom.js` | 9K | Product image zoom | KEEP |

### Recommendation

**KEEP ALL third-party libraries.** No proven unnecessary dependencies. The Fancybox CSS may be redundant with PhotoSwipe but is lightweight (14K) and may be referenced by some pages.

---

## PHASE 22 — IMAGE CLEANUP

### Image Directory Summary

| Directory | Count | Size | Classification |
|-----------|-------|------|----------------|
| `images/logo/` | 4 | 24K | REQUIRED — logo, favicon |
| `images/icon/` | 4 | 13K | REQUIRED — SVG icons |
| `images/payment/` | 14 | 60K | REQUIRED — payment method icons |
| `images/brand/` | 9 | 40K | KEEP — brand logos |
| `images/country/` | 4 | 24K | KEEP — country flags |
| `images/avatar/` | 10 | 116K | KEEP — user avatars |
| `images/demo/` | 30 | 368K | KEEP — demo content |
| `images/blog/` | 60 | 752K | KEEP — blog images |
| `images/testimonial/` | 27 | 640K | KEEP — testimonial images |
| `images/banner/` | 69 | 1.6M | REVIEW — many homepage-specific |
| `images/products/` | 25 dirs | 9.5M | REVIEW — organized by category |
| `images/slider/` | 22 | 2.9M | REVIEW — homepage slider images |
| `images/section/` | 42 | 1.3M | REVIEW — section-specific |
| `images/cls_categories/` | 22 | 4.1M | REVIEW — category images |
| `images/gallery/` | 10 | 1.7M | KEEP — product gallery |
| `images/video/` | 10 | 41M | REVIEW — video product demos (LARGEST) |

### Root SVGs

| File | Purpose | Status |
|------|---------|--------|
| `images/afford.svg` | Feature icon | KEEP |
| `images/convenient.svg` | Feature icon | KEEP |
| `images/leaf.svg` | Feature icon | KEEP |
| `images/cursor-close.svg` | UI icon | KEEP |

### Cleanup Strategy

1. **DO NOT delete images until all references are verified**
2. Keep all images referenced by retained HTML pages
3. `images/video/` (41M) is the largest directory — review if video files are actually used
4. `images/banner/` and `images/section/` may have unused images from removed homepage variants
5. `images/cls_categories/` (4.1M) — category images, keep for category pages
6. **Target: ZERO REQUIRED BROKEN IMAGES**

---

## PHASE 23 — CSS CLEANUP

### CSS Files

| File | Size | Role | Status |
|------|------|------|--------|
| `css/styles.css` | 519K | **MAIN STYLESHEET** — Vineta visual system | REQUIRED |
| `css/bootstrap.min.css` | 312K | Bootstrap framework | REQUIRED |
| `css/animate.css` | 65K | CSS animations | KEEP |
| `css/swiper-bundle.min.css` | 18K | Swiper component | REQUIRED |
| `css/photoswipe.css` | 7K | PhotoSwipe gallery | REQUIRED |
| `css/bootstrap-select.min.css` | 13K | Select dropdowns | KEEP |
| `css/jquery.fancybox.min.css` | 13K | Fancybox lightbox | REVIEW |
| `css/drift-basic.min.css` | 2K | Drift zoom | KEEP |
| `css/image-compare-viewer.min.css` | 4K | Image compare | KEEP |
| `css/styles.css.map` | 98K | Source map | OPTIONAL |

### Cleanup Strategy

1. **Preserve `css/styles.css`** — this IS the Vineta visual system
2. **DO NOT redesign** — cleanup only removes proven unused CSS
3. SCSS files (`scss/`) are source reference — keep as documentation
4. `css/styles.css.map` is optional — can be removed for production
5. **Target: Remove only proven unused CSS selectors** (requires thorough reference check)

---

## PHASE 24 — JAVASCRIPT CLEANUP

### JS Files Summary

| Category | Files | Total Size | Status |
|----------|-------|------------|--------|
| Core framework | jquery.min.js, bootstrap.min.js | 217K | REQUIRED |
| Core application | main.js, shop.js, carousel.js | 83K | REQUIRED |
| Slider/animation | swiper-bundle.min.js, wow.min.js | 371K | REQUIRED |
| Image/gallery | photoswipe*.min.js, lazysize.min.js | 86K | REQUIRED |
| Product UI | zoom.js, drift.min.js, model-viewer.min.js | 962K | KEEP |
| Shop UI | nouislider.min.js, bootstrap-select.min.js, infinityslide.js, jquery-validate.js | 171K | KEEP |
| Utility | count-down.js, paralaxei.js, simpleParallaxVanilla.umd.js, multiple-modal.js, image-compare-viewer.js | 32K | KEEP |

### Cleanup Strategy

1. **DO NOT remove any JS that is actively used by retained HTML pages**
2. `model-viewer.min.js` (936K) is large but required for 3D product pages
3. `swiper-bundle.min.js` (362K) is required for all sliders/carousels
4. `main.js` contains contact form AJAX — note for AUREON replacement
5. **Target: Remove only proven dead code**

---

## SUMMARY

| Category | Findings | Action |
|----------|----------|--------|
| Shopify/foreign logic | NONE in source | ✅ Clean |
| Third-party libraries | 13 libraries, all used | KEEP ALL |
| Images | 64MB, ~1369 files | REVIEW video/banner/section dirs |
| CSS | 10 files, 452K total | KEEP ALL (review fancybox) |
| JS | 23 files, 2.1MB total | KEEP ALL |
| PHP scripts | 3 files | REMOVE — replaced by WP |
| Secrets/credentials | themesflatc11@gmail.com in contact PHP | REMOVE |
