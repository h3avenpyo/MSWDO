@if(auth()->user()?->isDepartmentAdmin())
@push('styles')
<style>
    html body.portal-ui #mainContent :is(.tablet-profile-pagination, .user-logs-tablet-pagination [data-pagination-label], #referrals-table-pagination .referrals-profile-pagination [data-pagination-label], .referrals-phone-pagination [data-pagination-label], #quarterly-reports-results [data-pagination-label], .monitoring-tablet-pagination [data-pagination-label]) {
        display: flex !important; flex-direction: row !important; flex-wrap: wrap !important;
        align-items: center !important; justify-content: space-between !important;
        gap: 1rem !important; padding: 1rem 1.5rem !important; background: #fff !important;
        border-radius: 0 !important; box-shadow: none !important; text-align: left !important;
    }
    html body.portal-ui #mainContent :is(.user-logs-legacy-pagination, .monitoring-legacy-pagination, .quarterly-existing-pagination) { display: none !important; }
    html body.portal-ui #mainContent :is(.user-logs-tablet-pagination, .monitoring-tablet-pagination, .quarterly-phone-pagination) { display: block !important; }
    html body.portal-ui #mainContent #referrals-table-pagination > :not(.referrals-profile-pagination),
    html body.portal-ui #mainContent #referrals-card-pagination > :not(.referrals-phone-pagination) { display: none !important; }
    html body.portal-ui #mainContent :is(#referrals-table-pagination, #referrals-card-pagination) { padding: 0 !important; display: block !important; }
    html body.portal-ui #mainContent :is(.referrals-profile-pagination, .referrals-phone-pagination) { display: block !important; width: 100% !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination :is(#page-numbers, #mobile-page-numbers) { display: contents !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination #mobile-page-info { display: none !important; }
    html body.portal-ui #mainContent :is(.tablet-profile-pagination, .user-logs-tablet-pagination, .referrals-profile-pagination, .referrals-phone-pagination, #quarterly-reports-results [data-pagination-label], .monitoring-tablet-pagination) :is(button, a) {
        display: inline-flex !important; align-items: center !important; justify-content: center !important;
        flex: 0 0 auto !important; width: 2.25rem !important; min-width: 0 !important;
        height: 2.25rem !important; min-height: 0 !important; padding: 0 !important; margin: 0 !important;
        border-radius: .5rem !important; font-size: .875rem !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination button:disabled { background: #fff !important; border-color: #d1d5db !important; opacity: .5 !important; }
    html body.portal-ui #mainContent :is(.tablet-profile-pagination, [data-pagination-label]) > div:last-child { flex-wrap: nowrap !important; gap: .5rem !important; }
    @media (max-width: 47.9375rem) {
        html body.portal-ui #mainContent :is(.tablet-profile-pagination, .user-logs-tablet-pagination [data-pagination-label], .referrals-profile-pagination [data-pagination-label], .referrals-phone-pagination [data-pagination-label], #quarterly-reports-results [data-pagination-label], .monitoring-tablet-pagination [data-pagination-label]) { padding: .75rem !important; gap: .75rem !important; }
        html body.portal-ui #mainContent :is(.tablet-profile-pagination, .user-logs-tablet-pagination, .referrals-profile-pagination, .referrals-phone-pagination, #quarterly-reports-results [data-pagination-label], .monitoring-tablet-pagination) :is(button, a) { width: 2rem !important; height: 2rem !important; font-size: .6875rem !important; }
        html body.portal-ui #mainContent :is(.tablet-profile-pagination, [data-pagination-label]) > div:last-child { gap: .25rem !important; }
    }
</style>
@endpush
@if(!empty($paginationRegions))
@push('scripts')
<script>
(() => {
    const regions = @json($paginationRegions);
    const links = @json($paginationLinks);
    let pending;
    async function loadPage(url, pushHistory) {
        pending?.abort();
        const request = new AbortController();
        pending = request;
        const current = document.querySelector(regions[0]);
        current?.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: request.signal });
            if (!response.ok || response.redirected) throw new Error('Unable to load results');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const replacements = regions.map(selector => [document.querySelector(selector), page.querySelector(selector)]);
            if (replacements.some(([before, after]) => !before || !after)) throw new Error('Missing results');
            if (request.signal.aborted || pending !== request) return;
            replacements.forEach(([before, after]) => before.replaceWith(after));
            if (pushHistory) history.pushState({}, '', url);
            const target = replacements[0][1];
            target.setAttribute('tabindex', '-1');
            if (pushHistory) target.focus({ preventScroll: true });
        } catch (error) {
            if (error.name === 'AbortError' || pending !== request || !current) return;
            let message = current.querySelector('[data-pagination-error]');
            if (!message) {
                message = document.createElement('p');
                message.dataset.paginationError = '';
                message.className = 'px-5 py-3 text-sm text-red-600';
                message.setAttribute('role', 'alert');
                current.appendChild(message);
            }
            message.textContent = 'Unable to load results. Please try the page link again.';
        } finally {
            if (pending === request) current?.removeAttribute('aria-busy');
        }
    }
    document.addEventListener('click', event => {
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest(links);
        if (!link || link.target === '_blank') return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || url.pathname !== location.pathname) return;
        if (link.getAttribute('href') !== '#' && !url.searchParams.has('page')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (link.getAttribute('href') === '#' || link.classList.contains('pointer-events-none') || url.href === location.href) return;
        loadPage(url, true);
    }, true);
    document.addEventListener('input', () => pending?.abort(), true);
    document.addEventListener('submit', () => pending?.abort(), true);
    window.addEventListener('popstate', () => loadPage(new URL(location.href), false));
})();
</script>
@endpush
@endif
@endif
