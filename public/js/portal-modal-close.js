// Preserve each modal's existing dismiss handler; expose it as a footer button.
document.addEventListener('DOMContentLoaded', () => {
    if (!document.body.classList.contains('portal-ui')) return;
    const update = () => {
        document.querySelectorAll('.swal2-popup button, dialog button, [id$="Modal"] button').forEach(source => {
            if (source.dataset.footerDismiss || source.classList.contains('portal-modal-close')) return;
            const text = source.textContent.trim();
            const isIconClose = source.matches('.swal2-close, .btn-close')
                || (source.querySelector('.fa-times') && !text)
                || /^[×✕✖]$/.test(text);
            if (!isIconClose || source.disabled || source.hidden || source.classList.contains('hidden')
                || getComputedStyle(source).display === 'none') return;
            const root = source.closest('.swal2-popup, dialog, div[id$="Modal"]');
            if (!root || root.classList.contains('swal2-toast')) return;
            const existing = [...root.querySelectorAll('button, a')].find(button => button !== source
                && button.textContent.trim().toLowerCase() === 'close'
                && !button.hidden && !button.classList.contains('hidden'));
            // These form headers repeat an existing Cancel action. Keep its
            // original handler instead of adding a second footer dismiss button.
            const cancel = [...root.querySelectorAll('button')].find(button => button !== source
                && button.textContent.trim().toLowerCase() === 'cancel'
                && !button.hidden && !button.classList.contains('hidden'));
            const sameCancel = cancel && ((source.getAttribute('onclick')
                && source.getAttribute('onclick') === cancel.getAttribute('onclick'))
                || (root.id === 'recipientModal' && source.id === 'closeModal' && cancel.id === 'cancelBtn'));
            source.dataset.footerDismiss = 'true';
            source.classList.add('portal-modal-dismiss-source');
            if (sameCancel) return;
            if (existing) {
                existing.classList.add('portal-modal-close');
                return;
            }
            const panel = source.closest('.modal-content, .refer-modal, .bg-white') || root;
            let footer = panel.querySelector(':scope > .portal-modal-dismiss-footer');
            if (!footer) {
                footer = document.createElement('div');
                footer.className = 'portal-modal-dismiss-footer';
                panel.appendChild(footer);
            }
            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'portal-modal-close';
            close.textContent = 'Close';
            close.addEventListener('click', () => source.click());
            footer.appendChild(close);
        });
    };
    let queued = false;
    const observer = new MutationObserver(() => {
        if (queued) return;
        queued = true;
        queueMicrotask(() => { queued = false; update(); });
    });
    observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'style', 'open'] });
    update();
});
