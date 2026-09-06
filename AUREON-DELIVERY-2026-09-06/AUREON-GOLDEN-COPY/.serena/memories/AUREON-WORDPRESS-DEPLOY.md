# AUREON-WORDPRESS-DEPLOY — Complete Current State

## Purpose
Production deployment package for AUREON + Ferm Living on WordPress (InfinityFree or any host).

## Location
`C:\Users\hamma\Downloads\phantom\wordpress\AUREON-WORDPRESS-DEPLOY\`

## Version
v6.1.0 (Complete-Page Mode) | Date: 2026-09-01 | Status: FERM_DEMO_PACK_RELEASE_READY

## Directory Structure

```
AUREON-WORDPRESS-DEPLOY/
├── frontend/                    ← FRONTEND ENGINE (upload to wp-content/)
│   ├── views/                   ← loader.php, design.php, renderer.php, viewmodel.php, registry.php, assets.php
│   ├── adapters/                ← Platform adapters
│   ├── components/              ← UI components (hero, cards, cart, checkout, etc.)
│   ├── designs/                 ← Design packs (ferm-living/)
│   ├── sections/                ← Section templates
│   ├── tokens/                  ← Design tokens
│   └── manifest/                ← Route manifests
├── aureon/                      ← WordPress Theme (upload to wp-content/themes/)
│   ├── inc/                     ← Theme includes (frontend.php, aether-*.php)
│   ├── ferm-page.php            ← Ferm Living complete-page template
│   ├── functions.php            ← Theme functions
│   ├── front-page.php           ← Front page template
│   ├── header.php / footer.php  ← Shell templates
│   ├── woocommerce/             ← WC template overrides (checkout/form-checkout.php, thankyou.php)
│   ├── myaccount/               ← my-account.php
│   ├── page-*.php               ← Static page templates (about, contact, faq, login, register, etc.)
│   └── assets/                  ← Theme CSS/JS/fonts
├── aureon-studio/               ← Aureon Studio Plugin (upload to wp-content/plugins/)
│   ├── aureon-studio.php        ← Main plugin file
│   ├── inc/                     ← Plugin includes
│   ├── elements/                ← Elementor elements
│   ├── hooks/                   ← Hook system
│   ├── blog/                    ← Blog module
│   ├── colors/                  ← Color module
│   ├── backgrounds/             ← Background module
│   ├── copyright/               ← Copyright module
│   ├── disable-elements/        ← Disable elements module
│   ├── font-library/            ← Font library
│   ├── general/                 ← General utilities
│   ├── library/                 ← Customizer library
│   ├── langs/                   ← Translations (ar, bn_BD, cs_CZ, da_DK, de_DE, es_ES, fi, fr_FR, hr, hu_HU, it_IT, nb_NO, nl_NL, pl_PL, pt_BR, pt_PT, ru_RU, sv_SE, uk, vi, zh_CN)
│   ├── menu-plus/               ← Menu Plus module
│   ├── page-header/             ← Page header module
│   ├── secondary-nav/           ← Secondary navigation
│   ├── woocommerce/             ← WooCommerce integration
│   └── dist/                    ← Compiled assets
├── mu-plugins/                  ← Must-Use Plugin (upload to wp-content/mu-plugins/)
│   └── aureon-fix-wc-session.php ← WC session early init
├── aureon.zip                   ← Zipped theme for WP upload
├── aureon-studio.zip            ← Zipped plugin for WP upload
├── frontend.zip                 ← Zipped frontend engine
├── HOW-TO-INSTALL.txt           ← Installation guide
├── DEMO-MODES.md                ← Demo mode documentation
├── INFINITYFREE-DEPLOYMENT-FIX.md ← InfinityFree-specific fixes
└── transition-test.js           ← Transition test script
```

## Critical Deployment Rules

1. `frontend/` goes to `wp-content/frontend/` NOT inside theme
2. `aureon/` goes to `wp-content/themes/aureon/`
3. `aureon-studio/` goes to `wp-content/plugins/aureon-studio/`
4. `mu-plugins/aureon-fix-wc-session.php` goes to `wp-content/mu-plugins/`
5. Design pack activation: `aether_active_design` = `ferm-living`

## Deployment Verification Checklist

```
✅ wp-content/frontend/views/loader.php exists
✅ wp-content/frontend/designs/fermliving/ exists
✅ wp-content/themes/aureon/functions.php exists
✅ wp-content/themes/aureon/ferm-page.php exists
✅ wp-content/aureon-studio/aureon-studio.php exists
✅ wp-content/mu-plugins/aureon-fix-wc-session.php exists
```

## Release Gate (ALL PASSING)

```
FERM_DEMO_DATASET_PASS                    ✅
FERM_COMPLETE_PAGE_RUNTIME_REPAIR_PASS     ✅
FERM_DEMO_REAL_CLIENT_TRANSITION_PASS      ✅ 34/34
FERM_CUSTOMIZER_TRANSITION_FINAL_PASS      ✅ 34/34
FERM_REMOTE_FALLBACK_PASS                  ✅ (included in 34/34)
FERM_INFINITYFREE_HOSTING_PASS             ✅
              ↓
FERM_DEMO_PACK_RELEASE_READY               ✅
```

## Demo System

- 66 curated products across 9 categories
- 4 collections (furniture, lighting, accessories, sale)
- 510 verified CDN image references
- Zero local image downloads
- Demo ↔ Real transition: automatic when real WC products exist
- Modes: auto (default), force_demo, disabled

## Key Technical Details

- WC AJAX returns prices in **cents** (e.g., 2499 for $24.99)
- Cart bridge expects `$_POST['updates']` as JSON `{"cart_item_key": quantity}`
- jQuery bridge saves/restores WP jQuery, copies Bootstrap plugins
- Path bridge rewrites flat-file links to WP permalinks
- Frozen HTML served verbatim (complete-page mode)

## Known Non-Blocking Issues

- Category images: derived from first curated product
- Hero/editorial images: use product images as placeholders
- Logo: text fallback only
- FORCE_DEMO/DISABLED modes: documented, not runtime-tested
- Hero customizer: always shows demo content
- Textdomain notice: non-fatal PHP notice

## Support

- GitHub: https://github.com/HAmmadsiamil007/aureon
