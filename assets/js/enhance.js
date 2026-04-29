/* ================================================================
   assets/js/enhance.js — Wonderland Samarinda Enhancement Layer
   Termasuk: Theme Manager (dark/light toggle)
   ================================================================ */

/* ── Theme Manager ── */
(function () {
    'use strict';

    var STORAGE_KEY = 'wl_theme';
    var DARK = 'dark';
    var LIGHT = 'light';

    function getSavedTheme() {
        var saved = null;
        try {
            saved = window.localStorage.getItem(STORAGE_KEY);
        } catch (err) {
            saved = null;
        }
        return saved === DARK ? DARK : LIGHT;
    }

    function setStoredTheme(theme) {
        try {
            window.localStorage.setItem(STORAGE_KEY, theme);
        } catch (err) {

        }
    }

    function applyTheme(theme) {
        var nextTheme = theme === DARK ? DARK : LIGHT;
        document.documentElement.setAttribute('data-theme', nextTheme);
        document.documentElement.style.colorScheme = nextTheme;

        document.querySelectorAll('.wl-theme-toggle').forEach(function (button) {
            var icon = button.querySelector('.wl-theme-toggle-icon');
            var label = button.querySelector('.wl-theme-toggle-label');
            var isDark = nextTheme === DARK;

            button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            button.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
            button.setAttribute('title', isDark ? 'Ganti ke mode terang' : 'Ganti ke mode gelap');

            if (icon) {
                icon.textContent = isDark ? '☀' : '☾';
            }
            if (label) {
                label.textContent = isDark ? 'Light' : 'Dark';
            }
        });

        document.dispatchEvent(new CustomEvent('wl:theme-change', {
            detail: { theme: nextTheme }
        }));
    }

    function toggleTheme() {
        var current = document.documentElement.getAttribute('data-theme') === DARK ? DARK : LIGHT;
        var next = current === DARK ? LIGHT : DARK;
        setStoredTheme(next);
        applyTheme(next);
    }

    function bindToggle(button) {
        if (!button || button.dataset.themeBound === '1') {
            return;
        }

        button.dataset.themeBound = '1';
        button.addEventListener('click', function () {
            toggleTheme();
        });
    }

    function init() {
        applyTheme(getSavedTheme());
        document.querySelectorAll('.wl-theme-toggle').forEach(bindToggle);
    }

    window.wlApplyTheme = applyTheme;
    window.wlToggleTheme = toggleTheme;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

/* ── Enhancement Layer ── */

(function () {
    'use strict';

    /* ── helpers ── */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
    const raf = requestAnimationFrame;

    /* ============================================================
       ① PRELOADER — Galaxy stars + count-up
       ============================================================ */

function shouldSkipIntro() {
    const hasHash = window.location.hash !== '';
    const alreadySeen = sessionStorage.getItem('wl_splash_seen') === 'true';

    return hasHash || alreadySeen;
}

function skipIntro() {
    const preloader = document.getElementById('preloader');
    const splash = document.getElementById('splash');

    if (preloader) {
        preloader.style.display = 'none';
    }

    if (splash) {
        splash.style.display = 'none';
        splash.classList.remove('splash-visible');
    }

    document.body.style.overflow = '';

    initHeroCanvas();

    if (window.location.hash) {
        setTimeout(function () {
            const target = document.querySelector(window.location.hash);

            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 80);
    }
}
function shouldSkipIntro() {
    const hasHash = window.location.hash !== '';
    const alreadySeen = sessionStorage.getItem('wl_intro_seen') === '1';

    return hasHash || alreadySeen;
}
function buildPreloader() {
    const el = document.getElementById('preloader');

    if (!el) {
        initHeroCanvas();
        return;
    }
    if (shouldSkipIntro()) {
        el.style.display = 'none';

        const splash = document.getElementById('splash');
        if (splash) splash.style.display = 'none';

        document.body.style.overflow = '';

        initHeroCanvas();

        if (window.location.hash) {
            setTimeout(() => {
                const target = document.querySelector(window.location.hash);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }, 80);
        }

        return;
    }

    sessionStorage.setItem('wl_intro_seen', '1');

    const canvas = document.createElement('canvas');
    el.prepend(canvas);
    const ctx = canvas.getContext('2d');

    const stars = [];
    const NUM_STARS = 200;
    let W, H;

    function resize() {
        W = canvas.width = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    for (let i = 0; i < NUM_STARS; i++) {
        stars.push({
            x: Math.random(),
            y: Math.random(),
            r: Math.random() * 1.6 + 0.2,
            s: Math.random() * 0.6 + 0.2,
            o: Math.random()
        });
    }

    function drawStars() {
        ctx.clearRect(0, 0, W, H);
        stars.forEach(s => {
            s.o += 0.008 * s.s;
            if (s.o > 1) s.o = 0;

            ctx.beginPath();
            ctx.arc(s.x * W, s.y * H, s.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${Math.sin(s.o * Math.PI)})`;
            ctx.fill();
        });
    }

    const countEl = el.querySelector('.pre-count');
    const barEl = el.querySelector('.pre-bar');
    let count = 0;

    function animateCount() {
        drawStars();

        if (count < 100) {
            count++;

            if (countEl) countEl.textContent = count;
            if (barEl) barEl.style.width = count + '%';

            setTimeout(() => requestAnimationFrame(animateCount), 8);
        } else {
            el.classList.add('pre-hidden');

            el.addEventListener('transitionend', function handler() {
                el.removeEventListener('transitionend', handler);
                showSplash();
            });
        }
    }

    requestAnimationFrame(animateCount);
}
    /* ============================================================
       ② SPLASH SCREEN
       ============================================================ */
    function showSplash() {
        const splash = document.getElementById('splash');
        if (!splash) {
            revealPage();
            return;
        }
        splash.classList.add('splash-visible');

        const btn = splash.querySelector('.splash-explore');
        if (btn) {
            btn.addEventListener('click', () => {
                splash.classList.add('splash-exit');
                setTimeout(revealPage, 600);
            });
        }
    }

 function revealPage() {
    const preloader = document.getElementById('preloader');
    const splash = document.getElementById('splash');

    document.body.style.overflow = '';

    if (preloader) {
        preloader.style.display = 'none';
    }

    if (splash) {
        splash.style.display = 'none';
        splash.classList.remove('splash-visible');
        splash.classList.remove('splash-exit');
    }

    if (window.AOS) {
        AOS.refresh();
    }

    initHeroCanvas();
}
    /* ============================================================
       ③ HERO — animated galaxy canvas
       ============================================================ */
    function initHeroCanvas() {
        const hero = document.querySelector('.landing-hero');
        if (!hero) return;

        const existing = document.getElementById('hero-canvas');
        if (existing) { runHeroCanvas(existing); return; }

        const canvas = document.createElement('canvas');
        canvas.id = 'hero-canvas';
        hero.prepend(canvas);
        runHeroCanvas(canvas);
    }

    function runHeroCanvas(canvas) {
        const ctx = canvas.getContext('2d');
        const stars = [];
        let W, H;

        function resize() {
            const hero = canvas.parentElement;
            W = canvas.width  = hero ? hero.offsetWidth  : window.innerWidth;
            H = canvas.height = hero ? hero.offsetHeight : window.innerHeight;
        }
        resize();

        const observer = new ResizeObserver(resize);
        if (canvas.parentElement) observer.observe(canvas.parentElement);


        for (let i = 0; i < 160; i++) {
            stars.push({
                x: Math.random(),
                y: Math.random(),
                r: Math.random() * 1.4 + 0.3,
                vx: (Math.random() - 0.5) * 0.0003,
                vy: (Math.random() - 0.5) * 0.0003,
                o: Math.random(),
                phase: Math.random() * Math.PI * 2
            });
        }


        const orbs = [
            { cx: 0.1, cy: 0.2, r: 0.35, col: 'rgba(220,38,38,0.08)'  },
            { cx: 0.85,cy: 0.1, r: 0.3,  col: 'rgba(245,158,11,0.07)' },
            { cx: 0.5, cy: 0.7, r: 0.4,  col: 'rgba(16,185,129,0.05)' },
        ];

        let t = 0;
        function draw() {
            ctx.clearRect(0, 0, W, H);


            orbs.forEach(o => {
                const g = ctx.createRadialGradient(o.cx*W, o.cy*H, 0, o.cx*W, o.cy*H, o.r*W);
                g.addColorStop(0, o.col);
                g.addColorStop(1, 'transparent');
                ctx.beginPath();
                ctx.arc(o.cx*W, o.cy*H, o.r*W, 0, Math.PI*2);
                ctx.fillStyle = g;
                ctx.fill();
            });


            t += 0.008;
            stars.forEach(s => {
                s.x = (s.x + s.vx + 1) % 1;
                s.y = (s.y + s.vy + 1) % 1;
                const pulse = 0.4 + 0.6 * Math.abs(Math.sin(t + s.phase));
                ctx.beginPath();
                ctx.arc(s.x * W, s.y * H, s.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255,255,255,${pulse * 0.75})`;
                ctx.fill();
            });

            raf(draw);
        }
        raf(draw);
    }

    /* ============================================================
       ④ STAR RATINGS on attraction cards
       ── Injects stars next to the price; works with Vue-rendered cards
       ============================================================ */
    function injectStarRatings() {

        const getStars = () => (Math.random() > 0.3 ? 4 : 3) + Math.round(Math.random() * 10) / 10;

        function addStars(card) {
            if (card.querySelector('.wl-stars')) return;
            const body = card.querySelector('.landing-attraction-body');
            if (!body) return;
            const rating = getStars();
            const fullStars = Math.floor(rating);
            const halfStar  = rating - fullStars >= 0.5;
            let html = '<div class="wl-stars">';
            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) html += '<i class="bi bi-star-fill"></i>';
                else if (i === fullStars + 1 && halfStar) html += '<i class="bi bi-star-half"></i>';
                else html += '<i class="bi bi-star"></i>';
            }
            html += `<small class="ms-1 text-muted" style="font-size:.75rem">${rating.toFixed(1)}</small>`;
            html += '</div>';

            const footer = body.querySelector('.landing-attraction-footer');
            if (footer) footer.insertAdjacentHTML('beforebegin', html);
            else body.insertAdjacentHTML('beforeend', html);
        }


        $$('.landing-attraction-card').forEach(addStars);


        const target = document.querySelector('#wahana-app');
        if (!target) return;
        const mo = new MutationObserver(() => {
            $$('.landing-attraction-card').forEach(addStars);
        });
        mo.observe(target, { childList: true, subtree: true });
    }

    /* ============================================================
       ⑤ SPOTLIGHT MOUSE GLOW on attraction cards
       ============================================================ */
    function initSpotlight() {
        document.addEventListener('mousemove', e => {
            $$('.landing-attraction-card').forEach(card => {
                const rect = card.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width  * 100).toFixed(1);
                const y = ((e.clientY - rect.top)  / rect.height * 100).toFixed(1);
                card.style.setProperty('--mouse-x', x + '%');
                card.style.setProperty('--mouse-y', y + '%');
            });
        });
    }

    /* ============================================================
       ⑥ GALLERY LIGHTBOX
       ============================================================ */
    function initLightbox() {

        const lb = document.createElement('div');
        lb.id = 'wl-lightbox';
        lb.innerHTML = `
            <img src="" alt="Gallery" id="lb-img">
            <button class="lb-close" aria-label="Close"><i class="bi bi-x-lg"></i></button>`;
        document.body.appendChild(lb);

        const img = lb.querySelector('#lb-img');

        function open(src) {
            img.src = src;
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            lb.classList.remove('open');
            document.body.style.overflow = '';
            setTimeout(() => { img.src = ''; }, 300);
        }


        function bindGallery() {
            $$('.gallery-item img').forEach(el => {
                if (el.dataset.lbBound) return;
                el.dataset.lbBound = '1';
                el.style.cursor = 'zoom-in';
                el.addEventListener('click', () => open(el.src));
            });
        }
        bindGallery();

        lb.querySelector('.lb-close').addEventListener('click', close);
        lb.addEventListener('click', e => { if (e.target === lb) close(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });


        const gallerySection = document.querySelector('.landing-gallery-grid');
        if (gallerySection) {
            new MutationObserver(bindGallery).observe(gallerySection, { childList: true, subtree: true });
        }
    }

    /* ============================================================
       ⑦ NAVBAR scroll glass effect
       ============================================================ */
    function initNavbarScroll() {
        const nav = document.querySelector('.custom-navbar');
        if (!nav) return;
        const handler = () => {
            nav.classList.toggle('scrolled', window.scrollY > 60);
        };
        window.addEventListener('scroll', handler, { passive: true });
        handler();
    }

    /* ============================================================
       ⑧ SCROLL-TO-TOP
       ============================================================ */
    function initScrollTop() {
        const btn = document.createElement('button');
        btn.id = 'wl-scrolltop';
        btn.setAttribute('aria-label', 'Scroll to top');
        btn.innerHTML = '<i class="bi bi-chevron-up"></i>';
        document.body.appendChild(btn);

        window.addEventListener('scroll', () => {
            btn.classList.toggle('show', window.scrollY > 400);
        }, { passive: true });

        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ============================================================
       ⑨ CARD TILT-IN on scroll (IntersectionObserver)
       ============================================================ */
    function initCardReveal() {
        const io = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        function observe() {
            $$('.landing-attraction-card:not(.in-view)').forEach(el => io.observe(el));
        }
        observe();


        const app = document.getElementById('wahana-app');
        if (app) {
            new MutationObserver(observe).observe(app, { childList: true, subtree: true });
        }
    }

    /* ============================================================
       ⑩ CONTACT — make phone & email clickable in footer
       ── non-destructive: wraps text nodes in <a> tags ──
       ============================================================ */
    function linkifyContacts() {
        $$('.footer-contact li').forEach(li => {
            const span = li.querySelector('span');
            if (!span) return;
            const text = span.textContent.trim();

            if (/^\+?[\d\s\-()]{8,}$/.test(text)) {
                const a = document.createElement('a');
                a.href = 'tel:' + text.replace(/\s+/g, '');
                a.textContent = text;
                span.replaceWith(a);
            }

            if (text.includes('@')) {
                const a = document.createElement('a');
                a.href = 'mailto:' + text;
                a.textContent = text;
                span.replaceWith(a);
            }
        });
    }

    /* ============================================================
       INIT — run when DOM is ready
       ============================================================ */
    function init() {
const hasPreloader = !!document.getElementById('preloader');

if (hasPreloader && !shouldSkipIntro()) {
    document.body.style.overflow = 'hidden';
}

buildPreloader();
        injectStarRatings();
        initSpotlight();
        initLightbox();
        initNavbarScroll();
        initScrollTop();
        initCardReveal();
        linkifyContacts();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

/* ── Admin Mobile Sidebar ── */
(function () {
    'use strict';

    var sidebar  = document.getElementById('admSidebar');
    var backdrop = document.getElementById('admSidebarBackdrop');
    var toggle   = document.getElementById('admMenuToggle');

    if (!sidebar || !backdrop || !toggle) return;

    function openSidebar() {
        sidebar.classList.add('show');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', function () {
        if (sidebar.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    backdrop.addEventListener('click', closeSidebar);


    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });


    var navLinks = sidebar.querySelectorAll('.adm-nav-link');
    navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            closeSidebar();
        });
    });
})();
