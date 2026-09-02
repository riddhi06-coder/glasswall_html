/*
 * Header live search — queries search.php and shows a dropdown of matching
 * projects + products. Uses event delegation on document so it works no matter
 * when/if the header markup is injected (static pages) or re-rendered.
 */
(function () {
    var timer = null;

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function closeDropdown() {
        var dd = document.querySelector('#gwsSearch .gws-search-dropdown');
        if (dd) dd.hidden = true;
    }

    // --- Toggle open/close + outside-click (delegated) ---
    document.addEventListener('click', function (e) {
        var toggle = e.target.closest ? e.target.closest('.gws-search-toggle') : null;
        if (toggle) {
            e.preventDefault();
            e.stopPropagation();
            var wrap = toggle.closest('#gwsSearch') || document.getElementById('gwsSearch');
            var dd = wrap && wrap.querySelector('.gws-search-dropdown');
            if (dd) {
                dd.hidden = !dd.hidden;
                if (!dd.hidden) {
                    var inp = wrap.querySelector('#gwsSearchInput');
                    if (inp) setTimeout(function () { inp.focus(); }, 0);
                }
            }
            return;
        }
        // Clicked outside the widget -> close it.
        if (!(e.target.closest && e.target.closest('#gwsSearch'))) {
            closeDropdown();
        }
    });

    // --- Typing in the search input (delegated) ---
    document.addEventListener('input', function (e) {
        if (!e.target || e.target.id !== 'gwsSearchInput') return;
        var input = e.target;
        var list = document.getElementById('gwsSearchResults');
        var q = input.value.trim();
        clearTimeout(timer);
        if (q.length < 2) { if (list) list.innerHTML = ''; return; }
        timer = setTimeout(function () { run(q, list); }, 200);
    });

    // --- Escape closes ---
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
    });

    function run(q, list) {
        fetch('search.php?q=' + encodeURIComponent(q))
            .then(function (r) { return r.json(); })
            .then(function (items) { render(items, list); })
            .catch(function () {
                if (list) list.innerHTML = '<li class="gws-search-empty">Search is unavailable right now.</li>';
            });
    }

    function render(items, list) {
        if (!list) return;
        if (!items || !items.length) {
            list.innerHTML = '<li class="gws-search-empty">No results found.</li>';
            return;
        }
        list.innerHTML = items.map(function (it) {
            var tagClass = it.type === 'Product' ? 'gws-tag product' : 'gws-tag';
            return '<li><a href="' + escapeHtml(it.url) + '">' +
                   '<span>' + escapeHtml(it.title) +
                   (it.sub ? '<br><small>' + escapeHtml(it.sub) + '</small>' : '') + '</span>' +
                   '<span class="' + tagClass + '">' + escapeHtml(it.type) + '</span>' +
                   '</a></li>';
        }).join('');
    }
})();
