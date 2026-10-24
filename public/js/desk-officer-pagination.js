(() => {
    const bindings = [];
    // Existing action setup runs once per element, including newly paginated cards.
    window.portalPaginationElements = selector => ({
        forEach(callback) {
            const seen = new WeakSet();
            const bind = () => document.querySelectorAll(selector).forEach((element, index) => {
                if (seen.has(element)) return;
                seen.add(element);
                callback(element, index);
            });
            bindings.push(bind);
            bind();
        }
    });
    let pending;
    const pageParameter = name => /^(page|[a-z_]+_page)$/.test(name);
    function refreshLinks(url) {
        document.querySelectorAll('[data-ajax-pagination] a[href]').forEach(link => {
            const target = new URL(link.href, location.href);
            const owner = link.closest('[data-ajax-pagination]').dataset.ajaxPage;
            if (!owner || !target.searchParams.has(owner)) return;
            const number = target.searchParams.get(owner);
            target.search = url.search;
            target.searchParams.set(owner, number);
            link.href = target.href;
        });
    }
    async function load(url, key, push) {
        pending?.abort();
        const request = new AbortController();
        pending = request;
        const current = key ? document.querySelector(`[data-ajax-pagination="${CSS.escape(key)}"]`) : null;
        const busy = current || document.querySelector('#mainContent');
        busy?.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: request.signal });
            if (!response.ok || response.redirected) throw new Error('Request failed');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const regions = key ? [current] : Array.from(document.querySelectorAll('[data-ajax-pagination]'));
            const updates = regions.map(region => {
                const replacement = page.querySelector(`[data-ajax-pagination="${CSS.escape(region.dataset.ajaxPagination)}"]`);
                if (!replacement) throw new Error('Missing results');
                return [region, replacement];
            });
            if (request.signal.aborted || pending !== request) return;
            updates.forEach(([region, replacement]) => region.replaceWith(replacement));
            // Keep the hidden case print data in sync with the displayed page.
            document.querySelectorAll('[data-ajax-pagination-companion]').forEach(region => {
                const owner = region.dataset.ajaxPaginationCompanion;
                if (key && owner !== key) return;
                const replacement = page.querySelector(`[data-ajax-pagination-companion="${CSS.escape(owner)}"]`);
                if (replacement) region.replaceWith(replacement);
            });
            if (push) history.pushState({}, '', url);
            refreshLinks(url);
            bindings.forEach(bind => bind());
            // Preserve live search/status input values and reapply their existing filters.
            document.querySelectorAll('#case-search, #referrals-search-input, #archived-case-search, #archived-referral-search, #archived-report-search').forEach(input => {
                if (input.value) input.dispatchEvent(new Event('input', { bubbles: true }));
            });
            const status = document.getElementById('archived-status-filter');
            if (status?.value) status.dispatchEvent(new Event('change', { bubbles: true }));
            const updated = key && document.querySelector(`[data-ajax-pagination="${CSS.escape(key)}"]`);
            if (push && updated) {
                updated.setAttribute('tabindex', '-1');
                updated.focus({ preventScroll: true });
            }
        } catch (error) {
            if (error.name === 'AbortError' || pending !== request) return;
            const region = current || document.querySelector('[data-ajax-pagination]');
            if (!region) return;
            let message = region.querySelector('[data-pagination-error]');
            if (!message) {
                message = document.createElement('p');
                message.dataset.paginationError = '';
                message.className = 'px-4 py-3 text-sm text-red-600';
                message.setAttribute('role', 'alert');
                region.appendChild(message);
            }
            message.textContent = 'Unable to load this page. Please try the page link again.';
        } finally {
            if (pending === request) busy?.removeAttribute('aria-busy');
        }
    }
    document.addEventListener('click', event => {
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest('[data-ajax-pagination] a[href]');
        if (!link || (link.target && link.target !== '_self') || link.hasAttribute('download')) return;
        const region = link.closest('[data-ajax-pagination]');
        const url = new URL(link.href, location.href);
        const owner = region.dataset.ajaxPage || 'page';
        if (url.origin !== location.origin || url.pathname !== location.pathname || !url.searchParams.has(owner)) return;
        // Only pagination parameters may differ; action links continue to work normally.
        const targetFilters = new URLSearchParams(url.search);
        const currentFilters = new URLSearchParams(location.search);
        for (const params of [targetFilters, currentFilters]) {
            [...params.keys()].filter(pageParameter).forEach(name => params.delete(name));
            params.sort();
        }
        if (targetFilters.toString() !== currentFilters.toString()) return;
        event.preventDefault();
        if (link.getAttribute('aria-disabled') === 'true' || link.classList.contains('pointer-events-none') || url.href === location.href) return;
        load(url, region.dataset.ajaxPagination, true);
    });
    window.addEventListener('popstate', () => {
        if (document.querySelector('[data-ajax-pagination]')) load(new URL(location.href), null, false);
    });
})();
