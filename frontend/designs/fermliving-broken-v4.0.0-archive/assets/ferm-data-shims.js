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

  // Stub methods — replace with WC AJAX calls
  addItem: function(variantId, quantity) {
    console.log('[FermCart] addItem:', variantId, quantity);
    return Promise.resolve({ items_count: 0, total_price: 0 });
  },

  updateItem: function(key, quantity) {
    console.log('[FermCart] updateItem:', key, quantity);
    return Promise.resolve({ items_count: 0, total_price: 0 });
  },

  changeItem: function(key, quantity) {
    console.log('[FermCart] changeItem:', key, quantity);
    return Promise.resolve({ items_count: 0, total_price: 0 });
  },

  getCart: function() {
    console.log('[FermCart] getCart');
    return Promise.resolve({
      items: [],
      items_count: 0,
      total_price: 0
    });
  },

  clearCart: function() {
    console.log('[FermCart] clearCart');
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
// INITIALIZE
// ============================================================================

console.log('[Ferm] Data shims loaded. Running in standalone mode.');
console.log('[Ferm] Replace this file with WordPress/WooCommerce data during integration.');
