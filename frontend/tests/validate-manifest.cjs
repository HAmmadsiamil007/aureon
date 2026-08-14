#!/usr/bin/env node
/**
 * M8 — Design pack contract validator.
 *
 * Scans design pack manifest.json files and fails on:
 *   - malformed JSON / missing identity (id, label, version)
 *   - asset entries that resolve to no file (pack or base when base:true)
 *   - component overrides pointing at missing templates
 *   - mappings referencing component ids that do not exist in the base
 *     manifest (frontend/manifest/components.php) or the pack's overrides
 *
 * Exit code 1 on any failure. Wired into tests/verify.sh.
 *
 * Usage: node frontend/tests/validate-manifest.cjs
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..', '..');
const DESIGNS = path.join(ROOT, 'frontend', 'designs');
const BASE_MANIFEST = path.join(ROOT, 'frontend', 'manifest', 'components.php');

let errors = 0;
const fail = (msg) => { errors += 1; console.error('FAIL: ' + msg); };

// --- Base manifest ids (PHP `return array( 'id' => ... )` — regex parse) ---
let baseIds = [];
if (fs.existsSync(BASE_MANIFEST)) {
  const php = fs.readFileSync(BASE_MANIFEST, 'utf8');
  const re = /'([a-z][a-z0-9\/-]*)'\s*=>/g;
  let m;
  while ((m = re.exec(php)) !== null) baseIds.push(m[1]);
} else {
  fail('base manifest missing: frontend/manifest/components.php');
}

if (!fs.existsSync(DESIGNS)) {
  console.log('PASS: no design packs present');
  process.exit(0);
}

const packs = fs.readdirSync(DESIGNS, { withFileTypes: true })
  .filter((d) => d.isDirectory())
  .map((d) => d.name);

if (packs.length === 0) {
  console.log('PASS: no design packs present');
  process.exit(0);
}

for (const slug of packs) {
  const packDir = path.join(DESIGNS, slug);
  const mf = path.join(packDir, 'manifest.json');
  if (!fs.existsSync(mf)) {
    console.log(`SKIP: ${slug} has no manifest.json`);
    continue;
  }

  let manifest;
  try {
    manifest = JSON.parse(fs.readFileSync(mf, 'utf8'));
  } catch (e) {
    fail(`${slug}: manifest.json is not valid JSON (${e.message})`);
    continue;
  }

  if (manifest.id !== slug) fail(`${slug}: manifest.id ("${manifest.id}") must equal the directory name`);
  for (const key of ['id', 'label', 'version']) {
    if (!manifest[key]) fail(`${slug}: missing required manifest key "${key}"`);
  }

  // --- Assets ---
  const assets = manifest.assets || {};
  for (const kind of ['css', 'js']) {
    const list = Array.isArray(assets[kind]) ? assets[kind] : [];
    for (const entry of list) {
      const file = typeof entry === 'string' ? entry : entry && entry.file;
      if (!file || typeof file !== 'string') {
        fail(`${slug}: assets.${kind} entry must be a string or {file}: ${JSON.stringify(entry)}`);
        continue;
      }
      const fromBase = !!(entry && entry.base);
      const rel = path.join(...file.split('/'));
      const abs = path.join(fromBase ? path.join(ROOT, 'frontend', 'assets') : packDir, rel);
      if (!fs.existsSync(abs)) {
        fail(`${slug}: assets.${kind} "${file}"${fromBase ? ' (base)' : ''} does not exist`);
      }
    }
  }

  // --- Component overrides ---
  const overrides = (manifest.components && manifest.components.overrides) || {};
  for (const [id, rel] of Object.entries(overrides)) {
    const abs = path.join(packDir, rel);
    if (!fs.existsSync(abs)) fail(`${slug}: component override "${id}" -> ${rel} does not exist`);
    else if (!baseIds.includes(id)) fail(`${slug}: component override "${id}" is not a base manifest id (extend via mappings, not overrides)`);
  }

  // --- Mappings ---
  const mapped = (manifest.mappings && manifest.mappings.components) || [];
  const known = new Set([...baseIds, ...Object.keys(overrides)]);
  for (const id of mapped) {
    if (!known.has(id)) fail(`${slug}: mapping references unknown component id "${id}"`);
  }
}

if (errors > 0) {
  console.error(`RESULT: FAILED (${errors} errors)`);
  process.exit(1);
}
console.log(`RESULT: PASSED (${packs.length} pack(s) validated)`);
process.exit(0);