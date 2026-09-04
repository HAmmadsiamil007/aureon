# Ferm Demo Pack — After Cleanup Report

**Date:** 2026-08-31

## Before vs After

| Metric | Before | After | Reduction |
|--------|--------|-------|-----------|
| Total size | 2.9 GB | 85 MB | **97%** |
| Total files | 17,477 | 364 | **98%** |
| Local images | 17,399 | 297 | **98%** |
| Image size | 2.9 GB | 78 MB | **97%** |

## What Was Removed
- 17,102 unreferenced images from `cdn/shop/files/`
- 6 files from `_cdn.assets.struct.com/` (product spec sheets — unreferenced)
- 3 PDFs (unreferenced)
- 2 videos (unreferenced)

## What Was Kept
- 297 referenced local images (frozen HTML required)
- 15 HTML files
- 5 CSS files
- 9 JS files
- 10 JSON files (demo data + manifest)
- 10 font files
- 2 SVG files

## Remaining Pack Structure
```
fermliving/
├── *.html (15 files) — frozen frontend
├── composer.php — theme integration
├── manifest.json — design manifest
├── tokens.php — design tokens
├── demo/ — demo data (JSON only, no local images)
│   ├── demo-manifest.json
│   ├── demo-products.json
│   ├── demo-categories.json
│   ├── demo-collections.json
│   ├── demo-homepage.json
│   ├── demo-navigation.json
│   ├── demo-assets.json
│   └── test-runtime.php
├── cdn/
│   └── shop/
│       ├── files/ (297 referenced images)
│       └── t/164/assets/ (CSS/JS bridges)
├── data/ — client data
├── pages/ — page HTML
├── products/ — product page HTML
├── collections/ — collection page HTML
├── blogs/ — blog HTML
└── account/ — account HTML
```

## Verification
- ✅ All referenced images present
- ✅ No broken local references
- ✅ HTML integrity preserved
- ✅ JSON integrity preserved
- ✅ Demo data preserved
- ✅ Golden Core untouched
- ✅ Frontend presentation unchanged

## Key Result
The pack no longer contains a 6GB+ media library. All demo images are now remote URLs in JSON manifests. Only the 297 images directly referenced by the frozen HTML are kept locally.
