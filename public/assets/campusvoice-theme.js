(function () {
    var STORAGE_KEY = 'cv-theme';

    function currentTheme() {
        var t = document.documentElement.getAttribute('data-theme');
        return t === 'dark' ? 'dark' : 'light';
    }

    function applyTheme(dark) {
        var mode = dark ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', mode);
        try {
            localStorage.setItem(STORAGE_KEY, mode);
        } catch (e) {
            /* ignore */
        }
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        syncToggles();
    }

    function syncToggles() {
        var dark = currentTheme() === 'dark';
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
            btn.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', dark ? 'Light mode' : 'Dark mode');
        });
    }

    function bind() {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                applyTheme(currentTheme() !== 'dark');
            });
        });
        syncToggles();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bind);
    } else {
        bind();
    }
})();
