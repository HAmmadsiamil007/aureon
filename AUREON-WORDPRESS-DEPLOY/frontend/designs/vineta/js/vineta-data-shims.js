/**
 * Vineta Data Shims ??? Bridge between Vineta frontend and AUREON/WooCommerce.
 *
 * Provides:
 * - Cart AJAX functions (add, update, get)
 * - Cart count sync
 * - VinetaPageData consumer hooks
 * - Customizer bridge
 *
 * @package Aureon
 */

(function() {
    'use strict';

    // Bridge config (localized from PHP).
    var config = window.vineta_bridge || {};
    var pageData = window.VinetaPageData || {};

    // Cart operations.
    var VinetaCart = {
        add: function(productId, quantity) {
            quantity = quantity || 1;
            var formData = new FormData();
            formData.append('action', 'vineta_cart_add');
            formData.append('nonce', config.nonce);
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            return fetch(config.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (data.success) {
                    VinetaCart.updateCount(data.data.item_count);
                    VinetaCart.updateDrawer(data.data);
                }
                return data;
            });
        },

        update: function(updates) {
            var formData = new FormData();
            formData.append('action', 'vineta_cart_update');
            formData.append('nonce', config.nonce);
            formData.append('updates', JSON.stringify(updates));

            return fetch(config.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (data.success) {
                    VinetaCart.updateCount(data.data.item_count);
                    VinetaCart.updateDrawer(data.data);
                }
                return data;
            });
        },

        get: function() {
            var formData = new FormData();
            formData.append('action', 'vineta_cart_get');
            formData.append('nonce', config.nonce);

            return fetch(config.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(response) {
                return response.json();
            });
        },

        updateCount: function(count) {
            // Vineta header cart badge lives in .nav-cart .count-box (not the
            // generic classes this bridge used to look for). The wishlist badge
            // is a separate feature, so scope strictly to the cart item.
            // Vineta always renders the badge with a number (see styles.css
            // .count-box), so never hide it; only the legacy generic selectors
            // get the d-none toggle for backwards compatibility.
            var countBoxes = document.querySelectorAll('.nav-cart .count-box');
            countBoxes.forEach(function(el) {
                el.textContent = count;
            });
            var legacyEls = document.querySelectorAll('.count-cart, .cart-count, .tf-cart-count');
            legacyEls.forEach(function(el) {
                el.textContent = count;
                if (count > 0) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });
            // Update pageData.
            if (pageData.cart) {
                pageData.cart.item_count = count;
            }
        },

        updateDrawer: function(cartData) {
            // Dispatch custom event for cart drawer update.
            var event = new CustomEvent('vineta:cart-updated', { detail: cartData });
            document.dispatchEvent(event);
        }
    };

    // Initialize cart count from pageData.
    if (pageData.cart && pageData.cart.item_count !== undefined) {
        VinetaCart.updateCount(pageData.cart.item_count);
    }


    // Cart UI consumer ??? renders the REAL WooCommerce cart into the frozen
    // Vineta DOM: the global #shoppingCart drawer (every page) and the
    // /cart/ page table. Reuses the frozen row markup as a clone template so
    // Vineta CSS/JS keep working. Handlers are bound with capture-phase
    // delegation on document so Vineta's own demo handlers (main.js binds
    // .plus-btn/.minus-btn/.remove at ready on the frozen rows) can never
    // double-fire on rows this consumer re-creates.
    var VinetaCartUI = {
        money: function(cents, currency, suffix) {
            if (cents === null || cents === undefined) return '';
            currency = currency || 'USD';
            var v = (cents / 100).toFixed(2);
            var s = '';
            if (currency === 'USD') s = '$' + v;
            else if (currency === 'EUR') s = '\u20ac' + v;
            else if (currency === 'PKR') s = '\u20a8' + v;
            else s = currency + ' ' + v;
            // suffix mode was designed for symbol-prefix currencies
            // ($139.00 USD, EUR139.00 EUR). For code-prefix currencies like
            // CHF the code is already in s, so appending it would duplicate it.
            return suffix && s.indexOf(currency) === -1 ? s + ' ' + currency : s;
        },

        // current cart data: prefer live AJAX payload, fall back to pageData
        cart: function() {
            return pageData.cart || { items: [], item_count: 0, total_price: 0 };
        },

        // ---- Drawer (#shoppingCart) -------------------------------------------------
        renderDrawer: function() {
            var wrap = document.querySelector('#shoppingCart .tf-mini-cart-items');
            if (!wrap) return;
            var c = this.cart();
            var items = c.items || [];
            var currency = c.currency || (pageData.shop && pageData.shop.currency) || 'USD';

            // totals
            var totalEl = document.querySelector('#shoppingCart .tf-totals-total-value, .tf-cart-bottom-wrap .tf-totals-total-value, .tf-mini-cart-bottom-wrap .tf-totals-total-value');
            if (totalEl) totalEl.textContent = this.money(c.total_price, currency, true);

            // Template: capture the frozen row ONCE (as a detached clone) so
            // every later render ??? bfcache restores, empty->filled transitions,
            // live AJAX adds ??? has a stable clone source even after the wrap was
            // emptied client-side. Never re-query the live drawer after capture:
            // Vineta's own JS can restructure those sections at any time.
            if (!this._drawerTemplateCached) {
                var src = null;
                var existing = wrap.querySelectorAll(':scope > .tf-mini-cart-item');
                if (existing.length) {
                    src = existing[0];
                } else {
                    // Last-resort: the drawer's "You may also like" rows share the
                    // mini-cart item structure on every Vineta page.
                    var rec = document.querySelector('#shoppingCart .tf-minicart-recommendations .tf-mini-cart-item');
                    if (rec) src = rec;
                }
                if (src) {
                    this._drawerTemplate = src.cloneNode(true);
                    this._drawerTemplateCached = true;
                }
            }
            var template = this._drawerTemplate;
            if (!template) {
                // no template anywhere -> leave frozen markup as fallback
                return;
            }

            if (!items.length) {
                // empty state ??? remove demo rows and show a message
                wrap.innerHTML = '';
                var box = document.createElement('div');
                box.className = 'tf-mini-cart-empty text-center py-5';
                var p = document.createElement('p');
                p.className = 'text-md text-main';
                p.textContent = 'Your cart is currently empty.';
                var a = document.createElement('a');
                a.href = (pageData.config && pageData.config.shop_url) ? pageData.config.shop_url + 'shop/' : '/shop/';
                a.className = 'tf-btn btn-dark2 animate-btn mt-15 d-inline-flex';
                a.textContent = 'Continue shopping';
                box.appendChild(p);
                box.appendChild(a);
                wrap.appendChild(box);
                return;
            }

            var frag = document.createDocumentFragment();
            items.forEach(function(it) {
                var clone = template.cloneNode(true);
                this.fillDrawerItem(clone, it, currency);
                frag.appendChild(clone);
            }, this);
            wrap.innerHTML = '';
            wrap.appendChild(frag);
        },

        fillDrawerItem: function(row, it, currency) {
            var img = row.querySelector('.tf-mini-cart-image img');
            if (img) {
                var src = it.image || config.placeholder_image || '';
                if (src) { img.src = src; img.setAttribute('data-src', src); }
                img.alt = it.title || 'Product';
            }
            var link = row.querySelector('.tf-mini-cart-image a, a.title');
            var href = it.url || it.permalink || '#';
            row.querySelectorAll('a.title, .tf-mini-cart-image a').forEach(function(a) {
                a.href = href;
            });
            var title = row.querySelector('.title, a.title');
            if (title && !row.querySelector('.tf-mini-cart-image a.title')) {
                // title link filled below anyway; avoid double text set
            }
            row.querySelectorAll('a.title').forEach(function(a) { a.textContent = it.title || ''; });
            // variant select -> plain text (variation chosen server-side)
            var variantSel = row.querySelector('.info-variant select');
            var variantBox = row.querySelector('.info-variant');
            if (variantSel && variantBox) {
                if (it.variant_title) {
                    variantSel.outerHTML = '<p class="text-xs variant-label">' + vinetaEscape(it.variant_title) + '</p>';
                } else {
                    variantBox.style.display = 'none';
                }
            }
            // price
            var priceNew = row.querySelector('.new-price');
            if (priceNew) priceNew.textContent = this.money(it.price, currency);
            var priceOld = row.querySelector('.old-price');
            if (priceOld) priceOld.style.display = 'none';
            // quantity
            var qty = row.querySelector('.quantity-product, input[name="number"]');
            if (qty) qty.value = it.quantity;
            row.setAttribute('data-cart-key', it.key || '');
            row.setAttribute('data-line-price', it.line_price || 0);
        },

        // ---- /cart/ page table ------------------------------------------------------
        renderCartPage: function() {
            var tbody = document.querySelector('.table-page-cart tbody');
            if (!tbody) return;
            var c = this.cart();
            var items = c.items || [];
            var currency = c.currency || 'USD';

            // Totals
            var totalEl = document.querySelector('.total-discount .total, .cart-head .total, .tf-cart-page-total .total');
            if (totalEl) totalEl.textContent = this.money(c.total_price, currency, true);
            var subtotalEl = document.querySelector('.cart-head .subtotal');
            if (subtotalEl) subtotalEl.textContent = this.money(c.total_price, currency, true);

            var rows = tbody.querySelectorAll(':scope > .tf-cart-item');
            if (!rows.length) return;
            var template = rows[0];

            if (!items.length) {
                // Empty state on the cart page
                tbody.innerHTML = '';
                var tr = document.createElement('tr');
                var td = document.createElement('td');
                td.setAttribute('colspan', '5');
                td.className = 'text-center py-5';
                td.innerHTML = '<p class="text-md text-main mb-10">Your cart is currently empty.</p><a href="' + ((pageData.config && pageData.config.shop_url) ? pageData.config.shop_url + 'shop/' : '/shop/') + '" class="tf-btn btn-dark2 animate-btn d-inline-flex">Continue shopping</a>';
                tr.appendChild(td);
                tbody.appendChild(tr);
                var box = document.querySelector('.checkout-cart-box, .tf-page-cart-total, .cart-box.checkout-cart-box');
                if (box) box.style.display = 'none';
                return;
            }

            var frag = document.createDocumentFragment();
            items.forEach(function(it) {
                var clone = template.cloneNode(true);
                this.fillCartRow(clone, it, currency);
                frag.appendChild(clone);
            }, this);
            tbody.innerHTML = '';
            tbody.appendChild(frag);
            var showBox = document.querySelector('.checkout-cart-box, .tf-page-cart-total, .cart-box.checkout-cart-box');
            if (showBox) showBox.style.display = '';
        },

        fillCartRow: function(row, it, currency) {
            var img = row.querySelector('img');
            if (img) {
                var src = it.image || config.placeholder_image || '';
                if (src) { img.src = src; img.setAttribute('data-src', src); }
                img.alt = it.title || 'Product';
            }
            var href = it.url || it.permalink || '#';
            row.querySelectorAll('a').forEach(function(a) { if (a.getAttribute('href')) a.href = href; });
            var name = row.querySelector('.name');
            if (name) { name.textContent = it.title || ''; name.href = href; }
            var variant = row.querySelector('.variants');
            if (variant) {
                variant.textContent = it.variant_title || '';
                variant.style.display = it.variant_title ? '' : 'none';
            }
            var priceEl = row.querySelector('.tf-cart-item_price .cart-price, .price-on-sale, .tf-cart-item_price');
            if (priceEl) {
                priceEl.textContent = this.money(it.price, currency);
            }
            var qty = row.querySelector('.quantity-product, input[name="number"]');
            if (qty) qty.value = it.quantity;
            var totalRow = row.querySelector('.tf-cart-item_total .total-price, .total-price, .tf-cart-item_total');
            if (totalRow) totalRow.textContent = this.money(it.line_price, currency);
            row.setAttribute('data-cart-key', it.key || '');
        },

        // ---- Actions (capture-phase delegation, no conflicts with main.js) ----------
        bindActions: function() {
            var self = this;
            document.addEventListener('click', function(e) {
                var t = e.target;
                // Only handle elements inside our managed surfaces
                var drawerRow = t.closest ? t.closest('#shoppingCart .tf-mini-cart-items .tf-mini-cart-item') : null;
                var pageRow = t.closest ? t.closest('.table-page-cart tbody .tf-cart-item') : null;
                var row = drawerRow || pageRow;
                if (!row) return;
                var key = row.getAttribute('data-cart-key');
                var btn = t.closest ? t.closest('.plus-btn, .minus-btn, .btn-increase, .btn-decrease, .remove, .remove-cart, .icon-close') : null;
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation(); // stop Vineta main.js demo handlers

                var qtyInput = row.querySelector('.quantity-product, input[name="number"]');
                var cur = qtyInput ? parseInt(qtyInput.value, 10) : 1;
                if (isNaN(cur)) cur = 1;

                if (btn.classList.contains('plus-btn') || btn.classList.contains('btn-increase')) {
                    self.updateCart(key, cur + 1);
                } else if (btn.classList.contains('minus-btn') || btn.classList.contains('btn-decrease')) {
                    if (cur > 1) self.updateCart(key, cur - 1);
                } else if (btn.classList.contains('remove') || btn.classList.contains('remove-cart') || btn.classList.contains('icon-close')) {
                    self.updateCart(key, 0);
                }
            }, true);

            // Manual quantity typing -> commit on change
            document.addEventListener('change', function(e) {
                var row = e.target.closest ? e.target.closest('#shoppingCart .tf-mini-cart-items .tf-mini-cart-item, .table-page-cart tbody .tf-cart-item') : null;
                if (!row) return;
                var input = e.target;
                if (!input.classList.contains('quantity-product') && input.name !== 'number') return;
                var key = row.getAttribute('data-cart-key');
                var v = parseInt(input.value, 10);
                if (!key || isNaN(v) || v < 1) return;
                self.updateCart(key, v);
            }, true);
        },

        updateCart: function(key, quantity) {
            if (!key) return;
            var fd = new FormData();
            fd.append('action', 'vineta_cart_update');
            fd.append('nonce', config.nonce);
            var updates = {};
            updates[key] = quantity;
            fd.append('updates', JSON.stringify(updates));
            fetch(config.ajax_url, { method: 'POST', body: fd, credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res && res.success) {
                        pageData.cart = res.data;
                        if (window.VinetaCart) VinetaCart.updateCount(res.data.item_count);
                        VinetaCartUI.renderDrawer();
                        VinetaCartUI.renderCartPage();
                        // let other bridge modules react (product pages etc.)
                        document.dispatchEvent(new CustomEvent('vineta:cart-updated', { detail: res.data }));
                    }
                });
        },

        init: function() {
            this.renderDrawer();
            this.renderCartPage();
            this.bindActions();
            var self = this;
            // Vineta's main.js demo handlers bind at ready; re-render once the
            // document is interactive to guarantee a real-cart drawer/page even
            // if the initial render ran before part of the layout existed.
            var booted = false;
            var boot = function() {
                if (booted) return;
                booted = true;
                self.renderDrawer();
                self.renderCartPage();
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() { setTimeout(boot, 30); });
            } else {
                setTimeout(boot, 30);
            }
            // bfcache: restore can resurrect a DOM this consumer emptied
            window.addEventListener('pageshow', function() { booted = false; boot(); });
        }
    };
    window.VinetaCartUI = VinetaCartUI;

    // Expose globally.
    window.VinetaCart = VinetaCart;

    // Minimal HTML escaper for titles injected from WP.
    function vinetaEscape(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Navigation consumer ??? replace the frozen demo menu with the real WP menu.
    // Runs synchronously at parse time (footer script), BEFORE Vineta main.js
    // clones .box-nav-menu into the mobile drawer on DOM ready, so both the
    // desktop header and the mobile menu show the real items. When the WP menu
    // is empty the frozen Vineta menu remains as the presentation fallback.
    var VinetaNav = {
        renderMain: function(items) {
            if (!items || !items.length) return;
            var nav = document.querySelector('[data-aureon-slot="global.navigation"] .box-nav-menu, .box-navigation .box-nav-menu, header .box-nav-menu');
            if (!nav) return;
            var html = '';
            items.forEach(function(item) {
                if (!item || !item.title || !item.url) return;
                var children = item.children || [];
                if (children.length) {
                    html += '<li class="menu-item"><a href="' + item.url + '" class="item-link">' +
                        vinetaEscape(item.title) + '<i class="icon icon-arr-down" aria-hidden="true"></i></a>' +
                        '<div class="sub-menu"><div class="wrapper-sub-menu"><div class="mega-menu-item">' +
                        '<ul class="menu-list">';
                    children.forEach(function(c) {
                        if (c && c.title && c.url) {
                            html += '<li><a href="' + c.url + '" class="menu-link-text link">' + vinetaEscape(c.title) + '</a></li>';
                        }
                    });
                    html += '</ul></div></div></div></li>';
                } else {
                    html += '<li class="menu-item"><a href="' + item.url + '" class="item-link">' + vinetaEscape(item.title) + '</a></li>';
                }
            });
            if (html) {
                nav.innerHTML = html;
            }
        },
        renderFooter: function(items) {
            if (!items || !items.length) return;
            var footer = document.querySelector('footer,.footer');
            if (!footer) return;
            // The frozen Vineta footer ships two link columns ("About Us" /
            // "Resource"). The WP footer menu maps to the Resource column ???
            // replace that list's links only, keep heading + presentation.
            var headings = footer.querySelectorAll('.footer-heading, h5, h6, .heading');
            var target = null;
            headings.forEach(function(h) {
                var txt = (h.textContent || '').trim();
                if (/resource|support|help|quick links|explore/i.test(txt) && !target) {
                    var wrap = h.closest('.footer-col-block, .footer-col, div');
                    if (wrap && wrap.querySelector('ul')) target = wrap.querySelector('ul');
                }
            });
            if (!target) {
                // Fallback: last .footer-menu-list in the footer.
                var lists = footer.querySelectorAll('ul.footer-menu-list, ul li a[href]');
                var all = footer.querySelectorAll('.footer-col-block ul');
                target = all.length ? all[all.length - 1] : null;
            }
            if (!target) return;
            var html = '';
            items.forEach(function(item) {
                if (item && item.title && item.url) {
                    html += '<li><a href="' + item.url + '">' + vinetaEscape(item.title) + '</a></li>';
                }
            });
            if (html) {
                target.innerHTML = html;
            }
        }
    };

    try {
        VinetaNav.renderMain(pageData.navigation && pageData.navigation.main);
        VinetaNav.renderFooter(pageData.navigation && pageData.navigation.footer);
    } catch (e) {
        /* never let a nav failure break the cart bridge */
    }
    window.VinetaNav = VinetaNav;

    // Homepage consumer ??? fill the frozen Best-Sellers / featured-carousel DOM
    // from real WooCommerce products and fill category tiles from real terms.
    var VinetaHome = {
        renderFeaturedProducts: function(products, slot) {
            if (!products || !products.length) return;
            slot = slot || 'global.featured_products';
            var section = document.querySelector('[data-aureon-slot="' + slot + '"]');
            if (!section) return;
            var track = section.querySelector('.swiper .swiper-wrapper, .swiper-wrapper');
            if (!track) return;
            var slides = track.querySelectorAll(':scope > .swiper-slide');
            if (!slides.length) return;
            var templateSlide = slides[0];
            var frag = document.createDocumentFragment();
            products.forEach(function(product, idx) {
                var slide;
                if (idx < slides.length) {
                    slide = slides[idx];
                } else {
                    slide = templateSlide.cloneNode(true);
                }
                var card = slide.querySelector('.card-product');
                if (card && window.VinetaShop) {
                    VinetaShop.fillCard(card, product);
                }
                frag.appendChild(slide);
            });
            track.innerHTML = '';
            track.appendChild(frag);
            // Bind add-to-cart handlers on homepage product cards
            if (window.VinetaShop) {
                VinetaShop.bindAddToCart(section);
            }
        },

        renderCompare: function(products) {
            if (!products || !products.length) return;
            var section = document.querySelector('[data-aureon-slot="global.compare_products"]');
            if (!section) return;
            var rows = section.querySelectorAll('.tf-compare-item');
            if (!rows.length) return;
            // Fill as many rows as the modal has; any row beyond the product
            // list gets hidden so no demo row ever survives.
            rows.forEach(function(row, idx) {
                if (!products[idx]) {
                    if (row.parentNode) row.parentNode.removeChild(row);
                    return;
                }
            });
            products.forEach(function(product, idx) {
                var row = rows[idx];
                if (!row) return;
                var link = product.url || '#';
                var img = row.querySelector('.image img');
                if (img) {
                    var src = product.image || config.placeholder_image || '';
                    img.src = src;
                    img.setAttribute('data-src', src);
                    img.alt = product.title || '';
                }
                row.querySelectorAll('a').forEach(function(a) {
                    if (a.querySelector('img')) return;
                    a.href = link;
                });
                var name = row.querySelector('.link.text-line-clamp-2, .link');
                if (name) name.textContent = product.title || '';
                var priceNew = row.querySelector('.new-price');
                var priceOld = row.querySelector('.old-price');
                var onSale = product.on_sale && product.price_sale > 0;
                var currency = pageData.shop && pageData.shop.currency;
                if (priceNew) {
                    priceNew.textContent = VinetaShop.money(onSale ? product.price_sale : product.price, currency);
                }
                if (priceOld) {
                    if (onSale) {
                        priceOld.textContent = VinetaShop.money(product.price_regular, currency);
                        priceOld.style.display = '';
                    } else {
                        priceOld.style.display = 'none';
                    }
                }
            });
        },

        renderQuickView: function(product) {
            if (!product) return;
            var url = product.url || '#';
            var img = product.image || config.placeholder_image || '';
            var onSale = product.on_sale && product.price_sale > 0;
            var currency = pageData.shop && pageData.shop.currency;

            // #quickView modal
            var qv = document.querySelector('#quickView [data-aureon-slot="global.quickview_product"]');
            if (qv) {
                var name = qv.querySelector('.product-name a, .product-name');
                if (name) name.textContent = product.title || '';
                qv.querySelectorAll('.product-name a, .view-details, a.link').forEach(function(a) {
                    if (a.querySelector('img')) return;
                    if (a.className.indexOf('view-details') >= 0 || a.textContent.indexOf('View full details') >= 0) {
                        a.href = url;
                    } else if (a.className.indexOf('product-name') >= 0 || a.textContent === (product.title || '')) {
                        a.href = url;
                    }
                });
                var pNew = qv.querySelector('.price-new');
                var pOld = qv.querySelector('.price-old');
                var badge = qv.querySelector('.badge-sale');
                if (pNew) pNew.textContent = VinetaShop.money(onSale ? product.price_sale : product.price, currency);
                if (pOld) {
                    if (onSale) { pOld.textContent = VinetaShop.money(product.price_regular, currency); pOld.style.display = ''; }
                    else { pOld.style.display = 'none'; }
                }
                if (badge) {
                    if (onSale) {
                        var pct = product.price_regular > 0 ? Math.round((1 - product.price_sale / product.price_regular) * 100) : 0;
                        badge.textContent = pct + '% Off';
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                var desc = qv.querySelector('.tf-product-heading .text, .tf-product-heading p');
                if (desc && product.description) desc.textContent = product.description;
                var galImg = qv.querySelector('.tf-product-media-wrap .swiper-slide img');
                if (galImg) { galImg.src = img; galImg.setAttribute('data-src', img); galImg.alt = product.title || ''; }
                // Hide fake color/size pickers when the product has no attributes.
                if (!product.attributes || !product.attributes.length) {
                    var pickers = qv.querySelectorAll('.variant-picker-item, .tf-product-variant');
                    pickers.forEach(function(el) { el.style.display = 'none'; });
                }
            }

            // #quickAdd modal
            var qa = document.querySelector('#quickAdd [data-aureon-slot="global.quickadd_product"]');
            if (qa) {
                var qImg = qa.querySelector('.img-product, img');
                if (qImg) { qImg.src = img; qImg.setAttribute('data-src', img); qImg.alt = product.title || ''; }
                var qName = qa.querySelector('.name-product');
                if (qName) qName.textContent = product.title || '';
                qa.querySelectorAll('a').forEach(function(a) {
                    if (a.querySelector('img')) return;
                    if (a.href.indexOf('product-detail.html') >= 0 || a.className.indexOf('name-product') >= 0) a.href = url;
                });
                var qNew = qa.querySelector('.price-new');
                var qOld = qa.querySelector('.price-old');
                var qBadge = qa.querySelector('.on-sale-item');
                if (qNew) qNew.textContent = VinetaShop.money(onSale ? product.price_sale : product.price, currency);
                if (qOld) {
                    if (onSale) { qOld.textContent = VinetaShop.money(product.price_regular, currency); qOld.style.display = ''; }
                    else { qOld.style.display = 'none'; }
                }
                if (qBadge) {
                    if (onSale) { qBadge.style.display = ''; } else { qBadge.style.display = 'none'; }
                }
                if (!product.attributes || !product.attributes.length) {
                    var qPickers = qa.querySelectorAll('.item-product-variant, .quickadd-variant-color, .quickadd-variant-size');
                    qPickers.forEach(function(el) { el.style.display = 'none'; });
                }
            }
        },

        renderCartRecommendations: function(products, slot) {
            if (!products || !products.length) return;
            // Prefer the explicit slot; fall back to the frozen drawer markup on
            // templates that have not (yet) declared the slot ??? so no demo rows
            // can survive on any page.
            var section = document.querySelector('[data-aureon-slot="' + slot + '"]')
                || document.querySelector('#shoppingCart .tf-minicart-recommendations');
            if (!section) return;
            var track = section.querySelector('.swiper .swiper-wrapper, .swiper-wrapper');
            if (!track) return;
            var slides = track.querySelectorAll(':scope > .swiper-slide');
            if (!slides.length) return;
            var templateSlide = slides[0];
            var frag = document.createDocumentFragment();
            products.forEach(function(product, idx) {
                var slide;
                if (idx < slides.length) {
                    slide = slides[idx];
                } else {
                    slide = templateSlide.cloneNode(true);
                }
                var item = slide.querySelector('.tf-mini-cart-item');
                if (item) {
                    var link = product.url || '#';
                    var img = item.querySelector('.tf-mini-cart-image img, .image img');
                    if (img) {
                        var src = product.image || config.placeholder_image || '';
                        img.src = src;
                        img.setAttribute('data-src', src);
                        img.alt = product.title || 'Product thumbnail';
                    }
                    item.querySelectorAll('a[href="product-detail.html"], a.title').forEach(function(a) {
                        if (a.querySelector('img')) return;
                        a.href = link;
                    });
                    var title = item.querySelector('.title, .name-product');
                    if (title) title.textContent = product.title || '';
                    var priceNew = item.querySelector('.new-price');
                    var priceOld = item.querySelector('.old-price');
                    var onSale = product.on_sale && product.price_sale > 0;
                    if (priceNew) {
                        priceNew.textContent = VinetaShop.money(onSale ? product.price_sale : product.price,
                            pageData.shop && pageData.shop.currency);
                    }
                    if (priceOld) {
                        if (onSale) {
                            priceOld.textContent = VinetaShop.money(product.price_regular, pageData.shop && pageData.shop.currency);
                            priceOld.style.display = '';
                        } else {
                            priceOld.style.display = 'none';
                        }
                    }
                }
                frag.appendChild(slide);
            });
            track.innerHTML = '';
            track.appendChild(frag);
        },

        renderCategories: function(categories) {
            if (!categories || !categories.length) return;
            var section = document.querySelector('[data-aureon-slot="global.featured_categories"]');
            if (!section) return;
            var track = section.querySelector('.swiper .swiper-wrapper, .swiper-wrapper');
            if (!track) return;
            var slides = track.querySelectorAll(':scope > .swiper-slide');
            if (!slides.length) return;
            var templateSlide = slides[0];
            var frag = document.createDocumentFragment();
            categories.forEach(function(cat, idx) {
                var slide;
                if (idx < slides.length) {
                    slide = slides[idx];
                } else {
                    slide = templateSlide.cloneNode(true);
                }
                var img = slide.querySelector('.image img, .img-style img');
                if (img) {
                    var src = cat.image || config.placeholder_image || '';
                    img.src = src;
                    img.setAttribute('data-src', src);
                    img.alt = cat.name || '';
                }
                // text label + link (Vineta uses a .tf-btn.btn-cls or a link)
                var label = slide.querySelector('.tf-btn.btn-cls, .tf-btn, .cls-btn a, .text-type, a.link');
                slide.querySelectorAll('a').forEach(function(a) {
                    if (a.querySelector('img')) return;
                    a.href = cat.url || '#';
                });
                if (label) {
                    // keep icon markup, replace only text node content
                    var kids = label.childNodes;
                    for (var i = 0; i < kids.length; i++) {
                        if (kids[i].nodeType === 3) {
                            kids[i].textContent = cat.name || '';
                            break;
                        }
                    }
                    if (!kids.length) label.textContent = cat.name || '';
                }
                frag.appendChild(slide);
            });
            track.innerHTML = '';
            track.appendChild(frag);
        },

        init: function() {
            var home = pageData.home;
            if (!home) return;
            if (home.products && home.products.length) {
                this.renderFeaturedProducts(home.products, 'global.featured_products');
                // Today's Picks: same real catalog, filled from the second slice
                // so both homepage bands show live WooCommerce data (no demo tees).
                var picks = home.products.slice(Math.min(4, home.products.length));
                this.renderFeaturedProducts(picks, 'global.picks_products');
                // Search-modal "Featured product" carousel: same real catalog.
                this.renderFeaturedProducts(home.products, 'global.search_products');
                // Quickview + quickadd modals: fill from a real product so the
                // modal never advertises the demo "Striped T-Shirt".
                this.renderQuickView(home.products[0]);
                // Compare modal: fill rows from the real catalog.
                this.renderCompare(home.products.slice(0, 4));
            } else {
                // Empty real catalog: never show the frozen demo product bands.
                this.clearChromeDemo();
            }
            if (home.categories && home.categories.length) {
                this.renderCategories(home.categories);
                // Women/Men tabbed circles (flat-animate-tab) ??? same real terms,
                // grouped by their parent category so no static demo circle survives.
                this.renderCategoryTabs(home.categories);
            } else {
                var cats = document.querySelector('[data-aureon-slot="global.featured_categories"]');
                if (cats) cats.style.display = 'none';
                var tabRoot = document.querySelector('[data-aureon-slot="global.categories_tabs"]');
                if (tabRoot) {
                    var sec = tabRoot;
                    while (sec && sec.tagName !== 'SECTION' && sec.parentElement) sec = sec.parentElement;
                    if (sec) sec.style.display = 'none';
                }
            }
        },

        // Fill the homepage "Categories" Women/Men tab circles from real
        // WooCommerce categories (children grouped by their parent term). Tabs
        // whose group has no categories are hidden; when every group is empty
        // the whole section hides so no frozen demo circle can surface.
        renderCategoryTabs: function(categories) {
            if (!categories || !categories.length) return;
            var root = document.querySelector('[data-aureon-slot="global.categories_tabs"]');
            if (!root) return;
            var groups = { women: [], men: [] };
            categories.forEach(function(cat) {
                var parent = (cat.parent || '').toLowerCase();
                if (parent === 'women') groups.women.push(cat);
                else if (parent === 'men') groups.men.push(cat);
            });
            var any = false;
            Object.keys(groups).forEach(function(key) {
                var list = groups[key];
                var pane = root.querySelector('.tab-pane#' + key);
                var tabLink = root.querySelector('a[href="#' + key + '"]');
                if (!list.length) {
                    if (pane) pane.style.display = 'none';
                    if (tabLink && tabLink.parentNode) tabLink.parentNode.style.display = 'none';
                    return;
                }
                if (!pane) return;
                var track = pane.querySelector('.swiper-wrapper');
                if (!track) return;
                var tpl = track.querySelector(':scope > .swiper-slide');
                if (!tpl) return;
                var frag = document.createDocumentFragment();
                list.forEach(function(cat) {
                    var slide = tpl.cloneNode(true);
                    var img = slide.querySelector('img');
                    var src = cat.image || config.placeholder_image || '';
                    if (img && src) {
                        if (src.indexOf('http') !== 0 && config && config.site_url) {
                            src = config.site_url.replace(/\/$/, '') + '/' + src.replace(/^\/+/, '');
                        }
                        img.src = src;
                        img.setAttribute('data-src', src);
                        img.alt = cat.name || 'Category';
                    }
                    slide.querySelectorAll('a').forEach(function(a) {
                        if (a.tagName === 'A') a.href = cat.url || '#';
                    });
                    var nameEl = slide.querySelector('.cls-content a, .cls-content .link, .link.text-md');
                    if (nameEl) nameEl.textContent = cat.name || '';
                    frag.appendChild(slide);
                });
                track.innerHTML = '';
                track.appendChild(frag);
                if (pane) pane.style.display = '';
                if (tabLink && tabLink.parentNode) tabLink.parentNode.style.display = '';
                any = true;
            });
            if (!any) {
                var sec = root;
                while (sec && sec.tagName !== 'SECTION' && sec.parentElement) sec = sec.parentElement;
                if (sec) sec.style.display = 'none';
            }
        },

        // Client wiped the store (or no products exist yet): hide every product
        // demo block ??? homepage bands, drawer recommendations, search featured
        // carousel, quickview/quickadd + compare modal bodies ??? both via the
        // canonical slots and the generic frozen markup on templates without
        // slots. Sections reappear automatically once real data exists.
        clearChromeDemo: function() {
            var slots = [
                'global.featured_products',
                'global.picks_products',
                'global.search_products',
                'global.cart_recommendations',
                'global.compare_products',
                'global.quickview_product',
                'global.quickadd_product'
            ];
            slots.forEach(function(slot) {
                document.querySelectorAll('[data-aureon-slot="' + slot + '"]').forEach(function(el) {
                    el.style.display = 'none';
                });
            });
            // Generic fallbacks for templates that never received the slots.
            document.querySelectorAll('.tf-minicart-recommendations').forEach(function(el) {
                el.style.display = 'none';
            });
            document.querySelectorAll('.tf-compare-item').forEach(function(el) {
                el.style.display = 'none';
            });
        },

        // Sweep every page for product-card bands that are still showing the
        // frozen demo markup (prices in $/EUR/CHF) and fill them from the real
        // product list passed in (home featured / product related / pageData.chrome).
        // Cards that live inside a data-aureon-slot are handled by their own
        // consumer and are skipped; already-filled cards are skipped too.
        fillStrayCards: function(products) {
            if (!products || !products.length) return;
            var cards = document.querySelectorAll('.card-product');
            cards.forEach(function(card, idx) {
                if (card.hasAttribute('data-vineta-filled')) return;
                if (card.closest('[data-aureon-slot]')) return;
                var priceEl = card.querySelector('.price-new, .price, .price-wrap');
                var txt = priceEl ? (priceEl.textContent || '') : '';
                if (txt && !/\$|\u20ac|CHF|USD|EUR|\u20a8|PKR|Rs/i.test(txt)) return;
                var product = products[idx % products.length];
                if (product && window.VinetaShop) {
                    try {
                        VinetaShop.fillCard(card, product);
                        card.setAttribute('data-vineta-filled', '1');
                    } catch (e) { /* one card must not break the sweep */ }
                }
            });
        },

        // The frozen cart page ships static demo copy that has no real backing:
        // the free-shipping progress block ("Spend $100 more...") and the
        // gift-wrap row ("Add gift wrap. Only $10.00"). Hide them rather than
        // display a misleading hardcoded amount.
        hideDemoFreeShipHead: function() {
            document.querySelectorAll('.tf-cart-head, .check-gift').forEach(function(el) {
                el.style.display = 'none';
            });
        }
    };

    // Shop / collection consumer ??? render real WooCommerce products into the
    // existing Vineta card markup. Keeps the frozen card DOM as the template
    // (classes, buttons, badges, hover structure all preserved) and only swaps
    // in real data per card. When no real products exist the frozen grid stays
    // as the presentation fallback.
    var VinetaShop = {
        money: function(cents, currency) {
            if (cents === null || cents === undefined) return '';
            currency = currency || 'USD';
            var v = (cents / 100).toFixed(2);
            if (currency === 'USD') return '$' + v;
            if (currency === 'EUR') return '\u20ac' + v;
            if (currency === 'PKR') return '\u20a8' + v;
            return currency + ' ' + v;
        },

        fillCard: function(card, product) {
            if (!card || !product) return;
            var href = product.url || '#';
            // Mark card so the path-bridge MutationObserver skips it
            card.setAttribute('data-vineta-filled', '1');
            // Product link targets ??? cover ALL anchor types in the card
            card.querySelectorAll('a').forEach(function(a) {
                if (a.tagName !== 'A') return;
                // Skip add-to-cart, wishlist, quickview, compare buttons
                if (a.classList.contains('quickview') || a.classList.contains('box-icon') ||
                    a.getAttribute('data-vineta-add') || a.closest('.list-product-btn')) return;
                // Always overwrite product card links with the real WooCommerce URL
                a.href = href;
            });
            // Images
            var src = product.image || config.placeholder_image || '';
            var mainImg = card.querySelector('img.img-product');
            var hoverImg = card.querySelector('img.img-hover');
            if (mainImg) {
                mainImg.src = src;
                mainImg.setAttribute('data-src', src);
                mainImg.alt = product.title || 'Product image';
            }
            var hoverSrc = product.hover_image || product.image || src;
            if (hoverImg) {
                hoverImg.src = hoverSrc;
                hoverImg.setAttribute('data-src', hoverSrc);
                hoverImg.alt = product.title || 'Product image';
            }
            // Title
            card.querySelectorAll('.name-product').forEach(function(el) {
                el.textContent = product.title || '';
            });
            // Price + sale price
            var priceNew = card.querySelector('.price-new');
            var priceOld = card.querySelector('.price-old');
            var onSale = product.on_sale && product.price_sale > 0;
            var price = onSale ? product.price_sale : product.price;
            if (priceNew) priceNew.textContent = this.money(price, pageData.shop && pageData.shop.currency);
            if (priceOld) {
                if (onSale) {
                    priceOld.textContent = this.money(product.price_regular, pageData.shop && pageData.shop.currency);
                    priceOld.style.display = '';
                } else {
                    priceOld.style.display = 'none';
                }
            }
            // Badge (hide the frozen ribbon when the product is not on sale)
            var badgeWrap = card.querySelector('.on-sale-wrap');
            var badgeEl = card.querySelector('.on-sale-wrap .on-sale-item, .on-sale-item');
            if (badgeWrap) {
                if (product.badge && product.badge !== 'Sale' && badgeEl) {
                    badgeEl.textContent = product.badge;
                } else if (onSale && badgeEl) {
                    var pct = product.price_regular > 0 ? Math.round((1 - product.price_sale / product.price_regular) * 100) : 0;
                    badgeEl.textContent = pct + '% Off';
                    badgeWrap.style.display = '';
                } else if (!product.badge) {
                    badgeWrap.style.display = 'none';
                } else {
                    badgeWrap.style.display = '';
                }
            }
            // Swatches/sizes are demo decoration: hide them when the product has
            // no real attribute data so a card never advertises fake colors/sizes.
            if (!product.attributes || !product.attributes.length) {
                var swatches = card.querySelector('.list-color-product, .list-color');
                if (swatches) swatches.style.display = 'none';
                var sizes = card.querySelector('.size-box, .list-size-product');
                if (sizes) sizes.style.display = 'none';
            }
            // Hover image: hide the frozen second image when real data has none.
            if (!product.hover_image && !product.gallery) {
                if (hoverImg) hoverImg.style.display = 'none';
            }
            // Store id for cart binding, expose availability
            card.setAttribute('data-product-id', product.id || '');
            card.setAttribute('data-available', product.available ? 'In stock' : 'Out of stock');
            // Add-to-cart button ??? match both #shoppingCart and #quickAdd hrefs
            var btnLink = card.querySelector('.list-product-btn a[href="#shoppingCart"], .list-product-btn a[href="#quickAdd"]');
            if (btnLink) {
                btnLink.setAttribute('data-product-id', product.id || '');
                btnLink.setAttribute('data-vineta-add', '1');
            }
        },

        // Populate an existing grid/list container using its frozen first card
        // as template. Removes surplus demo cards after the real items.
        renderInto: function(container, products) {
            if (!container || !products || !products.length) return;
            var cards = container.querySelectorAll(':scope > .card-product');
            if (!cards.length) return;
            var template = cards[0];
            var frag = document.createDocumentFragment();
            products.forEach(function(product) {
                var clone = template.cloneNode(true);
                this.fillCard(clone, product);
                frag.appendChild(clone);
            }, this);
            container.innerHTML = '';
            container.appendChild(frag);
            this.bindAddToCart(container);
        },

        bindAddToCart: function(container) {
            container.querySelectorAll('a[data-vineta-add]').forEach(function(link) {
                if (link.getAttribute('data-bound')) return;
                link.setAttribute('data-bound', '1');
                link.addEventListener('click', function(e) {
                    var id = link.getAttribute('data-product-id');
                    if (!id) return;
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.VinetaCart) {
                        VinetaCart.add(id, 1).then(function(data) {
                            if (data && data.success) {
                                var drawer = document.querySelector('#shoppingCart');
                                if (drawer && window.bootstrap) {
                                    var off = window.bootstrap.Offcanvas.getOrCreateInstance(drawer);
                                    off.show();
                                }
                            }
                        });
                    }
                });
            });
        },

        initCollection: function() {
            var collection = pageData.collection;
            if (!collection || !collection.products) return;
            var grid = document.getElementById('gridLayout');
            var list = document.getElementById('listLayout');
            var isSearch = !!collection.is_search;

            // Page title/breadcrumb mirror the collection (search results get a
            // results heading; category archives get the term name).
            var title = collection.title;
            if (title) {
                document.querySelectorAll('.tf-page-title .title, .tf-page-title h1, .box-title .title').forEach(function(el) {
                    el.textContent = title;
                });
                document.querySelectorAll('.breadcrumb-item.current').forEach(function(el) {
                    el.textContent = title;
                });
            }
            var desc = collection.description;
            if (desc) {
                var dEl = document.querySelector('.tf-page-title .desc, .box-title .desc');
                if (dEl) {
                    dEl.textContent = desc;
                    dEl.style.display = '';
                }
            }

            if (collection.products.length) {
                if (grid) this.renderInto(grid, collection.products);
                if (list) this.renderInto(list, collection.products);
                return;
            }

            // No real products. Search results must NEVER fall back to the
            // frozen demo grid: show an explicit empty state instead.
            if (isSearch) {
                if (grid) this.showEmptyState(grid, collection);
                if (list) { list.innerHTML = ''; }
                return;
            }

            // No real products on a shop/category archive: hide the whole grid
            // section so the frozen demo grid never surfaces (client adds
            // products/categories later and it reappears automatically).
            var area = grid || list;
            if (area) {
                var sec = area;
                while (sec && sec.tagName !== 'SECTION' && sec.parentElement) sec = sec.parentElement;
                if (sec) sec.style.display = 'none';
                if (grid) grid.style.display = 'none';
                if (list) list.style.display = 'none';
            }
        },

        showEmptyState: function(grid, collection) {
            if (!grid) return;
            grid.innerHTML = '';
            var q = collection.query || '';
            var box = document.createElement('div');
            box.className = 'text-center py-5 w-100';
            var h = document.createElement('h2');
            h.className = 'title fs-24 fw-6 mb-10';
            h.textContent = q ? 'No results for \u201c' + q + '\u201d' : 'No results found';
            var p = document.createElement('p');
            p.className = 'text-secondary';
            p.textContent = q
                ? 'We could not find any products matching \u201c' + q + '\u201d. Try another search term.'
                : 'Please enter a search term to find products.';
            box.appendChild(h);
            box.appendChild(p);
            grid.appendChild(box);
        }
    };

    try {
        VinetaShop.initCollection();
    } catch (e) {
        /* grid failure must not break the rest of the bridge */
    }
    window.VinetaShop = VinetaShop;

    // Blog archive consumer ??? render real WP posts into the frozen .blog-item
    // cards, reusing the Vineta card DOM/CSS/JS.
    var VinetaBlog = {
        fillCard: function(card, post) {
            if (!card || !post) return;
            var url = post.url || '#';
            // Image + link
            var img = card.querySelector('.entry_image img, img');
            if (img) {
                var src = post.image || config.placeholder_image || '';
                if (src) { img.src = src; img.setAttribute('data-src', src); }
                if (!post.image) {
                    var wrap = card.querySelector('.entry_image');
                    if (wrap) wrap.style.display = 'none';
                }
            }
            card.querySelectorAll('.entry_image a, a.entry_title').forEach(function(a) {
                if (a.tagName === 'A') a.href = url;
            });
            // Title
            card.querySelectorAll('.entry_title').forEach(function(el) {
                el.textContent = post.title || '';
            });
            // Excerpt
            var ex = card.querySelector('.entry_sub, .entry_excerpt');
            if (ex) ex.textContent = post.excerpt || '';
            // Category
            var cat = card.querySelector('.entry-tag a, .entry-tag li a');
            if (cat) {
                cat.textContent = post.category || '';
                if (post.cat_url) cat.href = post.cat_url;
            }
            // Author
            var authorName = card.querySelector('.entry_name span, .entry_author .fw-medium');
            if (authorName) authorName.textContent = post.author || '';
            // Date
            var date = card.querySelector('.entry_date p, .entry_date');
            if (date) date.textContent = post.date || '';
            card.setAttribute('data-post-id', post.id || '');
        },

        renderInto: function(container, posts) {
            if (!container || !posts || !posts.length) return;
            var cards = container.querySelectorAll('.blog-item');
            if (!cards.length) return;
            var template = cards[0];
            var frag = document.createDocumentFragment();
            posts.forEach(function(post) {
                var clone = template.cloneNode(true);
                this.fillCard(clone, post);
                frag.appendChild(clone);
            }, this);
            // Remove surplus frozen demo cards after the real posts.
            for (var i = cards.length - 1; i >= 1; i--) cards[i].parentNode.removeChild(cards[i]);
            if (template.parentNode) {
                template.parentNode.replaceChild(frag, template);
            }
        },

        init: function() {
            var blog = pageData.blog;
            if (!blog || !blog.posts || !blog.posts.length) return;
            var grid = document.querySelector('[data-aureon-slot="blog.grid"] .s-blog-list-grid, .s-blog-list-grid');
            if (grid) this.renderInto(grid, blog.posts);
            // Hide pagination placeholders when there is only one page of posts.
            var total = blog.posts.length;
            if (total <= 1) {
                document.querySelectorAll('.wg-pagination, .tf-pagination, .wg-page').forEach(function(p) {
                    p.style.display = 'none';
                });
            }
        }
    };

    // Blog single-article consumer ??? fill the article.* slots on blog-single.html
    // with the real WP post content while keeping the frozen presentation.
    var VinetaArticle = {
        init: function() {
            var art = pageData.article;
            if (!art) return;
            // Title
            var title = document.querySelector('[data-aureon-slot="article.title"]');
            if (title) title.textContent = art.title || '';
            // Date
            var date = document.querySelector('[data-aureon-slot="article.date"] p, [data-aureon-slot="article.date"]');
            if (date) date.textContent = art.date || '';
            // Author
            var author = document.querySelector('[data-aureon-slot="article.author"] .fw-medium, [data-aureon-slot="article.author"] .entry_name span');
            if (author) author.textContent = art.author || '';
            // Category
            var cat = document.querySelector('.entry-tag a, [data-aureon-slot="article.category"] a, .entry-tag li a');
            if (cat) {
                cat.textContent = art.category || '';
                if (art.cat_url) cat.href = art.cat_url;
            }
            // Image
            var img = document.querySelector('[data-aureon-slot="article.image"] img, .entry_image img');
            if (img) {
                if (art.image) { img.src = art.image; img.setAttribute('data-src', art.image); img.alt = art.title || ''; }
                else {
                    var wrap = document.querySelector('[data-aureon-slot="article.image"]');
                    if (wrap) wrap.style.display = 'none';
                }
            }
            // Content: keep the frozen structure but replace the demo paragraphs
            // with the real post content.
            var content = document.querySelector('[data-aureon-slot="article.content"]');
            if (content && art.content) {
                var existing = content.querySelectorAll('p.text, p.text_2, p:not(.entry_title)');
                var used = false;
                existing.forEach(function(p) {
                    if (!used && !p.querySelector('img')) {
                        // placeholder paragraphs carry demo copy
                        if (/Lorem|Pellentesque|dapibus|eget|Donec|Vivamus|Sed|Quisque|Aenean/i.test(p.textContent)) {
                            p.outerHTML = '';
                        }
                    }
                });
                // Append the real content once (and only if it differs from demo).
                if (content.textContent.length < 400 || !/Timeless Wardrobe/.test(content.textContent)) {
                    var node = document.createElement('div');
                    node.innerHTML = art.content;
                    // Strip a possible leading image duplicate (image slot already shows it)
                    var realImgs = node.querySelectorAll('img');
                    realImgs.forEach(function(im) {
                        if (art.image && im.src && im.src === art.image) im.remove();
                    });
                    while (node.firstChild) {
                        content.appendChild(node.firstChild);
                    }
                }
            }
            // Comments count
            var comments = document.querySelector('[data-aureon-slot="article.comments"]');
            if (comments && art.comment_count !== undefined) {
                var cCount = comments.querySelector('.entry_comment p, p');
                if (cCount && comments.querySelectorAll('.entry_comment').length === 0) {}
            }
            var commentSlot = document.querySelector('.entry_comment p');
            if (commentSlot && art.comment_count !== undefined) {
                commentSlot.textContent = art.comment_count + ' comment' + (art.comment_count === 1 ? '' : 's');
            }
        }
    };

    try {
        VinetaCartUI.init();
    } catch (e) { /* cart UI failure must not break the rest of the bridge */ }
    // Re-render cart surfaces whenever the cart changes (drawer, badge, page)
    document.addEventListener('vineta:cart-updated', function(e) {
        if (e && e.detail) { pageData.cart = e.detail; }
        if (window.VinetaCart) VinetaCart.updateCount((pageData.cart && pageData.cart.item_count) || 0);
        try { VinetaCartUI.renderDrawer(); VinetaCartUI.renderCartPage(); } catch (err) {}
    });

    try {
        VinetaBlog.init();
    } catch (e) { /* blog failure must not break the rest of the bridge */ }
    try {
        VinetaArticle.init();
    } catch (e) { /* article failure must not break the rest of the bridge */ }

    // Generic WordPress page content ??? the four legal/info templates
    // (privacy-policy, term-and-condition, shipping, return-and-refund) share
    // a `.s-term-user .content` region that ships demo placeholder copy. When
    // composer supplies real WP page content (pageData.page.content), replace
    // the demo blocks so the published page copy wins.
    var VinetaPage = {
        init: function() {
            var wp = pageData.page;
            if (!wp || !wp.content) return;
            var region = document.querySelector('.s-term-user .content');
            if (!region) return;
            var node = document.createElement('div');
            node.innerHTML = wp.content;
            region.innerHTML = '';
            while (node.firstChild) {
                region.appendChild(node.firstChild);
            }
            // Sync the page title header when it still shows template copy.
            var h = document.querySelector('.tf-page-title .title');
            if (h && wp.title && wp.title !== h.textContent.trim()) {
                h.textContent = wp.title;
            }
        }
    };
    try {
        VinetaPage.init();
    } catch (e) { /* generic page content failure must not break the bridge */ }

    try {
        VinetaHome.init();
    } catch (e) {
        /* homepage consumer failure must not break the rest of the bridge */
    }
    window.VinetaHome = VinetaHome;

    // Global chrome fill (every page): cart-drawer "You may also like",
    // quickview/quickadd and compare modals must never show the demo tees.
    // Uses whatever real product list exists: home.products on the homepage,
    // pageData.product.related elsewhere.
    try {
        var chromeProducts = (pageData.home && pageData.home.products && pageData.home.products.length)
            ? pageData.home.products
            : (pageData.product && pageData.product.related && pageData.product.related.length)
                ? pageData.product.related
                : (pageData.chrome && pageData.chrome.products);
        if (chromeProducts && chromeProducts.length) {
            VinetaHome.renderCartRecommendations(chromeProducts, 'global.cart_recommendations');
            VinetaHome.renderQuickView(chromeProducts[0]);
            VinetaHome.renderCompare(chromeProducts.slice(0, 4));
            // Search-modal "Featured product" carousel (present on every template).
            var searchSection = document.querySelector('[data-aureon-slot="global.search_products"]');
            if (searchSection) {
                VinetaHome.renderFeaturedProducts(chromeProducts, 'global.search_products');
            }
            // Fill any remaining frozen product bands on this page (cart,
            // checkout, account, blog, ...) from the real catalog.
            VinetaHome.fillStrayCards(chromeProducts);
            VinetaHome.hideDemoFreeShipHead();
        } else {
            // No product data anywhere on this page: hide the demo product
            // chrome instead of letting frozen demo rows surface.
            VinetaHome.clearChromeDemo();
            VinetaHome.hideDemoFreeShipHead();
        }
    } catch (e) { /* global chrome fill failure must not break the bridge */ }

    // Customizer bridge ??? update DOM with Customizer values.
    var VinetaCustomizer = {
        updateLogo: function(logoUrl) {
            if (!logoUrl) return;
            var logos = document.querySelectorAll('.logo-header img, .footer-logo img');
            logos.forEach(function(img) {
                img.src = logoUrl;
            });
        },

        updateAnnouncement: function(items) {
            if (!items || !items.length) return;
            var marquee = document.querySelector('[data-aureon-slot="global.announcement"]');
            if (!marquee) return;
            var container = marquee.querySelector('.initial-child-container');
            if (!container) return;
            // Build new marquee items.
            var html = '';
            items.forEach(function(item, i) {
                if (item.visible === false) return;
                var text = item.text || item.title || '';
                if (text) {
                    html += '<div class="marquee-child-item"><p>' + text + '</p></div>';
                    if (i < items.length - 1) {
                        html += '<div class="marquee-child-item"><span class="dot"></span></div>';
                    }
                }
            });
            if (html) {
                container.innerHTML = html;
            }
        },

        updateSocial: function(socials) {
            if (!socials || !socials.length) return;
            var container = document.querySelector('[data-aureon-slot="global.social"]');
            if (!container) return;
            var html = '';
            socials.forEach(function(social) {
                if (social.url && social.label) {
                    var iconClass = 'icon-' + social.label.toLowerCase().replace(/\s+/g, '-');
                    html += '<li><a href="' + social.url + '" class="social-item social-' + social.label.toLowerCase().replace(/\s+/g, '-') + '" aria-label="' + social.label + '"><i class="icon ' + iconClass + '"></i></a></li>';
                }
            });
            if (html) {
                container.innerHTML = html;
            }
        },

        updateColors: function(colors) {
            if (!colors) return;
            // Vineta styles.css consumes --primary/--primary-2/--dark/--text/--line/--surface.
            // (ferm-page injects --accent/--bg/--main-color which Vineta never reads ???
            //  this re-maps to the variables the pack actually uses, mirroring the
            //  server-side vineta_emit_customizer_css bridge in composer.php.)
            var root = document.documentElement;
            var body = document.body;
            if (colors.accent) root.style.setProperty('--primary', colors.accent);
            if (colors.accent_hover) root.style.setProperty('--primary-2', colors.accent_hover);
            if (colors.text) root.style.setProperty('--dark', colors.text);
            if (colors.muted) root.style.setProperty('--text', colors.muted);
            if (colors.border) root.style.setProperty('--line', colors.border);
            if (colors.surface) root.style.setProperty('--surface', colors.surface);
            if (colors.bg && body) body.style.backgroundColor = colors.bg;
        },

        updateTypography: function(fonts) {
            if (!fonts) return;
            var root = document.documentElement;
            if (fonts.heading) {
                root.style.setProperty('--font-heading', fonts.heading);
                document.querySelectorAll('h1,h2,h3,h4,h5,h6,.heading').forEach(function(el) {
                    el.style.fontFamily = fonts.heading;
                });
            }
            if (fonts.body) {
                root.style.setProperty('--font-body', fonts.body);
                document.querySelectorAll('body,p,.text').forEach(function(el) {
                    el.style.fontFamily = fonts.body;
                });
            }
        },

        updateSiteTitle: function(site) {
            if (!site) return;
            if (site.name) {
                document.title = site.name;
                document.querySelectorAll('.logo-header .logo,.header__logo a').forEach(function(el) {
                    if (el.querySelector('img')) return;
                    var txt = el.querySelector('span,.site-name');
                    if (txt) txt.textContent = site.name;
                });
            }
            if (site.description) {
                document.querySelectorAll('.site-tagline,.tagline').forEach(function(el) {
                    el.textContent = site.description;
                });
            }
        },

        updateHero: function(slides) {
            // Single-slide fallback (legacy). Full slider uses updateHeroSlides.
            if (!slides || !slides.length) return;
            this.updateHeroSlides(slides);
        },

        // Pick the image for the current viewport: laptop/desktop uses
        // `image`, tablet uses `tablet_image` when set, phone uses
        // `mobile_image` when set. Falls back to the larger image.
        pickHeroImage: function(slide) {
            if (!slide) return '';
            var width = window.innerWidth || document.documentElement.clientWidth || 0;
            if (width < 768 && slide.mobile_image) return slide.mobile_image;
            if (width < 1200 && slide.tablet_image) return slide.tablet_image;
            return slide.image || '';
        },

        resolveHeroImageUrl: function(src) {
            if (!src) return '';
            if (src.indexOf('http') === 0) return src;
            // Resolve pack-relative image path to an absolute URL when needed.
            if (config && config.site_url) {
                return config.site_url.replace(/\/$/, '') + '/' + src.replace(/^\/+/, '');
            }
            return src;
        },

        // Map one Customizer hero row (aether_hero_slides schema) onto one
        // Vineta .swiper-slide. Schema keys come from frontend/tokens/tokens.php
        // (+ vineta tablet_image):
        //   id, visible, headline, accent, subline, badge, image, tablet_image,
        //   mobile_image, image_alt, overlay, primary_cta{label,url},
        //   secondary_cta{label,url}
        fillHeroSlide: function(slide, clone) {
            if (!slide || !clone) return;
            var img = clone.querySelector('.image img, .slider-image img, .img-slider img');
            var src = this.resolveHeroImageUrl(this.pickHeroImage(slide));
            if (img && src) {
                img.src = src;
                img.setAttribute('data-src', src);
                if (slide.image_alt) img.alt = slide.image_alt;
            }
            // badge / accent chip (only where the slide DOM exposes one)
            if (slide.badge || slide.accent) {
                var chip = clone.querySelector('.slider-badge, .badge, .box-badge, .chip-badge, .eyebrow, .box-overline');
                if (chip) chip.textContent = slide.badge || slide.accent;
            }
            // headline
            var headline = slide.headline || slide.title || slide.heading || '';
            if (headline) {
                var h = clone.querySelector('h1, h2, .heading');
                if (h) h.textContent = headline;
            }
            // subline
            var subline = slide.subline || slide.subheading || slide.description || '';
            if (subline) {
                var sub = clone.querySelector('.sub, .subheading, .subtitle, .description, .text-sub');
                if (sub) sub.textContent = subline;
            }
            // primary CTA (first button)
            var pLabel = (slide.primary_cta && slide.primary_cta.label) || slide.button_text || slide.button_label || '';
            var pUrl = (slide.primary_cta && slide.primary_cta.url) || slide.button_url || slide.url || '';
            var btns = clone.querySelectorAll('.tf-btn, .btn, .button');
            if (pLabel && btns.length) {
                btns[0].textContent = pLabel;
                if (pUrl) btns[0].href = pUrl;
            }
            // secondary CTA (second button)
            var sLabel = (slide.secondary_cta && slide.secondary_cta.label) || '';
            var sUrl = (slide.secondary_cta && slide.secondary_cta.url) || '';
            if (sLabel && btns.length > 1) {
                btns[1].textContent = sLabel;
                if (sUrl) btns[1].href = sUrl;
            }
        },

        // Full-slider consumer: rebuild every swiper slide from Customizer data,
        // preserving the Vineta slide DOM. One slide per data row; hidden rows
        // (visible === false) are skipped.
        updateHeroSlides: function(slides) {
            if (!slides || !slides.length) {
                // No hero slides saved (client cleared/never added them): hide
                // the banner so the frozen demo slide cannot surface.
                this.hideHero();
                return;
            }
            var hero = document.querySelector('[data-aureon-slot="global.hero"] .swiper, .tf-slideshow .swiper, .slider-viewport .swiper, [data-aureon-slot="hero"] .swiper');
            if (!hero) return;
            var track = hero.querySelector('.swiper-wrapper');
            if (!track) return;
            var existing = track.querySelectorAll(':scope > .swiper-slide');
            if (!existing.length) return;
            var template = existing[0];
            var frag = document.createDocumentFragment();
            var rendered = 0;
            var renderedSlides = [];
            slides.forEach(function(slide) {
                if (!slide || slide.visible === false) return;
                var clone = template.cloneNode(true);
                this.fillHeroSlide(slide, clone);
                frag.appendChild(clone);
                renderedSlides.push(slide);
                rendered++;
            }, this);
            if (!rendered) return;
            track.innerHTML = '';
            track.appendChild(frag);
            // Remember slides so the responsive image can be swapped live when
            // the Customizer device preview resizes (tablet/phone icons).
            this._heroTrack = track;
            this._heroSlides = renderedSlides;
            if (!this._heroResizeBound) {
                this._heroResizeBound = true;
                var self = this;
                var timer = null;
                window.addEventListener('resize', function() {
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(function() { self.refreshHeroImages(); }, 120);
                });
            }
        },

        // Swap slide <img> sources to the best image for the current viewport
        // without rebuilding the slider (safe mid-animation).
        refreshHeroImages: function() {
            var track = this._heroTrack;
            var slides = this._heroSlides;
            if (!track || !slides || !slides.length) return;
            var current = track.querySelectorAll(':scope > .swiper-slide');
            slides.forEach(function(slide, idx) {
                var el = current[idx];
                if (!el) return;
                var img = el.querySelector('.image img, .slider-image img, .img-slider img');
                if (!img) return;
                var src = this.resolveHeroImageUrl(this.pickHeroImage(slide));
                if (src && img.getAttribute('src') !== src) {
                    img.src = src;
                    img.setAttribute('data-src', src);
                }
            }, this);
        },
        // Hide the whole homepage banner (the slot sits on the hero <section>)
        // when there are no saved hero slides.
        hideHero: function() {
            var hero = document.querySelector('[data-aureon-slot="global.hero"], .tf-slideshow.slider-viewport, .tf-slideshow');
            if (hero) hero.style.display = 'none';
        },

        updateFooter: function(columns) {
            if (!columns || !columns.length) return;
            var footer = document.querySelector('footer,.footer');
            if (!footer) return;
            var colEls = footer.querySelectorAll('.footer-col,.col-lg-3,.col-md-4');
            columns.forEach(function(col, i) {
                if (!colEls[i]) return;
                var heading = colEls[i].querySelector('h5,h6,.heading,.title');
                if (heading && col.title) heading.textContent = col.title;
                if (col.links && col.links.length) {
                    var list = colEls[i].querySelector('ul');
                    if (list) {
                        list.innerHTML = '';
                        col.links.forEach(function(link) {
                            var li = document.createElement('li');
                            li.innerHTML = '<a href="' + (link.url || '#') + '">' + (link.title || link.label || '') + '</a>';
                            list.appendChild(li);
                        });
                    }
                }
            });
        },

        updateNewsletter: function(data) {
            if (!data) return;
            if (data.heading) {
                document.querySelectorAll('.newsletter-heading,.newsletter h3,.newsletter h2').forEach(function(el) {
                    el.textContent = data.heading;
                });
            }
            if (data.text) {
                document.querySelectorAll('.newsletter-text,.newsletter p').forEach(function(el) {
                    // never rewrite arbitrary page copy; only newsletter paragraphs
                    if (el.closest('.newsletter, .footer-newsletter')) el.textContent = data.text;
                });
            }
            // Footer newsletter column: the Vineta homepage carries the
            // global.newsletter slot inside the footer block, with the heading
            // and copy in the column above the form.
            document.querySelectorAll('form[data-aureon-slot="global.newsletter"], form.form-newsletter').forEach(function(form) {
                var col = form.closest('.footer-col-block, .footer-col, .footer-column, .col-lg-3, .col-md-4, .footer');
                if (!col) return;
                if (data.heading) {
                    var head = col.querySelector('.footer-heading, h5, h6, .heading, .title, h4');
                    if (head) head.textContent = data.heading;
                }
                if (data.text) {
                    var p = col.querySelector('.footer-newsletter p, p');
                    if (p) p.textContent = data.text;
                }
            });
        },

        // Apply option-driven search placeholder (aether_search_placeholder) to
        // the frozen header/search inputs instead of the static "Search" copy.
        updateSearch: function(search) {
            if (!search || !search.placeholder) return;
            var ph = String(search.placeholder);
            if (!ph) return;
            var inputs = document.querySelectorAll(
                'input[type="search"], input[name="s"], input[name*="search"], ' +
                'form[role="search"] input, .search-form input, .header-search input, ' +
                '[data-search] input, .tf-search input'
            );
            inputs.forEach(function(input) {
                input.setAttribute('placeholder', ph);
            });
        },

        init: function() {
            var customizer = pageData.customizer;
            if (!customizer) return;

            // Search UI copy lives on the top-level pageData.search payload.
            if (pageData.search) {
                this.updateSearch(pageData.search);
            }

            if (customizer.site) {
                if (customizer.site.logo_url) this.updateLogo(customizer.site.logo_url);
                this.updateSiteTitle(customizer.site);
            }
            if (customizer.announcement && customizer.announcement.length) {
                this.updateAnnouncement(customizer.announcement);
            }
            if (customizer.social && customizer.social.length) {
                this.updateSocial(customizer.social);
            }
            // Color/font repaint REMOVED per client directive (2026-09-04): the
            // saved Customizer values painted the pack black. The frontend renders
            // the original approved Vineta design from styles.css as-is. Content
            // bridges below (hero/announcement/footer/newsletter/social/site)
            // remain fully dynamic.
            if (Array.isArray(customizer.hero)) {
                // Array present but empty = client cleared the hero ??? hide the
                // banner until new slides are saved in the Customizer.
                this.updateHeroSlides(customizer.hero);
            }
            if (customizer.footer && customizer.footer.length) {
                this.updateFooter(customizer.footer);
            }
            if (customizer.newsletter) {
                this.updateNewsletter(customizer.newsletter);
            }
        }
    };

    // Initialize customizer bridge when DOM is ready.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            VinetaCustomizer.init();
        });
    } else {
        VinetaCustomizer.init();
    }

    window.VinetaCustomizer = VinetaCustomizer;

    // Forms bridge ??? sends the frozen Vineta newsletter + contact forms to the
    // REAL platform endpoints (aether_newsletter_subscribe / aether_contact_submit)
    // instead of the demo actions they ship with (mail/subscribe*.php,
    // contact/contact-process.php ??? files that do not exist on WordPress).
    // Bound in the CAPTURE phase on document so Vineta's own demo handlers in
    // main.js (blockForm / ajaxContactForm / ajaxSubscribe) can never run first
    // and fake a success or POST to a dead endpoint.
    var VinetaForms = {
        feedback: function(scope, msg, ok) {
            var old = scope.querySelectorAll('.feedback-message, .flat-alert');
            for (var i = 0; i < old.length; i++) {
                if (old[i].parentNode) old[i].parentNode.removeChild(old[i]);
            }
            var p = document.createElement('p');
            p.className = ok ? 'feedback-message text-sm text-main mt_10' : 'feedback-message text-sm mt_10';
            if (!ok) p.style.color = '#c0392b';
            p.textContent = msg;
            scope.insertBefore(p, scope.firstChild);
        },
        post: function(formData, cb) {
            fetch(config.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function(response) {
                return response.json();
            }).then(cb).catch(function() {
                cb({ success: false, data: { message: 'Something went wrong. Please try again.' } });
            });
        },
        isValidEmail: function(v) {
            return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v);
        },
        // Reads the first email input inside a Vineta newsletter form ??? field
        // names differ per instance (#subscribe-email vs .subscribe-email).
        emailOf: function(form) {
            var input = form.querySelector('input[type="email"]');
            return input ? input.value.trim() : '';
        },
        newsletter: function(form) {
            var email = this.emailOf(form);
            if (!email || !this.isValidEmail(email)) {
                this.feedback(form, 'Please enter a valid email address.', false);
                return;
            }
            var btn = form.querySelector('button[type="submit"], .subscribe-button');
            if (btn) btn.disabled = true;
            var fd = new FormData();
            fd.append('action', 'aether_newsletter_subscribe');
            fd.append('nonce', config.aether_nonce || '');
            fd.append('email', email);
            var self = this;
            var input = form.querySelector('input[type="email"]');
            this.post(fd, function(res) {
                if (btn) btn.disabled = false;
                if (res && res.success) {
                    self.feedback(form, (res.data && res.data.message) ? res.data.message : 'Thank you for subscribing!', true);
                    if (input) input.value = '';
                } else {
                    self.feedback(form, (res && res.data && res.data.message) ? res.data.message : 'Subscription failed. Please try again.', false);
                }
            });
        },
        contact: function(form) {
            var val = function(n) {
                var el = form.querySelector('[name="' + n + '"]');
                return el ? el.value.trim() : '';
            };
            var name = val('name'), email = val('email'), message = val('message');
            if (!name || !message) {
                this.feedback(form, 'Please fill in every required field.', false);
                return;
            }
            if (!this.isValidEmail(email)) {
                this.feedback(form, 'Please enter a valid email address.', false);
                return;
            }
            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            var fd = new FormData();
            fd.append('action', 'aether_contact_submit');
            fd.append('aether_contact_nonce', config.contact_nonce || '');
            fd.append('aether_name', name);
            fd.append('aether_email', email);
            fd.append('aether_message', message);
            var self = this;
            this.post(fd, function(res) {
                if (btn) btn.disabled = false;
                if (res && res.success) {
                    self.feedback(form, (res.data && res.data.message) ? res.data.message : 'Thank you ??? your message has been sent.', true);
                    form.querySelector('[name="name"]').value = '';
                    form.querySelector('[name="email"]').value = '';
                    form.querySelector('[name="message"]').value = '';
                } else {
                    self.feedback(form, (res && res.data && res.data.message) ? res.data.message : 'The message could not be sent. Please try again.', false);
                }
            });
        },
        // Replaces demo address/phone/email/hours in the contact info card with
        // the values from pageData.contact (driven by options/Customizer).
        fillContactInfo: function() {
            var info = pageData.contact;
            if (!info) return;
            var list = document.querySelector('[data-aureon-slot="static.contact_info"], .contact-list, .footer-info');
            if (!list) return;
            var items = list.querySelectorAll('li');
            var addressText = (info.address || []).join(', ');
            for (var i = 0; i < items.length; i++) {
                var txt = items[i].textContent.replace(/\s+/g, ' ').trim();
                var link = items[i].querySelector('a');
                if (/(address|direction)/i.test(txt) && addressText && link) {
                    link.textContent = addressText;
                    link.href = '#';
                } else if (/(phone|call|tel|^\d|^\()/i.test(txt) && info.phone && link) {
                    link.textContent = info.phone;
                    link.href = 'tel:' + info.phone.replace(/[^+\d]/g, '');
                } else if (/(email|@|mailto)/i.test(txt) && info.email && link) {
                    link.textContent = info.email;
                    link.href = 'mailto:' + info.email;
                } else if (/(open|hours|time)/i.test(txt) && info.hours) {
                    var span = items[i].querySelector('span, .text-main');
                    if (span) span.textContent = info.hours;
                }
            }
        },
        bind: function() {
            var self = this;
            // No nonce -> platform endpoint unknown; leave frozen behaviour alone.
            if (!config.ajax_url || (!config.aether_nonce && !config.contact_nonce)) return;

            document.addEventListener('submit', function(e) {
                var t = e.target;
                if (!t || !t.classList) return;
                if (t.classList.contains('form-newsletter')) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.newsletter(t);
                } else if (t.id === 'contactform') {
                    e.preventDefault();
                    e.stopPropagation();
                    self.contact(t);
                }
            }, true);

            // The footer form's Send button is type=button (no submit event), and
            // main.js's ajaxSubscribe would otherwise POST it to mail/subscribe*.php.
            document.addEventListener('click', function(e) {
                var b = e.target && e.target.closest ? e.target.closest('.form-newsletter button, .form-newsletter .subscribe-button') : null;
                if (!b) return;
                if (b.type === 'submit') return; // handled by the submit capture
                var form = b.closest('.form-newsletter');
                if (!form) return;
                e.preventDefault();
                e.stopPropagation();
                self.newsletter(form);
            }, true);

            this.fillContactInfo();
        }
    };

    VinetaForms.bind();

    // Accessibility pass ??? presentation-level only: marks decorative images as
    // such (alt="") and derives aria-labels for Vineta inputs that rely on a
    // placeholder alone (login/register/lost-password/newsletter forms). Runs
    // after the dynamic consumers so images injected from real data keep the
    // alt text their fillers set.
    var VinetaA11y = {
        init: function() {
            // 1) Images without any alt attribute.
            Array.prototype.forEach.call(document.images, function(img) {
                if (img.hasAttribute('alt')) return;
                // Product/cart/article images: derive from a nearby title if possible.
                var card = img.closest('.card-product, .tf-mini-cart-item, article, .swiper-slide, .tf-product-media');
                var title = null;
                if (card) {
                    var t = card.querySelector('.product-title, h3, h4, h5, .title, .heading, .name, a[href*="/product/"]');
                    if (t && t.textContent.trim().length < 120) title = t.textContent.trim();
                }
                img.setAttribute('alt', title ? title : '');
            });
            // 2) Inputs without a label/aria-label/title.
            var labelFor = {
                'username': 'Username', 'user_login': 'Username', 'log': 'Username',
                'password': 'Password', 'pwd': 'Password', 'email': 'Email address',
                'email-form': 'Email address', 'subscribe-email': 'Email address',
                'message': 'Message', 'name': 'Name', 'qty': 'Quantity',
                'quantity': 'Quantity', 'country': 'Country', 'state': 'State',
                'code': 'ZIP / Postcode', 'search': 'Search', 's': 'Search',
                'agree_checkbox': 'I agree to the terms and conditions',
                'CartDrawer-Form_agree': 'I agree to the terms and conditions',
                'address[country]': 'Shipping country', 'address[province]': 'Shipping state / province',
                'address[zip]': 'ZIP / Postcode',
                'shipping-country-form': 'Shipping country', 'shipping-province-form': 'Shipping state / province',
                'zipcode': 'ZIP / Postcode',
                'checkGift': 'Gift options', 'check-agree': 'I agree to the terms and conditions',
                'note': 'Order note'
            };
            Array.prototype.forEach.call(document.querySelectorAll('input, textarea, select'), function(el) {
                if (el.type === 'hidden' || el.type === 'submit' || el.type === 'button') return;
                if (el.getAttribute('aria-label') || el.getAttribute('title')) return;
                if (el.closest('label')) return;
                var label = null;
                var ph = (el.getAttribute('placeholder') || '').replace(/\s*\*+\s*$/, '').trim();
                if (ph) {
                    label = ph;
                } else if (el.name && labelFor[el.name]) {
                    label = labelFor[el.name];
                } else if (el.id && labelFor[el.id]) {
                    label = labelFor[el.id];
                }
                if (!label) {
                    // Currency/language pickers (bootstrap-select hides the raw select).
                    if (el.classList && el.classList.contains('image-select')) {
                        var host = el.closest('[class*="currency"], [class*="language"], [class*="Currency"], [class*="Language"], .dropdown, .tf-currency, .tf-language');
                        label = host ? (host.className.indexOf('currency') >= 0 || host.className.indexOf('Currency') >= 0 ? 'Currency selector' : 'Language selector') : 'Select option';
                    } else if (el.type === 'text' && el.closest('.box-ip-discount, .tf-mini-cart-tool-code, [class*="discount"], [class*="coupon"]')) {
                        label = 'Discount code';
                    } else if (el.type === 'radio') {
                        // Filter radios (availability/brand/colour) carry their visible
                        // label as sibling text inside the wrapping list item.
                        var host = el.closest('li, .filter-field, .tf-check-wrap, [class*="filter"], .widget-filter');
                        if (host) {
                            var tx = (host.textContent || '').replace(/\s+/g, ' ').trim();
                            if (tx) label = tx.slice(0, 60);
                        }
                        if (!label) label = 'Filter option';
                    } else if (el.classList && (el.classList.contains('quantity-product') || (el.parentElement && el.parentElement.className.indexOf('wg-quantity') >= 0))) {
                        label = 'Quantity';
                    }
                }
                if (label) el.setAttribute('aria-label', label);
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.setTimeout(VinetaA11y.init, 400);
        });
    } else {
        window.setTimeout(VinetaA11y.init, 400);
    }

    // Variable-product variation UI ??? consumes the MODERN product schema
    // (pageData.product.variants / options / product_type). The composer's
    // legacy inline variation builder reads an older schema whose attribute
    // options are term IDs, so it can never match a real variation. This
    // consumer rebuilds the picker from the real variant data (string option
    // values, per-variation price/SKU/availability) and owns add-to-cart so
    // the selected variation lands in the real WC cart.
    var VinetaVariations = {
        pretty: function(s) {
            if (!s) return '';
            return String(s).split(/[\s_-]+/).map(function(w) {
                return w.charAt(0).toUpperCase() + w.slice(1);
            }).join(' ');
        },
        fmt: function(cents, currency) {
            if (cents === null || cents === undefined) return '';
            currency = currency || 'USD';
            var v = (cents / 100).toFixed(2);
            if (currency === 'EUR') return '\u20ac' + v;
            if (currency === 'USD') return '$' + v;
            return currency + ' ' + v;
        },
        init: function() {
            var product = pageData.product;
            if (!product || product.product_type !== 'variable' || !product.variants || !product.variants.length) return;
            if (this._done) return;
            this._done = true;
            this.product = product;
            this.selected = {};   // attrIndex -> raw value
            this.priceEl = null;
            this.skuEl = document.querySelector('[data-aureon-slot="product.sku"]');

            // Container: reuse the section the legacy builder targeted, clearing
            // any numeric/term-id picker it may have created.
            var section = document.querySelector('.tf-product-info-variation');
            if (!section) {
                section = document.createElement('div');
                section.className = 'tf-product-info-variation';
                var wrap = document.querySelector('.tf-product-info-wrap');
                if (wrap) {
                    wrap.insertBefore(section, wrap.firstChild);
                } else {
                    var info = document.querySelector('.tf-product-info-main, .tf-product-info');
                    if (!info) return;
                    info.insertBefore(section, info.firstChild);
                }
            }
            // Neutralize any frozen/legacy variation markup inside the slot.
            var frozenSlot = document.querySelector('[data-aureon-slot="product.variation"]');
            if (frozenSlot) frozenSlot.style.display = 'none';
            var stale = section.querySelectorAll('.btn-variant, .variant-option');
            stale.forEach(function(el) { el.parentNode.removeChild(el); });

            // Build one group per option dimension present on the variants.
            var groupVals = [];
            var maxIdx = 3;
            var present = [false, false, false];
            product.variants.forEach(function(v) {
                if (v.option1 !== null && v.option1 !== undefined) present[0] = true;
                if (v.option2 !== null && v.option2 !== undefined) present[1] = true;
                if (v.option3 !== null && v.option3 !== undefined) present[2] = true;
            });
            var html = '';
            var self = this;
            for (var idx = 0; idx < maxIdx; idx++) {
                if (!present[idx]) continue;
                var seen = {};
                var vals = [];
                product.variants.forEach(function(v) {
                    var raw = v['option' + (idx + 1)];
                    if (raw !== null && raw !== undefined && !seen[raw]) {
                        seen[raw] = true;
                        vals.push(raw);
                    }
                });
                groupVals.push({ idx: idx, vals: vals, label: this.attrLabel(idx) });
                html += '<div class="variant-option mb-3"><label class="text-sm fw-medium mb-2 d-block">' + this.attrLabel(idx) + ':</label><div class="list-color-product d-flex gap-2 flex-wrap">';
                vals.forEach(function(raw, i) {
                    var disabled = self.optionDisabled(idx, raw) ? ' disabled' : '';
                    html += '<button type="button" class="btn-variant' + (disabled ? ' is-disabled' : '') + '" data-idx="' + idx + '" data-value="' + raw.replace(/"/g, '&quot;') + '"' + disabled + '>' + self.pretty(raw) + '</button>';
                });
                html += '</div></div>';
            }
            section.innerHTML = html;

            // Minimal inline styling consistent with the legacy buttons.
            var style = document.createElement('style');
            style.textContent = '.tf-product-info-variation .btn-variant{padding:8px 16px;border:1px solid #ccc;border-radius:4px;cursor:pointer;background:#fff;color:#333;font-size:14px;line-height:1.2;transition:all .2s ease}.tf-product-info-variation .btn-variant.is-selected{background:#333;border-color:#333;color:#fff}.tf-product-info-variation .btn-variant.is-disabled{opacity:.35;cursor:not-allowed;text-decoration:line-through}.tf-product-info-variation .variant-availability{margin:6px 0 0;font-size:13px}';
            document.head.appendChild(style);

            section.querySelectorAll('.btn-variant').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    self.select(parseInt(btn.getAttribute('data-idx'), 10), btn.getAttribute('data-value'), btn);
                });
            });

            // Preselect the first available variation (mirrors selected_variant_id).
            if (product.selected_variant_id) {
                var def = this.findVariantById(product.selected_variant_id);
                if (def) {
                    for (var i = 0; i < maxIdx; i++) {
                        var raw = def['option' + (i + 1)];
                        if (raw !== null && raw !== undefined) this.selected[i] = raw;
                    }
                }
            } else {
                var first = product.variants.filter(function(v) { return v.available !== false; })[0] || product.variants[0];
                if (first) {
                    for (var j = 0; j < maxIdx; j++) {
                        var r2 = first['option' + (j + 1)];
                        if (r2 !== null && r2 !== undefined) this.selected[j] = r2;
                    }
                }
            }
            section.querySelectorAll('.btn-variant').forEach(function(btn) {
                var idx = parseInt(btn.getAttribute('data-idx'), 10);
                if (self.selected[idx] === btn.getAttribute('data-value')) {
                    btn.classList.add('is-selected');
                }
                self.refreshDisabled(idx, btn);
            });
            this.applySelection();
            this.ownAddToCart();
        },
        attrLabel: function(idx) {
            var opt = this.product.options && this.product.options[idx];
            if (!opt) return 'Option ' + (idx + 1);
            return this.pretty(String(opt).replace(/^pa_/, ''));
        },
        select: function(idx, raw, btn) {
            this.selected[idx] = raw;
            var section = btn.closest('.tf-product-info-variation');
            section.querySelectorAll('.btn-variant').forEach(function(b) {
                var i = parseInt(b.getAttribute('data-idx'), 10);
                if (i === idx) b.classList.toggle('is-selected', b.getAttribute('data-value') === raw);
                this.refreshDisabled(i, b);
            }, this);
            this.applySelection();
        },
        // True when no available variation can exist if this value is chosen for idx.
        optionDisabled: function(idx, raw) {
            var sel = {};
            for (var k in this.selected) sel[k] = this.selected[k];
            sel[idx] = raw;
            return !this.product.variants.some(function(v) {
                if (v.available === false) return false;
                for (var i = 0; i < 3; i++) {
                    var r = v['option' + (i + 1)];
                    if (r === null || r === undefined) continue;
                    if (sel[i] !== undefined && sel[i] !== r) return false;
                }
                return true;
            });
        },
        refreshDisabled: function(idx, btn) {
            if (btn.getAttribute('data-idx') !== String(idx)) return;
            var disabled = this.optionDisabled(idx, btn.getAttribute('data-value'));
            btn.disabled = disabled;
            btn.classList.toggle('is-disabled', disabled);
            if (disabled) btn.classList.remove('is-selected');
        },
        findVariant: function() {
            var self = this;
            return this.product.variants.filter(function(v) {
                for (var i = 0; i < 3; i++) {
                    var r = v['option' + (i + 1)];
                    if (r === null || r === undefined) continue;
                    if (self.selected[i] !== undefined && self.selected[i] !== r) return false;
                }
                return true;
            })[0] || null;
        },
        findVariantById: function(id) {
            var out = null;
            this.product.variants.forEach(function(v) { if (v.id === id) out = v; });
            return out;
        },
        applySelection: function() {
            var match = this.findVariant();
            if (!match) return;
            window.vinetaSelectedVariationId = match.id;
            // Price region ??? update the visible .product-price block children
            // (.price-new / .price-old / sale badge) for the selected variation.
            if (!this.priceBlock) {
                var zones = Array.prototype.slice.call(document.querySelectorAll('.product-price, .tf-product-info-price'));
                var visible = zones.filter(function(z) {
                    return z.offsetParent !== null || z.getClientRects().length > 0;
                });
                this.priceBlock = (visible[0] || zones[0]) || null;
            }
            if (this.priceBlock) {
                var cur = this.priceBlock.querySelector('.price-new');
                var old = this.priceBlock.querySelector('.price-old');
                var badge = this.priceBlock.querySelector('.badge-sale, .on-sale');
                if (cur) cur.textContent = this.fmt(match.price, this.product.currency);
                if (old) {
                    if (match.compare_at_price && match.compare_at_price > match.price) {
                        old.textContent = this.fmt(match.compare_at_price, this.product.currency);
                        old.style.display = '';
                    } else {
                        old.style.display = 'none';
                    }
                }
                if (badge) {
                    if (match.compare_at_price && match.compare_at_price > match.price) {
                        badge.style.display = '';
                        var pct = Math.round((1 - match.price / match.compare_at_price) * 100);
                        if (!isNaN(pct)) badge.textContent = pct + '% Off';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
            if (this.skuEl && match.sku) this.skuEl.textContent = match.sku;
            // Availability line.
            var availBox = document.querySelector('.tf-product-info-variation .variant-availability');
            if (!availBox) {
                availBox = document.createElement('p');
                availBox.className = 'variant-availability';
                var section = document.querySelector('.tf-product-info-variation');
                if (section) section.appendChild(availBox);
            }
            availBox.textContent = match.available === false ? 'This combination is currently unavailable.' : (match.inventory_quantity !== null && match.inventory_quantity !== undefined ? 'In stock \u2014 ' + match.inventory_quantity + ' available' : 'In stock');
            availBox.style.color = match.available === false ? '#c0392b' : '#2e7d32';
        },
        qty: function() {
            var input = document.querySelector('.tf-product-info-quantity .quantity-product, .wg-quantity .quantity-product, .quantity input, input[name="number"]');
            var v = input ? parseInt(input.value, 10) : 1;
            return (isNaN(v) || v < 1) ? 1 : v;
        },
        ownAddToCart: function() {
            var self = this;
            document.addEventListener('click', function(e) {
                var btn = e.target && e.target.closest ? e.target.closest('[data-aureon-slot="product.add_to_cart"], .btn-add-to-cart') : null;
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();
                var match = self.findVariant();
                if (!match) {
                    alert('Please select a variation first.');
                    return;
                }
                if (match.available === false) {
                    alert('This combination is currently unavailable.');
                    return;
                }
                var fd = new FormData();
                fd.append('action', 'vineta_add_to_cart');
                fd.append('nonce', config.nonce);
                fd.append('product_id', self.product.id);
                fd.append('variation_id', match.id);
                fd.append('quantity', self.qty());
                fetch(config.ajax_url, { method: 'POST', body: fd, credentials: 'same-origin' })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res && res.success) {
                            // vineta_add_to_cart answers with raw WC cart_contents;
                            // the badge + drawer consumers need the normalized
                            // { items, item_count, total_price } payload, so fetch
                            // it again through vineta_cart_get before re-rendering.
                            VinetaCart.get()
                                .then(function(cart) {
                                    if (cart && cart.success) {
                                        pageData.cart = cart.data;
                                        VinetaCart.updateCount(cart.data.item_count);
                                        document.dispatchEvent(new CustomEvent('vineta:cart-updated', { detail: cart.data }));
                                    }
                                })
                                .catch(function() {});
                            var mc = document.querySelector('.tf-mini-cart-wrap');
                            if (mc) mc.classList.add('active-open');
                        }
                    })
                    .catch(function() {});
            }, true);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.setTimeout(function() { VinetaFooterFixes.init(); }, 100);
            window.setTimeout(VinetaVariations.init.bind(VinetaVariations), 600);
        });
    } else {
        window.setTimeout(function() { VinetaFooterFixes.init(); }, 100);
        window.setTimeout(VinetaVariations.init.bind(VinetaVariations), 600);
    }

    // Footer fixes: dynamic copyright year, Snapchat link label, newsletter button label, marquee typo
    var VinetaFooterFixes = {
        init: function() {
            // Fix "Life-time Guarantes" typo in announcement/marquee
            var allP = document.querySelectorAll('p');
            allP.forEach(function(el) {
                if (el.textContent.includes('Life-time Guarantes')) {
                    el.textContent = el.textContent.replace('Life-time Guarantes', 'Lifetime Guarantees');
                }
            });
            // Dynamic copyright year
            var footer = document.querySelector('footer, .footer');
            if (footer) {
                var allEls = footer.querySelectorAll('p, span');
                var currentYear = new Date().getFullYear();
                allEls.forEach(function(el) {
                    if (/\b2025\b/.test(el.textContent)) {
                        el.textContent = el.textContent.replace(/2025/g, String(currentYear));
                    }
                });
            }
            // Fix Snapchat link - add missing text
            var snapLinks = document.querySelectorAll('a[href*="snapchat"]');
            snapLinks.forEach(function(link) {
                if (!link.textContent.trim()) {
                    link.textContent = 'Snapchat';
                    link.setAttribute('aria-label', 'Follow us on Snapchat');
                }
            });
            // Fix newsletter submit button - add label
            var nlBtns = document.querySelectorAll('.form-newsletter button[type="submit"], .newsletter button[type="submit"], .footer-newsletter button');
            nlBtns.forEach(function(btn) {
                if (!btn.textContent.trim()) {
                    btn.innerHTML = '<i class="icon-arrow-right"></i>';
                    btn.setAttribute('aria-label', 'Subscribe');
                }
            });
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            VinetaFooterFixes.init();
        });
    } else {
        VinetaFooterFixes.init();
    }

    // Newsletter Popup — replaces the frozen blank-banner popup with a
    // CSS-styled version using the coral brand gradient and dynamic content.
    var VinetaNewsletterPopup = {
        init: function() {
            var data = (window.VinetaPageData && window.VinetaPageData.customizer && window.VinetaPageData.customizer.newsletter) || {};
            var heading = data.heading || 'Sign up to our Newsletter';
            var text = data.text || 'Be the first to get the latest news about trends, promotions, and much more!';
            var siteUrl = (window.VinetaPageData && window.VinetaPageData.site && window.VinetaPageData.site.url) || '/';
            var privacyUrl = siteUrl + 'privacy-policy/';

            // Remove the old blank-banner popup
            var old = document.querySelector('.modal-newsletter');
            if (old && old.parentNode) old.parentNode.removeChild(old);

            // Inject styles
            var css = document.createElement('style');
            css.textContent = [
                '.vn-popup-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1050;display:none;opacity:0;transition:opacity .3s}',
                '.vn-popup-overlay.vn-show{display:block;opacity:1}',
                '.vn-popup{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) scale(.92);z-index:1051;width:480px;max-width:92vw;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.25);display:none;opacity:0;transition:opacity .35s ease,transform .35s ease}',
                '.vn-popup.vn-show{display:block;opacity:1;transform:translate(-50%,-50%) scale(1)}',
                '.vn-banner{position:relative;height:220px;background:linear-gradient(135deg,#ff6f61 0%,#ff8a7a 40%,#ffb4a2 70%,#ffd6c0 100%);overflow:hidden;display:flex;align-items:center;justify-content:center}',
                '.vn-banner::before{content:"";position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")} ',
                '.vn-banner::after{content:"";position:absolute;bottom:-30px;left:50%;transform:translateX(-50%);width:80px;height:80px;background:#ff6f61;border-radius:50%;display:flex;align-items:center;justify-content:center}',
                '.vn-banner-icon{position:relative;z-index:2;color:#fff;font-size:42px;line-height:1}',
                '.vn-close{position:absolute;top:12px;right:12px;z-index:5;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.2);border:none;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);transition:background .2s}',
                '.vn-close:hover{background:rgba(255,255,255,.4)}',
                '.vn-body{padding:36px 32px 28px;text-align:center}',
                '.vn-body h3{font-family:var(--font-heading,"Playfair Display",serif);font-size:22px;font-weight:700;color:#222;margin:0 0 8px;line-height:1.3}',
                '.vn-body p{font-size:14px;color:#666;margin:0 0 20px;line-height:1.6}',
                '.vn-form{position:relative;margin-bottom:16px}',
                '.vn-form input[type="email"]{width:100%;padding:14px 44px 14px 16px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;color:#333;background:#fafafa;outline:none;transition:border-color .2s}',
                '.vn-form input[type="email"]:focus{border-color:#ff6f61}',
                '.vn-form input[type="email"]::placeholder{color:#aaa}',
                '.vn-form .vn-mail-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#999;font-size:16px;pointer-events:none}',
                '.vn-submit{width:100%;padding:14px;border:none;border-radius:8px;background:#222;color:#fff;font-size:15px;font-weight:600;cursor:pointer;transition:background .2s;letter-spacing:.3px}',
                '.vn-submit:hover{background:#ff6f61}',
                '.vn-social{display:flex;gap:12px;justify-content:center;margin:16px 0 12px}',
                '.vn-social a{width:36px;height:36px;border-radius:50%;background:#f5f5f5;display:flex;align-items:center;justify-content:center;color:#555;text-decoration:none;transition:background .2s,color .2s;font-size:14px}',
                '.vn-social a:hover{background:#ff6f61;color:#fff}',
                '.vn-privacy{font-size:12px;color:#999;margin:0}',
                '.vn-privacy a{color:#666;text-decoration:underline}',
                '@media(max-width:520px){.vn-popup{width:95vw}.vn-banner{height:160px}.vn-body{padding:24px 20px 20px}}'
            ].join('\n');
            document.head.appendChild(css);

            // Build popup HTML
            var overlay = document.createElement('div');
            overlay.className = 'vn-popup-overlay';
            overlay.id = 'vn-newsletter-overlay';

            var popup = document.createElement('div');
            popup.className = 'vn-popup';
            popup.id = 'vn-newsletter-popup';
            popup.setAttribute('role', 'dialog');
            popup.setAttribute('aria-label', 'Newsletter signup');

            popup.innerHTML =
                '<div class="vn-banner">' +
                    '<span class="vn-banner-icon">&#9993;</span>' +
                    '<button class="vn-close" aria-label="Close newsletter popup">&times;</button>' +
                '</div>' +
                '<div class="vn-body">' +
                    '<h3>' + escapeHtml(heading) + '</h3>' +
                    '<p>' + escapeHtml(text) + '</p>' +
                    '<form class="vn-form" data-mailchimp="true">' +
                        '<input type="email" name="email" placeholder="Your email address" required aria-label="Email address">' +
                        '<span class="vn-mail-icon">&#9993;</span>' +
                    '</form>' +
                    '<button class="vn-submit" type="button">Subscribe</button>' +
                    '<div class="vn-social">' +
                        '<a href="https://x.com/" aria-label="Follow us on X" target="_blank" rel="noopener">&#120143;</a>' +
                        '<a href="https://www.facebook.com/" aria-label="Follow us on Facebook" target="_blank" rel="noopener">f</a>' +
                        '<a href="https://www.instagram.com/" aria-label="Follow us on Instagram" target="_blank" rel="noopener">&#9737;</a>' +
                        '<a href="https://www.youtube.com/" aria-label="Follow us on YouTube" target="_blank" rel="noopener">&#9654;</a>' +
                    '</div>' +
                    '<p class="vn-privacy">Will be used in accordance with our <a href="' + escapeHtml(privacyUrl) + '">Privacy Policy</a></p>' +
                '</div>';

            document.body.appendChild(overlay);
            document.body.appendChild(popup);

            // Auto-show after 3 seconds (once per session per page)
            var pageKey = 'vn_popup_' + window.location.pathname;
            if (!sessionStorage.getItem(pageKey)) {
                setTimeout(function() {
                    showPopup();
                }, 3000);
            }

            // Close handlers
            function closePopup() {
                popup.classList.remove('vn-show');
                overlay.classList.remove('vn-show');
                sessionStorage.setItem(pageKey, '1');
            }

            popup.querySelector('.vn-close').addEventListener('click', closePopup);
            overlay.addEventListener('click', closePopup);

            // Subscribe button
            popup.querySelector('.vn-submit').addEventListener('click', function() {
                var emailInput = popup.querySelector('input[type="email"]');
                var email = emailInput ? emailInput.value.trim() : '';
                if (!email || !emailInput.checkValidity()) {
                    if (emailInput) emailInput.focus();
                    return;
                }
                // Send to WP AJAX
                var fd = new FormData();
                fd.append('action', 'aether_newsletter_subscribe');
                fd.append('email', email);
                var ajaxUrl = (window.vineta_bridge && window.vineta_bridge.ajax_url) || '/wp-admin/admin-ajax.php';
                fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success) {
                            emailInput.value = '';
                            popup.querySelector('.vn-body').innerHTML =
                                '<h3 style="color:#ff6f61;margin-top:20px">Thank you!</h3>' +
                                '<p>You have been subscribed to our newsletter.</p>';
                            setTimeout(closePopup, 2500);
                        } else {
                            var msg = (res.data && res.data.message) || 'Something went wrong. Please try again.';
                            alert(msg);
                        }
                    })
                    .catch(function() {
                        alert('Network error. Please try again later.');
                    });
            });

            // Keyboard: Escape to close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && popup.classList.contains('vn-show')) {
                    closePopup();
                }
            });

            function showPopup() {
                overlay.classList.add('vn-show');
                popup.classList.add('vn-show');
            }

            function escapeHtml(str) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            VinetaNewsletterPopup.init();
        });
    } else {
        VinetaNewsletterPopup.init();
    }

    })();



