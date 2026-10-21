<script>
const makeAdminArchiveFrameReadOnly = frame => {
    const doc = frame.contentDocument;
    if (!doc) return;
    window.applyArchiveReportModalDesign(doc, Swal.getTitle(), @json(auth()->user()?->isBcpcAdmin()));
    if (doc.querySelector('.case-profile-details')) {
        doc.body.classList.add('archive-case-modal-preview');
        @if(auth()->user()?->isVawcAdmin())
        doc.body.classList.add('bcpc-admin-header-theme');
        @endif
        window.applyArchiveCaseModalDesign(doc, Swal.getTitle());
        @if(auth()->user()?->isDepartmentAdmin())
        window.applyBcpcArchiveCaseDesign(doc);
        @endif
    }

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
    if (doc.body.classList.contains('archive-report-modal-preview') || doc.body.classList.contains('archive-case-modal-preview')) {
        doc.documentElement.style.setProperty('height', 'auto', 'important');
        doc.documentElement.style.setProperty('overflow-y', 'auto', 'important');
        doc.body.style.setProperty('height', 'auto', 'important');
        doc.body.style.setProperty('overflow-y', 'auto', 'important');
        const scrollArea = frame.parentElement;
        if (scrollArea) {
            scrollArea.style.setProperty('overflow-y', 'auto', 'important');
            scrollArea.style.setProperty('overflow-x', 'hidden', 'important');
            const fitFrameToContent = () => {
                const contentHeight = Math.max(doc.documentElement.scrollHeight, doc.body.scrollHeight, scrollArea.clientHeight);
                frame.style.setProperty('height', `${contentHeight}px`, 'important');
                frame.style.setProperty('flex', '0 0 auto', 'important');
            };
            doc.addEventListener('wheel', event => {
                scrollArea.scrollTop += event.deltaY;
                event.preventDefault();
            }, { passive: false });
            requestAnimationFrame(() => {
                fitFrameToContent();
                requestAnimationFrame(fitFrameToContent);
            });
        }
    }
};

document.addEventListener('click', event => {
    const link = event.target.closest('a[data-admin-archive-view]');
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    event.preventDefault();
    event.stopImmediatePropagation();

    const title = link.dataset.modalTitle || 'Archived Record Details';
    const type = link.dataset.adminArchiveView || 'report';
    const isSwdArchivedCase = @json(auth()->user()?->isVawcAdmin()) && type === 'case';
    const frame = document.createElement('iframe');
    frame.className = 'admin-archive-view-frame';
    frame.setAttribute('scrolling', 'yes');
    frame.style.visibility = 'hidden';
    frame.addEventListener('load', () => {
        try {
            makeAdminArchiveFrameReadOnly(frame);
        } catch (error) {
            console.error('Unable to prepare archived case preview.', error);
        } finally {
            frame.style.visibility = 'visible';
        }
    });
    @if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
    const previewUrl = new URL(link.href, window.location.href);
    if (type === 'case') previewUrl.searchParams.set('bcpc_modal_preview', '1');
    if (type === 'report') previewUrl.searchParams.set('archive_report_preview', '1');
    frame.src = previewUrl.href;
    @else
    frame.src = link.href;
    @endif
    frame.title = title;
    frame.loading = 'eager';

    if (isSwdArchivedCase) document.body.classList.add('bcpc-admin-header-theme');
    Swal.fire({
        title,
        html: frame,
        width: type === 'case' ? 'min(60rem, calc(100vw - 2rem))' : 'min(72rem, calc(100vw - 2rem))',
        customClass: { popup: `archive-view-modal-popup admin-archive-view-popup admin-archive-view-popup--${type}${isSwdArchivedCase ? ' swd-archive-case-view-popup' : ''}` },
        showConfirmButton: @json(auth()->user()?->isBcpcAdmin()) || isSwdArchivedCase,
        showCloseButton: !(@json(auth()->user()?->isBcpcAdmin()) || isSwdArchivedCase),
        @if(auth()->user()?->isBcpcAdmin()) confirmButtonText: 'Close',
        @elseif(auth()->user()?->isVawcAdmin()) confirmButtonText: 'Close', @endif
        allowOutsideClick: true
    }).then(() => {
        if (isSwdArchivedCase) document.body.classList.remove('bcpc-admin-header-theme');
    });
}, true);
</script>
