# 29 — HOOK / FILTER / ACTION CATALOG

## Theme Hooks

### Actions
| Hook | Priority | File | Purpose |
|------|----------|------|---------|
| after_setup_theme | 10 | functions.php | Theme setup |
| after_setup_theme | 20 | frontend.php | Register nav menus |
| after_setup_theme | 30 | frontend.php | Boot frontend engine |
| wp_enqueue_scripts | 20 | frontend.php | Enqueue AETHER assets |
| wp_enqueue_scripts | 20 | assets.php | Enqueue design pack assets |
| wp_enqueue_scripts | 98 | aether-tokens.php | Enqueue CSS tokens |
| wp_enqueue_scripts | 999 | composer.php | Disable Google Fonts (Ferm) |
| wp_enqueue_scripts | 1000 | frontend.php | Suppress theme output |
| wp_head | 1 | frontend.php | Favicons |
| wp_head | 1 | aether-performance.php | Resource hints |
| wp_head | 2 | aether-performance.php | Preload assets |
| wp_head | 5 | composer.php | Inject FermPageData (collections) |
| wp_body_open | 1 | aether-security.php | CSP nonce script |
| send_headers | 1 | aether-security.php | Security headers |
| send_headers | 2 | aether-security.php | HSTS header |
| init | 1 | aether-security.php | Remove X-Powered-By |
| template_redirect | — | aether-performance.php | Start output buffer |
| wp | — | aether-performance.php | Optimize WooCommerce |

### Filters
| Hook | Priority | File | Purpose |
|------|----------|------|---------|
| template_include | 99 | frontend.php | WC page templates |
| template_include | 998 | frontend.php | Complete-page routing |
| aureon_option_defaults | 10 | tokens.php | Engine defaults |
| aureon_option_defaults | 20 | design.php | Pack defaults |
| aether_frontpage_sections | — | front-page.php | Homepage sections |
| aether_component_data | — | renderer.php | Component data |
| aether_section_data | — | renderer.php | Section data |
| aether_component_manifest | — | renderer.php | Component manifest |
| body_class | — | design.php | Design body class |
| woocommerce_add_to_cart_fragments | — | aether-cart.php | Cart fragments |
| script_loader_tag | 10 | aether-security.php | CSP nonce on scripts |
| style_loader_src | 10 | aether-performance.php | Remove query strings |
| script_loader_src | 10 | aether-performance.php | Remove query strings |
| get_terms | 10 | composer.php | Filter demo categories |

## Ferm Composer Hooks

### Actions
| Hook | Purpose |
|------|---------|
| wp_ajax_ferm_cart_add | Cart add |
| wp_ajax_ferm_cart_update | Cart update |
| wp_ajax_ferm_cart_get | Cart get |
| wp_ajax_nopriv_ferm_cart_* | Guest cart access |
| woocommerce_product_query | Filter demo products |

### Filters
| Hook | Purpose |
|------|---------|
| aether_adapter_site_data | Site data |
| aether_adapter_header_data | Header data |
| aether_adapter_footer_data | Footer data |
| aether_adapter_wc_products_data | Product data |
| aether_adapter_product_data | Single product data |
| aether_adapter_wc_categories_data | Category data |
| aether_adapter_wc_filter_data | Filter data |
| aether_adapter_blog_data | Blog data |
| aether_adapter_about_data | About data |
| aether_adapter_contact_data | Contact data |
| aether_adapter_search_data | Search data |
| aether_adapter_newsletter_data | Newsletter data |
| aether_demo_products | Demo products |
| aether_demo_categories | Demo categories |
