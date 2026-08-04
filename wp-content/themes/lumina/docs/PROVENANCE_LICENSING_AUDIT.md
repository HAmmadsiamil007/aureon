# Lumina — Provenance & Licensing Audit (Phase 16.5)

**Audited:** 2026-08-04
**Product:** Lumina theme 1.0.0 + Lumina Companion 1.0.0
**Scope:** code origin, third-party dependencies, bundled assets, license
compatibility, attribution, SBOM
**Result:** ✅ PASS with 2 recommendations (see §7)

---

## 1. Purpose

Engineering QA verifies behavior; it cannot by itself establish copyright or
licensing posture. This audit answers the provenance questions required
before commercial distribution:

- Which code was written from scratch?
- Which third-party libraries ship in the distribution?
- What license applies to each, and is redistribution compatible?
- Are attribution requirements met?
- Is there a software bill of materials (SBOM)?

## 2. Code Origin

| Component                                                                                                                                                 | Origin                       | Evidence                                                                                                       |
| --------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------- | -------------------------------------------------------------------------------------------------------------- |
| Theme shell (`header.php`, `footer.php`, `index.php`, `single.php`, `page.php`, `archive.php`, `search.php`, `404.php`, `comments.php`, `searchform.php`) | Original, written for Lumina | Unique markup, `Lumina\Core` hooks, region actions `lumina_before/after_header` + `lumina_before/after_footer` |
| `app/` framework (container, tokens, render, components, templates, assets, bridges, woo, animation, performance, a11y)                                   | Original                     | Unique namespaces `Lumina\Core\*`, custom class design (ADR-009–028), WP-free CLI-verifiable architecture      |
| `wp-content/plugins/lumina-companion/src/` (17 modules)                                                                                                   | Original                     | Unique `Lumina\Companion\Modules\*` classes, module contract interface, guarded WP-free trait                  |
| `assets-src/` SCSS + TS                                                                                                                                   | Original                     | Token-driven `--lumina-*` system, custom component behaviors entry                                             |
| `assets/dist/` built bundles                                                                                                                              | Built from `assets-src/`     | Vite build output, `.vite/manifest.json`                                                                       |

**No code is copied or adapted from GeneratePress, GP Premium, or any other
commercial theme.** The 100%-clean-identifier gate (0 matches for
generatepress/gp_premium/phantom in all source and shipped archives) was
re-verified for this audit. Full provenance of every source file is
trackable in git history (`git log` per file from Phase 0).

## 3. Runtime Dependency SBOM (what ships)

The **theme and plugin ship zero PHP runtime dependencies** (composer
`require` = `php ^8.2` only). The only third-party code in the distributable
archives is the JavaScript bundled into `assets/dist/`:

| Library                    | Version                | License                                                      | Ships in                           | Commercial redestribution OK?                                                    |
| -------------------------- | ---------------------- | ------------------------------------------------------------ | ---------------------------------- | -------------------------------------------------------------------------------- |
| GSAP (incl. ScrollTrigger) | ^3.15.0                | GSAP Standard "No Charge" license (Webflow, eff. 2025-04-30) | `ScrollTrigger-*.js`, `index-*.js` | ✅ Yes — license explicitly states commercial usage is covered at no charge (§4) |
| Lenis (smooth scroll)      | ^1.3.25                | MIT                                                          | `lenis-*.js`, `animation-*.js`     | ✅ Yes                                                                           |
| Three.js                   | ^0.185.1               | MIT                                                          | `three.module-*.js` (lazy-loaded)  | ✅ Yes                                                                           |
| Rapier (three physics)     | transitively installed | Apache-2.0                                                   | **Not bundled** (absent from dist) | N/A                                                                              |

Bundled fonts, icon fonts, and images: **none** — zero font/icon/image
attribution obligations.

### Dev-only SBOM (40 composer packages — never shipped)

All permissive: 33× MIT, 2× BSD-3-Clause, 2× ISC, 2× LGPL-3.0-or-later
(phpcsstandards), 1× OSL-3.0 (netresearch/jsonmapper). Dev-only tooling
(phpstan, psalm, phpcs, wpcs, stubs) — excluded from ZIPs by the packaging
gate (`vendor/` never ships).

## 4. GSAP Commercial License — Findings

The GSAP "Standard No Charge" license (post-acquisition by Webflow,
effective 2025-04-30) grants: _"a non-exclusive, worldwide license to use,
reproduce, display, and implement GSAP Products solely for Permitted Uses."_

- **Permitted Uses** = any website/web app/digital interface.
- **Prohibited Uses** = tools that let users build visual animations without
  code that compete with Webflow's animation builder (a visual animation
  editor similar to Webflow).
- FAQ: _"Can I really use GSAP in commercial projects without paying
  anything? Yes, really! Commercial usage is covered under the standard
  license."_ Also: _"What if a WordPress plugin or theme… allows users to
  create GSAP-driven effects through a visual interface? …We want to
  encourage developers to build on top of GSAP."_

**Conclusion:** Lumina uses GSAP programmatically for its own animation
presets (reveal-on-scroll, counters). It is not a visual animation builder
competing with Webflow. Bundling GSAP in a commercial Lumina theme is
**permitted at no charge**. The GSAP license notice is preserved in the
bundled files (no notices removed).

## 5. Attribution

- MIT (Lenis, Three.js) — no attribution text required beyond retaining
  copyright notices, which the npm-distributed packages carry.
- GSAP — proprietary notices must not be removed; bundled files retain them.
- No other attribution obligations exist (no fonts, no icons, no images).

## 6. License of Lumina itself

- Theme + plugin: **GPL-2.0-or-later** (style.css, plugin header,
  composer.json, both readme.txt, full `license.txt` shipped in both ZIPs).
- GPL is compatible with MIT/BSD (permissive) and GSAP's proprietary
  no-charge license for redistribution purposes.

## 7. Recommendations (informational — no blocking findings)

1. **Preserve GSAP notices on upgrades.** When bumping `gsap`/`lenis`/`three`
   versions, verify the dist bundles still carry upstream license headers
   (the animation entry already keeps them). Add this to the Phase-17 release
   checklist.
2. **Brand check (legal, not technical).** See §8 — "Lumina" carries
   trademark/collision risk; consider a distinctive name before public launch.

## 8. Brand / Trademark Notes (from external research)

- **"Lumina" is heavily used** in the exact vertical: a commercial WordPress
  theme on ThemeForest (AncoraThemes), a WP.org theme (LuminAI), and a
  WP.org plugin (Lumina-Flux), plus multiple SaaS products.
- Exact-match `lumina.com` is unavailable/parked; a modifier domain
  (`luminathemes.com`, `getlumina.com`) would be required.
- A broad "Lumina" trademark filing in software/theme classes would likely
  face likelihood-of-confusion refusals (USPTO) due to existing registrations
  in the same commercial channel.
- **Recommendation:** retain Lumina as the internal codename; commission a
  distinctive public brand (e.g., via a naming/clearance exercise) before
  Phase 17 commercial release. This is a business decision, not an
  engineering defect.

## 9. Decision

**STATUS: ✅ PASS.** Provenance is original throughout; every shipped
third-party component is redistributable in a commercial GPL theme at no
charge; attribution obligations are satisfied; SBOM documented. The only
open items are the two informational recommendations above.
