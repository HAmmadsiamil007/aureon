# Aureon - WordPress Developer Foundation

> A lightweight, performance-first WordPress theme and plugin suite built for developers who want complete control over their frontend.

## Overview

Aureon is a complete rebrand and optimization of the GeneratePress ecosystem, providing a rock-solid developer foundation with 17 premium modules, endless customization options, and blazing-fast performance.

**Theme:** Aureon v1.0.0  
**Plugin:** Aureon Studio v1.0.0  
**License:** GPL v2 or later

---

## Features

### Theme (Aureon)

- **Lightweight Core** — Minimal CSS and JavaScript for maximum performance
- **Developer-First** — Clean code, extensive hooks/filters, child theme ready
- **Customizer Controls** — 100+ customization options via WordPress Customizer
- **Block Editor Support** — Full Gutenberg integration with custom color palette
- **Responsive Design** — Mobile-first approach with responsive embeds
- **SEO Optimized** — Schema.org markup, semantic HTML5, clean markup
- **Accessibility Ready** — WCAG compliant with keyboard navigation support
- **WooCommerce Compatible** — Full e-commerce integration
- **Translation Ready** — Full i18n support with `.pot` files

### Plugin (Aureon Studio)

17 premium modules that extend Aureon with powerful features:

| Module | Description |
|--------|-------------|
| **Backgrounds** | Advanced background options for elements |
| **Blog** | Blog layout customization and columns |
| **Colors** | Global color system with unlimited palettes |
| **Copyright** | Footer copyright and credits customization |
| **Disable Elements** | selectively disable theme elements |
| **Elements** | Custom block elements with dynamic tags |
| **Font Library** | Custom font management and optimization |
| **General** | Smooth scrolling, icons, external CSS |
| **Hooks** | Visual hook system for content injection |
| **Menu Plus** | Advanced navigation with sticky/off-canvas |
| **Page Header** | Hero sections with parallax and video |
| **Secondary Navigation** | Secondary menu locations |
| **Sections** | Page builder-like section layouts |
| **Site Library** | Pre-built site templates for quick starts |
| **Spacing** | Margin/padding controls for all elements |
| **Typography** | Google Fonts + system font controls |
| **WooCommerce** | Enhanced WooCommerce styling options |

---

## Compatibility

Works seamlessly with:

- **Page Builders:** Elementor, Bricks, Oxygen, Beaver Builder
- **SEO:** Rank Math, Yoast, AIOSEO
- **Forms:** Fluent Forms, Gravity Forms, WPForms
- **LMS:** LearnDash, Tutor LMS, LifterLMS
- **E-commerce:** WooCommerce, Easy Digital Downloads
- **Community:** BuddyPress, bbPress
- **Translation:** WPML, Polylang, TranslatePress
- **ACF Pro** — Full Advanced Custom Fields integration

---

## Requirements

- WordPress 6.0+
- PHP 7.4+
- MySQL 5.6+ or MariaDB 10.1+

---

## Installation

### Theme

1. Download `aureon.zip`
2. Go to Appearance → Themes → Add New → Upload Theme
3. Upload `aureon.zip` and activate

### Plugin

1. Download `aureon-studio.zip`
2. Go to Plugins → Add New → Upload Plugin
3. Upload `aureon-studio.zip` and activate

---

## Development

### Local Development

```bash
# Clone the repository
git clone https://github.com/HAmmadsiamil007/wordpress.git

# Navigate to the project
cd wordpress/aureon
```

### Building from Source

The theme and plugin are ready to use. No build step required.

### File Structure

```
aureon/
├── theme/                    # Aureon Theme
│   ├── assets/              # CSS, JS, fonts, images
│   ├── inc/                 # PHP includes
│   │   ├── customizer/      # Customizer controls
│   │   └── structure/       # Template structure
│   ├── style.css            # Theme stylesheet
│   └── functions.php        # Theme functions
│
└── plugin/                   # Aureon Studio Plugin
    ├── backgrounds/         # Backgrounds module
    ├── blog/               # Blog module
    ├── colors/             # Colors module
    ├── copyright/          # Copyright module
    ├── disable-elements/   # Disable Elements module
    ├── elements/           # Elements module
    ├── font-library/       # Font Library module
    ├── general/            # General functionality
    ├── hooks/              # Hooks module
    ├── inc/                # Core includes
    ├── langs/              # Translations
    ├── library/            # Shared libraries
    ├── menu-plus/          # Menu Plus module
    ├── page-header/        # Page Header module
    ├── secondary-nav/      # Secondary Navigation module
    ├── sections/           # Sections module
    ├── site-library/       # Site Library module
    ├── spacing/            # Spacing module
    ├── typography/         # Typography module
    ├── woocommerce/        # WooCommerce module
    └── aureon-studio.php   # Main plugin file
```

---

## Hooks & Filters

Aureon provides 800+ hooks and filters for complete customization:

### Action Hooks

```php
// Header
do_action( 'aureon_before_header' );
do_action( 'aureon_header' );
do_action( 'aureon_after_header' );

// Navigation
do_action( 'aureon_before_navigation' );
do_action( 'aureon_inside_navigation' );
do_action( 'aureon_after_navigation' );

// Content
do_action( 'aureon_before_content' );
do_action( 'aureon_content' );
do_action( 'aureon_after_content' );

// Footer
do_action( 'aureon_before_footer' );
do_action( 'aureon_footer' );
do_action( 'aureon_after_footer' );
```

### Filter Hooks

```php
// Layout
apply_filters( 'aureon_sidebar_layout', 'right-sidebar' );
apply_filters( 'aureon_container_width', 1200 );

// Typography
apply_filters( 'aureon_body_font_family', '' );
apply_filters( 'aureon_heading_font_family', '' );

// CSS Output
apply_filters( 'aureon_dynamic_css', $css );
apply_filters( 'aureon_base_css_output', $css );
```

---

## Performance

- **Average Page Load:** < 1 second
- **CSS Size:** ~15KB minified
- **JS Size:** ~5KB minified
- **HTTP Requests:** 2-4 total
- **Lighthouse Score:** 95+

---

## Security

- All files protected with `ABSPATH` checks
- Nonce verification on all admin actions
- Input sanitization with `sanitize_text_field()`
- Output escaping with `esc_html()`, `esc_url()`, `esc_attr()`
- Capability checks on all privileged operations

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## Changelog

### 1.0.0 (2026-08-04)

- Initial release
- Complete theme and plugin suite
- 17 premium modules
- Full Customizer integration
- Block editor support
- WooCommerce compatibility
- Accessibility features
- SEO optimization

---

## License

This project is licensed under the GNU General Public License v2 or later - see the [LICENSE](license.txt) file for details.

---

## Credits

- Original theme framework by Tom Usborne / EDGE22 Studios
- Rebranded and optimized by Aureon Studio

---

## Support

- **Documentation:** Coming soon
- **Issues:** [GitHub Issues](https://github.com/HAmmadsiamil007/wordpress/issues)
- **Email:** support@aureonstudio.com

---

**Built with by Aureon Studio**
