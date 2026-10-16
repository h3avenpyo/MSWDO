<style>
    .swal2-popup.admin-archive-view-popup {
        box-sizing: border-box;
        width: min(72rem, calc(100vw - 2rem)) !important;
        max-height: calc(100dvh - 2rem);
        overflow: hidden;
        padding: 0;
    }
    .swal2-popup.admin-archive-view-popup.admin-archive-view-popup--case {
        width: min(60rem, calc(100vw - 2rem)) !important;
    }
    .swal2-popup.admin-archive-view-popup .swal2-title {
        padding: 1rem 3.5rem .625rem;
    }
    .swal2-popup.admin-archive-view-popup .swal2-html-container {
        box-sizing: border-box;
        width: 100%;
        max-height: calc(100dvh - 5.5rem);
        margin: 0;
        padding: 0 .875rem .875rem;
        overflow: auto;
        overscroll-behavior: contain;
    }
    .admin-archive-view-frame {
        display: block;
        width: 100%;
        height: min(72dvh, 52rem);
        min-height: 24rem;
        border: 0;
    }
    @media (max-width: 47.9375rem) {
        .swal2-popup.admin-archive-view-popup,
        .swal2-popup.admin-archive-view-popup.admin-archive-view-popup--case {
            width: calc(100vw - 1rem) !important;
            max-height: calc(100dvh - 1rem);
        }
        .swal2-popup.admin-archive-view-popup .swal2-html-container {
            max-height: calc(100dvh - 5rem);
            padding: 0 .75rem .75rem;
        }
        .admin-archive-view-frame {
            height: calc(100dvh - 9rem);
            min-height: 18rem;
        }
    }
</style>
<script>
const makeAdminArchiveFrameReadOnly = frame => {
    const doc = frame.contentDocument;
    if (!doc) return;

    doc.querySelectorAll('#sidebar, #mobileTopBar, #sidebarToggleBtn, .sidebar-overlay, .portal-clock-only-header, nav, [role="navigation"]').forEach(element => element.remove());
    doc.querySelectorAll('button[aria-label="Notifications"]').forEach(button => button.closest('.relative')?.remove());
    doc.querySelectorAll('img[alt="User Profile"]').forEach(image => image.parentElement?.remove());
    doc.querySelectorAll('.portal-header-clock').forEach(clock => clock.remove());
    doc.querySelectorAll('a, button, [role="button"], [role="link"], [onclick], input[type="submit"], input[type="button"]').forEach(element => element.remove());
    doc.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(control => {
        if (control.tagName === 'TEXTAREA') control.readOnly = true;
        else control.disabled = true;
    });
    doc.querySelectorAll('[onchange], [oninput], [onsubmit]').forEach(element => {
        element.removeAttribute('onchange');
        element.removeAttribute('oninput');
        element.removeAttribute('onsubmit');
    });

    doc.addEventListener('submit', event => {
        event.preventDefault();
        event.stopImmediatePropagation();
    }, true);
};

document.addEventListener('click', event => {
    const link = event.target.closest('a[data-admin-archive-view]');
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    event.preventDefault();
    event.stopImmediatePropagation();

    const title = link.dataset.modalTitle || 'Archived Record Details';
    const type = link.dataset.adminArchiveView || 'report';
    const frame = document.createElement('iframe');
    frame.className = 'admin-archive-view-frame';
    frame.addEventListener('load', () => makeAdminArchiveFrameReadOnly(frame));
    frame.src = link.href;
    frame.title = title;
    frame.loading = 'eager';

    Swal.fire({
        title,
        html: frame,
        width: type === 'case' ? 'min(60rem, calc(100vw - 2rem))' : 'min(72rem, calc(100vw - 2rem))',
        customClass: { popup: `archive-view-modal-popup admin-archive-view-popup admin-archive-view-popup--${type}` },
        showConfirmButton: @json(auth()->user()?->isBcpcAdmin()),
        showCloseButton: @json(! auth()->user()?->isBcpcAdmin()),
        @if(auth()->user()?->isBcpcAdmin()) confirmButtonText: 'Close', @endif
        allowOutsideClick: true
    });
}, true);
</script>
