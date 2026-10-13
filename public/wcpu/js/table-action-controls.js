document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('mainContent') || document;
    const applyActionControlStyle = () => {
        root.querySelectorAll('table').forEach(table => {
            if (table.dataset.disableActionControls === 'true') return;
            const headerRow = table.tHead?.rows[0];
            if (!headerRow) return;
            const actionIndex = Array.from(headerRow.cells).findIndex(cell => /^actions?$/i.test(cell.textContent.trim()));
            if (actionIndex < 0) return;
            table.classList.add('portal-action-table');
            headerRow.cells[actionIndex].classList.add('table-action-heading');
            Array.from(table.tBodies).forEach(body => Array.from(body.rows).forEach(row => {
                if (Array.from(row.cells).some(cell => cell.colSpan > 1)) return;
                const cell = row.cells[actionIndex];
                if (!cell) return;
                cell.classList.add('table-action-cell');
                cell.querySelectorAll('a, button').forEach(control => {
                    if (control.dataset.iconAction === 'labelled') return;
                    control.classList.add('table-action-control');
                    const label = control.getAttribute('title') || control.textContent.trim().replace(/\s+/g, ' ');
                    if (label) {
                        control.setAttribute('title', label);
                        control.setAttribute('aria-label', label);
                    }
                });
            }));
        });
    };
    applyActionControlStyle();
    new MutationObserver(applyActionControlStyle).observe(root === document ? document.body : root, {childList: true, subtree: true});
});
