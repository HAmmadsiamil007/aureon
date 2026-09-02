# VINETA IMAGE/VIDEO AUDIT

**Date:** 2026-09-01
**Status:** AUDIT COMPLETE — ALL ASSETS REQUIRED

---

## Summary

| Directory | Files | Size | Status |
|-----------|-------|------|--------|
| images/avatar/ | 10 | 116K | ✅ REQUIRED |
| images/banner/ | 69 | 1.6M | ✅ REQUIRED |
| images/blog/ | 60 | 752K | ✅ REQUIRED |
| images/brand/ | 9 | 40K | ✅ REQUIRED |
| images/cls-categories/ | 22 | 4.1M | ✅ REQUIRED |
| images/country/ | 4 | 24K | ✅ REQUIRED |
| images/demo/ | 30 | 368K | ✅ REQUIRED |
| images/gallery/ | 10 | 1.7M | ✅ REQUIRED |
| images/icon/ | 4 | 13K | ✅ REQUIRED |
| images/logo/ | 4 | 24K | ✅ REQUIRED |
| images/payment/ | 14 | 60K | ✅ REQUIRED |
| images/products/ | 25 | 9.5M | ✅ REQUIRED |
| images/section/ | 42 | 1.3M | ✅ REQUIRED |
| images/slider/ | 22 | 2.9M | ✅ REQUIRED |
| images/testimonial/ | 27 | 640K | ✅ REQUIRED |
| images/video/ | 10 | 41M | ✅ REQUIRED |
| Root SVGs | 4 | ~10K | ✅ REQUIRED |

---

## Video/3D Assets (41MB)

| File | Size | Referenced By | Purpose | Status |
|------|------|---------------|---------|--------|
| bag-3d.glb | 4.5MB | product-3d.html | 3D product model | ✅ REQUIRED |
| bicycle.mp4 | 10MB | home-bicycle.html | Homepage video | ✅ REQUIRED |
| item-pet-1.mp4 | 2.2MB | home-pet-accessories.html | Homepage video | ✅ REQUIRED |
| item-pet-2.mp4 | 519KB | home-pet-accessories.html | Homepage video | ✅ REQUIRED |
| item-pet-3.mp4 | 5.4MB | home-pet-accessories.html | Homepage video | ✅ REQUIRED |
| skincare.mp4 | 3.5MB | home-skincare.html | Homepage video | ✅ REQUIRED |
| skincare-2.mp4 | 1.5MB | home-skincare.html | Homepage video | ✅ REQUIRED |
| skincare-3.mp4 | 10.6MB | home-skincare.html | Homepage video | ✅ REQUIRED |
| thumb-3d.jpg | 3.4KB | product-3d.html | 3D model thumbnail | ✅ REQUIRED |
| video-product.mp4 | 4.3MB | product-video.html | Video product feature | ✅ REQUIRED |

**All video/3D files are referenced by legitimate product variant pages.**

- `product-3d.html` — Demonstrates 3D product viewing capability (uses Google model-viewer)
- `product-video.html` — Demonstrates video product capability
- `home-bicycle.html`, `home-pet-accessories.html`, `home-skincare.html` — Homepage video backgrounds

These files demonstrate genuine Vineta capabilities and should be retained.

---

## Decision: KEEP ALL

**Reasoning:**
1. Every image/video file is referenced by at least one HTML file
2. Video files demonstrate genuine product capabilities (3D, video products)
3. The selected production frontend (index.html) doesn't directly reference video files, but the product variant pages that do are retained as genuine capability demonstrations
4. Removing these would silently eliminate product presentation capabilities

**Recommendation:** Keep all assets. The 41MB video directory is large but demonstrates real features that differentiate Vineta from simpler templates.

---

## Future Optimization (Post-AUREON Connection)

When connected to WordPress/WooCommerce:
- Video URLs will come from WooCommerce product data
- 3D model URLs will come from WooCommerce product data
- Homepage videos will come from WordPress Customizer
- Images will be served from WordPress media library
- Local video files may be replaced by CDN-hosted versions

This can reduce the local asset size significantly.
