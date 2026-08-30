# Phase 17.1 — Stage 2 (Shell) COMPLETE & VERIFIED LIVE

## Deployed & verified on container (aureon_wp @ localhost:8080)

### New engine files (Stage 2)
- `frontend/views/loader.php` — engine bootstrap: defines `AETHER_FRONTEND_DIR` (dirname of views/ = wp-content/frontend/), `aether_frontend_boot()` requires tokens.php + registry + renderer + viewmodel + composer + glob adapters/* + sections/*
- `frontend/views/registry.php` — `aether_section_registry()` + `aether_register_section()` using **global $aether_section_registry** (static-local copy bug avoided)
- `frontend/views/composer.php` — `aether_compose_header()` (preloader→fog→skip-link→.page-content→mobile-chrome→announcement→header→#main skip target→`<main id="swup">`) + `aether_compose_footer()` (close main→footer→close page-content). Shell toggles read `aureon_get_option('aether_preloader_enabled')` etc.
- `frontend/adapters/adapter-shell.php` — `aether_adapter_announcement()` (3 items w/ icons + separator spans), `aether_adapter_header()` (brand/menu/icons/cart_count via WC), `aether_adapter_mobile()` (announcement 2 msgs, 3 nav groups, CTA, socials)
- `frontend/adapters/adapter-menu.php` — `aether_adapter_menu($location)` (real WP menu → tree, fallback curated), `aether_build_menu_tree()`, `aether_menu_item_is_active()`, `aether_fallback_menu()`, `aether_adapter_socials()` (instagram/twitter/tiktok/youtube)
- `frontend/adapters/adapter-site.php` — REWRITTEN: `aether_adapter_site()` (name/brand/tagline/logo/url — brand key needed by preloader) + rich `aether_adapter_footer()` (brand, socials, 3 link columns Shop/Support/Company, newsletter, copyright `date_i18n('Y')`, 3 legal links, 5 payment icons)
- `frontend/components/shell/` — 7 components, source-faithful: preloader (brand from data, no WP calls), fog (3 layers), skip-link, announcement (separator spans BETWEEN items), mobile-chrome (exact source structure), header (nav-links + nav-dropdown w/ children + nav-mobile-icons + header-actions, cart-count badge), footer (footer-top/brand/links/newsletter + footer-bottom/legal/payments)
- `frontend/assets/` — COPY from source: css/style+motion+responsive+a11y.css, js/lenis-scroll+animations+main+phantom-bridge.js, images/fog/{fog1,fog2}.png, images/favicon/{favicon.ico,favicon-16x16,favicon-32x32,apple-icon-180x180}
- `frontend/manifest/components.php` — ADDED `shell/header` + `shell/footer` keys (were missing → silent no-op)

### Theme-side wiring
- `aureon/theme/functions.php` — 12 dead `aether-*.php` requires REPLACED with single `require inc/frontend.php` (bootstrap blocker FIXED)
- `aureon/theme/inc/frontend.php` (NEW) — requires `get_template_directory() . '/../../frontend/views/loader.php'` (TWO levels up = wp-content/frontend; one level = themes/frontend WRONG); registers nav locations primary+footer (after_setup_theme 20); boots engine (30); suppresses theme output at wp_enqueue_scripts 1000 (dequeue/deregister 11 styles + 5 scripts incl. theme FA 4.7); removes wp_footer hookups — **exact names: `aureon_do_a11y_scripts` (NOT aureon_a11y_scripts), `aureon_do_search_modal` (NOT aureon_construct_search_modal), `aureon_clone_sidebar_navigation`**; also removes aureon_after_header featured_page_header; enqueues at 20 (CDN: bootstrap 5.3.3, FA 6.5.1, swiper@11, gsap 3.12.5, ScrollTrigger, lenis 1.1.18, Google Fonts Cabinet Grotesk+Satoshi; local via content_url()/frontend/assets, filemtime cache-busting, dep chain lenis→lenis-scroll, bootstrap+gsap→animations→main→phantom-bridge); favicons via wp_head 1 (theme-color #09090B)
- `header.php` REWRITTEN — doctype/head/wp_head/body + wp_body_open + `aether_compose_header()` (theme's aureon_before_header/aureon_header/do_attr('page') wrappers GONE)
- `footer.php` REWRITTEN — `aether_compose_footer()` + wp_footer + close
- `index.php` REWRITTEN — loop only, no `aureon_do_attr('content')/('main')` wrappers, no `aureon_construct_sidebars()` (composer owns main#swup)

### Verified live (Playwright + raw HTML)
- 200 OK; AETHER CSS order style→motion→responsive→a11y; palette applied (body rgb(9,9,11), header rgba(9,9,11,.92) fixed, footer rgb(9,9,11), Satoshi)
- One each: preloader (renders, then main.js:528-538 removes after load anim — source contract), fog-system, skip-link, page-content, announcementBar, mobileHeader+overlay, header, mainNav, #main, main#swup, footer
- Zero duplicates of header/footer/preloader; 0 theme styles/scripts in output (main.min.css, menu.min.js, FA4.7, back-to-top, aureon-a11y, search-modal all GONE)
- WC JS kept (jquery/blockUI/add-to-cart/woocommerce.js — needed for data layer)
- Old front-page.php content renders inside main#swup (hero-slider 3 slides, categories, bestsellers 4, reviews, faq) — old inline Phase-17 markup still there, Stage 3 will replace; old `assets/aether` image paths 404 silently (no fatal)

## Gotchas learned
- **Deploy paths**: theme+plugin are bind-mounted (C:\...\wordpress\aureon\theme → /var/www/html/wp-content/themes/aureon), so theme edits are LIVE instantly. frontend/ lives in docker volume → deploy via tar to **/var/www/html/wp-content** (NOT /var/www/html root — first deploy went to container root, fatal "Failed to open stream")
- Enqueue URLs via `content_url()` not template uri + `../` (ugly relative URLs)
- tar SCHILY.fflags warnings = read-only source flags, harmless
- Playwright browser caches 301s indefinitely — after deploys, clear cache or use ?nocache= query

## Next: Stage 3 (Home) — replace front-page.php inline sections with section components (hero/slider, categories, bestsellers, reviews, faq, newsletter) using manifest sections + section-hero.php pattern; assets/images (hero jpeg) need copying from source; old aether assets/aether/ references die then.
