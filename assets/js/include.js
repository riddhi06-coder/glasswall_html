/*
 * Shared header/footer loader.
 * Injects the shared partials into the placeholders on each page:
 *     <div id="site-header"></div>   -> partials/header.html
 *     <div id="site-footer"></div>   -> partials/footer.html
 * Edit partials/header.html or partials/footer.html once; every page updates.
 *
 * Runs SYNCHRONOUSLY and BEFORE the theme scripts (jQuery, GSAP, main.js) so the
 * theme's menu / sticky header / smooth-scroll init binds to the injected DOM.
 */
(function () {
    function load(placeholderId, url) {
        var el = document.getElementById(placeholderId);
        if (!el) return;
        try {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, false); // synchronous on purpose (see note above)
            xhr.send(null);
            if (xhr.status >= 200 && xhr.status < 300 && xhr.responseText) {
                el.outerHTML = xhr.responseText; // replace the placeholder with the partial
            }
        } catch (e) { /* ignore */ }
    }
    load('site-header', 'partials/header.html');
    load('site-footer', 'partials/footer.html');
})();
