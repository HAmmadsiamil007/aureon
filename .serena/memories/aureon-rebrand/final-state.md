# Aureon Rebrand — Final State (v1.0.0)

## Completed
- Full rebrand of GeneratePress 3.6.1 + GP Premium 2.5.6 → Aureon theme + Aureon Studio plugin
- 39 ordered replacement rules applied (longest-first, case-sensitive)
- Sentinel protection for GenerateBlocks, regenerate, generated words
- File renames: generate-*.php → aureon-*.php, gp-premium.php → aureon-studio.php
- Version constants: AUREON_VERSION='4.0.0', AUREON_STUDIO_VERSION='3.0.0', display 1.0.0
- JS variables renamed: gp* → aureon*
- Endpoint URLs neutralized to example.com
- License system rewritten: local-only validation (any key accepted)
- License files cleaned: GPL v2+, Aureon Studio copyright only
- Docblocks updated: GenerateBlocks references removed
- WPML config added
- 209 PHP files validated, 0 syntax errors
- Professional audit: 15 issues found and fixed

## Folder Structure
```
wordpress/
├── aureon/
│   ├── theme/          (145 files — Aureon theme)
│   └── plugin/         (331 files — Aureon Studio plugin)
├── generatepress/      (144+330 files, gitignored — original GP files)
├── README.md           (full documentation)
├── .gitignore          (excludes generatepress/, ZIPs, IDE files)
├── rebrand.ps1         (rebrand execution script)
├── AUREON_REBRAND_PLAN.md
└── Report/             (original GP Premium audit reports)
```

## Git Status
- **Branch:** main
- **Remote:** https://github.com/HAmmadsiamil007/wordpress.git
- **Latest commit:** a56a655 — feat(aureon): Complete rebrand
- **Pushed:** Yes (Aug 4, 2026)

## Version Strategy
- AUREON_VERSION='4.0.0' (passes >= 3.1.0 version gates)
- AUREON_STUDIO_VERSION='3.0.0'
- Display headers show 1.0.0

## Key Files
- aureon/theme/style.css — Theme Name: Aureon, Version: 1.0.0, Text Domain: aureon
- aureon/plugin/aureon-studio.php — AUREON_STUDIO_VERSION='3.0.0', display 1.0.0
- aureon/theme/functions.php — AUREON_VERSION='4.0.0'
- aureon/plugin/site-library/class-site-library-rest.php — Endpoints neutralized
- aureon/plugin/inc/legacy/activation.php — License rewritten to local-only

## Sentinel Words (DO NOT RENAME)
- GenerateBlocks, generateblocks
- Regenerate, regenerate
- Generated, generated

## Block Attribute Names (DO NOT RENAME)
- gpDynamicTextType, gpDynamicDisplayType, etc. (DB schema)

## Known Dead Endpoints
- Site Library API: https://example.com/invalid
- No external URLs owned
