document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('mainContent');
    if (!root) return;
    function stylePagination() {
        const controls = root.querySelectorAll('#page-numbers button, [data-page], [id*="page"][id*="prev"], [id*="page"][id*="next"], [aria-label="Previous page"], [aria-label="Next page"], nav[role="navigation"] a, nav[role="navigation"] [aria-current] > span, nav[role="navigation"] [aria-disabled] > span, a[href*="page="]');
        controls.forEach(control => {
            control.classList.add('admin-page-control');
            const active = control.getAttribute('aria-current') === 'page' || control.parentElement?.getAttribute('aria-current') === 'page'
                || /\bbg-purple-(600|700)\b/.test(control.className);
            control.classList.toggle('admin-page-current', active);
        });
    }
    stylePagination();
    new MutationObserver(stylePagination).observe(root, {childList: true, subtree: true});
});
