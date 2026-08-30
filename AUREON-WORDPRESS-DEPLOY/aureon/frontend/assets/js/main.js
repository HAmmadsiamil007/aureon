// AETHER — Main JavaScript

document.addEventListener('DOMContentLoaded', () => {
    // Preloader MUST resolve independently — even if a later module throws
    // (e.g. Swiper CDN blocked), the splash must never stay on screen.
    try {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            const progress = preloader.querySelector('.preloader-progress');
            let p = 0;
            const interval = setInterval(() => {
                p += Math.random() * 25 + 5;
                if (p >= 100) {
                    p = 100;
                    clearInterval(interval);
                    preloader.classList.add('loaded');
                    setTimeout(() => { preloader.remove(); }, 700);
                }
                if (progress) progress.style.width = p + '%';
            }, 200);
        }
    } catch (err) { /* Rule 7: never block the page on preloader failures */ }

    // Register GSAP plugins (if available)
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }

    // ─── Header — Fixed, no scroll effects ────────────────────

    // ─── Mobile Menu Toggle ──────────────────────────────────
        // Smart Sticky Header (hide on scroll down, show on scroll up)
    const header = document.getElementById('header');
    if (header) {
        let lastScrollY = 0;
        let ticking = false;
        const SCROLL_THRESHOLD = 10;

        function updateHeader() {
            const scrollY = window.scrollY;
            const isMobile = window.innerWidth <= 768;
            if (isMobile) { ticking = false; return; }

            // Premium scroll: transparent → solid background, never hide
            if (scrollY > 80) {
                header.classList.add('header--scrolled');
            } else {
                header.classList.remove('header--scrolled');
            }

            lastScrollY = scrollY;
            ticking = false;
        }
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateHeader);
                ticking = true;
            }
        }, { passive: true });
    }

const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenuBtn.classList.toggle('active');
            mainNav.classList.toggle('active');
            document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
        });
    }

    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (mobileMenuBtn) mobileMenuBtn.classList.remove('active');
            if (mainNav) mainNav.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    document.querySelectorAll('.nav-mobile-icons .header-icon').forEach(icon => {
        icon.addEventListener('click', () => {
            if (mobileMenuBtn) mobileMenuBtn.classList.remove('active');
            if (mainNav) mainNav.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // ─── Mobile Slide-Out Menu ───────────────────────────────
    const mobileHamburger = document.getElementById('mobileHamburger');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuClose = document.getElementById('mobileMenuClose');

    function openMobileMenu() {
        if (mobileHamburger) mobileHamburger.classList.add('active');
        if (mobileMenuOverlay) mobileMenuOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (mobileHamburger) mobileHamburger.classList.remove('active');
        if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (mobileHamburger) mobileHamburger.addEventListener('click', openMobileMenu);
    if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', (e) => {
            if (e.target === mobileMenuOverlay) closeMobileMenu();
        });
    }

    // Close menu on any mobile nav link click
    document.querySelectorAll('.mobile-nav-link, .mobile-cta').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });

    // ─── Mobile Announcement Rotation ────────────────────────
    const mobileAnnouncementTexts = document.querySelectorAll('.mobile-announcement-text');
    let mobileAnnouncementIndex = 0;

    if (mobileAnnouncementTexts.length > 1) {
        setInterval(() => {
            mobileAnnouncementTexts[mobileAnnouncementIndex].classList.remove('active');
            mobileAnnouncementIndex = (mobileAnnouncementIndex + 1) % mobileAnnouncementTexts.length;
            mobileAnnouncementTexts[mobileAnnouncementIndex].classList.add('active');
        }, 5000);
    }

    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('a') || e.target.closest('button')) return;
            const link = card.querySelector('a[href]');
            if (link && link.href && link.href !== '#') {
                window.location.href = link.href;
            }
        });
    });

    document.querySelectorAll('.nav-dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                toggle.closest('.nav-dropdown').classList.toggle('open');
            }
        });
    });

    // ─── Hero Swiper ─────────────────────────────────────────
    const heroSwiperEl = document.querySelector('.hero-swiper');

    if (heroSwiperEl && typeof Swiper !== 'undefined') {
        const heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            speed: 1200,
            parallax: true,
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            pagination: false,
            on: {
                slideChange: function () {
                    const current = (this.realIndex + 1).toString().padStart(2, '0');
                    const counter = document.querySelector('.hero-current-slide');
                    if (counter) counter.textContent = current;
                },
                autoplayTimeLeft: function (swiper, time, progress) {
                    const bar = document.querySelector('.hero-slider-progress');
                    if (bar) {
                        bar.style.setProperty('--progress', (1 - progress) * 100 + '%');
                    }
                },
            },
        });

        const prevBtn = document.querySelector('.hero-nav-prev');
        const nextBtn = document.querySelector('.hero-nav-next');

        if (prevBtn) prevBtn.addEventListener('click', () => heroSwiper.slidePrev());
        if (nextBtn) nextBtn.addEventListener('click', () => heroSwiper.slideNext());

        heroSwiper.on('autoplayTimeLeft', (swiper, timeLeft, progress) => {
            const bar = document.querySelector('.hero-slider-progress');
            if (bar) {
                bar.style.setProperty('--progress', (1 - progress) * 100 + '%');
            }
        });

        let progressInterval;
        function startProgress(duration) {
            const bar = document.querySelector('.hero-slider-progress');
            if (!bar) return;
            let start = Date.now();
            bar.style.cssText = '';
            const styleId = 'hero-progress-style';
            let styleEl = document.getElementById(styleId);
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = styleId;
                document.head.appendChild(styleEl);
            }
            clearInterval(progressInterval);
            progressInterval = setInterval(() => {
                const elapsed = Date.now() - start;
                const pct = Math.min((elapsed / duration) * 100, 100);
                styleEl.textContent = `.hero-slider-progress::after { width: ${pct}% !important; }`;
                if (pct >= 100) clearInterval(progressInterval);
            }, 30);
        }

        heroSwiper.on('slideChange', () => {
            startProgress(6000);
            document.dispatchEvent(new CustomEvent('heroAnimateSlide', {
                detail: { index: heroSwiper.realIndex }
            }));
        });
        startProgress(6000);
    }

    // ─── Reviews Swiper ──────────────────────────────────────
    const reviewsSwiperEl = document.querySelector('.reviews-swiper');

    if (reviewsSwiperEl && typeof Swiper !== 'undefined') {
        new Swiper('.reviews-swiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            pagination: {
                el: '.reviews-pagination',
                clickable: true,
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
        });
    }

    // ─── FAQ Accordion ───────────────────────────────────────
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const isActive = item.classList.contains('active');
            const column = item.closest('.faq-column') || item.closest('.faq-grid');
            column.querySelectorAll('.faq-item').forEach(i => {
                i.classList.remove('active');
                i.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
                i.querySelector('.faq-question i').className = 'fas fa-plus';
            });
            if (!isActive) {
                item.classList.add('active');
                btn.setAttribute('aria-expanded', 'true');
                btn.querySelector('i').className = 'fas fa-minus';
            }
        });
    });

    // ─── Newsletter Forms (AJAX with graceful simulated fallback) ──────
    // Posts to admin-ajax (aether_newsletter_subscribe). If AJAX is
    // unavailable or errors, fall back to the previous client-side success.

    function aetherSubscribeNewsletter(email, successEl, form) {
        if (!email) return;
        if (window.aetherAjax && window.aetherAjax.ajaxUrl && window.aetherAjax.nonce) {
            var body = new URLSearchParams();
            body.append('action', 'aether_newsletter_subscribe');
            body.append('nonce', window.aetherAjax.nonce);
            body.append('email', email);
            fetch(window.aetherAjax.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: body.toString()
            }).then(function (response) {
                return response.json();
            }).then(function (json) {
                if (json && json.success) {
                    form.style.display = 'none';
                    if (successEl) successEl.classList.add('is-visible');
                } else {
                    aetherSubscribeNewsletterFallback(form, successEl, json && json.data && json.data.message);
                }
            }).catch(function () {
                aetherSubscribeNewsletterFallback(form, successEl);
            });
        } else {
            aetherSubscribeNewsletterFallback(form, successEl);
        }
    }

    function aetherSubscribeNewsletterFallback(form, successEl, message) {
        form.style.display = 'none';
        if (successEl) {
            if (message) {
                var msg = successEl.querySelector('.newsletter-success-text');
                if (msg) msg.textContent = message;
            }
            successEl.classList.add('is-visible');
        }
    }

    document.querySelectorAll('.newsletter-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = form.querySelector('.newsletter-input');
            var success = form.closest('.newsletter-inner') ? form.closest('.newsletter-inner').querySelector('.newsletter-success') : null;
            if (!success) success = form.parentElement.querySelector('.newsletter-success');
            aetherSubscribeNewsletter(email ? email.value : '', success, form);
        });
    });

    document.querySelectorAll('.footer-newsletter-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var emailInput = form.querySelector('input');
            aetherSubscribeNewsletter(emailInput ? emailInput.value : '', null, form);
        });
    });

    // ─── Contact Form (AJAX) ────────────────────────────────
    // Posts the AETHER source form (hidden action + aether_contact_nonce)
    // to admin-ajax; surfaces the JSON result in .aether-form-status.

    document.querySelectorAll('.aether-contact-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var status = form.querySelector('.aether-form-status');
            var btn = form.querySelector('button[type="submit"]');
            var setStatus = function (message, isError) {
                if (!status) return;
                status.textContent = message;
                status.classList.toggle('is-error', !!isError);
                status.classList.toggle('is-success', !isError);
                status.style.display = 'block';
            };
            setStatus('', false);
            if (btn) btn.disabled = true;
            var actionUrl = form.getAttribute('action') || (window.aetherAjax && window.aetherAjax.ajaxUrl) || '';
            fetch(actionUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams(new FormData(form)).toString()
            }).then(function (response) {
                return response.json();
            }).then(function (json) {
                if (json && json.success) {
                    setStatus(json.data && json.data.message ? json.data.message : 'Sent.', false);
                    form.reset();
                } else {
                    setStatus(json && json.data && json.data.message ? json.data.message : 'Something went wrong — please try again.', true);
                }
            }).catch(function () {
                setStatus('Network error — please try again.', true);
            }).then(function () {
                if (btn) btn.disabled = false;
            });
        });
    });

    // ─── Add to Cart (wc-ajax: AJAX without page reload) ────────
    // Buttons carry .add-to-cart-btn + data-product-id + data-product-type.
    // Simple products POST to ?wc-ajax=add_to_cart; the header count is
    // refreshed from the a.aether-cart-count fragment. Variable/grouped
    // products keep the native href flow (WC redirects to the product page).

    function aetherAddToCart(btn) {
        var id = btn.getAttribute('data-product-id');
        if (!id || !window.aetherAjax || !window.aetherAjax.wcAjaxUrl) return;

        var qty = 1;
        var qtyValue = btn.closest('.pd-info') ? document.getElementById('qtyValue') : null;
        if (qtyValue) {
            var parsed = parseInt(qtyValue.textContent, 10);
            if (!isNaN(parsed) && parsed > 0) qty = parsed;
        }

        var wcUrl = window.aetherAjax.wcAjaxUrl;
        if (wcUrl.indexOf('wc-ajax') === -1) {
            wcUrl += (wcUrl.indexOf('?') === -1 ? '?' : '&') + 'wc-ajax=add_to_cart';
        }

        var body = new URLSearchParams();
        body.append('product_id', id);
        body.append('quantity', qty);

        btn.classList.add('is-loading');

        fetch(wcUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString()
        }).then(function (response) {
            return response.json();
        }).then(function (json) {
            btn.classList.remove('is-loading');
            if (json && json.error && json.product_url) {
                window.location.href = json.product_url;
            } else if (json && json.fragments) {
                if (json.fragments['a.aether-cart-count']) {
                    var doc = new DOMParser().parseFromString(json.fragments['a.aether-cart-count'], 'text/html');
                    var count = doc.querySelector('.cart-count');
                    if (count) {
                        document.querySelectorAll('.cart-count').forEach(function (el) {
                            el.textContent = count.textContent;
                        });
                    }
                }
                btn.classList.add('is-added');
                var original = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Added';
                setTimeout(function () {
                    btn.classList.remove('is-added');
                    btn.innerHTML = original;
                }, 2000);
            } else {
                window.location.href = btn.href;
            }
        }).catch(function () {
            btn.classList.remove('is-loading');
            window.location.href = btn.href;
        });
    }

    document.addEventListener('click', function (e) {
        var target = e.target;
        var btn = target && target.closest ? target.closest('.add-to-cart-btn[data-product-id]') : null;
        if (!btn) return;
        var type = btn.getAttribute('data-product-type');
        if (type && type !== 'simple') return;
        e.preventDefault();
        if (!btn.classList.contains('is-loading')) aetherAddToCart(btn);
    });

    // ─── Wishlist Toggle (AJAX) ───────────────────────────────
    function aetherWishlistToggle(btn) {
        if (!window.aetherAjax || !window.aetherAjax.ajaxUrl || !window.aetherAjax.nonce) {
            btn.classList.toggle('is-active');
            return;
        }
        var card = btn.closest('[data-product-id]');
        var productId = card ? card.getAttribute('data-product-id') : btn.getAttribute('data-product-id');
        if (!productId) return;

        var body = new URLSearchParams();
        body.append('action', 'aether_wishlist_toggle');
        body.append('nonce', window.aetherAjax.nonce);
        body.append('product_id', productId);

        btn.classList.add('is-loading');

        fetch(window.aetherAjax.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString()
        }).then(function (response) {
            return response.json();
        }).then(function (json) {
            btn.classList.remove('is-loading');
            if (json && json.success) {
                btn.classList.toggle('is-active', json.data.action === 'added');
                btn.setAttribute('aria-label', json.data.action === 'added' ? 'Remove from wishlist' : 'Add to wishlist');
                aetherWishlistUpdateCount(json.data.count);
            } else if (json && json.data && json.data.redirect) {
                window.location.href = json.data.redirect;
            }
        }).catch(function () {
            btn.classList.remove('is-loading');
        });
    }

    function aetherWishlistUpdateCount(count) {
        var badge = document.querySelector('[data-wishlist-count]');
        if (badge) badge.textContent = count;
    }

    document.querySelectorAll('.product-action-btn[aria-label="Add to wishlist"], .product-action-btn[aria-label="Remove from wishlist"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            aetherWishlistToggle(btn);
        });
    });

    // Wishlist page: remove button removes the card, then swaps to empty state.
    document.querySelectorAll('.wishlist-remove').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var card = btn.closest('[data-product-id]');
            if (!card) return;
            var body = new URLSearchParams();
            body.append('action', 'aether_wishlist_toggle');
            body.append('nonce', window.aetherAjax ? window.aetherAjax.nonce : '');
            body.append('product_id', card.getAttribute('data-product-id'));
            fetch(window.aetherAjax.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: body.toString()
            }).then(function (response) {
                return response.json();
            }).then(function (json) {
                if (json && json.success && json.data.action === 'removed') {
                    card.remove();
                    aetherWishlistUpdateCount(json.data.count);
                    var grid = document.querySelector('[data-wishlist-grid]');
                    var emptyState = document.querySelector('[data-wishlist-empty]');
                    if (grid && emptyState && grid.children.length === 0) {
                        grid.hidden = true;
                        emptyState.hidden = false;
                    }
                }
            }).catch(function () {});
        });
    });

    // ─── Quick View Modal ────────────────────────────────────
    var quickViewModal = document.getElementById('aetherQuickView');

    function aetherEscapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function (ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }

    function aetherQuickViewClose() {
        if (!quickViewModal) return;
        quickViewModal.hidden = true;
        document.body.style.overflow = '';
    }

    function aetherQuickViewOpen(productId) {
        if (!quickViewModal || !window.aetherAjax) return;
        var bodyEl = quickViewModal.querySelector('[data-quickview-body]');
        var params = new URLSearchParams();
        params.append('action', 'aether_quick_view');
        params.append('nonce', window.aetherAjax.nonce);
        params.append('product_id', productId);
        fetch(window.aetherAjax.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: params.toString()
        }).then(function (response) {
            return response.json();
        }).then(function (json) {
            if (!json || !json.success || !json.data) return;
            var d = json.data;
            bodyEl.innerHTML =
                (d.image ? '<div class="quick-view-media"><img src="' + aetherEscapeHtml(d.image) + '" alt="' + aetherEscapeHtml(d.name) + '"></div>' : '') +
                '<div class="quick-view-info">' +
                '<h3 class="quick-view-title">' + aetherEscapeHtml(d.name) + '</h3>' +
                (d.price ? '<div class="quick-view-price">' + aetherEscapeHtml(d.price) + '</div>' : '') +
                (d.short_desc ? '<p class="quick-view-desc">' + aetherEscapeHtml(d.short_desc) + '</p>' : '') +
                '<a href="' + aetherEscapeHtml(d.url) + '" class="btn btn-primary">View Details</a>' +
                '</div>';
            quickViewModal.hidden = false;
            document.body.style.overflow = 'hidden';
        }).catch(function () {});
    }

    if (quickViewModal) {
        quickViewModal.addEventListener('click', function (e) {
            if (e.target.closest('[data-quickview-close]')) aetherQuickViewClose();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !quickViewModal.hidden) aetherQuickViewClose();
        });
    }

    document.querySelectorAll('.product-action-btn[aria-label="Quick view"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var card = btn.closest('[data-product-id]');
            if (!card) return;
            aetherQuickViewOpen(card.getAttribute('data-product-id'));
        });
    });


    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // --- Checkout Place Order (WordPress build: WooCommerce AJAX handles order submission)
    // ─── Password Toggle (login + join-now) ──────────────────
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    if (togglePasswordBtn && passwordField) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });
    }

    // ─── Password Strength (@data-strength-target) ───────────────
    document.querySelectorAll('[data-strength-target]').forEach(function (box) {
        const targetId = box.getAttribute('data-strength-target');
        const input = document.getElementById(targetId);
        const strengthText = box.parentElement.querySelector('.strength-text');
        if (!input || !strengthText) return;

        const segments = box.querySelectorAll('.strength-bar');

        function updateStrengths() {
            const val = input.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            segments.forEach(s => { s.classList.remove('active', 'weak', 'medium', 'strong'); });

            if (val.length === 0) {
                strengthText.textContent = '';
                strengthText.style.color = '';
            } else if (score <= 1) {
                if (segments[0]) segments[0].classList.add('active', 'weak');
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#E74C3C';
            } else if (score === 2) {
                if (segments[0]) segments[0].classList.add('active', 'medium');
                if (segments[1]) segments[1].classList.add('active', 'medium');
                strengthText.textContent = 'Fair password';
                strengthText.style.color = '#F39C12';
            } else if (score === 3) {
                for (let i = 0; i < 3; i++) if (segments[i]) segments[i].classList.add('active', 'strong');
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#2ECC71';
            } else {
                segments.forEach(s => { if (s) s.classList.add('active', 'strong'); });
                strengthText.textContent = 'Very strong password';
                strengthText.style.color = '#2ECC71';
            }
        }

        input.addEventListener('input', updateStrengths);
    });

    // ─── Forgot Password Modal ─────────────────────────────────
    const forgotModal = document.getElementById('forgotModal');
    const forgotClose = document.getElementById('forgotModalClose');

    function openForgot() {
        if (!forgotModal) return;
        const forgotEmail = document.getElementById('forgotEmail');
        const loginEmail = document.getElementById('username');
        if (forgotEmail && loginEmail && loginEmail.value && !forgotEmail.value) {
            forgotEmail.value = loginEmail.value;
        }
        forgotModal.hidden = false;
        forgotModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { if (forgotEmail) forgotEmail.focus(); }, 100);
    }

    function closeForgot() {
        if (!forgotModal) return;
        forgotModal.classList.remove('active');
        forgotModal.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-forgot-toggle]').forEach(btn => {
        btn.addEventListener('click', (e) => { e.preventDefault(); openForgot(); });
    });

    if (forgotClose) forgotClose.addEventListener('click', closeForgot);
    if (forgotModal) {
        forgotModal.addEventListener('click', e => { if (e.target === forgotModal) closeForgot(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && forgotModal.classList.contains('active')) closeForgot(); });
    }

    // ─── Product Detail: Gallery Swiper ──────────────────────
    if (typeof Swiper !== 'undefined' && document.querySelector('.pd-gallery-thumbs-swiper')) {
        const thumbsSwiper = new Swiper('.pd-gallery-thumbs-swiper', {
            spaceBetween: 12, slidesPerView: 4, freeMode: true, watchSlidesProgress: true
        });
        new Swiper('.pd-gallery-swiper', {
            thumbs: { swiper: thumbsSwiper }, effect: 'fade', fadeEffect: { crossFade: true }
        });
    }

    // ─── Product Detail: Related Swiper ──────────────────────
    if (typeof Swiper !== 'undefined' && document.querySelector('.pd-related-swiper')) {
        new Swiper('.pd-related-swiper', {
            slidesPerView: 1.2, spaceBetween: 20,
            breakpoints: { 576: { slidesPerView: 2 }, 992: { slidesPerView: 3.2 } }
        });
    }

    // ─── Product Detail: Color / Size Selection ──────────────
    document.querySelectorAll('.pd-color-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.pd-color-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const nameEl = document.getElementById('pdColorName');
            if (nameEl) nameEl.textContent = this.dataset.color;
        });
    });

    document.querySelectorAll('.pd-size-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.pd-size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const nameEl = document.getElementById('pdSizeName');
            if (nameEl) nameEl.textContent = this.textContent;
        });
    });

    // ─── Product Detail: Quantity ────────────────────────────
    const qtyVal = document.getElementById('qtyValue');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');
    if (qtyVal && qtyMinus && qtyPlus) {
        qtyMinus.addEventListener('click', () => {
            let v = parseInt(qtyVal.textContent);
            if (v > 1) qtyVal.textContent = v - 1;
        });
        qtyPlus.addEventListener('click', () => {
            let v = parseInt(qtyVal.textContent);
            if (v < 10) qtyVal.textContent = v + 1;
        });
    }

    // ─── Product Detail: Sticky Bar ──────────────────────────
    const pdStickyBar = document.getElementById('pdStickyBar');
    const pdAddToCart = document.getElementById('pdAddToCart');
    if (pdStickyBar && pdAddToCart) {
        window.addEventListener('scroll', () => {
            const rect = pdAddToCart.getBoundingClientRect();
            pdStickyBar.classList.toggle('visible', rect.bottom < 0);
        });
    }

    // ─── Product Detail: Accordion ──────────────────────────
    document.querySelectorAll('.pd-accordion-header').forEach(header => {
        header.addEventListener('click', function () {
            const item = this.parentElement;
            const body = item.querySelector('.pd-accordion-body');
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.pd-accordion-item').forEach(i => {
                i.classList.remove('active');
                const b = i.querySelector('.pd-accordion-body');
                if (b) b.style.maxHeight = null;
            });
            if (!isActive && body) {
                item.classList.add('active');
                body.style.maxHeight = body.scrollHeight + 'px';
            }
        });
    });

    // ─── Product Detail: Size Guide Modal ────────────────────
    const sizeGuideModal = document.getElementById('sizeGuideModal');
    const openSizeGuide = document.getElementById('openSizeGuide');
    const closeSizeGuide = document.getElementById('closeSizeGuide');
    if (sizeGuideModal && openSizeGuide && closeSizeGuide) {
        openSizeGuide.addEventListener('click', (e) => {
            e.preventDefault();
            sizeGuideModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
        closeSizeGuide.addEventListener('click', () => {
            sizeGuideModal.classList.remove('open');
            document.body.style.overflow = '';
        });
        sizeGuideModal.addEventListener('click', (e) => {
            if (e.target === sizeGuideModal) {
                sizeGuideModal.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    }

    // ─── Search Overlay ─────────────────────────────────────
    // Shared open routine — desktop header icons and the mobile drawer's
    // search box both open the same overlay (mobile search was previously
    // inert; it is now wired here).
    function aetherOpenSearchOverlay() {
        let overlay = document.getElementById('searchOverlay');
        if (!overlay) {
            const searchUrl = (window.aetherAjax && window.aetherAjax.searchUrl) ? window.aetherAjax.searchUrl : '/?s=';
            const shopUrl = (window.aetherAjax && window.aetherAjax.shopUrl) ? window.aetherAjax.shopUrl : '/shop/';
            overlay = document.createElement('div');
            overlay.id = 'searchOverlay';
            overlay.innerHTML = '<div class="search-overlay"><div class="search-container"><button class="search-close" aria-label="Close search"><i class="fas fa-times"></i></button><div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" class="search-input" placeholder="Search AETHER..." autofocus></div><div class="search-suggestions"><p class="search-suggestion-label">Popular Searches</p><a href="' + shopUrl + '" class="search-suggestion"><i class="fas fa-fire"></i> Void Runner</a><a href="' + shopUrl + '" class="search-suggestion"><i class="fas fa-bolt"></i> Cloud Stride</a><a href="' + shopUrl + '" class="search-suggestion"><i class="fas fa-star"></i> New Arrivals</a></div></div></div>';
            document.body.appendChild(overlay);
            overlay.querySelector('.search-close').addEventListener('click', () => overlay.classList.remove('active'));
            overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('active'); });
            const searchInput = overlay.querySelector('.search-input');
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') overlay.classList.remove('active');
                if (e.key === 'Enter' && searchInput.value.trim()) window.location.href = searchUrl + encodeURIComponent(searchInput.value.trim());
            });
        }
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { const inp = overlay.querySelector('.search-input'); if (inp) inp.focus(); }, 100);
    }

    document.querySelectorAll('[aria-label="Search"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            aetherOpenSearchOverlay();
        });
    });

    // Mobile drawer search box — opens the same overlay (drawer closes first).
    const mobileSearch = document.querySelector('.mobile-search');
    if (mobileSearch) {
        mobileSearch.addEventListener('click', (e) => {
            e.preventDefault();
            closeMobileMenu();
            aetherOpenSearchOverlay();
        });
    }

    // ─── Product Detail: Magnifying Glass Zoom ──────────────
    document.querySelectorAll('.pd-gallery-main').forEach(gallery => {
        const img = gallery.querySelector('img');
        if (!img) return;
        gallery.style.cursor = 'none';
        let lens = null;

        gallery.addEventListener('mouseenter', () => {
            lens = document.createElement('div');
            lens.className = 'magnify-lens';
            gallery.appendChild(lens);
            const zoom = 2.5;
            const lensW = 120, lensH = 120;
            gallery.addEventListener('mousemove', function moveHandler(e) {
                const rect = gallery.getBoundingClientRect();
                let x = e.clientX - rect.left;
                let y = e.clientY - rect.top;
                lens.style.left = (x - lensW / 2) + 'px';
                lens.style.top = (y - lensH / 2) + 'px';
                const bgX = (x / rect.width) * 100;
                const bgY = (y / rect.height) * 100;
                lens.style.backgroundImage = 'url(' + img.src + ')';
                lens.style.backgroundSize = (rect.width * zoom) + 'px ' + (rect.height * zoom) + 'px';
                lens.style.backgroundPosition = bgX + '% ' + bgY + '%';
            });
            gallery._moveHandler = gallery.listeners && gallery.listeners.move;
        });

        gallery.addEventListener('mouseleave', () => {
            if (lens) { lens.remove(); lens = null; }
        });
    });

    // ─── Filter Buttons (shop page) ─────────────────────────
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.filter-buttons').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ─── Pagination (shop page) ─────────────────────────────
    document.querySelectorAll('.pagination-page').forEach(page => {
        page.addEventListener('click', function() {
            this.closest('.pagination-pages').querySelectorAll('.pagination-page').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
        });
    });

});
