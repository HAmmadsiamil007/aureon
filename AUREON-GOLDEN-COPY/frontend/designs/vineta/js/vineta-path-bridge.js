/**
 * Vineta Path Bridge — rewrites frozen-HTML links to WordPress permalinks.
 *
 * The generic ferm-page.php rewriter handles Shopify-style paths
 * (collections/, products/, blogs/). Vineta uses flat file names
 * (shop-default.html, product-detail.html, etc.) that need their own
 * mapping. This script runs on DOMContentLoaded AFTER the Ferm rewriter
 * and fixes any links the generic rewriter didn't handle correctly.
 *
 * @package Aureon/Vineta
 */
(function () {
  'use strict';

  var S = (window.vineta_bridge && window.vineta_bridge.site_url) ? window.vineta_bridge.site_url.replace(/\/$/, '') : '';

  // Vineta flat-file → WordPress permalink map.
  var MAP = {
    // Shop pages
    'shop-default.html':                '/shop/',
    'shop-left-sidebar.html':           '/shop/',
    'shop-right-sidebar.html':          '/shop/',
    'shop-horizontal-filter.html':      '/shop/',
    'shop-filter-drawer.html':          '/shop/',
    'shop-collection-list.html':        '/shop/',
    'shop-sub-collection.html':         '/shop/',
    'shop-sub-collection-02.html':      '/shop/',
    'shop-grid-3-columns.html':         '/shop/',
    'shop-fullwidth.html':              '/shop/',
    'shop-load-more-button.html':       '/shop/',
    'shop-infinity-scroll.html':        '/shop/',
    'shop-filter-sidebar.html':         '/shop/',
    'shop-filter-hidden.html':          '/shop/',
    'shop-collection.html':             '/shop/',

    // Product pages — DO NOT map product-detail.html here.
    // Data-shims replace these hrefs with real /product/slug/ URLs.
    // Leaving them unmapped means the bridge won't touch them until
    // data-shims overwrite them.
    'product-style-01.html':            '/shop/',
    'product-style-02.html':            '/shop/',
    'product-left-sidebar.html':        '/shop/',
    'product-full-width.html':          '/shop/',
    'product-360-view.html':            '/shop/',
    'product-video.html':               '/shop/',
    'product-sticky-add-cart.html':     '/shop/',

    // Blog pages
    'blog-grid-01.html':                '/blog/',
    'blog-grid-02.html':                '/blog/',
    'blog-grid-03.html':                '/blog/',
    'blog-left-sidebar.html':           '/blog/',
    'blog-right-sidebar.html':          '/blog/',
    'blog-list.html':                   '/blog/',
    'blog-list-01.html':                '/blog/',
    'blog-list-02.html':                '/blog/',
    'blog-single.html':                 '/blog/',
    'blog-masonry.html':                '/blog/',
    'blog-standard.html':               '/blog/',

    // Cart / Checkout
    'view-cart.html':                   '/cart/',
    'cart.html':                        '/cart/',
    'checkout.html':                    '/checkout/',
    'order-complete.html':              '/checkout/',

    // Account
    'account-page.html':                '/my-account/',
    'login.html':                       '/my-account/',
    'register.html':                    '/my-account/',
    'forgot-password.html':             '/my-account/',
    'order-tracking.html':              '/my-account/',

    // Static pages
    'about-us.html':                    '/about-us/',
    'contact-us.html':                  '/contact-us/',
    'faq.html':                         '/faq/',
    'shipping.html':                    '/shipping/',
    'return-and-refund.html':           '/return-and-refund/',
    'privacy-policy.html':              '/privacy-policy/',
    'term-and-condition.html':          '/term-and-condition/',
    'store-location.html':              '/store-location/',
    'cookies.html':                     '/cookies/',
    'wishlist.html':                    '/my-account/',
    'compare.html':                     '/shop/',
    'coming-soon.html':                 '/coming-soon/',
    'error-404.html':                   '/',
    '404.html':                         '/',
    'home-fashion-02.html':             '/',
    'before-you-leave.html':            '/',
    'cart-drawer-v2.html':              '/cart/',
    'cart-empty.html':                  '/cart/',
    'account-addresses.html':           '/my-account/',
    'account-details.html':             '/my-account/',
    'account-orders.html':              '/my-account/',
    'newsletter-popup-02.html':         '/',
    'newsletter-popup-03.html':         '/',
  };

  // Pattern fallbacks for any other flat demo file not in the map.
  function patternRoute(clean) {
    if (/^blog-/.test(clean)) return '/blog/';
    if (/^shop-/.test(clean)) return '/shop/';
    if (/^account-/.test(clean)) return '/my-account/';
    if (/^order-/.test(clean)) return '/my-account/';
    if (/^home-/.test(clean)) return '/';
    if (/^newsletter-/.test(clean)) return '/';
    if (clean === 'before-you-leave.html' || clean === '404.html') return '/';
    // product-* demo pages (NOT product-detail.html) → /shop/
    if (/^product-/.test(clean)) return '/shop/';
    return '';
  }

  function rewriteValue(val) {
    if (!val) return val;
    // Skip anchors, absolute URLs, mailto, tel, javascript
    if (val.charAt(0) === '#' || val.indexOf('http') === 0 ||
        val.indexOf('mailto:') === 0 || val.indexOf('tel:') === 0 ||
        val.indexOf('javascript:') === 0) return val;
    // Skip WordPress admin/API paths (already correct)
    if (val.indexOf('/wp-') === 0 || val.indexOf('/cart') === 0 ||
        val.indexOf('/checkout') === 0 || val.indexOf('/my-account') === 0 ||
        val.indexOf('/product/') === 0 || val.indexOf('/shop') === 0 ||
        val.indexOf('/blog') === 0) return val;
    // Skip pure anchors or empty
    if (val === '/' || val === '#') return val;

    // Strip leading ../ or ./
    var clean = val.replace(/^\.+\//, '');
    // Look up in map
    if (MAP[clean]) {
      return S + MAP[clean];
    }
    // Pattern fallback (blog-list-01.html, product-grid.html, ...)
    var route = patternRoute(clean);
    if (route) {
      return S + route;
    }
    // Final fallback: strip .html, prepend site root
    return S + '/' + clean.replace(/\.html$/, '');
  }

  function rewriteLinks() {
    // Rewrite <a href> links — skip links already set to /product/ by data-shims
    document.querySelectorAll('a[href]').forEach(function (a) {
      // Skip links that data-shims already set to real WooCommerce URLs
      if (a.getAttribute('data-vineta-filled')) return;
      var h = a.getAttribute('href');
      var rewritten = rewriteValue(h);
      if (rewritten !== h) a.href = rewritten;
    });

    // Rewrite <form action> links (checkout, login, register, etc.)
    document.querySelectorAll('form[action]').forEach(function (f) {
      var action = f.getAttribute('action');
      var rewritten = rewriteValue(action);
      if (rewritten !== action) f.action = rewritten;
    });
  }

  // Run after DOMContentLoaded (after the Ferm rewriter)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', rewriteLinks);
  } else {
    rewriteLinks();
  }

  // Observe for dynamically added links — debounced to avoid interfering
  // with data-shims product URL injection.
  if (typeof MutationObserver !== 'undefined') {
    var pending = false;
    var obs = new MutationObserver(function () {
      if (pending) return;
      pending = true;
      setTimeout(function () {
        pending = false;
        rewriteLinks();
      }, 100);
    });
    obs.observe(document.documentElement, { childList: true, subtree: true });
  }
})();
