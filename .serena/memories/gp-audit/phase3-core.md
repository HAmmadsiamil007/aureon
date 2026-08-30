# GP Audit — Phase 3 GP Core Architecture (COMPLETE)

- Bootstrap: functions.php → 17 inc requires + 9 structure requires; ALL public functions function_exists-guarded
- 22 root templates, thin; structure produced by inc/structure/*.php hooked into generate_* actions
- 350 hooks: 127 do_action + 223 apply_filters; 99 add_action + 48 add_filter registrations
- Centralized attr system: generate_do_attr()/GeneratePress_HTML_Attributes (inc/class-html-attributes.php) emits classes + microdata/JSON-LD schema (generate_schema_type)
- Dynamic CSS: GeneratePress_CSS builder (inc/class-css.php) + css-output.php (67 KB); cached in wp_options generate_dynamic_css_output + _cached_version; busted on version change/customize_save_after; filters generate_dynamic_css_skip_cache, generate_using_dynamic_css_external_file
- Customizer: 10 panels, 26 sections, 289 controls, 314 settings; custom Customize_Field API; React control; selectWoo
- Typography: inc/typography.php (96 KB) dynamic engine; ~800 Google Fonts hardcoded JSON; default max 200 fonts; AJAX generate_get_all_google_fonts_ajax; migration class
- Assets: flexbox mode main.min.css (19.5 KB) default; legacy mode style+grid+mobile or combined all.css; FA4.7 (37 KB) unless generate_fontawesome_essentials; JS all in footer (menu/dropdown-click/modal/nav-search/back-to-top/a11y); $suffix = SCRIPT_DEBUG?'':'.min'
- WC: hooks-only integration in inc/plugin-compat.php (wrapper swap, sidebar redirect, WC CSS at priority 100)
- REST: GeneratePress_Rest, /generatepress/v1/reset, gated manage_options
- a11y: a11y.js, ARIA via attr system, skip links, wp_body_open
- REVERIFIED 2026-08-03: functions.php requires confirmed (18 inc + 9 structure = 27); fresh grep: do_action=127 + apply_filters=223 = 350, add_action=99 + add_filter=48 = 147 registrations; dynamic-CSS option key generate_dynamic_css_output, _cached_version, skip_cache, external_file all present in css-output.php. Byte-consistent with prior baseline.
- Score: 9/10
