// Presentation only: retain visibility controls, callbacks and action order.
(() => {
    const classifyPopup = popup => {
        if (popup.matches('.swal2-toast')) return 'toast';
        if (popup.matches('.app-logout-modal, .portal-logout-modal')) return 'logout';
        if (popup.matches('.admin-archive-view-popup, .desk-archive-view-popup, .admin-archived-referral-popup, .archived-referral-view-popup, .swd-archive-detail-popup')
            || popup.querySelector('iframe, .case-modal-details, .archived-referral-details, .swd-archive-detail')
            || popup.querySelector('.swal2-confirm')?.textContent.trim().toLowerCase() === 'close') return 'detail';
        if (popup.querySelector('.swal2-html-container form, .swal2-html-container input, .swal2-html-container select, .swal2-html-container textarea')) return 'form';
        return 'alert';
    };
    const toggle = (node, name, enabled) => {
        if (node.classList.contains(name) !== enabled) node.classList.toggle(name, enabled);
    };
    const update = () => {
        document.querySelectorAll('.swal2-popup').forEach(popup => {
            const kind = classifyPopup(popup);
            ['alert', 'form', 'detail'].forEach(value => toggle(popup, `portal-${value}-popup`, kind === value));
        });
        let nativeOpen = false;
        document.querySelectorAll('dialog, div[id$="Modal"], .modal[id]').forEach(root => {
            if (!root.matches('dialog, .modal, .fixed, [x-data], [role="dialog"]')) return;
            const panel = root.querySelector('.modal-content, .refer-modal')
                || (root.matches('dialog') ? root.querySelector(':scope > .bg-white') || root : root.querySelector('.bg-white'));
            if (!panel) return;
            toggle(panel, 'portal-modal-panel', true);
            const header = panel.querySelector(':scope > .modal-header, :scope > .case-modal-top-header, :scope > .swd-archive-dialog-header, :scope > div.border-b:has(h2, h3, h4, h5), :scope > div:first-child[class*="bg-"]:has(h2, h3, h4, h5)');
            toggle(panel, 'portal-modal-framed', !!header);
            if (header) {
                toggle(header, 'portal-modal-header', true);
                panel.querySelectorAll(':scope > .modal-body, :scope > .p-6:not(.portal-modal-header), :scope > [x-html], :scope > form, :scope > #referralDetails, :scope > #userModalContent, :scope > #editModalContent, :scope > #modalContent, :scope > #activityDetails, :scope > [data-swd-archive-content], :scope > #viewRecipientContent, :scope > #viewArchivedRecipientContent').forEach(body => toggle(body, 'portal-modal-body', true));
            }
            panel.querySelectorAll('div').forEach(row => {
                if (header?.contains(row) || row === header) return;
                const actions = row.querySelectorAll(':scope > button, :scope > form:has(button[type="submit"])');
                if (actions.length >= 2) toggle(row, 'portal-modal-actions', true);
            });
            root.querySelectorAll('.fixed.inset-0').forEach(overlay => {
                if (!panel.contains(overlay)) toggle(overlay, 'portal-modal-overlay', true);
            });
            if (root.matches('.fixed')) toggle(root, 'portal-modal-overlay', true);
            if ((!root.matches('dialog') || root.open)
                && panel.getClientRects().length && getComputedStyle(panel).visibility !== 'hidden') nativeOpen = true;
        });
        toggle(document.body, 'portal-native-modal-open', nativeOpen);
    };
    document.addEventListener('DOMContentLoaded', () => {
        if (!document.body.classList.contains('portal-ui')) return;
        let queued = false;
        const observer = new MutationObserver(() => {
            if (queued) return;
            queued = true;
            requestAnimationFrame(() => { queued = false; update(); });
        });
        observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'style', 'open'] });
        update();
    });
})();
