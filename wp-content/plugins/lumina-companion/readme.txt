=== Lumina Companion ===
Contributors: luminastudio
Tags: lumina, frontend, typography, spacing
Requires at least: 6.5
Tested up to: 6.9
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Original companion plugin for the Lumina theme — spacing, typography, page header, secondary navigation, menu plus, sections, and WooCommerce styling. 100% original code.

== Description ==

Lumina Companion extends the Lumina theme with premium feature categories,
implemented as 100% original code:

* **Spacing** — per-element spacing controls (container width, gutter,
  section, card, gap) emitted as design tokens.
* **Typography** — body/heading font families, sizes, weights, line height.
* **Page Header** — configurable page header region above the content.
* **Secondary Navigation** — a secondary menu location + slim bar.
* **Menu Plus** — mega-menu support for items marked `menu-item-mega`.
* **Sections** — content regions on public Lumina hooks.
* **Site Library** — a REST endpoint listing user-supplied site presets.
* **WooCommerce** — WC styling via public hooks (only when WC is active).

The plugin registers its features only when the **Lumina** theme is active and
degrades to a no-op on any other theme.

== Installation ==

1. Upload the `lumina-companion` folder to `/wp-content/plugins/`.
2. Activate the plugin through the Plugins screen.
3. Configure settings under **Appearance → Customize → Lumina Companion**.

== Frequently Asked Questions ==

= Does it require the Lumina theme? =

Yes. Modules activate only when the Lumina theme is active.

== Changelog ==

= 1.0.0 =
* Initial release — original companion plugin for the Lumina theme.
