/**
 * Vineta Data Shims — Bridge between Vineta frontend and AUREON/WooCommerce.
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
            var countEls = document.querySelectorAll('.count-cart, .cart-count, .tf-cart-count');
            countEls.forEach(function(el) {
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

    // Expose globally.
    window.VinetaCart = VinetaCart;

    // Customizer bridge — update DOM with Customizer values.
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
            var root = document.documentElement;
            if (colors.bg) root.style.setProperty('--body-bg', colors.bg);
            if (colors.surface) root.style.setProperty('--surface', colors.surface);
            if (colors.text) root.style.setProperty('--body-text', colors.text);
            if (colors.muted) root.style.setProperty('--text-muted', colors.muted);
            if (colors.accent) root.style.setProperty('--main-color', colors.accent);
            if (colors.accent_hover) root.style.setProperty('--main-color-hover', colors.accent_hover);
            if (colors.border) root.style.setProperty('--border-color', colors.border);
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
            if (!slides || !slides.length) return;
            var hero = document.querySelector('[data-aureon-slot="hero"],.hero-section,.banner-section,.slider');
            if (!hero) return;
            var slide = slides[0];
            if (!slide) return;
            // Update hero image
            if (slide.image) {
                var img = hero.querySelector('img');
                if (img) img.src = slide.image;
            }
            // Update hero heading
            if (slide.heading || slide.title) {
                var heading = hero.querySelector('h1,h2,.heading,.title');
                if (heading) heading.textContent = slide.heading || slide.title;
            }
            // Update hero subheading
            if (slide.subheading || slide.description) {
                var sub = hero.querySelector('.subheading,.description,.subtitle,.text');
                if (sub) sub.textContent = slide.subheading || slide.description;
            }
            // Update hero button text and link
            if (slide.button_text || slide.button_label) {
                var btn = hero.querySelector('.tf-btn,.btn,.button');
                if (btn) {
                    btn.textContent = slide.button_text || slide.button_label;
                    if (slide.button_url || slide.url) btn.href = slide.button_url || slide.url;
                }
            }
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
                    el.textContent = data.text;
                });
            }
        },

        init: function() {
            var customizer = pageData.customizer;
            if (!customizer) return;

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
            if (customizer.colors) {
                this.updateColors(customizer.colors);
            }
            if (customizer.fonts) {
                this.updateTypography(customizer.fonts);
            }
            if (customizer.hero && customizer.hero.length) {
                this.updateHero(customizer.hero);
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

})();
