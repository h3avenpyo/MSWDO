/**
 * Financial Assistance Step 2 Monthly Liquidation JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // Search Debounce
    const filterForm = document.getElementById('liquidationFilterForm');
    const searchInput = document.getElementById('liquidationSearchInput');

    if (filterForm && searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                filterForm.submit();
            }, 550);
        });

        if (searchInput.value.trim().length > 0 && document.activeElement !== searchInput) {
            const val = searchInput.value;
            searchInput.focus();
            searchInput.setSelectionRange(val.length, val.length);
        }
    }

    // Auto-submit on select changes (Month, Status)
    if (filterForm) {
        const selects = filterForm.querySelectorAll('select[name="status"], select[name="month"]');
        selects.forEach(function (sel) {
            sel.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }
});
