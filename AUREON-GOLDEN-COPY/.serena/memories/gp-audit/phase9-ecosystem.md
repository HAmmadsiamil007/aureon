# GP Audit - Phase 9 Plugin Ecosystem (COMPLETE - refreshed)

- GeneratePress 3.6.1: latest stable (Dec 1 2025), wp.org, 500,000+ active installs, 5/5 stars, GPLv2+
- GP Premium 2.5.6: latest (May 29 2026), commercial $59/yr / $349 lifetime, NOT on wp.org
- Developer: Tom Usborne / EDGE22 Studios Ltd., active through 2026; docs.generatepress.com
- Compatibility matrix (code-based): WooCommerce native (add_theme_support + plugin-compat.php + gated WC module); ACF ok; RankMath/Yoast ok (schema via generate_schema_type, use JSON-LD to avoid dup schema); WPML ok (ships wpml-config.xml 7353 B); Polylang ok; Elementor explicitly endorsed (readme); Bricks/Oxygen/Beaver ok (thin wrappers + generate_has_default_loop / full-width); forms (Fluent/WPForms/CF7/Gravity) ok; BuddyPress theme tag; bbPress ok; EDD ok; LearnDash ok; TEC ok; GenerateBlocks preferred sibling (dedicated dynamic tags)
- Known integration notes: FA 4.7 vs FA5+ icon class coexistence (generate_fontawesome_essentials); selectWoo duplicated theme+plugin (admin-only contexts); do_shortcode on widget_text (intentional legacy); WXR import only from official gpsites.co; schema dup mgmt
- Security advisories: 3 total, ALL patched in audited versions (2023-6807, 2024-3469, 2026 Font Library upload)
- REVERIFIED 2026-08-03: fresh scans confirm WC native (theme add_theme_support + plugin-compat.php hooks-only + plugin WC module gated); WPML ships wpml-config.xml (7353 B in plugin); Elementor/Beaver/Bricks explicitly compatible; 22 locales in langs/; FA 4.7 local (37 KB), generate_fontawesome_essentials filter; selectWoo duplicated admin-only; all 3 CVEs patched in audited versions; GenerateBlocks dynamic tags supported. 500K+ wp.org installs confirmed via theme header URI.
- Score: 9/10
