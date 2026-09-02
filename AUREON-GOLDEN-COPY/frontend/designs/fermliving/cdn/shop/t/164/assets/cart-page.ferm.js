/**
 * cart-page.ferm.js — Minimal cart-page hydration bridge
 *
 * Reads window.FermPageData.cart and populates the existing frozen
 * Ferm cart DOM with real WooCommerce cart items.
 *
 * This is a DATA/HYDRATION bridge, NOT a frontend rebuild.
 * It uses Ferm's exact CSS classes and DOM structure.
 */
(function () {
  'use strict';

  /* ── State ────────────────────────────────────────────────────── */
  let cartData = null;

  /* ── Selectors (frozen Ferm DOM) ──────────────────────────────── */
  const SEL = {
    cartMain:      '[data-component="cartMain"]',
    productFlags:  '#cart-product-flags',
    emptyState:    '[data-component="cartMain"] > .limit > p',
    /* Drawer selectors (shared across all pages) */
    drawer:        '#cart-drawer',
    drawerTitle:   '#cart-drawer-title',
    drawerCount:   '#cart-drawer-title span',
    drawerItems:   '#cart-drawer [data-cart-drawer-content] .flex-grow.basis-0',
    drawerFooter:  '#cart-drawer [data-cart-drawer-content] > .hidden.p-4',
    drawerTotal:   '#cart-drawer [data-cart-drawer-content] > .hidden.p-4 .text-base.font-medium',
    drawerCheckout:'#cart-drawer a[href*="checkout"]',
    /* Header cart count */
    headerCount:   '[data-cart-count]',
    headerCountLabel: '[data-cart-count-label]',
    /* Shipping progress */
    shippingText:  '[data-shipping-text]',
    shippingBar:   '[data-bar-container]',
    shippingFill:  '[data-bar-fill]',
    /* Cart page specific */
    pageFooter:    '[data-component="cartMain"] .limit > div:last-child',
  };

  /* ── Helpers ──────────────────────────────────────────────────── */
  function formatPrice(cents) {
    if (typeof cents !== 'number') return 'EUR 0,00';
    return 'EUR ' + (cents / 100).toFixed(2).replace('.', ',');
  }

  function escapeHtml(str) {
    var d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
  }

  /* ── Build cart item HTML (Ferm exact classes) ────────────────── */
  function buildCartItemHTML(item) {
    var title = escapeHtml(item.title || item.product_title || '');
    var price = formatPrice(item.price || 0);
    var linePrice = formatPrice(item.line_price || (item.price * item.quantity));
    var qty = item.quantity || 1;
    var key = escapeHtml(String(item.key || item.id || ''));
    var productId = parseInt(item.product_id || item.id || 0, 10);
    var variantId = parseInt(item.variant_id || item.id || 0, 10);
    var image = item.image || '';
    var url = item.url || '#';
    var variantTitle = escapeHtml(item.variant_title || '');

    return '' +
    '<div class="flex gap-4 border-b border-full-black/5 py-4 md:gap-6 md:py-6" data-cart-item="' + key + '">' +
      /* Image */
      '<a href="' + escapeHtml(url) + '" class="relative aspect-[1/1.2] w-[100px] flex-shrink-0 overflow-hidden md:w-[140px]">' +
        (image
          ? '<img src="' + escapeHtml(image) + '" alt="' + title + '" class="h-full w-full object-cover" loading="lazy">'
          : '<div class="h-full w-full bg-full-black/5"></div>') +
      '</a>' +
      /* Details */
      '<div class="flex flex-1 flex-col justify-between">' +
        '<div>' +
          '<div class="flex items-start justify-between gap-2">' +
            '<a href="' + escapeHtml(url) + '" class="text-sm font-medium leading-[19px] hover:opacity-70 md:text-base">' + title + '</a>' +
            '<button type="button" class="shrink-0 p-1 text-black/50 hover:text-black" data-cart-remove="' + key + '" aria-label="Remove ' + title + '">' +
              '<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.05078 11L10.9503 1.10051" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"/><path d="M1.05078 1L10.9503 10.8995" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"/></svg>' +
            '</button>' +
          '</div>' +
          (variantTitle && variantTitle !== '' && variantTitle !== 'Default Title'
            ? '<div class="mt-0.5 text-xxs uppercase text-full-black/60">' + variantTitle + '</div>'
            : '') +
          '<div class="mt-1 text-xs uppercase text-full-black/75">' + price + '</div>' +
        '</div>' +
        /* Quantity controls */
        '<div class="mt-3 flex items-center gap-3 md:mt-4">' +
          '<div class="flex items-center border border-full-black/10">' +
            '<button type="button" class="flex h-8 w-8 items-center justify-center text-black/60 hover:text-black disabled:opacity-30" data-cart-qty-minus="' + key + '"' + (qty <= 1 ? ' disabled' : '') + '>' +
              '<svg width="10" height="2" viewBox="0 0 10 2"><path d="M0 1h10" stroke="currentColor" stroke-width="1.25"/></svg>' +
            '</button>' +
            '<span class="flex h-8 w-10 items-center justify-center border-x border-full-black/10 text-xs font-medium" data-cart-qty-value="' + key + '">' + qty + '</span>' +
            '<button type="button" class="flex h-8 w-8 items-center justify-center text-black/60 hover:text-black" data-cart-qty-plus="' + key + '">' +
              '<svg width="10" height="10" viewBox="0 0 10 10"><path d="M5 0v10M0 5h10" stroke="currentColor" stroke-width="1.25"/></svg>' +
            '</button>' +
          '</div>' +
          '<div class="ml-auto text-sm font-medium">' + linePrice + '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }

  /* ── Build cart page items container ──────────────────────────── */
  function buildCartPageHTML(cart) {
    if (!cart || !cart.items || cart.items.length === 0) {
      return '<p class="mb-16 border-t border-full-black/5 pt-8 text-sm text-black md:mb-32">No items</p>';
    }

    var html = '';
    /* Items list */
    html += '<div class="mb-8 border-t border-full-black/5">';
    for (var i = 0; i < cart.items.length; i++) {
      html += buildCartItemHTML(cart.items[i]);
    }
    html += '</div>';

    /* Totals + checkout */
    html += '<div class="mb-16">';
    html += '<div class="flex items-center justify-between border-t border-full-black/5 pt-4 pb-2">';
    html += '<div class="text-base text-black">Subtotal</div>';
    html += '<div class="text-base font-medium text-black/80">' + formatPrice(cart.total_price || 0) + '</div>';
    html += '</div>';
    html += '<div class="text-xxs text-black/50 mb-4">Taxes and shipping calculated at checkout</div>';
    html += '<a href="/checkout" class="font-secondary box-border flex h-12 w-fit max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out text-cream bg-black hover:bg-transparent hover:text-black w-full md:w-auto">Go to checkout</a>';
    html += '<a href="/" class="mt-4 inline-block text-sm underline text-black hover:opacity-70">Continue shopping</a>';
    html += '</div>';

    return html;
  }

  /* ── Render cart page ─────────────────────────────────────────── */
  function renderCartPage(cart) {
    var cartMain = document.querySelector(SEL.cartMain);
    if (!cartMain) return;

    var limit = cartMain.querySelector('.limit');
    if (!limit) return;

    /* Find the container: flags div + content after it */
    var flags = limit.querySelector(SEL.productFlags);
    if (!flags) return;

    /* Remove existing content after flags (empty state or previous items) */
    var next = flags.nextElementSibling;
    while (next) {
      var remove = next;
      next = next.nextElementSibling;
      remove.remove();
    }

    /* Update flags */
    var isEmpty = !cart || !cart.items || cart.items.length === 0;
    flags.setAttribute('data-is-empty', isEmpty ? 'true' : 'false');
    flags.setAttribute('data-has-mto', 'false');
    flags.classList.toggle('hidden', true); /* always hidden */

    /* Inject new content */
    var container = document.createElement('div');
    container.innerHTML = buildCartPageHTML(cart);
    while (container.firstChild) {
      limit.appendChild(container.firstChild);
    }
  }

  /* ── Render cart drawer items ─────────────────────────────────── */
  function renderDrawerItems(cart) {
    var drawer = document.querySelector(SEL.drawer);
    if (!drawer) return;

    var itemsContainer = drawer.querySelector('.flex-grow.basis-0');
    if (!itemsContainer) return;

    /* Clear existing */
    itemsContainer.innerHTML = '';

    if (!cart || !cart.items || cart.items.length === 0) {
      /* Empty state — leave container empty (Ferm default) */
      return;
    }

    /* Build items */
    var html = '';
    for (var i = 0; i < cart.items.length; i++) {
      var item = cart.items[i];
      var key = escapeHtml(String(item.key || item.id || ''));
      var title = escapeHtml(item.title || item.product_title || '');
      var price = formatPrice(item.price || 0);
      var qty = item.quantity || 1;
      var image = item.image || '';
      var url = item.url || '#';

      html += '' +
      '<div class="flex gap-4 border-b border-full-black/5 py-4" data-cart-item="' + key + '">' +
        '<a href="' + escapeHtml(url) + '" class="relative aspect-[1/1.2] w-[80px] flex-shrink-0 overflow-hidden">' +
          (image
            ? '<img src="' + escapeHtml(image) + '" alt="' + title + '" class="h-full w-full object-cover" loading="lazy">'
            : '<div class="h-full w-full bg-full-black/5"></div>') +
        '</a>' +
        '<div class="flex flex-1 flex-col justify-between">' +
          '<div>' +
            '<div class="flex items-start justify-between gap-2">' +
              '<div class="text-sm font-medium leading-[19px] line-clamp-1">' + title + '</div>' +
              '<button type="button" class="shrink-0 p-1 text-black/50 hover:text-black" data-cart-remove="' + key + '" aria-label="Remove">' +
                '<svg width="10" height="10" viewBox="0 0 10 10"><path d="M1 1l8 8M9 1l-8 8" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/></svg>' +
              '</button>' +
            '</div>' +
            '<div class="text-xxs uppercase text-full-black/75">' + price + '</div>' +
          '</div>' +
          '<div class="flex items-center gap-2">' +
            '<div class="flex items-center border border-full-black/10">' +
              '<button type="button" class="flex h-6 w-6 items-center justify-center text-black/60 hover:text-black disabled:opacity-30" data-cart-qty-minus="' + key + '"' + (qty <= 1 ? ' disabled' : '') + '>' +
                '<svg width="8" height="2" viewBox="0 0 8 2"><path d="M0 1h8" stroke="currentColor" stroke-width="1"/></svg>' +
              '</button>' +
              '<span class="flex h-6 w-8 items-center justify-center border-x border-full-black/10 text-xxs font-medium">' + qty + '</span>' +
              '<button type="button" class="flex h-6 w-6 items-center justify-center text-black/60 hover:text-black" data-cart-qty-plus="' + key + '">' +
                '<svg width="8" height="8" viewBox="0 0 8 8"><path d="M4 0v8M0 4h8" stroke="currentColor" stroke-width="1"/></svg>' +
              '</button>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
    }

    itemsContainer.innerHTML = html;
  }

  /* ── Update drawer footer ─────────────────────────────────────── */
  function renderDrawerFooter(cart) {
    var drawer = document.querySelector(SEL.drawer);
    if (!drawer) return;

    var footer = drawer.querySelector('.hidden.p-4');
    if (!footer) return;

    var isEmpty = !cart || !cart.items || cart.items.length === 0;
    footer.classList.toggle('hidden', isEmpty);
    footer.style.display = isEmpty ? 'none' : '';

    if (!isEmpty) {
      /* Update total */
      var totalEl = footer.querySelector('.text-base.font-medium');
      if (totalEl) totalEl.textContent = formatPrice(cart.total_price || 0);

      /* Update checkout link */
      var checkoutLink = footer.querySelector('a[href*="checkout"]');
      if (checkoutLink) checkoutLink.href = '/checkout';
    }
  }

  /* ── Update header cart count ─────────────────────────────────── */
  function updateHeaderCount(cart) {
    var count = cart ? (cart.item_count || 0) : 0;
    var countEls = document.querySelectorAll(SEL.headerCount);
    var labelEls = document.querySelectorAll(SEL.headerCountLabel);

    for (var i = 0; i < countEls.length; i++) {
      countEls[i].textContent = count;
      countEls[i].classList.toggle('hidden', count === 0);
    }
    for (var j = 0; j < labelEls.length; j++) {
      labelEls[j].textContent = 'Cart (' + count + ')';
    }

    /* Update drawer title count */
    var drawerCount = document.querySelector(SEL.drawerCount);
    if (drawerCount) drawerCount.textContent = '(' + count + ')';

    /* Update drawer root attribute */
    var drawer = document.querySelector(SEL.drawer);
    if (drawer) drawer.setAttribute('data-cart-drawer-count-number', String(count));
  }

  /* ── Update shipping progress ─────────────────────────────────── */
  function updateShippingProgress(cart) {
    var shippingEl = document.querySelector(SEL.shippingText);
    if (!shippingEl) return;

    var totalCents = cart ? (cart.total_price || 0) : 0;
    var threshold = parseInt(shippingEl.getAttribute('data-free-shipping-threshold') || '15000', 10);

    shippingEl.setAttribute('data-cart-total-price', String(totalCents));

    if (totalCents >= threshold) {
      /* Free shipping reached */
      var msg = shippingEl.querySelector('[data-shipping-message]');
      if (msg) msg.innerHTML = 'You\'ve got <strong>free EU delivery</strong>!';

      var bar = shippingEl.querySelector(SEL.shippingBar);
      if (bar) bar.classList.remove('hidden');

      var fill = shippingEl.querySelector(SEL.shippingFill);
      if (fill) fill.style.width = '100%';
    } else if (totalCents > 0) {
      /* Progress toward free shipping */
      var bar2 = shippingEl.querySelector(SEL.shippingBar);
      if (bar2) bar2.classList.remove('hidden');

      var fill2 = shippingEl.querySelector(SEL.shippingFill);
      if (fill2) {
        var pct = Math.min(100, (totalCents / threshold) * 100);
        fill2.style.width = pct + '%';
      }
    }
  }

  /* ── Full render ──────────────────────────────────────────────── */
  function render(cart) {
    cartData = cart;
    renderCartPage(cart);
    renderDrawerItems(cart);
    renderDrawerFooter(cart);
    updateHeaderCount(cart);
    updateShippingProgress(cart);
  }

  /* ── Event delegation for quantity/remove ─────────────────────── */
  function handleAction(url, updates, successCb) {
    var bridge = window.ferm_bridge || {};
    var params = [
      'action=ferm_cart_update',
      'nonce=' + encodeURIComponent(bridge.nonce || ''),
      'updates=' + encodeURIComponent(JSON.stringify(updates))
    ];
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.join('&')
    })
    .then(function (r) { return r.json(); })
    .then(function (resp) {
      var cart = resp && resp.data ? resp.data : resp;
      if (cart && cart.cart) {
        render(cart.cart);
        if (typeof successCb === 'function') successCb(cart.cart);
      } else if (cart && cart.items) {
        render(cart);
        if (typeof successCb === 'function') successCb(cart);
      }
    })
    .catch(function (err) {
      console.error('[cart-page] Action failed:', err);
    });
  }

  function onDocClick(e) {
    var target = e.target.closest('[data-cart-qty-minus], [data-cart-qty-plus], [data-cart-remove]');
    if (!target) return;

    var key;
    var action;

    if (target.hasAttribute('data-cart-qty-minus')) {
      key = target.getAttribute('data-cart-qty-minus');
      action = 'minus';
    } else if (target.hasAttribute('data-cart-qty-plus')) {
      key = target.getAttribute('data-cart-qty-plus');
      action = 'plus';
    } else if (target.hasAttribute('data-cart-remove')) {
      key = target.getAttribute('data-cart-remove');
      action = 'remove';
    }

    if (!key) return;
    e.preventDefault();

    /* Find current quantity */
    var currentQty = 1;
    if (cartData && cartData.items) {
      for (var i = 0; i < cartData.items.length; i++) {
        if (String(cartData.items[i].key || cartData.items[i].id) === key) {
          currentQty = cartData.items[i].quantity || 1;
          break;
        }
      }
    }

    var newQty;
    if (action === 'remove') {
      newQty = 0;
    } else if (action === 'minus') {
      newQty = Math.max(0, currentQty - 1);
    } else {
      newQty = currentQty + 1;
    }

    /* Use WordPress AJAX */
    var ajaxUrl = (window.ferm_bridge && window.ferm_bridge.ajax_url) || '/wp-admin/admin-ajax.php';
    var updates = {};
    updates[key] = newQty;
    handleAction(ajaxUrl, updates);
  }

  /* ── Listen for FermCart updates (from drawer/header) ─────────── */
  function onFermCartUpdate(cart) {
    render(cart);
  }

  /* ── Init ─────────────────────────────────────────────────────── */
  function init() {
    /* Read initial cart data */
    var data = window.FermPageData;
    if (data && data.cart) {
      render(data.cart);
    }

    /* Delegate click events for quantity/remove */
    document.addEventListener('click', onDocClick, false);

    /* Patch FermCart._updateCartCountUI to also trigger full render */
    if (window.FermCart) {
      var origUpdateUI = window.FermCart._updateCartCountUI;
      window.FermCart._updateCartCountUI = function () {
        if (typeof origUpdateUI === 'function') origUpdateUI.call(window.FermCart);
        /* Rebuild cart from FermCart state */
        var cart = {
          items: window.FermCart.items || [],
          item_count: window.FermCart.item_count || 0,
          total_price: window.FermCart.total_price || 0
        };
        render(cart);
      };
    }
  }

  /* Run on DOM ready */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
