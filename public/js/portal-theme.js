document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('mainContent');
    if (!root) return;
    const inDialog = element => element.closest('dialog, .modal, [role="dialog"], [id*="Modal"], [id*="modal"]');
    let header = [...root.querySelectorAll('header')].find(element => !inDialog(element));
    if (!header) {
        const heading = [...root.querySelectorAll('h1, h2')].find(element => !inDialog(element) && !element.closest('form, table'));
        const title = (root.dataset.pageTitle || '').replace(/\s+-\s+WCPU.*$/i, '').trim();
        if (heading || title) {
            header = document.createElement('header');
            const content = document.createElement('div');
            if (heading) {
                content.append(heading);
            } else {
                const label = document.createElement('h1');
                label.textContent = title;
                content.append(label);
            }
            header.append(content);
            root.prepend(header);
        }
    }
    if (header) {
        header.classList.add('portal-page-header');
        if (header.parentElement !== root) root.prepend(header);

    }

    const styledTables = new WeakSet();
    function styleTables() {
        root.querySelectorAll('table').forEach(table => {
            if (styledTables.has(table) || table.closest('#calendar, dialog, .modal, [role="dialog"]')) return;
            styledTables.add(table);
            let container = table.parentElement;
            if (!container.classList.contains('overflow-x-auto') && !container.classList.contains('overflow-auto') && !container.classList.contains('table-responsive')) {
                container = document.createElement('div');
                table.before(container);
                container.append(table);
            }
            container.classList.add('portal-table-scroll');
        });
    }
    styleTables();
    new MutationObserver(styleTables).observe(root, {childList: true, subtree: true});
});

// Forms that permanently delete or archive a record use the same responsive
// confirmation popup throughout both portals.
document.addEventListener('submit', event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirmAction || form.dataset.confirmed === 'true') return;

    event.preventDefault();
    const action = form.dataset.confirmAction;
    const isDelete = action === 'delete';
    const message = form.dataset.confirmMessage || (isDelete ? 'Are you sure you want to delete this item?' : 'Are you sure you want to archive this item?');
    const confirmSubmission = () => {
        form.dataset.confirmed = 'true';
        form.submit();
    };

    if (window.Swal) {
        Swal.fire({
            title: form.dataset.confirmTitle || (isDelete ? 'Delete item?' : 'Archive item?'),
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: isDelete ? 'Delete' : 'OK, Archive',
            confirmButtonColor: '#C41E3A',
            width: 'min(28rem, calc(100vw - 2rem))',
        }).then(result => {
            if (result.isConfirmed) confirmSubmission();
        });
    }
}, true);

// Keep the required explanation field in sync wherever a case edit form uses
// the "Others" abuse type.
document.addEventListener('change', event => {
    if (event.target.id !== 'abuse_type') return;
    const form = event.target.closest('form') || event.target.parentElement;
    const container = form?.querySelector('#other-abuse-container');
    const details = form?.querySelector('#other_abuse_details');
    if (!container || !details) return;
    const isOther = event.target.value === 'Others';
    container.classList.toggle('hidden', !isOther);
    details.required = isOther;
    if (!isOther) details.value = '';
});
