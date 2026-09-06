#!/usr/bin/env node
/**
 * Vineta selector-contract tests (audit fix T-15).
 *
 * Asserts that every DOM class/id/data-slot the bridge stack depends on
 * still exists in the frozen pack templates. Run before any frontend edit:
 *
 *   node tests/contract-tests.cjs
 *
 * Exit 0 = contract intact. Exit 1 = a bridge dependency would break.
 * Zero dependencies. Selectors are extracted from real bridge code
 * (composer.php, vineta-data-shims.js, vineta-path-bridge.js, ferm-page.php),
 * not invented. Maintainer rule: when bridge JS starts using a new selector,
 * add it to the relevant template's REQUIRED list here.
 */
'use strict';

const fs = require('fs');
const path = require('path');

const PACK = path.join(__dirname, '..', 'AUREON-WORDPRESS-DEPLOY', 'frontend', 'designs', 'vineta');

let failures = 0;
let checks = 0;

function readTemplate(rel) {
  const file = path.join(PACK, rel);
  if (!fs.existsSync(file)) {
    console.error(`  MISSING TEMPLATE: ${rel} (referenced by manifest/route map)`);
    failures++;
    return '';
  }
  return fs.readFileSync(file, 'utf8');
}

function assertTemplate(rel, selectors, label) {
  const html = readTemplate(rel);
  if (!html) return;
  for (const sel of selectors) {
    checks++;
    let ok;
    if (sel.startsWith('data-aureon-slot=')) {
      // Attribute-value match: data-aureon-slot="x"
      const attr = sel.slice('data-aureon-slot='.length);
      ok = html.includes(`data-aureon-slot="${attr}"`);
    } else if (sel.startsWith('#')) {
      ok = new RegExp(`id=["']${sel.slice(1)}["']`).test(html);
    } else if (sel.startsWith('tag:')) {
      ok = html.includes(sel.slice(4));
    } else {
      ok = html.includes(`class="${sel}`) || html.includes(` ${sel}"`) || html.includes(` ${sel} `) || html.includes(`class='${sel}`);
    }
    if (!ok) {
      failures++;
      console.error(`  FAIL [${label}] ${rel}: missing "${sel}"`);
    }
  }
}

// ── Templates actually resolved by the route map (manifest.json pages) ──
// 1. Every complete-page template is spliced with WP menus: header nav
//    (box-nav-menu) + footer (footer-menu-list fallback path).
for (const rel of [
  'index.html', 'shop-default.html', 'product-detail.html',
  'blog-grid-01.html', 'blog-single.html', 'about-us.html',
  'contact-us.html', 'faq.html', '404.html',
]) {
  assertTemplate(rel, ['box-nav-menu', 'tag:<footer', 'footer-menu-list'], 'menu-splice');
}

// 2. Homepage dynamic slots (shims consume these; names verified in template).
assertTemplate('index.html', [
  'data-aureon-slot=global.featured_categories',
  'data-aureon-slot=global.categories_tabs',
  'data-aureon-slot=global.featured_products',
  'data-aureon-slot=global.announcement',
  'data-aureon-slot=global.footer',
  'data-aureon-slot=hero',
], 'homepage-slots');

// 3. Shop/category/search (shared shop-default.html; real slot names).
assertTemplate('shop-default.html', [
  'data-aureon-slot=shop.product_grid',
  'data-aureon-slot=shop.product_card',
  'data-aureon-slot=shop.filters',
], 'shop-slots');

// 4. Product page hydration targets. NOTE: .tf-product-info-variation is
// self-healing (bridge creates it inside .tf-product-info-wrap at runtime),
// so the contract is the wrap + the frozen variation slot it hides.
assertTemplate('product-detail.html', [
  'tf-product-info-wrap',
  'data-aureon-slot=product.variation',
  'data-aureon-slot=product.description',
  'data-aureon-slot=product.gallery',
  'data-aureon-slot=product.sku',
  'data-aureon-slot=product.stock',
  'price-new',
  'badge-sale',
], 'product-hydration');

// 5. Cart drawer / mini-cart (shims VinetaCart consumers).
assertTemplate('index.html', ['tf-mini-cart-wrap'], 'cart-drawer');

// 6. Login form (shims: querySelector('#login form.form-login, form.form-login'))
// used by the standalone account/auth flow. The ferm-era #customer_login /
// #submit-login rewrite is dead for vineta (account routes never serve
// ferm-page.php) and is deliberately NOT a contract.
assertTemplate('account-page.html', [
  'tag:id="login"',
  'form-login',
], 'auth-form');

// 7. Logo (shims consume the global.logo slot; the ferm-era .header__logo
// JS swap is dead for vineta). Footer logo class also checked.
assertTemplate('index.html', [
  'data-aureon-slot=global.logo',
  'logo-header',
  'footer-logo',
], 'logo');

// 8. Search (shims + composer bridge consume global.search + form-search).
assertTemplate('index.html', [
  'data-aureon-slot=global.search',
  'form-search',
], 'search');

// 9. Newsletter (shims submit handler targets form-newsletter inputs).
assertTemplate('index.html', [
  'data-aureon-slot=global.newsletter',
  'form-newsletter',
], 'newsletter');

// ── Bridge JS files must not have lost their slot conventions ──
const shims = fs.readFileSync(path.join(PACK, 'js', 'vineta-data-shims.js'), 'utf8');
for (const [needle, label] of [
  ['vineta_cart_add', 'cart ajax action'],
  ["formData.append('nonce'", 'nonce field'],
  ['data-aureon-slot', 'slot consumption'],
]) {
  checks++;
  if (!shims.includes(needle)) {
    failures++;
    console.error(`  FAIL [shims] vineta-data-shims.js missing: ${needle} (${label})`);
  }
}

console.log(`\nSelector contract: ${checks - failures}/${checks} checks passed.`);
if (failures > 0) {
  console.error(`${failures} CONTRACT FAILURES — do not edit the frontend until resolved.`);
  process.exit(1);
}
console.log('CONTRACT OK — bridge dependencies intact.');
