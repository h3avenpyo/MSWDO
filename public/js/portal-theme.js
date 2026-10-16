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

document.addEventListener('DOMContentLoaded', () => {
    if (!document.body.matches('.desk-officer-header-theme, .bcpc-admin-header-theme, .swd-admin-header-theme')) return;

    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    const tooltip = document.createElement('div');
    tooltip.setAttribute('role', 'tooltip');
    Object.assign(tooltip.style, {
        position: 'fixed',
        zIndex: '9999',
        display: 'none',
        padding: '0.45rem 0.65rem',
        borderRadius: '0.375rem',
        background: '#1f2937',
        color: '#ffffff',
        fontSize: '0.75rem',
        fontWeight: '600',
        lineHeight: '1rem',
        whiteSpace: 'nowrap',
        pointerEvents: 'none',
        boxShadow: '0 4px 12px rgba(15, 23, 42, 0.2)'
    });
    document.body.appendChild(tooltip);

    let activeItem = null;
    let activeTitle = null;

    const hideTooltip = () => {
        tooltip.style.display = 'none';
        if (activeItem && activeTitle !== null) activeItem.setAttribute('title', activeTitle);
        activeItem = null;
        activeTitle = null;
    };

    const showTooltip = item => {
        if (sidebar.getBoundingClientRect().width > 90) return;
        const label = item.dataset.tooltip || item.getAttribute('title') || item.querySelector('.sidebar-text')?.textContent.trim();
        if (!label) return;

        hideTooltip();
        activeItem = item;
        activeTitle = item.getAttribute('title');
        if (activeTitle !== null) item.removeAttribute('title');
        tooltip.textContent = label;
        tooltip.style.display = 'block';

        const itemRect = item.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        tooltip.style.left = `${itemRect.right + 8}px`;
        tooltip.style.top = `${Math.min(window.innerHeight - tooltipRect.height - 8, Math.max(8, itemRect.top + ((itemRect.height - tooltipRect.height) / 2)))}px`;
    };

    sidebar.addEventListener('pointerover', event => {
        const item = event.target.closest('[data-tooltip], [title], [x-show] > a, [x-show] > button');
        if (item && sidebar.contains(item)) showTooltip(item);
    });
    sidebar.addEventListener('pointerout', event => {
        if (activeItem && !activeItem.contains(event.relatedTarget)) hideTooltip();
    });
    sidebar.addEventListener('focusin', event => {
        const item = event.target.closest('[data-tooltip], [title], [x-show] > a, [x-show] > button');
        if (item && sidebar.contains(item)) showTooltip(item);
    });
    sidebar.addEventListener('focusout', hideTooltip);
    window.addEventListener('resize', hideTooltip);
});

// Restrict only person-name fields in the BCPC/VAWC admin and desk-officer
// portals. Unicode letters are retained so names such as Jose with an accent
// remain valid, along with spaces, hyphens, and apostrophes.
const personNameInputSelector = [
    'client_name',
    'victim_name',
    'child_name',
    'child_full_name',
    'parent_name',
    'mother_name',
    'father_name',
    'guardian',
    'guardian_name',
    'guardian_abuser_name',
    'complainant_name',
    'respondent_name',
    'perpetrator_name',
    'abuser_name',
    'reporting_person',
    'reporting_person_name',
    'referring_party_name',
    'referred_by_name',
    'recipient_name',
    'witness_name',
    'representative_name',
    'first_name',
    'middle_name',
    'last_name',
    'full_name',
    'name'
].map(name => `input:not([type="hidden"])[name="${name}"]`).join(',');

const isBcpcOrVawcPortal = () => document.body.matches(
    '.desk-officer-header-theme, .bcpc-admin-header-theme, .swd-admin-header-theme'
);

const sanitizePersonNameInput = input => {
    if (!(input instanceof HTMLInputElement) || !input.matches(personNameInputSelector)) return;

    const originalValue = input.value;
    const sanitizedValue = originalValue.replace(/[^\p{L}\p{M} '\u2019-]/gu, '');
    if (sanitizedValue === originalValue) return;

    const selectionStart = input.selectionStart;
    const removedBeforeCaret = selectionStart === null
        ? 0
        : originalValue.slice(0, selectionStart).length - originalValue.slice(0, selectionStart).replace(/[^\p{L}\p{M} '\u2019-]/gu, '').length;

    input.value = sanitizedValue;
    if (selectionStart !== null && document.activeElement === input) {
        const nextCaret = Math.max(0, selectionStart - removedBeforeCaret);
        input.setSelectionRange(nextCaret, nextCaret);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (!isBcpcOrVawcPortal()) return;

    document.querySelectorAll(personNameInputSelector).forEach(sanitizePersonNameInput);
    new MutationObserver(mutations => {
        mutations.forEach(mutation => mutation.addedNodes.forEach(node => {
            if (!(node instanceof Element)) return;
            if (node.matches(personNameInputSelector)) sanitizePersonNameInput(node);
            node.querySelectorAll(personNameInputSelector).forEach(sanitizePersonNameInput);
        }));
    }).observe(document.body, { childList: true, subtree: true });
});

document.addEventListener('input', event => {
    if (!isBcpcOrVawcPortal()) return;
    sanitizePersonNameInput(event.target);
});

document.addEventListener('submit', event => {
    if (!isBcpcOrVawcPortal() || !(event.target instanceof HTMLFormElement)) return;
    event.target.querySelectorAll(personNameInputSelector).forEach(sanitizePersonNameInput);
}, true);

// Number inputs normally accept scientific notation (for example, "e").
// Age fields in these portals accept decimal digits only.
document.addEventListener('keydown', event => {
    if (!document.body.matches('.desk-officer-header-theme, .bcpc-admin-header-theme, .swd-admin-header-theme')) return;

    const input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.matches('input[name*="age"]:not([type="hidden"])')) return;
    if (['e', 'E', '+', '-', '.', ','].includes(event.key)) event.preventDefault();
});

document.addEventListener('input', event => {
    if (!document.body.matches('.desk-officer-header-theme, .bcpc-admin-header-theme, .swd-admin-header-theme')) return;

    const input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.matches('input[name*="age"]:not([type="hidden"])')) return;

    const sanitizedValue = input.value.replace(/\D/g, '');
    if (sanitizedValue !== input.value) input.value = sanitizedValue;
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
