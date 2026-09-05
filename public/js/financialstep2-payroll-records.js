/**
 * Financial Assistance Step 2 Payroll Records JavaScript
 */

/**
 * Expand all collapsible payroll record cards on the Date View.
 */
function expandAllRecords() {
    document.querySelectorAll('.record-collapse').forEach(function (el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
            const collapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
            collapse.show();
        }
    });
}

/**
 * Collapse all collapsible payroll record cards on the Date View.
 */
function collapseAllRecords() {
    document.querySelectorAll('.record-collapse').forEach(function (el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
            const collapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
            collapse.hide();
        }
    });
}

// Expose functions globally for backward compatibility
window.expandAllRecords = expandAllRecords;
window.collapseAllRecords = collapseAllRecords;

document.addEventListener('DOMContentLoaded', function () {
    // Delegated / bound triggers for Expand All and Collapse All
    const btnExpandAll = document.getElementById('btnExpandAll');
    if (btnExpandAll) {
        btnExpandAll.addEventListener('click', function (e) {
            e.preventDefault();
            expandAllRecords();
        });
    }

    const btnCollapseAll = document.getElementById('btnCollapseAll');
    if (btnCollapseAll) {
        btnCollapseAll.addEventListener('click', function (e) {
            e.preventDefault();
            collapseAllRecords();
        });
    }

    document.addEventListener('click', function (e) {
        const expandTrigger = e.target.closest('.btn-expand-all');
        if (expandTrigger) {
            e.preventDefault();
            expandAllRecords();
        }

        const collapseTrigger = e.target.closest('.btn-collapse-all');
        if (collapseTrigger) {
            e.preventDefault();
            collapseAllRecords();
        }
    });

    // Automatic Search Debounce & Cursor Position Management
    const filterForm = document.getElementById('payrollRecordsFilterForm') || document.getElementById('datePayrollFilterForm');
    const searchInput = document.getElementById('recordsSearchInput');

    if (filterForm && searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                filterForm.submit();
            }, 550);
        });

        // Maintain cursor position at end of text after auto-submit
        if (searchInput.value.trim().length > 0 && document.activeElement !== searchInput) {
            const val = searchInput.value;
            searchInput.focus();
            searchInput.setSelectionRange(val.length, val.length);
        }
    }

    // Automatic form submission on filter change for selects and date inputs
    const filterForms = document.querySelectorAll('#payrollRecordsFilterForm, #datePayrollFilterForm');
    filterForms.forEach(function (form) {
        const inputs = form.querySelectorAll('select[name="barangay"], select[name="payroll_id"], select[name="sort"], input[name="date_from"], input[name="date_to"]');
        inputs.forEach(function (input) {
            input.addEventListener('change', function () {
                form.submit();
            });
        });
    });
});
