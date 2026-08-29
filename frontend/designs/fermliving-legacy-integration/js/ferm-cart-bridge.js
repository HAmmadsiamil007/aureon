/**
 * Ferm Living — Shopify Shim + WooCommerce Cart Bridge
 *
 * Provides window.Shopify globals that the frozen Ferm JS expects,
 * intercepts cart API calls, and forwards them to WooCommerce AJAX.
 *
 * This file must load BEFORE the frozen app.js.
 *
 * @package Aureon\Designs\FermLiving
 */
(function () {
  'use strict';

  /* ── window.Shopify shim ───────────────────────────────────── */
  window.Shopify = window.Shopify || {};
  window.Shopify.routes = window.Shopify.routes || {};
  window.Shopify.routes.root = '/';
  window.Shopify.currency = window.Shopify.currency || { active: 'EUR', rate: '1.0' };
  window.Shopify.money_format = window.Shopify.money_format || 'EUR {{amount_with_comma_separator}}';
  window.Shopify.shop = window.Shopify.shop || 'ferm-living';
  window.Shopify.locale = window.Shopify.locale || 'en';
  window.Shopify.country = window.Shopify.country || 'DK';

  /* formatMoney — convert cents to formatted string */
  if (!window.Shopify.formatMoney) {
    window.Shopify.formatMoney = function (cents, format) {
      var value = (typeof cents === 'string') ? parseInt(cents, 10) : cents;
      if (isNaN(value)) value = 0;
      var amount = (value / 100).toFixed(2);
      var separator = format && format.indexOf(',') !== -1 ? '.' : ',';
      amount = amount.replace('.', separator);
      return (format || 'EUR {{amount}}').replace('{{amount}}', amount).replace('{{amount_with_comma_separator}}', amount);
    };
  }

  /* __MONEY_FORMAT__ */
  window.__MONEY_FORMAT__ = window.__MONEY_FORMAT__ || window.Shopify.money_format;

  /* shop globals */
  window.shop = window.shop || { klaviyoCompanyId: '', campaign: { threshold: 0 } };

  /* ── Third-party stubs (no-ops) ─────────────────────────────── */
  window._swat = window._swat || {};
  window.SwymCallbacks = window.SwymCallbacks || [];
  window.dataLayer = window.dataLayer || [];

  /* Clerk.io stub */
  window.Clerk = window.Clerk || { 'ContentLoaded': function () {} };

  /* Klaviyo stub */
  window._klOnsite = window._klOnsite || [];
  window.klaviyo = window.klaviyo || [];

  /* Flowbox stub */
  window.flowbox = window.flowbox || function () { (flowbox.q = flowbox.q || []).push(arguments); };
  flowbox.q = flowbox.q || [];

  /* Roomle stub */
  window.Roomle = window.Roomle || {};

  /* ── WooCommerce Cart Bridge ────────────────────────────────── */
  var WC_AJAX_URL = (window.ferm_bridge && window.ferm_bridge.ajax_url) || '/wp-admin/admin-ajax.php';
  var WC_NONCE    = (window.ferm_bridge && window.ferm_bridge.nonce) || '';

  /**
   * Intercept fetch calls to /cart/*.js and redirect to WooCommerce AJAX.
   */
  var _originalFetch = window.fetch;
  window.fetch = function (url, options) {
    var urlStr = (typeof url === 'string') ? url : (url.url || '');
    var method = (options && options.method) || 'GET';

    /* Match Shopify cart endpoints */
    if (urlStr.match(/\/cart\/(add|change|update|clear)\.js/) && method.toUpperCase() === 'POST') {
      return handleCartBridge(urlStr, options);
    }

    /* Match GET /cart.js */
    if (urlStr.match(/\/cart\.js$/) && method.toUpperCase() === 'GET') {
      return handleCartGet();
    }

    return _originalFetch.apply(this, arguments);
  };

  /**
   * Handle POST /cart/add.js → WooCommerce add-to-cart.
   */
  function handleCartBridge(url, options) {
    var body = parseBody(options && options.body);
    var isAdd = url.indexOf('/cart/add.js') !== -1;
    var isChange = url.indexOf('/cart/change.js') !== -1;
    var isUpdate = url.indexOf('/cart/update.js') !== -1;

    if (isAdd) {
      var items = body.items || [body];
      var item = items[0] || {};
      var productId = item.id || body.id;
      var quantity = item.quantity || body.quantity || 1;

      return wcAddToCart(productId, quantity);
    }

    if (isChange || isUpdate) {
      return wcUpdateCart(body);
    }

    return Promise.resolve(jsonResponse({ items_count: 0, items: [], total_price: 0 }));
  }

  /**
   * Handle GET /cart.js → WooCommerce cart state.
   */
  function handleCartGet() {
    return new Promise(function (resolve) {
      _originalFetch(WC_AJAX_URL + '?action=ferm_cart_get', {
        method: 'GET',
        credentials: 'same-origin',
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success && data.data) {
            resolve(jsonResponse(data.data));
          } else {
            resolve(jsonResponse({ items_count: 0, items: [], total_price: 0 }));
          }
        })
        .catch(function () {
          resolve(jsonResponse({ items_count: 0, items: [], total_price: 0 }));
        });
    });
  }

  /**
   * WooCommerce add-to-cart via AJAX.
   */
  function wcAddToCart(productId, quantity) {
    var formData = new FormData();
    formData.append('action', 'ferm_cart_add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity || 1);
    if (WC_NONCE) formData.append('_ajax_nonce', WC_NONCE);

    return _originalFetch(WC_AJAX_URL, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData,
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success && data.data) {
          /* Dispatch cart events */
          document.dispatchEvent(new CustomEvent('cart:update'));
          document.dispatchEvent(new CustomEvent('cart:open'));

          return jsonResponse(data.data);
        }
        return jsonResponse({ items_count: 0, items: [], total_price: 0, error: data.data || 'Add to cart failed' });
      })
      .catch(function () {
        return jsonResponse({ items_count: 0, items: [], total_price: 0, error: 'Network error' });
      });
  }

  /**
   * WooCommerce cart update via AJAX.
   */
  function wcUpdateCart(body) {
    var formData = new FormData();
    formData.append('action', 'ferm_cart_update');
    if (body.updates) formData.append('updates', JSON.stringify(body.updates));
    if (WC_NONCE) formData.append('_ajax_nonce', WC_NONCE);

    return _originalFetch(WC_AJAX_URL, {
      method: 'POST',
      credentials: 'same-origin',
      body: formData,
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success && data.data) {
          document.dispatchEvent(new CustomEvent('cart:update'));
          return jsonResponse(data.data);
        }
        return jsonResponse({ items_count: 0, items: [], total_price: 0 });
      })
      .catch(function () {
        return jsonResponse({ items_count: 0, items: [], total_price: 0 });
      });
  }

  /* ── Utilities ──────────────────────────────────────────────── */
  function parseBody(body) {
    if (!body) return {};
    if (typeof body === 'string') {
      try { return JSON.parse(body); } catch (e) { return {}; }
    }
    if (body instanceof FormData) {
      var obj = {};
      body.forEach(function (v, k) { obj[k] = v; });
      return obj;
    }
    return body;
  }

  function jsonResponse(data) {
    return new Response(JSON.stringify(data), {
      status: 200,
      headers: { 'Content-Type': 'application/json' },
    });
  }

  /* ── shopify:section:load event shim ────────────────────────── */
  /* The frozen JS dispatches this event; we forward it as a no-op */
  if (!window.Shopify || !window.Shopify._sectionLoadFired) {
    window.Shopify = window.Shopify || {};
    window.Shopify._sectionLoadFired = false;
  }

})();
