document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('mainContent');
    if (!root) return;
    const icons = {
        archive: 'archive', delete: 'trash',
        view: 'eye', edit: 'pen-to-square',
        activate: 'eye', deactivate: 'eye-slash', guardian: 'user-shield',
        logs: 'history', reset: 'key', change: 'key'
    };
    function updateActions() {
        root.querySelectorAll('button:not([data-icon-action]), a:not([data-icon-action])').forEach(button => {
            const tableAction = button.closest('table, .referral-card, #recipientsCardsMobile, #users-cards-mobile, #logs-cards-mobile');
            if (!tableAction && button.closest('[role="dialog"], dialog, [id*="Modal"], [id*="modal"]')) return;
            const label = button.textContent.trim().replace(/\s+/g, ' ') || button.title;
            if (!tableAction && /\b(print|download|restore|summary)\b/i.test(label)) {
                button.dataset.iconAction = 'labelled';
                button.querySelectorAll('.sr-only').forEach(span => span.classList.remove('sr-only'));
                if (!button.textContent.trim()) {
                    const text = document.createElement('span');
                    text.textContent = label;
                    text.className = 'ml-1';
                    button.append(text);
                }
                return;
            }
            const action = label.toLowerCase().split(' ')[0];
            const iconName = icons[action] || (tableAction && {print: 'print', download: 'download', restore: 'rotate-left', summary: 'chart-bar'}[action]);
            if (!iconName) return;
            button.dataset.iconAction = 'true';
            button.title = label;
            button.setAttribute('aria-label', label);
            if (!button.querySelector('i, svg')) {
                const icon = document.createElement('i');
                icon.className = `fas fa-${iconName}`;
                icon.setAttribute('aria-hidden', 'true');
                button.prepend(icon);
            }
            const walker = document.createTreeWalker(button, NodeFilter.SHOW_TEXT);
            const nodes = [];
            while (walker.nextNode()) {
                if (walker.currentNode.textContent.trim() && !walker.currentNode.parentElement.closest('i, svg, .sr-only')) nodes.push(walker.currentNode);
            }
            nodes.forEach(node => {
                const span = document.createElement('span');
                span.className = 'sr-only';
                node.replaceWith(span);
                span.append(node);
            });
        });
    }
    updateActions();
    new MutationObserver(updateActions).observe(root, {childList: true, subtree: true});
});
