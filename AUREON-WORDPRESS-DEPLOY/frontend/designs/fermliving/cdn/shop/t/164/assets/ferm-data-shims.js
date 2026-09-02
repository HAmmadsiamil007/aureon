/**
 * Ferm Living — Standalone Data Shims
 * 
 * Provides mock data for standalone template rendering.
 * Replace these with WordPress/WooCommerce data during integration.
 * 
 * Usage: Include this script BEFORE app.js to satisfy global variable requirements.
 */

// ============================================================================
// SHOPIFY COMPATIBILITY SHIM
// ============================================================================

window.Shopify = window.Shopify || {};

window.Shopify.routes = {
  root: '/'
};

window.Shopify.currency = {
  active: 'EUR',
  rate: '1.0'
};

window.Shopify.money_format = 'EUR {{amount_with_comma_separator}}';

window.Shopify.formatMoney = function(cents, format) {
  if (typeof cents === 'string') {
    cents = cents.replace('.', '');
  }
  var value = '';
  var placeholderRegex = /\{\{\s*(\w+)\s*\}\}/;
  var formatString = format || window.Shopify.money_format;

  switch (String(formatString.match(placeholderRegex)[1])) {
    case 'amount':
      value = (cents / 100).toFixed(2);
      break;
    case 'amount_with_comma_separator':
      value = (cents / 100).toFixed(2).replace('.', ',');
      break;
    case 'amount_no_decimals':
      value = Math.round(cents / 100);
      break;
    case 'amount_no_decimals_with_comma_separator':
      value = Math.round(cents / 100).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      break;
    default:
      value = (cents / 100).toFixed(2);
  }

  return formatString.replace(placeholderRegex, value);
};

// Money format global
window.__MONEY_FORMAT__ = 'EUR {{amount_with_comma_separator}}';

// ============================================================================
// SHOP GLOBAL (Klaviyo/Campaign config)
// ============================================================================

window.shop = window.shop || {
  klaviyoCompanyId: 'Wz7REr',
  campaign: {
    threshold: 0
  }
};

// ============================================================================
// CART STATE (Standalone)
// ============================================================================

window.FermCart = window.FermCart || {
  items: [],
  item_count: 0,
  total_price: 0,
  currency: 'EUR',

  _updateCartCountUI: function() {
    var count = window.FermCart.item_count;
    var els = document.querySelectorAll('[data-cart-count]');
    els.forEach(function(el) {
      el.textContent = String(count);
      if (count > 0) { el.classList.remove('hidden'); } else { el.classList.add('hidden'); }
    });
  },

  addItem: function(variantId, quantity) {
    var self = this;
    var bridge = window.ferm_bridge || {};
    var ajaxUrl = bridge.ajax_url || '/wp-admin/admin-ajax.php';
    var body = new FormData();
    body.append('action', 'ferm_cart_add');
    body.append('product_id', variantId);
    body.append('quantity', quantity || 1);
    body.append('nonce', bridge.nonce || '');
    return fetch(ajaxUrl, { method: 'POST', body: body })
      .then(function(r) { return r.json(); })
      .then(function(r) {
        if (r.success && r.data) {
          window.FermCart.items = r.data.items || [];
          window.FermCart.item_count = r.data.item_count || 0;
          window.FermCart.total_price = r.data.total_price || 0;
          self._updateCartCountUI();
        }
        return r.data || { items_count: 0, total_price: 0 };
      });
  },

  updateItem: function(key, quantity) {
    var self = this;
    var bridge = window.ferm_bridge || {};
    var ajaxUrl = bridge.ajax_url || '/wp-admin/admin-ajax.php';
    var updates = {};
    updates[key] = quantity;
    var body = new FormData();
    body.append('action', 'ferm_cart_update');
    body.append('updates', JSON.stringify(updates));
    body.append('nonce', bridge.nonce || '');
    return fetch(ajaxUrl, { method: 'POST', body: body })
      .then(function(r) { return r.json(); })
      .then(function(r) {
        if (r.success && r.data) {
          window.FermCart.items = r.data.items || [];
          window.FermCart.item_count = r.data.item_count || 0;
          window.FermCart.total_price = r.data.total_price || 0;
          self._updateCartCountUI();
        }
        return r.data || { items_count: 0, total_price: 0 };
      });
  },

  changeItem: function(key, quantity) {
    return window.FermCart.updateItem(key, quantity);
  },

  getCart: function() {
    var self = this;
    var bridge = window.ferm_bridge || {};
    var ajaxUrl = bridge.ajax_url || '/wp-admin/admin-ajax.php';
    var body = new FormData();
    body.append('action', 'ferm_cart_get');
    body.append('nonce', bridge.nonce || '');
    return fetch(ajaxUrl, { method: 'POST', body: body })
      .then(function(r) { return r.json(); })
      .then(function(r) {
        if (r.success && r.data) {
          window.FermCart.items = r.data.items || [];
          window.FermCart.item_count = r.data.item_count || 0;
          window.FermCart.total_price = r.data.total_price || 0;
          self._updateCartCountUI();
        }
        return r.data || { items: [], items_count: 0, total_price: 0 };
      });
  },

  clearCart: function() {
    return Promise.resolve({ items: [], items_count: 0, total_price: 0 });
  }
};

// ============================================================================
// SHOPIFY CART API INTERCEPT
// ============================================================================

// Intercept fetch calls to Shopify cart endpoints and route to FermCart stubs
(function() {
  var originalFetch = window.fetch;
  var cartEndpoints = /\/cart\/(add|change|update|clear)\.js/;

  window.fetch = function(url, options) {
    if (typeof url === 'string' && cartEndpoints.test(url)) {
      var match = url.match(/\/cart\/(\w+)\.js/);
      var action = match ? match[1] : 'unknown';
      console.log('[FermCart] Intercepted Shopify cart endpoint:', action);

      // Parse request body
      var body = {};
      if (options && options.body) {
        try {
          body = JSON.parse(options.body);
        } catch(e) {
          body = {};
        }
      }

      // Route to appropriate FermCart method
      switch(action) {
        case 'add':
          return FermCart.addItem(body.items ? body.items[0].id : null, body.items ? body.items[0].quantity : 1)
            .then(function(result) {
              return { json: function() { return Promise.resolve(result); } };
            });
        case 'change':
          return FermCart.changeItem(body.id, body.quantity)
            .then(function(result) {
              return { json: function() { return Promise.resolve(result); } };
            });
        case 'update':
          return FermCart.updateItem(body.id, body.quantity)
            .then(function(result) {
              return { json: function() { return Promise.resolve(result); } };
            });
        case 'clear':
          return FermCart.clearCart()
            .then(function(result) {
              return { json: function() { return Promise.resolve(result); } };
            });
      }
    }

    // For non-cart requests, use original fetch
    return originalFetch.apply(this, arguments);
  };
})();

// ============================================================================
// SECTION RENDERING STUB
// ============================================================================

// When cart/add.js is called with sections param, return rendered HTML fragments
window.FermSectionRenderer = window.FermSectionRenderer || {
  renderCartDrawer: function() {
    return '<div data-cart-drawer-content><p class="text-center py-8">Cart is empty</p></div>';
  },

  renderMainCart: function() {
    return '<div data-cart-main-content><p class="text-center py-8">Cart is empty</p></div>';
  },

  getSectionsResponse: function() {
    return {
      'cart-drawer': this.renderCartDrawer(),
      'main-cart': this.renderMainCart()
    };
  }
};

// ============================================================================
// THIRD-PARTY STUBS
// ============================================================================

// Clerk.io
window.Clerk = window.Clerk || function() {};
window.Clerk('ready', function() {});

// Klaviyo
window._klOnsite = window._klOnsite || [];
window.klaviyo = window.klaviyo || function() {
  (window._klOnsite = window._klOnsite || []).push(arguments);
};

// Swym/Wishlist
window._swat = window._swat || {
  init: function() {},
  registerProductPageView: function() {},
  addToWishList: function() {},
  removeFromWishList: function() {},
  isProductInWishList: function() { return false; },
  getWishListItems: function() { return Promise.resolve([]); }
};
window.SwymCallbacks = window.SwymCallbacks || [];

// Roomle
window.Roomle = window.Roomle || function() {
  return { init: function() {} };
};

// Ablyft (A/B testing)
window.ablyftTrack = window.ablyftTrack || function() {};

// DataLayer
window.dataLayer = window.dataLayer || [];
window.dataLayer.push = window.dataLayer.push || function() {};

// ============================================================================
// PRODUCT DATA SHIMS (for standalone template rendering)
// ============================================================================

window.FermProducts = window.FermProducts || {
  // Mock product data — replace with WP_Query results
  products: [
    {
      id: 1001,
      title: 'Rico Sofa 2 in Bouclé Off White',
      handle: 'rico-sofa-2-boucle-off-white',
      price: 359500,
      compare_at_price: null,
      sku: '232015000',
      available: true,
      inventory_quantity: 15,
      images: [
        { src: '/cdn/shop/files/232015000_RicoSofa2_BCL_OWH_Front.jpg', alt: 'Rico Sofa 2' },
        { src: '/cdn/shop/files/232015000_RicoSofa2_BCL_OWH_Angle.jpg', alt: 'Rico Sofa 2 angle' }
      ],
      variants: [
        { id: 100101, title: 'Default', price: 359500, available: true, option1: 'Off White' }
      ],
      options: [{ name: 'Color', values: ['Off White'] }],
      type: 'Sofa',
      vendor: 'ferm LIVING',
      tags: ['new', 'bestseller']
    },
    {
      id: 1002,
      title: 'Meridian Lamp in Black',
      handle: 'meridian-lamp-black',
      price: 59950,
      compare_at_price: null,
      sku: '233001000',
      available: true,
      inventory_quantity: 42,
      images: [
        { src: '/cdn/shop/files/233001000_MeridianLamp_BLK_Front.jpg', alt: 'Meridian Lamp' }
      ],
      variants: [
        { id: 100201, title: 'Default', price: 59950, available: true, option1: 'Black' }
      ],
      options: [{ name: 'Color', values: ['Black'] }],
      type: 'Lamp',
      vendor: 'ferm LIVING',
      tags: ['new']
    }
  ],

  getByHandle: function(handle) {
    return this.products.find(function(p) { return p.handle === handle; });
  },

  getById: function(id) {
    return this.products.find(function(p) { return p.id === id; });
  }
};

// ============================================================================
// COLLECTION DATA SHIMS
// ============================================================================

window.FermCollections = window.FermCollections || {
  collections: [
    {
      id: 2001,
      title: 'Furniture',
      handle: 'furniture',
      description: '<p>Timeless furniture for modern living.</p>',
      image: '/cdn/shop/files/collection-furniture.jpg',
      products_count: 45
    },
    {
      id: 2002,
      title: 'Lighting',
      handle: 'lighting',
      description: '<p>Illuminate your space with design lighting.</p>',
      image: '/cdn/shop/files/collection-lighting.jpg',
      products_count: 32
    },
    {
      id: 2003,
      title: 'Accessories',
      handle: 'accessories',
      description: '<p>Complete your home with accessories.</p>',
      image: '/cdn/shop/files/collection-accessories.jpg',
      products_count: 67
    }
  ],

  getByHandle: function(handle) {
    return this.collections.find(function(c) { return c.handle === handle; });
  }
};

// ============================================================================
// NAVIGATION DATA SHIMS
// ============================================================================

window.FermNavigation = window.FermNavigation || {
  main: [
    {
      title: 'Shop',
      url: '/collections/all',
      children: [
        { title: 'Furniture', url: '/collections/furniture' },
        { title: 'Lighting', url: '/collections/lighting' },
        { title: 'Accessories', url: '/collections/accessories' },
        { title: 'Rugs', url: '/collections/rugs' },
        { title: 'Kitchen', url: '/collections/kitchen' }
      ]
    },
    {
      title: 'Inspiration',
      url: '/blogs/stories',
      children: [
        { title: 'Stories', url: '/blogs/stories' },
        { title: 'Room Inspiration', url: '/pages/room-inspiration' }
      ]
    },
    {
      title: 'Rooms',
      url: '/pages/room-inspiration',
      children: [
        { title: 'Living Room', url: '/collections/furniture?room=living' },
        { title: 'Bedroom', url: '/collections/furniture?room=bedroom' },
        { title: 'Kitchen', url: '/collections/kitchen' },
        { title: 'Bathroom', url: '/collections/accessories?room=bathroom' }
      ]
    },
    {
      title: 'Professionals',
      url: '/pages/professionals'
    }
  ],

  footer: [
    { title: 'About ferm LIVING', url: '/pages/about-ferm-living' },
    { title: 'Contact', url: '/pages/contact' },
    { title: 'Store Locator', url: '/pages/store-locator' },
    { title: 'FAQ', url: '/pages/faq' },
    { title: 'Terms & Conditions', url: '/pages/terms-conditions' },
    { title: 'Privacy Policy', url: '/pages/privacy-policy' }
  ]
};

// ============================================================================
// USP BAR DATA
// ============================================================================

window.FermUSPs = window.FermUSPs || [
  { text: 'Free shipping on orders over €150', url: '/pages/shipping' },
  { text: '30-day return policy', url: '/pages/returns' },
  { text: 'Secure payment with Klarna', url: '/pages/payment' },
  { text: 'Sustainable materials and production', url: '/pages/sustainability' }
];

// ============================================================================
// CUSTOMER DATA SHIM
// ============================================================================

window.FermCustomer = window.FermCustomer || {
  logged_in: false,
  id: null,
  email: null,
  first_name: null,
  last_name: null,
  addresses: []
};

// ============================================================================
// INITIALIZE — Use FermPageData when available (WordPress integration)
// ============================================================================

(function() {
  var data = window.FermPageData;
  if (!data) {
    console.log('[Ferm] Data shims loaded. Running in standalone mode.');
    return;
  }

  // Merge cart data.
  if (data.cart) {
    window.FermCart.items = data.cart.items || [];
    window.FermCart.item_count = data.cart.item_count || 0;
    window.FermCart.total_price = data.cart.total_price || 0;
    window.FermCart.currency = data.cart.currency || 'EUR';
  }

  // Merge customer data.
  if (data.customer) {
    window.FermCustomer.logged_in = data.customer.logged_in || false;
    window.FermCustomer.id = data.customer.id;
    window.FermCustomer.email = data.customer.email;
    window.FermCustomer.first_name = data.customer.first_name;
    window.FermCustomer.last_name = data.customer.last_name;
    window.FermCustomer.addresses = data.customer.addresses || [];
  }

  // Merge navigation data.
  if (data.navigation) {
    if (data.navigation.main) {
      window.FermNavigation.main = data.navigation.main;
    }
    if (data.navigation.footer) {
      window.FermNavigation.footer = data.navigation.footer;
    }
  }

  // Update Shopify shim from real data.
  if (data.shop) {
    window.Shopify.currency.active = data.shop.currency || 'EUR';
    window.Shopify.money_format = data.shop.money_format || 'EUR {{amount_with_comma_separator}}';
    window.__MONEY_FORMAT__ = data.shop.money_format || 'EUR {{amount_with_comma_separator}}';
  }

  // Update body data attributes from config.
  if (data.config && data.config.template) {
    document.body.setAttribute('data-template', data.config.template);
  }
  if (data.config && data.config.money_format) {
    document.body.setAttribute('data-money-format', data.config.money_format);
  }

  console.log('[Ferm] FermPageData loaded. Running in WordPress integration mode.');
})();

// ============================================================
// PRODUCT DOM BRIDGE
// Updates frozen Ferm product DOM with real WooCommerce data
// from FermPageData.product.
// ============================================================
(function() {
  var pd = window.FermPageData;
  if (!pd || !pd.product) return;

  var product = pd.product;

  // Find the product section in the frozen DOM.
  var productSection = document.querySelector('[data-component="productPage"]') ||
                       document.querySelector('[data-section-type="product"]') ||
                       document.querySelector('.product-page');
  if (!productSection) return; // Not a product page.

  // Update product ID data-attributes.
  var idElements = productSection.querySelectorAll('[data-product-id]');
  idElements.forEach(function(el) {
    el.setAttribute('data-product-id', product.id);
  });

  // Update variant ID data-attributes.
  var variantElements = productSection.querySelectorAll('[data-variant-id]');
  variantElements.forEach(function(el) {
    var variantId = product.variant_id || product.id;
    el.setAttribute('data-variant-id', variantId);
  });

  // Update SKU display.
  var skuElements = productSection.querySelectorAll('[data-sku]');
  skuElements.forEach(function(el) {
    el.textContent = product.sku || '';
    el.setAttribute('data-sku', product.sku || '');
  });

  // Update product title (exclude the addToCart container to preserve its children).
  var titleElements = productSection.querySelectorAll('[data-product-title]');
  titleElements.forEach(function(el) {
    // Skip the addToCart container — setting textContent would destroy its children.
    if (el.getAttribute('data-component') === 'addToCart') return;
    el.textContent = product.title;
    el.setAttribute('data-product-title', product.title);
  });

  // Update h1 heading.
  var h1 = productSection.querySelector('h1');
  if (h1 && product.title) {
    h1.textContent = product.title;
  }

  // Update price display.
  var priceElements = productSection.querySelectorAll('[data-product-price]');
  priceElements.forEach(function(el) {
    el.setAttribute('data-product-price', product.price);
  });

  // Update price HTML if available.
  var priceHtmlEl = productSection.querySelector('.product-price, [data-price]');
  if (priceHtmlEl && product.price_html) {
    priceHtmlEl.innerHTML = product.price_html;
  }

  // Update compare/sale price.
  var compareEl = productSection.querySelector('[data-compare-price]');
  if (compareEl) {
    if (product.compare_at_price && product.compare_at_price > product.price) {
      var fmt = window.Shopify && Shopify.formatMoney
        ? Shopify.formatMoney(product.compare_at_price, Shopify.money_format)
        : (product.compare_at_price / 100).toFixed(2);
      compareEl.textContent = fmt;
      compareEl.classList.remove('hidden');
    } else {
      compareEl.classList.add('hidden');
    }
  }

  // Update gallery images.
  if (product.gallery && product.gallery.length > 0) {
    var mainImg = productSection.querySelector('[data-featured-image-container] img') ||
                  productSection.querySelector('.product-gallery img') ||
                  productSection.querySelector('.product__top img');
    if (mainImg && product.gallery[0].src) {
      mainImg.src = product.gallery[0].src;
      mainImg.alt = product.gallery[0].alt || product.title;
    }

    // Update variant image mapping.
    var mediaEl = productSection.querySelector('[data-media]');
    if (mediaEl) {
      var mediaData = product.gallery.map(function(img) {
        return { src: img.src, alt: img.alt || product.title };
      });
      mediaEl.setAttribute('data-media', JSON.stringify(mediaData));
    }
  }

  // Update availability/CTA state.
  var ctaState = productSection.querySelector('[data-cta-state]');
  if (ctaState) {
    if (product.availability === 'in-stock' || product.availability === 'low-stock') {
      ctaState.setAttribute('data-cta-state', 'add');
    } else {
      ctaState.setAttribute('data-cta-state', 'sold-out');
    }
  }

  // Update store name.
  var storeEl = productSection.querySelector('[data-store-name]');
  if (storeEl && pd.shop) {
    storeEl.setAttribute('data-store-name', pd.shop.name || '');
  }

  // --- addToCart section-render prevention ---
  // The frozen HTML's visible addToCart has data-cart-template="" which
  // signals Shopify section rendering. The lazy webpack chunks detect this,
  // fetch from Shopify's section render API, and when it fails (no Shopify),
  // the container is emptied. Fix: remove data-cart-template BEFORE app.js
  // runs so section rendering never triggers. This is deterministic — no
  // timers, no observers, no DOM reconstruction.
  var addToCart = productSection.querySelector('[data-component="addToCart"]');
  if (addToCart) {
    addToCart.removeAttribute('data-cart-template');
  }

  console.log('[Ferm] Product DOM bridge applied. Product:', product.title);
})();

// ============================================================
// VARIANT SELECTION BRIDGE
// Handles color swatch clicks for variable products.
// Updates price, SKU, image, variant ID, availability from
// FermPageData.product.variants.
// ============================================================
(function() {
  var pd = window.FermPageData;
  if (!pd || !pd.product || !pd.product.variants || pd.product.variants.length === 0) return;

  var product = pd.product;
  var variants = product.variants;
  var productSection = document.querySelector('[data-component="productPage"]') ||
                       document.querySelector('[data-section-type="product"]');
  if (!productSection) return;

  var selectedVariantId = product.selected_variant_id || variants[0].id;

  function getVariantById(id) {
    for (var i = 0; i < variants.length; i++) {
      if (variants[i].id === id) return variants[i];
    }
    return null;
  }

  function getVariantByOption(optionValue) {
    for (var i = 0; i < variants.length; i++) {
      if (variants[i].option1 && variants[i].option1.toLowerCase() === optionValue.toLowerCase()) {
        return variants[i];
      }
    }
    return null;
  }

  function formatPrice(cents) {
    if (typeof Shopify !== 'undefined' && Shopify.formatMoney) {
      return Shopify.formatMoney(cents, Shopify.money_format);
    }
    return '$' + (cents / 100).toFixed(2);
  }

  function applyVariant(variant) {
    if (!variant) return;
    selectedVariantId = variant.id;

    // Update variant ID on all relevant elements.
    productSection.querySelectorAll('[data-variant-id]').forEach(function(el) {
      el.setAttribute('data-variant-id', variant.id);
    });

    // Update SKU.
    productSection.querySelectorAll('[data-sku]').forEach(function(el) {
      el.textContent = variant.sku || '';
      el.setAttribute('data-sku', variant.sku || '');
    });

    // Update price.
    var priceEl = productSection.querySelector('.product-price, [data-price], [data-component="price"]');
    if (priceEl) {
      priceEl.textContent = formatPrice(variant.price);
    }
    productSection.querySelectorAll('[data-product-price]').forEach(function(el) {
      el.setAttribute('data-product-price', variant.price);
    });

    // Update variant price attribute on addToCart.
    var addToCart = productSection.querySelector('[data-component="addToCart"]');
    if (addToCart) {
      addToCart.setAttribute('data-product-price', variant.price);
    }

    // Update variant image.
    if (variant.featured_image && variant.featured_image.src) {
      var mainImg = productSection.querySelector('[data-featured-image-container] img') ||
                    productSection.querySelector('.product-gallery img') ||
                    productSection.querySelector('.product__top img');
      if (mainImg) {
        mainImg.src = variant.featured_image.src;
        mainImg.alt = variant.featured_image.alt || product.title;
      }
    }

    // Update availability.
    var ctaState = productSection.querySelector('[data-cta-state]');
    if (ctaState) {
      ctaState.setAttribute('data-cta-state', variant.available ? 'add' : 'sold-out');
    }

    // Update color name display.
    var colorNameEls = productSection.querySelectorAll('.text-right.text-sm');
    colorNameEls.forEach(function(el) {
      if (variant.option1) el.textContent = variant.option1;
    });

    // Update product info data attributes.
    productSection.querySelectorAll('[data-product-id]').forEach(function(el) {
      el.setAttribute('data-product-id', product.id);
    });

    console.log('[Ferm] Variant selected:', variant.id, variant.option1, formatPrice(variant.price));
  }

  // --- Inject color swatches from FermPageData.product.colors ---
  // The frozen HTML may have hardcoded swatches for a different product.
  // Replace them with real WC variant swatches.
  var productColors = product.colors || [];
  if (productColors.length > 0) {
    var addToCart = productSection.querySelector('[data-component="addToCart"]');
    if (addToCart) {
      // Find the existing swatch container (the flex wrap div inside addToCart).
      var swatchContainer = addToCart.querySelector('.flex.items-center.flex-wrap') ||
                            addToCart.querySelector('.flex.items-center.mb-2\.5');
      if (swatchContainer) {
        // Clear existing swatches.
        swatchContainer.innerHTML = '';

        // Build new swatches.
        productColors.forEach(function(color, ci) {
          var isActive = (color.name === (product.color_name || productColors[0].name));
          var outlineClass = isActive ? 'outline outline-1 border-2 outline-black' : 'border border-black/5';

          var a = document.createElement('a');
          a.href = '#';
          a.className = 'relative rotate-45 cursor-pointer overflow-hidden rounded-full p-0 h-5 w-5 ' + outlineClass;
          a.setAttribute('data-color-handle', color.handle || color.name.toLowerCase());
          a.setAttribute('data-hex', color.hex || '#ccc');
          a.title = color.name;
          a.style.order = (ci + 1).toString();

          var div = document.createElement('div');
          div.className = 'absolute h-full w-full';
          div.style.backgroundColor = color.hex || '#ccc';
          a.appendChild(div);

          swatchContainer.appendChild(a);
        });

        // Update color name display.
        var colorNameEl = addToCart.querySelector('.text-right.text-sm');
        if (colorNameEl && product.color_name) {
          colorNameEl.textContent = product.color_name;
        }

        console.log('[Ferm] Injected', productColors.length, 'color swatches');
      }
    }
  }

  // --- Attach click handlers to color swatches ---
  var swatches = productSection.querySelectorAll('[data-color-handle], [data-hex]');
  swatches.forEach(function(swatch) {
    swatch.addEventListener('click', function(e) {
      e.preventDefault();
      var handle = swatch.getAttribute('data-color-handle') || '';
      var variant = getVariantByOption(handle);
      if (variant) {
        applyVariant(variant);

        // Update active swatch styling.
        swatches.forEach(function(s) {
          s.classList.remove('outline', 'outline-1', 'border-2', 'outline-black');
          s.classList.add('border', 'border-black/5');
        });
        swatch.classList.remove('border', 'border-black/5');
        swatch.classList.add('outline', 'outline-1', 'border-2', 'outline-black');
      }
    });
  });

  // Apply default variant.
  var defaultVariant = getVariantById(selectedVariantId);
  if (defaultVariant) {
    applyVariant(defaultVariant);
    // Highlight the first swatch.
    if (swatches.length > 0) {
      swatches[0].classList.remove('border', 'border-black/5');
      swatches[0].classList.add('outline', 'outline-1', 'border-2', 'outline-black');
    }
  }

  console.log('[Ferm] Variant selection bridge applied. Variants:', variants.length);
})();

// ============================================================
// COLLECTION / ARCHIVE BRIDGE
// Replaces hardcoded frozen Ferm product thumbs with real WC
// products from FermPageData.collection.
// Supports both real WooCommerce products and demo products.
// ============================================================
(function() {
  var pd = window.FermPageData;
  if (!pd || !pd.collection) return;

  var collection = pd.collection;
  var products = collection.products || [];
  if (products.length === 0) return;

  // Find the collection template container.
  var collectionSection = document.querySelector('[data-component="collectionTemplate"]');
  if (!collectionSection) return;

  // Update collection title.
  var h1 = collectionSection.querySelector('h1');
  if (h1 && collection.title) {
    h1.textContent = collection.title;
  }

  // Update product count if displayed.
  var countEl = collectionSection.querySelector('[data-product-count]');
  if (countEl) {
    countEl.textContent = products.length + ' products';
  }

  // Find the product grid — the container holding productThumb elements.
  var productGrid = null;
  var thumbs = collectionSection.querySelectorAll('[data-component="productThumb"]');
  if (thumbs.length > 0) {
    productGrid = thumbs[0].parentElement;
  }
  if (!productGrid) return;

  // Clear existing hardcoded thumbs.
  productGrid.innerHTML = '';

  // Build new product thumbs from real WC data.
  products.forEach(function(product) {
    var div = document.createElement('div');
    div.className = 'product product-thumb h-[100%] relative';
    div.setAttribute('data-component', 'productThumb');

    var inner = document.createElement('div');
    inner.className = 'flex flex-col gap-4 h-[100%]';

    // Top section with image.
    var top = document.createElement('div');
    top.className = 'product-thumb__top group relative tab_p:aspect-[1/1.33] aspect-[1/1.53]';

    // Carousel container.
    var carousel = document.createElement('div');
    carousel.className = 'product-thumb-carousel absolute inset-0';
    carousel.setAttribute('data-component', 'productThumbCarousel');
    carousel.setAttribute('data-product-title', product.title);

    var viewport = document.createElement('div');
    viewport.className = 'embla__viewport h-full w-full overflow-hidden';

    var container = document.createElement('div');
    container.className = 'embla__container flex h-full touch-pan-y';

    var slide = document.createElement('div');
    slide.className = 'embla__slide relative min-w-0 flex-[0_0_100%]';

    var link = document.createElement('a');
    link.href = product.url;
    link.setAttribute('aria-label', product.title);
    link.className = 'product__top block h-full w-full text-black no-underline';
    link.setAttribute('data-product-click', 'true');
    link.setAttribute('data-product-title', product.title);
    link.setAttribute('data-product-price', product.price);
    link.setAttribute('data-product-id', product.id);

    var img = document.createElement('img');
    img.src = product.image || '';
    img.alt = product.title;
    img.className = 'absolute top-0 left-0 h-full w-full object-cover';
    img.setAttribute('width', '600');
    img.setAttribute('height', '800');
    img.setAttribute('loading', 'lazy');
    img.setAttribute('sizes', '(min-width: 992px) 25vw, 50vw');

    link.appendChild(img);
    slide.appendChild(link);
    container.appendChild(slide);
    viewport.appendChild(container);
    carousel.appendChild(viewport);
    top.appendChild(carousel);

    // Badge.
    if (product.badge) {
      var badge = document.createElement('div');
      badge.className = 'absolute right-0 top-1 z-10 mr-1.5';
      badge.innerHTML = '<div class="bg-cream flex items-center justify-center px-1.5 py-[9px] text-xxs uppercase text-black">' + product.badge + '</div>';
      top.appendChild(badge);
    }

    inner.appendChild(top);

    // Bottom info.
    var bottom = document.createElement('div');
    bottom.className = 'flex flex-col gap-1';

    var titleEl = document.createElement('div');
    titleEl.className = 'text-sm font-medium leading-[19px] line-clamp-2';
    titleEl.textContent = product.title;
    bottom.appendChild(titleEl);

    var priceEl = document.createElement('div');
    priceEl.className = 'text-xxs uppercase leading-[15px] text-black/75';
    priceEl.textContent = product.price_html || '';
    bottom.appendChild(priceEl);

    inner.appendChild(bottom);
    div.appendChild(inner);
    productGrid.appendChild(div);
  });

  console.log('[Ferm] Collection bridge applied.', products.length, 'products rendered.');
})();
