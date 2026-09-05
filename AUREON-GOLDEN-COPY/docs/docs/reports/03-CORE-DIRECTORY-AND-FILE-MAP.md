# 03 — CORE DIRECTORY AND FILE MAP

## Golden Core Structure

```
aureon/
├── frontend/                    # CORE ENGINE
│   ├── views/                   # Engine kernel
│   │   ├── loader.php           # Engine boot
│   │   ├── design.php           # Design resolver
│   │   ├── assets.php           # Asset pipeline
│   │   ├── composer.php         # Shell composition
│   │   ├── renderer.php         # Component/section rendering
│   │   ├── registry.php         # Section registry
│   │   └── viewmodel.php        # Data normalization
│   ├── adapters/                # WP/WC data access (23 files)
│   │   ├── adapter-product.php
│   │   ├── adapter-cart.php
│   │   ├── adapter-menu.php
│   │   ├── adapter-site.php
│   │   ├── adapter-hero.php
│   │   ├── adapter-wc-products.php
│   │   ├── adapter-wc-categories.php
│   │   ├── adapter-wc-filter.php
│   │   ├── adapter-account.php
│   │   ├── adapter-auth.php
│   │   ├── adapter-blog.php
│   │   ├── adapter-article.php
│   │   ├── adapter-about.php
│   │   ├── adapter-contact.php
│   │   ├── adapter-faq.php
│   │   ├── adapter-order.php
│   │   ├── adapter-team.php
│   │   ├── adapter-testimonials.php
│   │   ├── adapter-wishlist.php
│   │   ├── adapter-coming-soon.php
│   │   ├── adapter-shop-hero.php
│   │   ├── adapter-options.php
│   │   └── adapter-shell.php
│   ├── components/              # Presentation components
│   │   ├── shell/               # Site chrome (7 files)
│   │   ├── hero/                # Hero system (4 files)
│   │   ├── section/             # Section components (6 files)
│   │   ├── cards/               # Card components (6 files)
│   │   ├── product/             # Product components (8 files)
│   │   ├── cart/                # Cart components (2 files)
│   │   ├── checkout/            # Checkout components (1 file)
│   │   ├── account/             # Account components (2 files)
│   │   ├── auth/                # Auth components (1 file)
│   │   ├── order/               # Order components (1 file)
│   │   ├── commerce/            # Commerce components (2 files)
│   │   ├── content/             # Content components (6 files)
│   │   ├── forms/               # Form components (5 files)
│   │   └── utility/             # Utility components (3 files)
│   ├── sections/                # Page section compositions (25 files)
│   ├── manifest/                # Component manifest
│   │   └── components.php
│   ├── tokens/                  # Design tokens
│   │   └── tokens.php
│   ├── designs/                 # Client design packs
│   │   ├── fermliving/          # Active complete-page design
│   │   ├── lumen/               # Component-mode design
│   │   └── testclient/          # Isolation test design
│   └── boost-products.php       # Product boost utility
├── theme/                       # WordPress theme layer
│   ├── functions.php            # Theme bootstrap
│   ├── header.php               # Document open + shell
│   ├── footer.php               # Shell close + document close
│   ├── front-page.php           # Homepage template
│   ├── page.php                 # Static page template
│   ├── single.php               # Single post template
│   ├── single-product.php       # Single product template
│   ├── index.php                # Fallback template
│   ├── cart.php                 # Cart template
│   ├── 404.php                  # 404 template
│   ├── search.php               # Search results template
│   ├── ferm-page.php            # Complete-page host template
│   ├── inc/                     # Theme includes
│   │   ├── frontend.php         # Frontend engine integration
│   │   ├── aether-tokens.php    # CSS custom properties
│   │   ├── aether-security.php  # Security headers
│   │   ├── aether-ajax.php      # AJAX handlers
│   │   ├── aether-cart.php      # Cart fragment
│   │   ├── aether-performance.php # Performance optimizations
│   │   ├── aether-analytics.php # Analytics
│   │   ├── aether-newsletter.php # Newsletter
│   │   ├── aether-seo.php       # SEO
│   │   ├── customizer.php       # Customizer registration
│   │   ├── defaults.php         # Theme defaults
│   │   └── ... (other includes)
│   └── woocommerce/             # WC template overrides
└── plugin/                      # Plugin layer (minimal)
```

## File Classification

| Category | Files | Safe to Modify? |
|----------|-------|-----------------|
| **CORE** | views/*.php, loader.php, design.php | ⚠️ Review required |
| **PLATFORM** | theme/inc/*.php, tokens.php | ⚠️ Review required |
| **BUSINESS** | adapters/adapter-wc-*.php, adapter-cart.php | ⚠️ Review required |
| **PRESENTATION** | components/**/*.php, sections/section-*.php | ✅ Client pack |
| **BRIDGE** | designs/*/composer.php, ferm-page.php | ⚠️ Review required |
| **CONFIG** | manifest.json, tokens.php | ✅ Pack-specific |
| **SECURITY** | aether-security.php | ❌ High risk |
| **LEGACY** | deprecated.php, fermliving-legacy-integration/ | ⚠️ Do not modify |

## Design Pack Structure

```
aureon/frontend/designs/<slug>/
├── manifest.json          # Pack descriptor
├── tokens.php             # Pack defaults
├── composer.php           # Data bridge
├── index.html             # Homepage
├── product.html           # Product page
├── cart.html              # Cart page
├── account.html           # Account page
├── blog.html              # Blog page
├── collection.html        # Collection page
├── page.html              # Static page
├── checkout.html          # Checkout page
├── css/                   # Pack stylesheets
├── js/                    # Pack scripts
├── cdn/                   # Pack assets (images, fonts)
├── data/                  # Demo data JSON
├── mapper/                # Data remapping
├── sections/              # Pack-specific sections
└── components/            # Pack component overrides
```
