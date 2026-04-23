<?php
/**
 * Sets data-theme on <html> before first paint. Must be first in <head> after charset/viewport.
 */
?>
<script>
(function () {
    try {
        var k = 'cv-theme';
        var s = localStorage.getItem(k);
        var dark;
        if (s === 'dark') dark = true;
        else if (s === 'light') dark = false;
        else dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var mode = dark ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', mode);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
    } catch (e) { /* no-op */ }
})();
</script>
