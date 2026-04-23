/**
 * Mobile portal header: hamburger, backdrop, body class, scroll lock, resize close.
 */
(function () {
    var MQ = window.matchMedia('(min-width: 901px)');

    function isWide() {
        return MQ.matches;
    }

    var btn = document.getElementById('portal-menu-btn');
    var backdrop = document.getElementById('portal-nav-backdrop');
    var navPanel = document.getElementById('portal-nav-panel');
    if (!btn || !navPanel) {
        return;
    }

    var body = document.body;
    var openClass = 'portal-nav-open';
    var scrollY = 0;

    function setOpen(open) {
        if (open) {
            body.classList.add(openClass);
            btn.setAttribute('aria-expanded', 'true');
            btn.setAttribute('aria-label', 'Close menu');
            if (backdrop) {
                backdrop.removeAttribute('hidden');
                backdrop.setAttribute('aria-hidden', 'false');
            }
            if (!isWide() && navPanel) {
                navPanel.setAttribute('aria-hidden', 'false');
            }
            if (!isWide()) {
                scrollY = window.scrollY || 0;
                body.style.position = 'fixed';
                body.style.top = '-' + scrollY + 'px';
                body.style.left = '0';
                body.style.right = '0';
                body.style.width = '100%';
            }
        } else {
            body.classList.remove(openClass);
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('aria-label', 'Open menu');
            if (backdrop) {
                backdrop.setAttribute('hidden', '');
                backdrop.setAttribute('aria-hidden', 'true');
            }
            if (navPanel) {
                if (isWide()) {
                    navPanel.removeAttribute('aria-hidden');
                } else {
                    navPanel.setAttribute('aria-hidden', 'true');
                }
            }
            if (body.style.position === 'fixed') {
                body.style.position = '';
                body.style.top = '';
                body.style.left = '';
                body.style.right = '';
                body.style.width = '';
                window.scrollTo(0, scrollY);
            }
        }
    }

    function syncAriaForWidth() {
        if (isWide()) {
            if (navPanel) {
                navPanel.removeAttribute('aria-hidden');
            }
            if (body.classList.contains(openClass)) {
                setOpen(false);
            }
        } else if (navPanel) {
            navPanel.setAttribute('aria-hidden', !body.classList.contains(openClass) ? 'true' : 'false');
        }
    }

    btn.addEventListener('click', function () {
        if (isWide()) {
            return;
        }
        setOpen(!body.classList.contains(openClass));
    });

    if (backdrop) {
        backdrop.setAttribute('aria-hidden', 'true');
        backdrop.addEventListener('click', function () {
            setOpen(false);
        });
    }

    navPanel.addEventListener('click', function (e) {
        if (!isWide() && e.target.closest('a[href]')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && body.classList.contains(openClass)) {
            setOpen(false);
        }
    });

    function onMqChange() {
        syncAriaForWidth();
    }

    if (typeof MQ.addEventListener === 'function') {
        MQ.addEventListener('change', onMqChange);
    } else if (typeof MQ.addListener === 'function') {
        MQ.addListener(onMqChange);
    }

    window.addEventListener('resize', function () {
        if (isWide() && body.classList.contains(openClass)) {
            setOpen(false);
        }
    });

    syncAriaForWidth();
})();
