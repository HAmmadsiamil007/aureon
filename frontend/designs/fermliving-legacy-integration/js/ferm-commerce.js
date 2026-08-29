/**
 * Ferm Living Design Pack — Commerce JS
 *
 * Handles cart quantity updates (WC AJAX), checkout field interactions,
 * account tab navigation, and search live suggestions.
 *
 * @package Aureon\Designs\FermLiving
 */

(function () {
	'use strict';

	var config = window.fermCartData || window.fermCheckoutData || window.fermAccountData || window.fermSearchData || {};
	var ajaxUrl = config.ajax_url || '/wp-admin/admin-ajax.php';
	var nonce = config.nonce || '';

	/* ======================================================================
	   1. CART: QUANTITY UPDATES
	   ====================================================================== */

	var cartEl = document.querySelector('[data-ferm-cart]');
	if (cartEl) {
		/* Quantity +/- buttons */
		cartEl.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-action]');
			if (!btn) return;

			var action = btn.getAttribute('data-action');
			var wrap = btn.closest('.ferm-cart__item-qty');
			if (!wrap) return;

			var input = wrap.querySelector('.ferm-cart__qty-input');
			if (!input) return;

			var key = input.getAttribute('data-cart-item-key');
			var current = parseInt(input.value, 10) || 1;

			if (action === 'plus') {
				input.value = current + 1;
			} else if (action === 'minus') {
				if (current > 1) {
					input.value = current - 1;
				}
			} else if (action === 'remove') {
				if (!confirm(config.i18n && config.i18n.remove_confirm || 'Are you sure you want to remove this item?')) {
					return;
				}
			}

			fermUpdateCartItem(key, input.value, action === 'remove');
		});

		/* Direct quantity input change */
		cartEl.addEventListener('change', function (e) {
			if (!e.target.classList.contains('ferm-cart__qty-input')) return;
			var key = e.target.getAttribute('data-cart-item-key');
			var qty = parseInt(e.target.value, 10);
			if (isNaN(qty) || qty < 1) {
				qty = 1;
				e.target.value = 1;
			}
			fermUpdateCartItem(key, qty, false);
		});

		/* Coupon apply */
		var couponBtn = cartEl.querySelector('[data-ferm-coupon-apply]');
		if (couponBtn) {
			couponBtn.addEventListener('click', function () {
				var input = cartEl.querySelector('[data-ferm-coupon-input]');
				if (!input || !input.value.trim()) return;
				fermApplyCoupon(input.value.trim());
			});
		}

		/* Coupon input enter key */
		var couponInput = cartEl.querySelector('[data-ferm-coupon-input]');
		if (couponInput) {
			couponInput.addEventListener('keydown', function (e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					if (couponInput.value.trim()) {
						fermApplyCoupon(couponInput.value.trim());
					}
				}
			});
		}
	}

	function fermUpdateCartItem(key, qty, remove) {
		fermShowUpdating(true);

		fetch(ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'action=ferm_cart_update&nonce=' + encodeURIComponent(nonce) + '&cart_item_key=' + encodeURIComponent(key) + '&quantity=' + encodeURIComponent(qty) + '&remove=' + (remove ? '1' : '0')
		})
		.then(function (r) { return r.json(); })
		.then(function (res) {
			if (res.success) {
				if (remove) {
					var itemEl = document.querySelector('[data-ferm-cart-item="' + key + '"]');
					if (itemEl) itemEl.remove();
				}
				fermUpdateSummary(res.data);
				if (res.data.is_empty) {
					fermShowEmpty();
				}
			} else {
				fermShowUpdating(false);
			}
		})
		.catch(function () {
			fermShowUpdating(false);
		});
	}

	function fermApplyCoupon(code) {
		fermShowUpdating(true);

		fetch(ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'action=ferm_cart_coupon&nonce=' + encodeURIComponent(nonce) + '&coupon_code=' + encodeURIComponent(code)
		})
		.then(function (r) { return r.json(); })
		.then(function (res) {
			if (res.success) {
				fermUpdateSummary(res.data);
			} else {
				fermShowUpdating(false);
				alert(res.data || 'Invalid coupon code.');
			}
		})
		.catch(function () {
			fermShowUpdating(false);
		});
	}

	function fermUpdateSummary(data) {
		var subtotalEl = document.querySelector('[data-ferm-cart-subtotal]');
		var shippingEl = document.querySelector('[data-ferm-cart-shipping]');
		var totalEl = document.querySelector('[data-ferm-cart-total]');

		if (subtotalEl && data.subtotal) subtotalEl.textContent = data.subtotal;
		if (shippingEl && data.shipping) shippingEl.textContent = data.shipping;
		if (totalEl && data.total) totalEl.textContent = data.total;

		fermShowUpdating(false);
	}

	function fermShowEmpty() {
		var itemsEl = document.querySelector('[data-ferm-cart-items]');
		var couponEl = document.querySelector('.ferm-cart__coupon');
		var summaryEl = document.querySelector('[data-ferm-cart-summary]');
		var emptyEl = document.querySelector('[data-ferm-cart-empty]');

		if (itemsEl) itemsEl.style.display = 'none';
		if (couponEl) couponEl.style.display = 'none';
		if (summaryEl) summaryEl.style.display = 'none';
		if (emptyEl) emptyEl.style.display = 'block';
	}

	function fermShowUpdating(show) {
		var summaryEl = document.querySelector('[data-ferm-cart-summary]');
		if (summaryEl) {
			summaryEl.style.opacity = show ? '0.5' : '1';
			summaryEl.style.pointerEvents = show ? 'none' : '';
		}
	}

	/* ======================================================================
	   2. CHECKOUT: SHIP-TO-DIFFERENT-ADDRESS TOGGLE
	   ====================================================================== */

	var checkoutEl = document.querySelector('[data-ferm-checkout]');
	if (checkoutEl) {
		var shipToggle = document.getElementById('ship-to-different-address');
		var shipWrap = document.getElementById('ship-to-different-address-wrap');
		if (shipToggle && shipWrap) {
			shipToggle.addEventListener('change', function () {
				shipWrap.style.display = this.checked ? 'block' : 'none';
			});
		}

		/* Basic field validation on blur */
		var fields = checkoutEl.querySelectorAll('.ferm-checkout__field input[required], .ferm-checkout__field select[required]');
		for (var i = 0; i < fields.length; i++) {
			fields[i].addEventListener('blur', function () {
				var field = this.closest('.ferm-checkout__field');
				if (!field) return;
				if (!this.value.trim()) {
					field.classList.add('ferm-checkout__field--error');
				} else {
					field.classList.remove('ferm-checkout__field--error');
				}
			});
		}

		/* Email field format check */
		var emailField = checkoutEl.querySelector('#billing_email');
		if (emailField) {
			emailField.addEventListener('blur', function () {
				var field = this.closest('.ferm-checkout__field');
				if (!field) return;
				if (this.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
					field.classList.add('ferm-checkout__field--error');
				} else {
					field.classList.remove('ferm-checkout__field--error');
				}
			});
		}
	}

	/* ======================================================================
	   3. ACCOUNT: TAB NAVIGATION
	   ====================================================================== */

	var accountEl = document.querySelector('[data-ferm-account]');
	if (accountEl) {
		var tabs = accountEl.querySelectorAll('[data-ferm-account-tabs] .ferm-account__tab');
		var panels = accountEl.querySelectorAll('[data-ferm-account-panel]');

		for (var t = 0; t < tabs.length; t++) {
			tabs[t].addEventListener('click', function () {
				var target = this.getAttribute('data-tab');

				/* Update tab active state */
				for (var i = 0; i < tabs.length; i++) {
					tabs[i].classList.remove('is-active');
				}
				this.classList.add('is-active');

				/* Show target panel */
				for (var p = 0; p < panels.length; p++) {
					panels[p].classList.remove('is-active');
				}
				var panel = accountEl.querySelector('[data-ferm-account-panel="' + target + '"]');
				if (panel) panel.classList.add('is-active');
			});
		}
	}

	/* ======================================================================
	   4. SEARCH: LIVE RESULTS / DEBOUNCED INPUT
	   ====================================================================== */

	var searchEl = document.querySelector('[data-ferm-search]');
	if (searchEl) {
		var searchInput = searchEl.querySelector('[data-ferm-search-input]');
		if (searchInput) {
			var searchTimeout = null;

			/* Redirect to ?s= on form submit */
			searchInput.addEventListener('keydown', function (e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					var val = this.value.trim();
					if (val) {
						var shopUrl = (config.shop_url || '/shop/') + '?s=' + encodeURIComponent(val) + '&post_type=product';
						window.location.href = shopUrl;
					}
				}
			});

			/* Live search suggestions (debounced) */
			searchInput.addEventListener('input', function () {
				var self = this;
				clearTimeout(searchTimeout);
				searchTimeout = setTimeout(function () {
					var val = self.value.trim();
					if (val.length < 2) return;

					fetch(ajaxUrl + '?action=ferm_search_live&nonce=' + encodeURIComponent(nonce) + '&q=' + encodeURIComponent(val))
						.then(function (r) { return r.json(); })
						.then(function (res) {
							if (res.success && res.data && res.data.suggestions) {
								fermShowSuggestions(searchEl, res.data.suggestions);
							}
						})
						.catch(function () {});
				}, 300);
			});

			/* Remove suggestions on outside click */
			document.addEventListener('click', function (e) {
				if (!e.target.closest('.ferm-search__input-wrap')) {
					var existing = searchEl.querySelector('.ferm-search__suggestions');
					if (existing) existing.remove();
				}
			});
		}
	}

	function fermShowSuggestions(container, suggestions) {
		/* Remove existing */
		var existing = container.querySelector('.ferm-search__suggestions');
		if (existing) existing.remove();

		if (!suggestions || !suggestions.length) return;

		var wrap = document.createElement('div');
		wrap.className = 'ferm-search__suggestions';
		wrap.style.marginTop = '16px';

		for (var i = 0; i < suggestions.length; i++) {
			var a = document.createElement('a');
			a.href = suggestions[i].url || '#';
			a.className = 'ferm-search__suggestion';
			a.textContent = suggestions[i].name || suggestions[i];
			wrap.appendChild(a);
		}

		var inputWrap = container.querySelector('.ferm-search__input-wrap');
		if (inputWrap) {
			inputWrap.parentNode.insertBefore(wrap, inputWrap.nextSibling);
		}
	}

})();
