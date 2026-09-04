# 30 — DEPENDENCY MATRIX

## Core Engine Dependencies

| File | Depends On | Used By | Risk |
|------|-----------|---------|------|
| views/loader.php | — | theme/functions.php | HIGH |
| views/design.php | loader.php | assets.php, ferm-page.php | HIGH |
| views/assets.php | design.php | frontend.php | HIGH |
| views/composer.php | design.php | header.php, footer.php | MEDIUM |
| views/renderer.php | design.php, manifest/components.php | sections, templates | HIGH |
| views/registry.php | — | renderer.php | MEDIUM |
| views/viewmodel.php | — | adapters, components | LOW |
| ferm-page.php | design.php | template_include (priority 998) | HIGH |

## Adapter Dependencies

| Adapter | Depends On | Consumers |
|---------|-----------|-----------|
| adapter-product.php | WC product | product section, FermPageData |
| adapter-cart.php | WC cart | cart section, FermPageData |
| adapter-menu.php | WP nav menus | shell/header, FermPageData |
| adapter-site.php | bloginfo, custom_logo | shell components |
| adapter-hero.php | Customizer slides | hero section |
| adapter-wc-products.php | WP_Query + WC | shop grid, bestsellers |
| adapter-wc-categories.php | get_terms | categories section |
| adapter-wc-filter.php | Customizer | filter bar |
| adapter-account.php | WC customer | account section |
| adapter-auth.php | WP auth | login form |
| adapter-blog.php | WP posts | blog grid |
| adapter-article.php | WP post | article content |

## Theme Dependencies

| File | Depends On | Risk |
|------|-----------|------|
| functions.php | All inc/ files | HIGH |
| inc/frontend.php | views/loader.php | HIGH |
| inc/aether-tokens.php | aureon_get_option | MEDIUM |
| inc/aether-security.php | — | HIGH |
| inc/aether-ajax.php | WC, nonce | MEDIUM |
| inc/aether-cart.php | WC cart | MEDIUM |
| inc/aether-performance.php | — | LOW |

## High-Risk Files

| File | Reason |
|------|--------|
| views/design.php | Design resolution affects everything |
| views/loader.php | Engine boot affects everything |
| ferm-page.php | Complete-page routing |
| inc/frontend.php | Asset suppression, template routing |
| views/assets.php | Asset loading pipeline |
