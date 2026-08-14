# FRONTEND.md — Frontend Integration Framework: Complete Implementation Guide

**Aureon + Aureon Studio — Phase 17 Frontend Integration Framework**
**Version:** 1.0 (2026-08-06)
**Status:** Approved-for-implementation reference. Companion docs: `../frontend/*.md` (10 reports).

> **⚠️ 2026-08-15 — superseded for CURRENT state.** This document is the historical Phase 17 implementation guide. The engine has since moved to the **M6–M10 design-pack platform**. For current architecture use:
> - [`AETHER-BRIDGE.md`](./AETHER-BRIDGE.md) — how the frontend connects to the theme core + all feature bridges
> - [`FRONTEND-OPERATIONS.md`](./FRONTEND-OPERATIONS.md) — edit / replace / create dynamic frontends
> - [`STATUS.md`](./STATUS.md) §0 — M6–M10 platform status + verification evidence

---

## Table of Contents

1. [What This Is](#1-what-this-is)
2. [Architecture (read this first)](#2-architecture)
3. [Step 1 — Create the `frontend/` Folder Structure](#3-create-the-folder-structure)
4. [Step 2 — Add Files (assets, components, sections, adapters)](#4-add-files)
5. [Step 3 — Connect to the Core Theme (functions.php, enqueue, hooks)](#5-connect-to-core-theme)
6. [Step 4 — Connect to the Aureon Studio Plugin (WC + modules)](#6-connect-to-plugin)
7. [Step 5 — Design Tokens + Customizer (how to customize everything)](#7-customizer-tokens)
8. [Step 6 — Replace the Complete Frontend (template-by-template)](#8-replace-frontend)
9. [Step 7 — Animation Bridge + REST](#9-animations-rest)
10. [Step 8 — Performance & Accessibility](#10-performance-a11y)
11. [Step 9 — Visual Regression (prove it matches)](#11-visual-regression)
12. [Runbook: Common Tasks](#12-runbook)
13. [Troubleshooting](#13-troubleshooting)
14. [Checklist (final sign-off)](#14-checklist)

---

## 1. What This Is

This document is the **step-by-step operator's manual** for integrating the AETHER static frontend (a premium sneaker-store design: dark void `#09090B` + gold `#C8956C`) into the **Aureon theme** + **Aureon Studio plugin**, as a proper component framework — not a page-copy.

**One rule above all:** components NEVER call WordPress functions. Adapters fetch data; renderers escape and output. If you break this rule, you get the maintenance mess we rolled back.

---

## 2. Architecture

```
WordPress → WC → Aureon Modules → Adapters → ViewModels → Renderer → Components → HTML
                                        │
                            ┌───────────┴────────────┐
                            │ Data flow               │
                            │ (filter: aether_*_data) │
                            └─────────────────────────┘
```

| Layer | Folder | Rules |
|---|---|---|
| Adapters | `frontend/adapters/` | Only layer that calls `get_option()`, `WC()`, `WP_Query`, etc. Returns arrays. |
| ViewModels | `frontend/views/` | Normalizes adapter arrays (types, defaults, formatting). |
| Renderer | `frontend/views/` (renderer.php) | Single escape boundary: `esc_html/esc_attr/esc_url/wp_kses_post`. |
| Components | `frontend/components/` | Pure PHP templates. Input `$componentData`. Output markup + `data-*` behavior attributes. |
| Sections | `frontend/sections/` | Compositions of components per page region. |
| Layouts | `frontend/layouts/` | Page shells (header/footer wrappers). |
| Tokens | `frontend/tokens/` | Token definitions mapped to Customizer settings. |
| Assets | `frontend/assets/` | Curated CSS/JS/images/fonts (dead code excluded). |

---

## 3. Create the Folder Structure

### 3.1 Make the tree

Run this from the repo root (`C:\Users\hamma\Downloads\wordpress`):

```powershell
New-Item -ItemType Directory -Force -Path `
  frontend\assets\css, frontend\assets\js, frontend\assets\img, frontend\assets\fonts, frontend\assets\vendor, `
  frontend\components, frontend\sections, frontend\layouts, frontend\adapters, frontend\views, `
  frontend\tokens, frontend\manifest, frontend\regression\golden, frontend\regression\candidate, `
  frontend\regression\diff, frontend\docs
```

Result:

```
frontend/
├── assets/
│   ├── css/        frontend.css, motion.css, a11y.css, aether-wc.css
│   ├── js/         aether-core.js, aether-animations.js, aether-lenis.js,
│   │               aether-forms.js, aether-cart.js, aether-gallery.js, aether-auth.js
│   ├── img/         (curated from source/assets/images — only what's used)
│   ├── fonts/       CabinetGrotesk-*, Satoshi-* (local @font-face)
│   └── vendor/      gsap.min.js+ST, lenis.min.js, swiper-bundle.min.js,
│                    bootstrap.bundle.min.js, jquery.min.js
├── components/      (PHP partials)
├── sections/        (PHP partials)
├── layouts/         header.php/footer.php wrappers
├── adapters/        adapter-site.php, adapter-menu.php, adapter-wc.php …
├── views/           renderer.php, viewmodel.php
├── tokens/          tokens.php
├── manifest/        components.php, assets.php
├── regression/      golden/ candidate/ diff/ + run script
├── docs/            (Phase 17 reports)
└── source/          (static reference — NEVER edit)
```

### 3.2 Where the framework boots

`frontend/` is **not** a WordPress folder by itself. The theme includes it:

- `aureon/theme/inc/frontend.php` ← main bootstrap (we create it)
- `aureon/theme/inc/adapters/…` ← adapter requires
- `aureon/plugin/woocommerce/adapters/…` ← WC adapters (plugin-side)

---

## 4. Add Files

### 4.1 Manifest (single source of truth)

`frontend/manifest/components.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

return array(
	'shell/preloader'      => array( 'template' => 'components/shell/preloader.php' ),
	'shell/announcement'   => array( 'template' => 'components/shell/announcement.php' ),
	'shell/header'         => array( 'template' => 'components/shell/header.php' ),
	'shell/footer'         => array( 'template' => 'components/shell/footer.php' ),
	'hero/slider'          => array( 'template' => 'components/hero/slider.php' ),
	'section/header'       => array( 'template' => 'components/section/header.php' ),
	'card/product'         => array( 'template' => 'components/cards/product.php' ),
	'card/blog-card'       => array( 'template' => 'components/cards/blog.php' ),
	'form/newsletter'      => array( 'template' => 'components/forms/newsletter.php' ),
	// …extend as you extract (see COMPONENT_INVENTORY.md)
);
```

### 4.2 Component template anatomy (the contract)

`frontend/components/cards/product.php`:

```php
<?php
/**
 * Product card component.
 * Input:  $componentData (array) — from adapter (see 4.4)
 * Output: escaped HTML. NEVER call WC/WP functions here.
 *
 * @param array $componentData {
 *     @type int    $id
 *     @type string $name
 *     @type string $price      (formatted, e.g. "£145.00")
 *     @type string $url
 *     @type array  $image      { id, url, alt }
 *     @type string $badge      (optional "Sale")
 *     @type float  $rating     (optional)
 *     @type array  $behavior   (optional) tilt/reveal/zoom flags
 * }
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$defaults = array(
	'id'       => 0,
	'name'     => '',
	'price'    => '',
	'url'      => '',
	'image'    => array( 'url' => '', 'alt' => '' ),
	'badge'    => '',
	'rating'   => 0,
	'behavior' => array( 'tilt' => true, 'reveal' => true ),
);
$componentData = wp_parse_args( $componentData, $defaults );

$class  = 'product-card';
$attrs  = '';
if ( ! empty( $componentData['behavior']['tilt'] ) ) {
	$attrs .= ' data-tilt';
}
if ( ! empty( $componentData['behavior']['reveal'] ) ) {
	$attrs .= ' data-reveal-item';
}
?>

<article class="<?php echo esc_attr( $class ); ?>" <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute-whitelisted above ?>>
	<a class="product-card__media" href="<?php echo esc_url( $componentData['url'] ); ?>">
		<?php if ( ! empty( $componentData['image']['url'] ) ) : ?>
			<img src="<?php echo esc_url( $componentData['image']['url'] ); ?>"
				alt="<?php echo esc_attr( $componentData['image']['alt'] ); ?>" loading="lazy" />
		<?php endif; ?>
		<?php if ( ! empty( $componentData['badge'] ) ) : ?>
			<span class="product-card__badge"><?php echo esc_html( $componentData['badge'] ); ?></span>
		<?php endif; ?>
	</a>
	<h3 class="product-card__title">
		<a href="<?php echo esc_url( $componentData['url'] ); ?>"><?php echo esc_html( $componentData['name'] ); ?></a>
	</h3>
	<div class="product-card__price"><?php echo esc_html( $componentData['price'] ); ?></div>
</article>
```

### 4.3 Section template (composes components)

`frontend/sections/bestsellers.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// $sectionData provided by the section engine (see 5.4).
$header  = $sectionData['section_header'];
$products = $sectionData['products']; // already-adapter-ized array of $componentData[]
?>

<section class="bestsellers" id="bestsellers">
	<?php
	aether_render_component( 'section/header', $header );

	if ( ! empty( $products ) ) :
		?>
		<div class="products-swiper swiper" data-reveal-group>
			<div class="swiper-wrapper">
				<?php foreach ( $products as $product ) : ?>
					<div class="swiper-slide">
						<?php aether_render_component( 'card/product', $product ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="swiper-pagination"></div>
		</div>
	<?php endif; ?>
</section>
```

### 4.4 Adapter (the only layer allowed to touch WP)

`frontend/adapters/adapter-wc.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Build $componentData[] for product cards.
 * Used by: card/product, card/product-slider.
 */
function aether_adapter_wc_products( $context = array() ) {
	$defaults = array(
		'limit'   => 8,
		'cat'     => '',
		'orderby' => 'popularity',
	);
	$args = wp_parse_args( $context, $defaults );

	$data = array( 'products' => array() );

	if ( ! function_exists( 'wc_get_products' ) ) {
		return apply_filters( 'aether_wc_products_data', $data, $context );
	}

	$query_args = array(
		'limit'   => absint( $args['limit'] ),
		'status'  => 'publish',
		'orderby' => sanitize_key( $args['orderby'] ),
	);
	if ( $args['cat'] ) {
		$query_args['category'] = array( sanitize_title( $args['cat'] ) );
	}

	$products = wc_get_products( $query_args );

	foreach ( $products as $product ) {
		$image_id = $product->get_image_id();
		$image    = $image_id ? wp_get_attachment_image_src( $image_id, 'medium_large' ) : false;

		$data['products'][] = array(
			'id'      => $product->get_id(),
			'name'    => $product->get_name(),
			'price'   => wp_strip_all_tags( wc_price( (float) $product->get_price() ) ),
			'url'     => get_permalink( $product->get_id() ),
			'image'   => array(
				'url' => $image ? $image[0] : wc_placeholder_img_src(),
				'alt' => $product->get_name(),
			),
			'badge'   => $product->is_on_sale() ? __( 'Sale', 'aureon' ) : '',
			'rating'  => (float) $product->get_average_rating(),
		);
	}

	return apply_filters( 'aether_wc_products_data', $data, $context );
}
```

### 4.5 Renderer (single escape boundary)

`frontend/views/renderer.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_render_component( $id, $data = array() ) {
	$manifest = aether_component_manifest(); // from 4.1

	if ( ! isset( $manifest[ $id ] ) ) {
		return;
	}

	$template = AETHER_FRONTEND_DIR . $manifest[ $id ]['template'];

	if ( ! file_exists( $template ) ) {
		return;
	}

	$componentData = apply_filters( 'aether_component_data', $data, $id );

	include $template;
}

function aether_render_section( $id, $data = array() ) {
	// Same pattern against manifest sections + aether_section_data filter.
}
```

---

## 5. Connect to the Core Theme

### 5.1 Bootstrap file — `aureon/theme/inc/frontend.php`

Create this file and require it from `functions.php` **inside** `aureon_setup()`, after the other requires:

```php
// functions.php — inside aureon_setup(), after existing requires:
require $theme_dir . '/inc/frontend.php';
```

`inc/frontend.php` content:

```php
<?php
/**
 * Phase 17 Frontend Integration Framework bootstrap.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'AETHER_FRONTEND_DIR', get_template_directory() . '/../frontend/' );
define( 'AETHER_FRONTEND_URI', get_template_directory_uri() . '/../frontend/' );

require AETHER_FRONTEND_DIR . 'manifest/components.php';
require AETHER_FRONTEND_DIR . 'views/renderer.php';
require AETHER_FRONTEND_DIR . 'views/viewmodel.php';
require AETHER_FRONTEND_DIR . 'tokens/tokens.php';

foreach ( glob( AETHER_FRONTEND_DIR . 'adapters/*.php' ) as $adapter ) {
	require $adapter;
}

foreach ( glob( AETHER_FRONTEND_DIR . 'sections/*.php' ) as $section ) {
	require $section;
}

add_action( 'wp_enqueue_scripts', 'aether_frontend_enqueue', 50 );
add_action( 'after_setup_theme', 'aether_frontend_setup' );
```

> **Path note:** if you move `frontend/` elsewhere, update both constants. Symlink-friendly.

### 5.2 Asset enqueue (CSS/JS)

`aether_frontend_enqueue()` in the same file:

```php
function aether_frontend_enqueue() {
	$uri = AETHER_FRONTEND_URI;
	$ver = AUREON_VERSION;

	// Vendor (pinned, local — no CDN).
	wp_enqueue_script( 'aether-vendor', $uri . 'assets/vendor/bundle.js', array(), '1.0.0', true );

	// Core JS.
	wp_enqueue_script( 'aether-core', $uri . 'assets/js/aether-core.js', array( 'aether-vendor' ), $ver, true );
	wp_enqueue_script( 'aether-lenis', $uri . 'assets/js/aether-lenis.js', array( 'aether-vendor' ), $ver, true );

	// Motion (only if enabled in Customizer, see 7.3).
	if ( aureon_get_option( 'aether_motion_enabled' ) ) {
		wp_enqueue_script( 'aether-animations', $uri . 'assets/js/aether-animations.js', array( 'aether-vendor' ), $ver, true );
		wp_enqueue_style( 'aether-motion', $uri . 'assets/css/motion.css', array(), $ver );
	}

	// CSS: tokenized main bundle + a11y (always on).
	wp_enqueue_style( 'aether-frontend', $uri . 'assets/css/frontend.css', array(), $ver );
	wp_enqueue_style( 'aether-a11y', $uri . 'assets/css/a11y.css', array( 'aether-frontend' ), $ver );

	// Page-specific.
	if ( is_page_template( 'page-contact.php' ) ) {
		wp_enqueue_script( 'aether-forms', $uri . 'assets/js/aether-forms.js', array( 'aether-core' ), $ver, true );
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script( 'aether-gallery', $uri . 'assets/js/aether-gallery.js', array( 'aether-vendor' ), $ver, true );
	}
	if ( is_cart() || is_checkout() ) {
		wp_enqueue_script( 'aether-cart', $uri . 'assets/js/aether-cart.js', array( 'aether-core' ), $ver, true );
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_script( 'aether-auth', $uri . 'assets/js/aether-auth.js', array(), $ver, true );
	}

	// Pass server data to JS (replace phantom-bridge).
	wp_localize_script( 'aether-core', 'aetherData', array(
		'nonce'    => wp_create_nonce( 'wp_rest' ),
		'restUrl'  => esc_url_raw( rest_url( 'aureon/v1/frontend/' ) ),
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
	) );
}
```

### 5.3 Wire into existing theme hooks (no new hook system)

From `inc/frontend.php` (or a hooks file):

```php
function aether_frontend_setup() {
	// Announcement bar — existing header hooks.
	add_action( 'aureon_before_header_content', 'aether_render_announcement' );
	// Back to top — existing filter + action.
	add_filter( 'aureon_back_to_top_icon', function() { return 'fa-angle-up'; } );
	// Footer newsletter.
	add_action( 'aureon_after_footer_widgets', 'aether_render_footer_newsletter' );
	// Header actions (cart + search icons) — existing hook the WC module already uses.
	add_action( 'aureon_menu_bar_items', 'aether_render_header_actions', 15 );
	// Body classes for AETHER page styling.
	add_filter( 'body_class', 'aether_frontend_body_class' );
}

function aether_frontend_body_class( $classes ) {
	// Map WP conditional to template-style classes (home-page, shop-page, …).
	// Use is_front_page(), is_shop(), is_product(), is_cart(), is_checkout(),
	// is_account_page(), is_single(), is_page() … see TEMPLATE_MAPPING.md.
	return $classes;
}

function aether_render_header_actions() {
	aether_render_component( 'shell/header-actions', array() );
}
```

### 5.4 Section engine

`frontend/sections/section-engine.php` (required by frontend.php):

```php
function aether_register_section( $id, $config ) {
	global $aether_sections;
	$aether_sections[ $id ] = $config;
}

function aether_render_register_sections() {
	aether_register_section( 'hero-slider', array(
		'template'  => 'sections/hero-slider.php',
		'areas'     => array( 'front-page' ),
		'behavior'  => array( 'swiper' => true ),
	) );
	aether_register_section( 'bestsellers', array(
		'template' => 'sections/bestsellers.php',
		'areas'    => array( 'front-page' ),
	) );
	// …
}

add_action( 'after_setup_theme', 'aether_render_register_sections' );

function aether_render_section( $id, $data = array() ) {
	global $aether_sections;
	if ( empty( $aether_sections[ $id ] ) ) { return; }
	$config = $aether_sections[ $id ];
	// Behavior enqueue: if $config['behavior']['swiper'], enqueue swiper script once.
	include AETHER_FRONTEND_DIR . $config['template'];
}
```

---

## 6. Connect to the Plugin (Aureon Studio)

### 6.1 WooCommerce adapters (plugin-side)

Create `aureon/plugin/woocommerce/adapters/` and require from `woocommerce.php`:

```php
// in woocommerce.php after existing requires:
foreach ( glob( dirname( __FILE__ ) . '/adapters/*.php' ) as $file ) {
	require $file;
}
```

Existing WC hooks the framework reuses (already verified in the module):

| Hook | What it does for us |
|---|---|
| `aureon_wc_before/after_shop_loop` | Shop section wrappers (already hooked) |
| `loop_shop_columns` / `loop_shop_per_page` | Grid columns + per-page |
| `aureon_menu_bar_items` + `aureon_wc_do_cart_menu_item` | Cart icon in header |
| `wp_nav_menu_items` → `aureon_wc_menu_cart` | Nav cart fragment |
| `woocommerce_product_loop_start/end` | Card grid wrapper |
| `aureon_color_option_defaults` / `aureon_font_option_defaults` | WC color/font defaults |

New WC template overrides (copy of WC core templates, then AETHER sections):

```
aureon/plugin/woocommerce/templates/
├── archive-product.php      (page-hero + filter-bar + shop-grid)
├── content-product.php      (renders card/product component)
├── single-product.php       (pd-hero, pd-specs, pd-reviews, pd-related)
├── cart/cart.php            (cart-section)
├── checkout/form-checkout.php (checkout-section)
├── myaccount/my-account.php (auth + dashboard)
└── checkout/thankyou.php    (order-confirmation)
```

Register with WooCommerce via the template override path filter (existing mechanism):

```php
add_filter( 'woocommerce_locate_template', 'aether_wc_locate_template', 20, 3 );
function aether_wc_locate_template( $template, $template_name, $template_path ) {
	$our_template = AETHER_PLUGIN_DIR . 'woocommerce/templates/' . $template_name;
	if ( file_exists( $our_template ) ) {
		return $our_template;
	}
	return $template;
}
```

### 6.2 Customizer fields from the plugin

Plugin modules already register fields via `Aureon_Customize_Field::add_field()` (see `woocommerce/fields/woocommerce-colors.php`). Add an AETHER section group the same way (see §7).

---

## 7. Customizer Tokens (how to customize everything)

### 7.1 Add defaults (extension point, not fork)

`frontend/tokens/tokens.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'aureon_option_defaults', 'aether_frontend_defaults' );
function aether_frontend_defaults( $defaults ) {
	return array_merge( $defaults, array(
		// Motion toggles.
		'aether_motion_enabled'      => true,
		'aether_motion_reveal'       => true,
		'aether_motion_tilt'         => true,
		'aether_motion_parallax'     => true,
		'aether_motion_text'         => true,
		// Preloader & fog.
		'aether_preloader_enabled'   => true,
		'aether_fog_enabled'         => true,
		// Newsletter.
		'aether_newsletter_enabled'  => true,
		'aether_newsletter_text'     => __( 'Stay Connected', 'aureon' ),
		// Layout tokens.
		'aether_container_max'       => '1200px',
		'aether_section_padding'     => '100px 0',
		'aether_announcement_height' => '40px',
		'aether_header_height'       => '80px',
		// Radii.
		'aether_radius_sm'           => '8px',
		'aether_radius_md'           => '12px',
		'aether_radius_lg'           => '24px',
		// Google OAuth (server-side only).
		'aether_google_client_id'    => '',
		'aether_google_client_secret' => '',
	) );
}

// Colors ride the existing color-defaults bridge:
add_filter( 'aureon_color_option_defaults', 'aether_frontend_color_defaults' );
function aether_frontend_color_defaults( $defaults ) {
	return array_merge( $defaults, array(
		'aether_color_bg'      => '#09090B',
		'aether_color_surface' => '#141416',
		'aether_color_text'    => '#FFFFFF',
		'aether_color_muted'   => '#A8B5C0',
		'aether_color_accent'  => '#C8956C',
		'aether_color_border'  => 'rgba(255,255,255,0.08)',
	) );
}
```

### 7.2 Emit CSS vars (extend the existing CSS pipeline)

The theme prints dynamic CSS via `aureon_base_css()` (`inc/css-output.php`). Hook onto the printed CSS:

```php
add_action( 'wp_enqueue_scripts', 'aether_frontend_token_css', 5 );
function aether_frontend_token_css() {
	wp_register_style( 'aether-tokens', false ); // inline style handle
	wp_enqueue_style( 'aether-tokens' );

	$css  = ':root{';
	$css .= '--aureon-frontend-bg:' . sanitize_hex_color( aureon_get_option( 'aether_color_bg' ) ) . ';';
	$css .= '--aureon-frontend-surface:' . sanitize_hex_color( aureon_get_option( 'aether_color_surface' ) ) . ';';
	$css .= '--aureon-frontend-accent:' . sanitize_hex_color( aureon_get_option( 'aether_color_accent' ) ) . ';';
	$css .= '--aureon-frontend-muted:' . esc_attr( aureon_get_option( 'aether_color_muted' ) ) . ';';
	$css .= '--aureon-frontend-radius-md:' . esc_attr( aureon_get_option( 'aether_radius_md' ) ) . ';';
	$css .= '--aureon-frontend-container:' . esc_attr( aureon_get_option( 'aether_container_max' ) ) . ';';
	$css .= '}';

	wp_add_inline_style( 'aether-tokens', $css );
}
```

> Pro tip: use the `Aureon_CSS` class instead for media queries/selectors (see `inc/css-output.php` patterns). Keep the token output **above** the component CSS in output order.

### 7.3 Register Customizer controls

Create `aureon/theme/inc/customizer/fields/frontend.php` (follow `buttons.php` patterns exactly):

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

Aureon_Customize_Field::add_title(
	'aether_frontend_title',
	array(
		'section' => 'aureon_colors_section',
		'title'   => __( 'AETHER Frontend', 'aureon' ),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[aether_color_bg]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => aureon_get_option( 'aether_color_bg' ),
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label'   => __( 'Background (Void)', 'aureon' ),
		'section' => 'aureon_colors_section',
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[aether_color_accent]',
	'Aureon_Customize_Color_Control',
	array( /* …same pattern… */ ),
	array(
		'label'   => __( 'Accent (Gold)', 'aureon' ),
		'section' => 'aureon_colors_section',
	)
);

// Motion toggle — the theme has NO dedicated toggle control; use the
// checkbox pattern from inc/customizer.php (hide_title/hide_tagline).
// This snippet runs inside aureon_customize_register( $wp_customize ).
$wp_customize->add_setting(
	'aureon_settings[aether_motion_enabled]',
	array(
		'default'           => true,
		'type'              => 'option',
		'sanitize_callback' => 'aureon_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	'aureon_settings[aether_motion_enabled]',
	array(
		'type'    => 'checkbox',
		'label'   => __( 'Enable Motion Effects', 'aureon' ),
		'section' => 'aureon_layout_section',
	)
);
```

> **Boolean free:** this checkbox is identical to the theme's own `hide_title` pattern. `transport` is `refresh` implicitly here — a toggle that changes script enqueue must refresh to re-render `wp_enqueue_scripts`.

> **Customizer tip:** `transport => 'postMessage'` needs a `customize-preview.js` handler; `'refresh'` is safe for toggles that change script enqueue (like motion). Live-preview the CSS vars via the existing preview JS (`aether-color-accent` → update `--aureon-frontend-accent`).

Require the file in `inc/customizer.php` where other field files are included (search for `fields/buttons.php` require and add `fields/frontend.php` beside it).

### 7.4 What the user can change (result)

With the above, from **Appearance → Customize** a site owner can change: background, surface, accent, muted text, radii, container width, section padding, header/announcement heights, motion on/off, preloader/fog on/off, newsletter on/off + text, Google OAuth credentials — **without touching code**. That is "customize like a pro."

---

## 8. Replace the Complete Frontend (template-by-template)

Order: **header/footer → front page → shop/product → cart/checkout → blog → auth → static pages → utility**.

### 8.1 Header shell

`frontend/layouts/header.php` (used by theme's `inc/structure/header.php` via `get_template_part` or include):

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<?php if ( aureon_get_option( 'aether_preloader_enabled' ) ) : ?>
	<?php aether_render_component( 'shell/preloader', array() ); ?>
<?php endif; ?>

<?php if ( aureon_get_option( 'aether_fog_enabled' ) ) : ?>
	<?php aether_render_component( 'shell/fog', array() ); ?>
<?php endif; ?>

<a class="skip-to-content screen-reader-text" href="#content">
	<?php esc_html_e( 'Skip to content', 'aureon' ); ?>
</a>

<?php
// Announcement bar → existing hook (5.3).
do_action( 'aureon_before_header_content' );

// The AETHER sticky header component (logo, menu, actions).
aether_render_component( 'shell/header', array(
	'data' => aether_adapter_site(),
	'menu' => aether_adapter_menu( array( 'theme_location' => 'primary' ) ),
) );

// Mobile chrome (header + overlay menu) — always present.
aether_render_component( 'shell/mobile-chrome', array(
	'menu' => aether_adapter_menu( array( 'theme_location' => 'mobile' ) ),
) );

// Drawers.
aether_render_component( 'nav/search-modal', array() );
aether_render_component( 'nav/mini-cart', array() );
```

> Register menu locations in `aureon_setup()` if not present: `register_nav_menus( array( 'primary' => …, 'mobile' => … ) )`.

### 8.2 Front page

`aureon/theme/front-page.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>

<main id="aether-main" class="page-content">
	<?php
	aether_render_section( 'hero-slider', array(
		'slides' => aether_adapter_hero_slides(), // Customizer repeater or default 3 slides
	) );

	aether_render_section( 'categories', array(
		'header'   => aether_adapter_section_header( 'categories' ),
		'cats'     => aether_adapter_wc_categories( array( 'limit' => 6 ) ),
	) );

	aether_render_section( 'bestsellers', array(
		'header'   => aether_adapter_section_header( 'bestsellers' ),
		'products' => aether_adapter_wc_products( array( 'limit' => 8, 'orderby' => 'popularity' ) ),
	) );

	aether_render_section( 'reviews', array(
		'header'   => aether_adapter_section_header( 'reviews' ),
		'reviews'  => aether_adapter_testimonials( array( 'limit' => 6 ) ),
	) );

	aether_render_section( 'faq-section', array(
		'header' => aether_adapter_section_header( 'faq' ),
		'faq'    => aether_adapter_faq_items(),
	) );

	if ( aureon_get_option( 'aether_newsletter_enabled' ) ) {
		aether_render_section( 'newsletter-section', array(
			'text' => aureon_get_option( 'aether_newsletter_text' ),
		) );
	}
	?>
</main>

<?php get_footer(); ?>
```

### 8.3 Shop archive (plugin templates)

`archive-product.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main id="aether-main" class="page-content">
	<?php
	aether_render_section( 'page-hero', array(
		'title'       => woocommerce_page_title( false ),
		'description' => get_the_archive_description(),
	) );

	aether_render_section( 'filter-bar', array(
		'ordering' => true,
		'results'  => woocommerce_result_count( false ),
	) );

	do_action( 'aureon_wc_before_shop_loop' ); // existing wrapper

	echo '<div class="shop-grid">';
	if ( woocommerce_product_loop() ) {
		while ( have_posts() ) {
			the_post();
			wc_get_template_part( 'content', 'product' ); // → card/product component
		}
	}
	echo '</div>';

	do_action( 'aureon_wc_after_shop_loop' ); // pagination etc.
	?>
</main>
<?php get_footer(); ?>
```

`content-product.php`:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $product;
aether_render_component( 'card/product', array(
	'id'     => $product->get_id(),
	'name'   => $product->get_name(),
	'price'  => wp_strip_all_tags( wc_price( (float) $product->get_price() ) ),
	'url'    => get_permalink( $product->get_id() ),
	'image'  => aether_adapter_product_image( $product ),
	'badge'  => $product->is_on_sale() ? __( 'Sale', 'aureon' ) : '',
	'rating' => (float) $product->get_average_rating(),
) );
```

> This is the one place a template calls WC directly — inside a template, not a component. Acceptable per architecture (templates may act as thin adapters).

### 8.4 Blog index + single

`aureon/theme/home.php` (posts page) and `single.php` — rebuild using `aether_adapter_blog_posts()` / `aether_adapter_article_*()` from ADAPTER_SPECIFICATION §2.3. `single.php` already exists (modified in the rollback) — replace content loop with article sections.

### 8.5 Static pages (contact, faq, team, testimonials, about, wishlist)

Create page templates in theme root: `page-contact.php`, `page-faq.php`, `page-team.php`, `page-testimonials.php`, `page-about.php`, `page-wishlist.php`. Each follows the front-page pattern (get_header → sections → get_footer). Legal pages use the default `page.php` (which renders `content/page` sections).

### 8.6 Utility (404, thank-you, coming-soon)

- `404.php` — error sections (`error_code` phantom).
- WooCommerce `checkout/thankyou.php` — order sections.
- Coming-soon: theme option `aether_maintenance` → `wp_die`/redirect to a coming-soon template (never ship as a static page copy).

---

## 9. Animations + REST

### 9.1 Animation bridge (declarative)

- Components emit `data-reveal-item`, `data-reveal-group`, `data-motion-text="words|lines"`, `data-tilt`, `data-parallax-section`, `data-image-zoom` (see §4.2 example).
- `aether-animations.js` (refactored from source `animations.js`): same engine, `autoAssignReveals()` map updated to the component class names.
- Gate per Customizer toggle (`aether_motion_reveal`, `aether_motion_tilt`, …) — if off, the renderer simply doesn't emit the attribute (PHP-side) — see 5.2 for the enqueue gate.
- Reduced motion: engine already respects `prefers-reduced-motion`; keep that.

### 9.2 REST endpoints (extend existing class-rest.php)

```php
// in aureon/theme/inc/class-rest.php register_routes():
register_rest_route( $namespace, '/frontend/page-data', array(
	'methods'  => WP_REST_Server::READABLE,
	'callback' => array( $this, 'frontend_page_data' ),
	'permission_callback' => '__return_true',
) );

register_rest_route( $namespace, '/frontend/newsletter', array(
	'methods'  => WP_REST_Server::CREATABLE,
	'callback' => array( $this, 'frontend_newsletter' ),
	'permission_callback' => array( $this, 'frontend_form_permission' ), // nonce + honeypot
) );
```

`aether-core.js` uses `aetherData.restUrl` + `aetherData.nonce` (localized in 5.2). This **replaces** `phantom-data.js` (whose `init()` was broken) and `contact-form.php`.

### 9.3 Fix the known bugs at import time

| Bug (from audit) | Fix |
|---|---|
| `a11y.css` broken `href="assets/css/a11y.css"">` | not imported at all — we enqueue it properly (5.2) |
| `phantom-data.js:212` undefined `init()` | file is not used; REST layer replaces it |
| `firebase-auth.js` module path `../assets/js/…` | fix to `./` or bundle path in `aether-auth.js`; server-side verification only |
| `#d4af37` legacy gold | normalized to `#C8956C` in tokens (7.1) |
| duplicate `blog-page` body class | WP `body_class()` handles it; don't replicate static class |
| checkout newsletter id collision | components generate `uniqid('newsletter-')` |

---

## 10. Performance & Accessibility

### 10.1 Performance

- Single vendor bundle (`assets/vendor/bundle.js`) — GSAP 3.12.5 + ScrollTrigger + Lenis 1.1.18 + Swiper 11 + Bootstrap 5.3.3 + jQuery 3.7.1, minified, `defer` (enqueue in footer = `true`).
- Component CSS only once per page; `frontend.css` tokenized (no hardcoded hex — verify with: `Select-String -Path frontend\assets\css\frontend.css -Pattern '#[0-9a-fA-F]{3,8}'`).
- Images: `loading="lazy"` in components, `sizes` attrs; hero uses preload.
- Lazy sections: render below-the-fold sections only when needed (or keep SSR and let lazy images handle it).
- `aether:content-updated` event → bridge re-scans + `ScrollTrigger.refresh()`.
- Cache-busting: version args = `AUREON_VERSION` (theme release) + `filemtime()` for dev (see `general.php` pattern).

### 10.2 Accessibility

- `a11y.css` always enqueued (5.2).
- Reduced motion: everything gated (9.1).
- Swiper: clickable pagination, `aria-live="polite"`, visible focus.
- Tilt: `@media (hover: none)` disabled; keyboard-safe.
- Forms: labels bound (`for`/`id`), `aria-describedby` for errors, honeypot + nonce.
- Contrast: AETHER palette is dark + gold — verify AA on text/muted surfaces in regression.

---

## 11. Visual Regression (prove it matches)

1. Serve static reference: `npx serve frontend/source -p 8080` (or `php -S`).
2. Capture goldens (Playwright): 22 pages × viewports (1440×900, 375×812).
3. On WP: set Customizer defaults (they ARE the AETHER values — no config needed), disable admin bar for capture.
4. Capture candidates, pixel-diff, classify:

```
Grade A = pass  |  Grade B = only documented diffs  |  Grade C = fail (blocking)
```

Full matrix + protect list in `frontend/VISUAL_REGRESSION_PLAN.md`. Scripts under `frontend/regression/`.

---

## 12. Runbook: Common Tasks

| Task | Do this |
|---|---|
| Add a new component | 1) create template in `frontend/components/…` 2) register in `manifest/components.php` 3) call `aether_render_component( 'id', $data )` |
| Change a color site-wide | Customizer → Colors → AETHER Frontend (no code) |
| Turn off all animation | Customizer → Layout → Enable Motion Effects = off |
| Add a new section to home | register in `aether_render_register_sections()` + render in `front-page.php` |
| Add a Customizer control | copy pattern from 7.3; require file in `inc/customizer.php` |
| Add a REST endpoint | add route in `class-rest.php`; consume via `aetherData.restUrl` |
| Update vendor lib | bump files in `frontend/assets/vendor/` + version arg; rerun regression |
| Re-import source | NEVER overwrite `frontend/source/` (golden reference); copy new files to `assets/` manually |

---

## 13. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Styles missing / broken layout | `AETHER_FRONTEND_DIR` path wrong | check constants; confirm `frontend/` next to `aureon/` |
| Motion not running | `aether_motion_enabled` off | Customizer toggle; check `aether-vendor` handle loads first |
| ScrollTrigger offsets wrong after images load | refresh ordering | `window.load` → `ScrollTrigger.refresh()`; listen `aether:content-updated` |
| Mini-cart count stale | fragment cache | use `woocommerce_after_mini_cart` hooks; cart fragments refresh event |
| Variable product won't add to cart | WC variation JS missing | keep WC's `variations_form` classes in `product/meta` component |
| Gold looks off vs template | `#d4af37` legacy | normalize in tokens; only `--aureon-frontend-accent` is truth |
| White flash before preloader | CSS loads after HTML | inline critical tokens (`aether-tokens` printed early, 7.2) |
| Google sign-in fails | config missing / wrong path | set client id/secret in Customizer; ensure `aether-auth.js` module path fixed |
| Customizer control not showing | field file not required | add require in `inc/customizer.php` beside other field files |

---

## 14. Checklist (final sign-off)

- [ ] `frontend/` tree per §3; `frontend/source/` untouched
- [ ] Manifest lists every component you use
- [ ] `inc/frontend.php` required in `functions.php`; constants correct
- [ ] Enqueue loads vendor → core → page-specific, all local, no CDN
- [ ] Tokens emitted as `--aureon-frontend-*` vars; `frontend.css` has 0 hardcoded hex
- [ ] Customizer "AETHER Frontend" group live (colors, radii, motion, newsletter, OAuth)
- [ ] Hooks: announcement (before_header), header actions (menu_bar_items), footer newsletter (after_footer_widgets)
- [ ] WC: adapters in plugin; template overrides registered; cart fragments sync
- [ ] REST: page-data + newsletter + contact endpoints; nonce wired
- [ ] Known bugs from §9.3 all fixed
- [ ] `a11y.css` active; reduced-motion tested
- [ ] Regression: all pages Grade A or B
- [ ] No WP/WC function calls inside any component (grep: `rg "wc_|get_option|get_post|WP_Query" frontend/components` → empty)

---

*Companion reports: `frontend/FRONTEND_ARCHITECTURE_REPORT.md`, `frontend/COMPONENT_INVENTORY.md`, `frontend/SECTION_LIBRARY.md`, `frontend/ADAPTER_SPECIFICATION.md`, `frontend/TEMPLATE_MAPPING.md`, `frontend/TOKEN_MIGRATION_REPORT.md`, `frontend/ANIMATION_INTEGRATION_REPORT.md`, `frontend/WOO_INTEGRATION_REPORT.md`, `frontend/VISUAL_REGRESSION_PLAN.md`, `frontend/MASTER_FRONTEND_IMPLEMENTATION_PLAN.md`.*