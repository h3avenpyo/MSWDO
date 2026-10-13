document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#mainContent table').forEach(table => {
        if (table.closest('#calendar, dialog, [role="dialog"]')) return;
        const rows = Array.from(table.tBodies).flatMap(body => Array.from(body.rows));
        if (rows.length <= 5) return;
        let page = 1;
        const pages = Math.ceil(rows.length / 5);
        const controls = document.createElement('div');
        controls.className = 'flex items-center justify-between gap-3 p-4 text-sm';
        const previous = document.createElement('button');
        const next = document.createElement('button');
        const label = document.createElement('span');
        label.setAttribute('aria-live', 'polite');
        [[previous, 'Previous page', 'left'], [next, 'Next page', 'right']].forEach(([button, title, direction]) => {
            button.type = 'button';
            button.title = title;
            button.setAttribute('aria-label', title);
            button.className = 'admin-page-control';
            button.innerHTML = `<i class="fas fa-chevron-${direction}" aria-hidden="true"></i>`;
        });
        function render() {
            rows.forEach((row, index) => { row.hidden = index < (page - 1) * 5 || index >= page * 5; });
            label.textContent = `Page ${page} of ${pages}`;
            previous.disabled = page === 1;
            next.disabled = page === pages;
        }
        previous.addEventListener('click', () => { page--; render(); });
        next.addEventListener('click', () => { page++; render(); });
        label.className = 'admin-page-label';
        controls.append(previous, label, next);
        table.after(controls);
        render();
    });
});
