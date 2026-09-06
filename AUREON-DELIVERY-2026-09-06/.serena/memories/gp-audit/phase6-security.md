# GP Audit — Phase 6 Security Forensics (COMPLETE)

- ZERO: base64_decode, gzinflate, gzuncompress, gzdecode, zlib_decode, str_rot13, convert_uudecode, create_function, preg_replace /e, shell_exec, system(), passthru, popen, proc_open, exec(), posix_kill, posix_mkfifo, raw unserialize, assert(), variable variables, call_user_func, file_get_contents(remote), remote includes, null bytes, BOM
- 2 eval() ONLY, both legitimate gated features:
  - elements/class-hooks.php:215 PHP Hook element — DISALLOW_FILE_EDIT + manage_options + unfiltered_html (class-metabox.php:1660)
  - hooks/functions/hooks.php:22 legacy GP Hooks — add_theme_page with apply_filters('generate_hooks_capability','manage_options') (functions.php:132)
- Domains: 100% official (generatepress.com 32, docs.generatepress.com 14, w3.org 22, schema.org 19, github.com 7, fonts.googleapis.com 5, gpsites.co 4, wordpress.org family 7, gnu/php-fig/mdn 3, mysite.com 1 placeholder). NO tracking/telemetry/unknown domains.
- No SQLi (zero direct $wpdb in theme; none user-input in plugin), no path traversal, no secrets/hardcoded creds, no object injection (maybe_unserialize only on WP-owned data)
- Polyglot scan: no <?php in any non-PHP file; magic bytes valid (screenshot.png 1200×900 PNG, placeholders, fonts)
- CSRF: nonces on meta-box saves, REST manage_options + core nonces, check_ajax_referer on pickers
- CVEs: CVE-2023-6807 (Stored XSS ≤2.3.2, fixed 2.3.3), CVE-2024-3469 (Reflected XSS ≤2.4.0, fixed 2.4.1), Font Library Arbitrary Upload (2.5.0-2.5.5, FIXED IN 2.5.6 — verified: manage_options gate class-font-library-rest.php:523-533 + MIME whitelist ttf/woff/woff2). Theme has NO CVEs.
- XSS gaps (defense-in-depth only, trusted-filter points): navigation.php:341-349 walker, navigation.php:51-55 menu label, footer.php:88 copyright filter, footer.php:78-86 bloginfo name, theme-functions.php:722-726 attr filter, header meta viewport filter
- dist/block-elements.js \x3c\x3e = JSON-escaped markup (benign); packages.js 0 B placeholder; editor-rtl.css 77 B stub
- REVERIFIED 2026-08-03: fresh full grep (both packages) confirms ZERO base64_decode/gzinflate/gzuncompress/gzdecode/zlib_decode/str_rot13/convert_uudecode/create_function/shell_exec/passthru/proc_open/exec/posix_kill/posix_mkfifo/assert/variable-variables/call_user_func/remote includes/preg_replace /e; 2 eval() only in elements/class-hooks.php:215 (PHP Hook element, DISALLOW_FILE_EDIT+manage_options+unfiltered_html) and hooks/functions/hooks.php:22 (legacy GP Hooks, manage_options via filter); domain census matches (all official, mysite.com placeholder only); CVE-2024-3469 patched, Font Library arbitrary upload fixed in 2.5.6 — manage_options gate class-font-library-rest.php:523-533 + MIME whitelist ttf/woff/woff2 in class-font-library.php:399-406 confirmed; no SQLi/path traversal/secrets/object injection.
- Score: 8.5/10
