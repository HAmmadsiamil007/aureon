# GP Audit — Phase 4 GP Premium Architecture (COMPLETE)

- Bootstrap: gp-premium.php requires modules lazily via generatepress_is_module_active(option,constant) — option 'activated' or constant defined
- Modules: backgrounds, blog, copyright, disable-elements, elements (+dynamic-tags, adjacent-posts), secondary-nav, spacing, menu-plus, woocommerce (only if WC active), site-library (REST+WXR), font-library (class-{font-library,rest,optimize,cpt}.php)
- Deprecated modules: hooks, page-header, sections (option-gated); colors/typography version-gated in generate_premium_load_modules — typography only if !generate_is_using_dynamic_typography (3.6.1 = dynamic → NOT loaded); colors only if theme < 3.1.0-alpha.1 (3.6.1 → NOT loaded). Correct double-load prevention.
- License/update: EDD SL updater library/class-plugin-updater.php vs https://generatepress.com; option gen_premium_license_key + gen_premium_license_key_status; REST routes /modules /license /beta /export /import /reset — ALL gated update_settings_permission()=manage_options (inc/class-rest.php:130-133); license key masked in dashboard get_license_key()
- 327 hooks: 54 do_action + 273 apply_filters; 299 add_action + 203 add_filter
- DB: options generate_package_*, gen_premium_*, generate_settings; post meta (elements/page-header/sections/disable-elements); CPTs gp_elements + font CPT; NO transients of note; NO cron (zero wp_schedule_event); NO register_activation_hook
- AJAX: generate_elements_get_location_* (cap checks class-metabox.php:2014-2078), generatepress_regenerate_css_file, generate_get_all_google_fonts_ajax (guarded both sides)
- Elements (flagship): class-metabox.php 103 KB; class-hooks.php:215 eval('?>'.$content.'<?php ') — PHP Hook element, gated DISALLOW_FILE_EDIT+manage_options+unfiltered_html (metabox:1660)
- Font Library CVE fix VERIFIED: all REST routes permission_callback=edit_posts_permission()=manage_options (class-font-library-rest.php:523-533); upload MIME whitelist {ttf,woff,woff2} + wp_handle_upload mimes override + wp_check_filetype (class-font-library.php:399-406,466-585)
- Site Library: WXR importer bundled (68 KB), maybe_unserialize safe; images via media_handle_sideload
- Capabilities: manage_options/edit_posts/edit_post/unfiltered_html only; NO custom roles, NO privilege escalation
- REVERIFIED 2026-08-03: gp-premium.php full read (309 lines) — module requires lazy & option/constant-gated; WC module gated on is_plugin_active('woocommerce/woocommerce.php'); colors/typography version-gated in generate_premium_load_modules (typography only if !generate_is_using_dynamic_typography, colors only if theme <3.1.0-alpha.1 → both NOT loaded on 3.6.1); EDD updater lib class-plugin-updater vs generatepress.com, gen_premium_license_key option; admin_notices theme-requirement warn; no hidden endpoints. Fresh hook census: do_action=54 + apply_filters=273 = 327. Consistent with baseline.
- Score: 9/10
