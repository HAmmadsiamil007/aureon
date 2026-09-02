/**
 * Test Client JavaScript — Isolation Verification
 *
 * This file contains unique identifiers that prove isolation:
 * - Unique global namespace (window.TestClient)
 * - Unique event listeners
 * - Unique function names
 * - Unique initialization
 *
 * @package Aureon Test Client
 */

(function() {
  'use strict';

  // Unique global namespace — testclient only
  window.TestClient = {
    version: '1.0.0',
    initialized: false,
    config: {},
    cart: [],
    notifications: []
  };

  // Unique initialization — testclient only
  function initTestClient() {
    if (window.TestClient.initialized) {
      console.warn('[TestClient] Already initialized — skipping duplicate init');
      return;
    }

    console.log('[TestClient] Initializing test client v' + window.TestClient.version);
    window.TestClient.initialized = true;

    // Load config from FermPageData if available
    if (window.FermPageData && window.FermPageData.config) {
      window.TestClient.config = window.FermPageData.config;
    }

    // Initialize components
    initNavigation();
    initCart();
    initSearch();
    initNewsletter();
    initAnimations();

    console.log('[TestClient] Initialization complete');
  }

  // Unique navigation handler — testclient only
  function initNavigation() {
    var header = document.querySelector('.tc-header');
    if (!header) return;

    // Mobile menu toggle
    var navToggle = header.querySelector('.tc-header__nav-toggle');
    var navMenu = header.querySelector('.tc-header__nav');
    if (navToggle && navMenu) {
      navToggle.addEventListener('click', function() {
        navMenu.classList.toggle('tc-header__nav--open');
        console.log('[TestClient] Navigation toggled');
      });
    }

    // Active state for current page
    var currentPath = window.location.pathname;
    var navLinks = header.querySelectorAll('.tc-header__nav a');
    navLinks.forEach(function(link) {
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('tc-header__nav-link--active');
      }
    });

    console.log('[TestClient] Navigation initialized');
  }

  // Unique cart handler — testclient only
  function initCart() {
    // Listen for cart updates from FermPageData
    if (window.FermPageData && window.FermPageData.cart) {
      window.TestClient.cart = window.FermPageData.cart.items || [];
      updateCartDisplay();
    }

    // Add to cart buttons
    var addToCartButtons = document.querySelectorAll('.tc-product-card__add-to-cart');
    addToCartButtons.forEach(function(button) {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        var productId = this.getAttribute('data-product-id');
        addToCart(productId);
      });
    });

    console.log('[TestClient] Cart initialized');
  }

  // Unique add to cart — testclient only
  function addToCart(productId) {
    console.log('[TestClient] Adding product ' + productId + ' to cart');

    // Show notification
    showNotification('Product added to cart!', 'success');

    // Update cart count
    var cartCount = document.querySelector('.tc-header__cart-count');
    if (cartCount) {
      var current = parseInt(cartCount.textContent) || 0;
      cartCount.textContent = current + 1;
    }
  }

  // Unique update cart display — testclient only
  function updateCartDisplay() {
    var cartItems = document.querySelector('.tc-cart__items');
    if (!cartItems || !window.TestClient.cart.length) return;

    cartItems.innerHTML = '';
    window.TestClient.cart.forEach(function(item) {
      var itemEl = document.createElement('div');
      itemEl.className = 'tc-cart__item tc-animate';
      itemEl.innerHTML = 
        '<img class="tc-cart__item-image" src="' + (item.image || '') + '" alt="' + (item.title || '') + '">' +
        '<div class="tc-cart__item-details">' +
          '<div class="tc-cart__item-title">' + (item.title || '') + '</div>' +
          '<div class="tc-cart__item-price">€' + ((item.price / 100).toFixed(2)) + '</div>' +
          '<div class="tc-cart__item-qty">Qty: ' + (item.quantity || 1) + '</div>' +
        '</div>';
      cartItems.appendChild(itemEl);
    });
  }

  // Unique search handler — testclient only
  function initSearch() {
    var searchInput = document.querySelector('.tc-search__input');
    if (!searchInput) return;

    var debounceTimer;
    searchInput.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function() {
        var query = searchInput.value.trim();
        if (query.length >= 2) {
          console.log('[TestClient] Search query: ' + query);
          performSearch(query);
        }
      }, 300);
    });

    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        var query = searchInput.value.trim();
        if (query) {
          window.location.href = window.TestClient.config.search_url + encodeURIComponent(query);
        }
      }
    });

    console.log('[TestClient] Search initialized');
  }

  // Unique perform search — testclient only
  function performSearch(query) {
    console.log('[TestClient] Performing search for: ' + query);
    // In a real implementation, this would call the search API
    // For testing, we just log the action
  }

  // Unique newsletter handler — testclient only
  function initNewsletter() {
    var form = document.querySelector('.tc-newsletter__form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var email = form.querySelector('.tc-newsletter__input').value;
      if (email && email.includes('@')) {
        console.log('[TestClient] Newsletter signup: ' + email);
        showNotification('Thank you for subscribing!', 'success');
        form.reset();
      } else {
        showNotification('Please enter a valid email address', 'error');
      }
    });

    console.log('[TestClient] Newsletter initialized');
  }

  // Unique animation handler — testclient only
  function initAnimations() {
    // Intersection Observer for fade-in animations
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('tc-animate');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.tc-animate-on-scroll').forEach(function(el) {
        observer.observe(el);
      });
    }

    console.log('[TestClient] Animations initialized');
  }

  // Unique notification handler — testclient only
  function showNotification(message, type) {
    type = type || 'success';
    var notification = document.createElement('div');
    notification.className = 'tc-notification tc-notification--' + type;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Auto-remove after 3 seconds
    setTimeout(function() {
      notification.style.opacity = '0';
      notification.style.transition = 'opacity 0.3s';
      setTimeout(function() {
        notification.remove();
      }, 300);
    }, 3000);

    window.TestClient.notifications.push({
      message: message,
      type: type,
      timestamp: Date.now()
    });
  }

  // Unique utility functions — testclient only
  window.TestClient.utils = {
    formatPrice: function(cents) {
      return '€' + (cents / 100).toFixed(2);
    },
    formatDate: function(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    debounce: function(func, wait) {
      var timeout;
      return function() {
        var context = this;
        var args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          func.apply(context, args);
        }, wait);
      };
    }
  };

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTestClient);
  } else {
    initTestClient();
  }

})();
