// AETHER — Main JavaScript

document.addEventListener('DOMContentLoaded', () => {
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
        const HOME_HERO_HEIGHT = 600;

        function updateHeader() {
            const scrollY = window.scrollY;
            const isHome = document.body.classList.contains('home-page');
            const isMobile = window.innerWidth <= 768;
            if (isMobile) { ticking = false; return; }
            if (scrollY > 80) {
                header.classList.add('header--scrolled');
            } else {
                header.classList.remove('header--scrolled');
            }
            const hideStart = isHome ? HOME_HERO_HEIGHT : 100;
            if (scrollY > hideStart) {
                const delta = scrollY - lastScrollY;
                if (delta > SCROLL_THRESHOLD) {
                    header.classList.add('header--hidden');
                } else if (delta < -SCROLL_THRESHOLD) {
                    header.classList.remove('header--hidden');
                }
            } else {
                header.classList.remove('header--hidden');
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
            var shopUrl = (window.phantomData && phantomData.urls && phantomData.urls.shop) || '/shop/';
            window.location.href = shopUrl;
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

    if (heroSwiperEl) {
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

    if (reviewsSwiperEl) {
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

    // ─── Newsletter Forms (AJAX submission) ──────────────────
    document.querySelectorAll('.newsletter-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = form.querySelector('.newsletter-input');
            var success = form.closest('.newsletter-inner') ? form.closest('.newsletter-inner').querySelector('.newsletter-success') : null;
            if (!success) success = form.parentElement.querySelector('.newsletter-success');
            var nonce = form.querySelector('input[name="aether_nonce"]');
            if (email && email.value && nonce) {
                var submitBtn = form.querySelector('.newsletter-btn');
                if (submitBtn) submitBtn.disabled = true;
                fetch((window.phantomData && phantomData.rest_url ? phantomData.rest_url : '/wp-json/') + 'aether/v1/newsletter/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce.value },
                    body: JSON.stringify({ email: email.value })
                }).then(function(r) { return r.json(); }).then(function(data) {
                    form.style.display = 'none';
                    if (success) {
                        success.classList.add('is-visible');
                        if (data.data && data.data.message) {
                            var msgEl = success.querySelector('p');
                            if (msgEl) msgEl.textContent = data.data.message;
                        }
                    }
                }).catch(function() {
                    form.style.display = 'none';
                    if (success) success.classList.add('is-visible');
                    if (submitBtn) submitBtn.disabled = false;
                });
            }
        });
    });

    document.querySelectorAll('.footer-newsletter-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var emailInput = form.querySelector('input[type="email"]');
            if (emailInput && emailInput.value) {
                fetch((window.phantomData && phantomData.rest_url ? phantomData.rest_url : '/wp-json/') + 'aether/v1/newsletter/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: emailInput.value })
                }).then(function() {
                    form.innerHTML = '<p style="color:var(--gold);font-size:0.85rem;text-align:center;padding:8px 0;">Welcome to the AETHER community!</p>';
                }).catch(function() {
                    form.innerHTML = '<p style="color:var(--gold);font-size:0.85rem;text-align:center;padding:8px 0;">Welcome to the AETHER community!</p>';
                });
            }
        });
    });

    // ─── Smooth Scroll for Anchor Links ──────────────────────
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

    // ─── Checkout Place Order ────────────────────────────────
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', (e) => {
            e.preventDefault();
            var checkoutUrl = (window.phantomData && phantomData.urls && phantomData.urls.checkout) || '/checkout/';
            window.location.href = checkoutUrl;
        });
    }

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

    // ─── Password Strength (join-now) ────────────────────────
    const strengthField = document.getElementById('password');
    if (strengthField && document.getElementById('seg1')) {
        strengthField.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const segments = [
                document.getElementById('seg1'),
                document.getElementById('seg2'),
                document.getElementById('seg3'),
                document.getElementById('seg4')
            ];
            const strengthText = document.getElementById('strengthText');
            if (!strengthText) return;

            segments.forEach(s => { if (s) s.className = 'strength-segment'; });

            if (val.length === 0) {
                strengthText.textContent = '';
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
        });
    }

    // ─── Product Detail: Gallery Swiper ──────────────────────
    if (document.querySelector('.pd-gallery-thumbs-swiper')) {
        const thumbsSwiper = new Swiper('.pd-gallery-thumbs-swiper', {
            spaceBetween: 12, slidesPerView: 4, freeMode: true, watchSlidesProgress: true
        });
        new Swiper('.pd-gallery-swiper', {
            thumbs: { swiper: thumbsSwiper }, effect: 'fade', fadeEffect: { crossFade: true }
        });
    }

    // ─── Product Detail: Related Swiper ──────────────────────
    if (document.querySelector('.pd-related-swiper')) {
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
    document.querySelectorAll('[aria-label="Search"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            var shopUrl = (window.phantomData && phantomData.urls && phantomData.urls.shop) || '/shop/';
            var searchUrl = (window.phantomData && phantomData.urls && phantomData.urls.search) || '/';
            let overlay = document.getElementById('searchOverlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'searchOverlay';
                overlay.innerHTML = '<div class="search-overlay"><div class="search-container"><button class="search-close" aria-label="Close search"><i class="fas fa-times"></i></button><div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" class="search-input" placeholder="Search products..." autofocus></div><div class="search-suggestions"><p class="search-suggestion-label">Popular Searches</p><a href="' + shopUrl + '" class="search-suggestion"><i class="fas fa-fire"></i> Void Runner</a><a href="' + shopUrl + '" class="search-suggestion"><i class="fas fa-bolt"></i> Cloud Stride</a><a href="' + shopUrl + '" class="search-suggestion"><i class="fas fa-star"></i> New Arrivals</a></div></div></div>';
                document.body.appendChild(overlay);
                overlay.querySelector('.search-close').addEventListener('click', () => overlay.classList.remove('active'));
                overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('active'); });
                const searchInput = overlay.querySelector('.search-input');
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') overlay.classList.remove('active');
                    if (e.key === 'Enter' && searchInput.value.trim()) window.location.href = searchUrl + '?s=' + encodeURIComponent(searchInput.value.trim()) + '&post_type=product';
                });
            }
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => overlay.querySelector('.search-input').focus(), 100);
        });
    });

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

    // ─── Preloader Fade-Out ────────────────────────
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

    // ─── Quick View Modal (REST API) ───────────────────────────
    document.querySelectorAll('.quick-view-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var productId = this.dataset.productId;
            if (!productId) return;

            var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
            var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';

            fetch(restBase + 'aether/v1/products/quick-view/' + productId, {
                headers: { 'X-WP-Nonce': nonce }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.html) {
                    var modal = document.createElement('div');
                    modal.className = 'pd-modal-overlay';
                    modal.innerHTML = data.html;
                    document.body.appendChild(modal);
                    document.body.style.overflow = 'hidden';
                    setTimeout(function() { modal.classList.add('active'); }, 10);

                    var closeBtn = modal.querySelector('.pd-modal-close');
                    if (closeBtn) closeBtn.addEventListener('click', function() {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                        setTimeout(function() { modal.remove(); }, 300);
                    });
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            modal.classList.remove('active');
                            document.body.style.overflow = '';
                            setTimeout(function() { modal.remove(); }, 300);
                        }
                    });
                }
            });
        });
    });

    // ─── Wishlist Toggle (REST API) ────────────────────────────
    document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var productId = this.dataset.productId;
            if (!productId) return;

            var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
            var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';

            fetch(restBase + 'aether/v1/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify({ product_id: parseInt(productId, 10) })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success) {
                    var icon = btn.querySelector('i');
                    if (icon && data.data) {
                        icon.className = data.data.action === 'added' ? 'fas fa-heart' : 'far fa-heart';
                    }
                    document.querySelectorAll('.wishlist-count').forEach(function(el) {
                        if (data.data) el.textContent = data.data.count;
                    });
                }
            });
        });
    });

    // ─── Search Overlay — Live Search via REST API ─────────────
    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('search-input')) return;
        var query = e.target.value.trim();
        var suggestionsEl = e.target.closest('.search-container')?.querySelector('.search-suggestions');
        if (!suggestionsEl) return;

        if (query.length < 2) {
            suggestionsEl.innerHTML = '<p class="search-suggestion-label">Popular Searches</p><a href="' + ((window.phantomData && phantomData.urls && phantomData.urls.shop) || '/shop/') + '" class="search-suggestion"><i class="fas fa-fire"></i> Void Runner</a><a href="' + ((window.phantomData && phantomData.urls && phantomData.urls.shop) || '/shop/') + '" class="search-suggestion"><i class="fas fa-bolt"></i> Cloud Stride</a>';
            return;
        }

        var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
        var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';

        fetch(restBase + 'aether/v1/search?q=' + encodeURIComponent(query) + '&limit=5', {
            headers: { 'X-WP-Nonce': nonce }
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data && data.html) {
                suggestionsEl.innerHTML = data.html;
            }
        });
    });

    // ─── Filter Buttons — AJAX Product Filtering ───────────────
    document.querySelectorAll('.filter-btn[data-category]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var category = this.dataset.category || 'all';
            var sort = this.dataset.sort || 'date';
            var grid = document.querySelector('.shop-grid');
            if (!grid) return;

            var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
            var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';

            grid.style.opacity = '0.5';
            fetch(restBase + 'aether/v1/products/filter?category=' + encodeURIComponent(category) + '&sort=' + sort + '&per_page=12&page=1', {
                headers: { 'X-WP-Nonce': nonce }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data && data.html) {
                    grid.outerHTML = data.html;
                }
                grid.style.opacity = '1';
            }).catch(function() {
                grid.style.opacity = '1';
            });
        });
    });

    // ─── Cart Item Quantity Update ─────────────────────────────
    document.querySelectorAll('.cart-item-qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var key = this.dataset.key;
            var action = this.dataset.action;
            if (!key || !action) return;

            var qtyEl = this.closest('.cart-item-qty')?.querySelector('.cart-item-qty-value');
            if (!qtyEl) return;
            var currentQty = parseInt(qtyEl.textContent, 10);
            var newQty = action === 'plus' ? currentQty + 1 : currentQty - 1;
            if (newQty < 1) newQty = 1;

            var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
            var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';

            fetch(restBase + 'aether/v1/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify({ cart_item_key: key, quantity: newQty })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success !== false) {
                    qtyEl.textContent = newQty;
                    if (data.data) {
                        document.querySelectorAll('.cart-total, .cart-total-price').forEach(function(el) {
                            if (data.data.cart_total) el.textContent = data.data.cart_total;
                        });
                        document.querySelectorAll('.cart-count').forEach(function(el) {
                            el.textContent = data.data.cart_count || 0;
                        });
                    }
                }
            });
        });
    });

    // ─── Cart Item Remove ──────────────────────────────────────
    document.querySelectorAll('.cart-item-remove-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var key = this.dataset.key;
            if (!key) return;

            var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
            var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';

            fetch(restBase + 'aether/v1/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify({ cart_item_key: key })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success !== false) {
                    var row = this.closest('tr, .cart-item');
                    if (row) row.style.display = 'none';
                    if (data.data) {
                        document.querySelectorAll('.cart-total, .cart-total-price').forEach(function(el) {
                            if (data.data.cart_total) el.textContent = data.data.cart_total;
                        });
                        document.querySelectorAll('.cart-count').forEach(function(el) {
                            el.textContent = data.data.cart_count || 0;
                        });
                        if (data.data.is_empty) location.reload();
                    }
                }
            }.bind(this));
        });
    });

    // ─── Mini Cart Toggle ──────────────────────────────────────
    document.querySelectorAll('.mini-cart-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var dropdown = document.querySelector('.mini-cart-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('active');
                if (dropdown.classList.contains('active')) {
                    var restBase = (window.phantomData && phantomData.rest_url) ? phantomData.rest_url : '/wp-json/';
                    var nonce = (window.phantomData && phantomData.nonce) ? phantomData.nonce : '';
                    fetch(restBase + 'aether/v1/cart/mini', {
                        headers: { 'X-WP-Nonce': nonce }
                    }).then(function(r) { return r.json(); }).then(function(data) {
                        if (data.html) {
                            var content = dropdown.querySelector('.mini-cart-content');
                            if (content) content.outerHTML = data.html;
                        }
                    });
                }
            }
        });
    });

    // ─── Add to Cart (WC AJAX) ────────────────────────────────
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var productId = this.dataset.productId;
            if (!productId) return;

            var originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            this.disabled = true;

            fetch('/?wc-ajax=add_to_cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId + '&quantity=1'
            }).then(function(r) { return r.json(); }).then(function(data) {
                this.innerHTML = '<i class="fas fa-check"></i> Added!';
                var self = this;
                setTimeout(function() {
                    self.innerHTML = originalText;
                    self.disabled = false;
                }, 1500);
                document.querySelectorAll('.cart-count').forEach(function(el) {
                    var count = parseInt(el.textContent || '0', 10);
                    el.textContent = count + 1;
                });
            }.bind(this)).catch(function() {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    });
});
