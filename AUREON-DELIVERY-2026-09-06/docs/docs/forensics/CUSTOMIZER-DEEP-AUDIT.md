# Customizer Deep Audit

This document maps every Customizer control discovered in code to its corresponding storage mechanism, reader function, default values, bridge adapters, and current operational status within the AETHER frontend.

| CONTROL | ID | STORAGE | READER | DEFAULT | BRIDGE (adapter) | DOM TARGET | CSS VAR | JS CONSUMER | RESET | FALLBACK | STATUS |
|---------|----|---------|--------|---------|------------------|------------|---------|-------------|-------|----------|--------|
| Logo | `custom_logo` | `get_theme_mod` | `get_theme_mod('custom_logo')` | `''` | `aether_adapter_site()` | Header Logo | N/A | N/A | None | N/A | WORKING |
| Favicon | `site_icon` | `get_option` | `get_option('site_icon')` | `''` | Core `wp_head` | `<link rel="icon">` | N/A | N/A | None | N/A | WORKING |
| Site Title | `blogname` | `get_option` | `get_bloginfo('name')` | `''` | `aether_adapter_site()`, `aether_adapter_header()`, `aether_adapter_mobile()` | Title tags / Brand labels | N/A | N/A | None | N/A | WORKING |
| Tagline | `blogdescription` | `get_option` | `get_bloginfo('description')` | `''` | `aether_adapter_site()` | Subtitles | N/A | N/A | None | N/A | WORKING |
| Announcement Bar | `aether_announcement_items` / `aether_announcement_text` | `aureon_get_option` | `aureon_get_option('aether_announcement_items')` | Array in `tokens.php` | `aether_adapter_announcement()`, `aether_adapter_mobile()` | Announcement Component | N/A | N/A | None | N/A | WORKING |
| Hero / Slides | `aether_hero_slides` | `aureon_get_option` | `aureon_get_option('aether_hero_slides')` | Array in `tokens.php` | `aether_adapter_hero()` | Hero Slider | N/A | Slider JS | None | N/A | WORKING |
| Colors (Bg/Surface/Text) | `aether_color_*` | `aureon_get_option` | `aureon_get_option()` | Hex codes in `tokens.php` | `aether_frontend_color_defaults` | CSS `:root` variables | `--aether-color-*` | N/A | None | N/A | WORKING_BUT_UNVERIFIED |
| WooCommerce Primary Color | `wc_primary` (implied from CSS) | `aureon_get_option` | `aether_sanitize_color()` | Hex codes | `aether-tokens.php` bridge | CSS `:root` variables | `--aether-wc-primary` | N/A | None | N/A | WORKING |
| Typography (Heading) | `aether_font_heading` | `aureon_get_option` | `aureon_get_option()` | `Cabinet Grotesk` | `aether_frontend_font_defaults` | CSS `:root` variables | `--aether-font-heading` | N/A | None | N/A | WORKING_BUT_UNVERIFIED |
| Typography (Body) | `aether_font_body` | `aureon_get_option` | `aureon_get_option()` | `Satoshi` | `aether_frontend_font_defaults` | CSS `:root` variables | `--aether-font-body` | N/A | None | N/A | WORKING_BUT_UNVERIFIED |
| Layout Tokens | `aether_container_max`, `aether_section_padding`, etc. | `aureon_get_option` | `aureon_get_option()` | Defined in `tokens.php` | Dynamic CSS Generator | CSS Variables | `--aether-container-max` | N/A | None | N/A | WORKING_BUT_UNVERIFIED |
| Header / Menu | `primary` menu location | `get_nav_menu_locations` | `wp_get_nav_menu_items()` | N/A | `aether_adapter_menu()`, `aether_adapter_header()` | Header Navigation | N/A | N/A | None | Fallback Menu | WORKING |
| Footer Columns | `aether_footer_columns` | `aureon_get_option` | `aureon_get_option('aether_footer_columns')` | Array in `tokens.php` | `aether_adapter_footer()` | Footer Links | N/A | N/A | None | Default Cols | WORKING |
| Social Links | `aether_social_items` | `aureon_get_option` | Not read | Array in `tokens.php` | N/A | N/A | N/A | N/A | None | N/A | STORED_NOT_CONSUMED |
| Newsletter Heading/Text | `aether_newsletter_text` | `aureon_get_option` | Not read by `adapter-site.php` | Defined in `tokens.php` | N/A | N/A | N/A | N/A | None | N/A | STORED_NOT_CONSUMED |
| WooCommerce Display | `aether_shop_per_page`, `aether_product_sizes`, etc. | `aureon_get_option` | `aureon_get_option()` | Arrays/ints in `tokens.php` | `adapter-product.php` | Product Cards/Pages | N/A | N/A | None | N/A | WORKING |

## Notes on Findings:

1. **Social Links (`aether_social_items`)**: The value is likely configured and stored in `aureon_get_option` but `aether_adapter_socials()` in `adapter-menu.php` returns a hardcoded array. Thus, custom values are stored but not consumed.
2. **Newsletter Texts**: `tokens.php` registers options like `aether_newsletter_text` and `aether_newsletter_subtitle`, however, `adapter-site.php` hardcodes the newsletter heading and text in `aether_adapter_footer()`.
3. **Colors & Typography**: Registered in `tokens.php` and mapped via `aether_frontend_color_defaults` and `aether_frontend_font_defaults`, but functionality requires verifying the actual CSS output generator for `:root` variables. Status marked as `WORKING_BUT_UNVERIFIED`.
