@if(auth()->user()?->isVawcAdmin())
<style>
    #swdArchiveViewDialog { position: fixed; inset: 0; margin: auto; width: min(1100px, 95vw); max-width: 95vw; max-height: 85vh; padding: 0; border: 0; border-radius: .75rem; color: #374151; background: #fff; }
    #swdArchiveViewDialog::backdrop { background: rgba(0, 0, 0, .4); }
    #swdArchiveViewDialog .swd-archive-dialog-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
    #swdArchiveViewDialog h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
    #swdArchiveViewDialog [data-swd-archive-content] { text-align: left; max-height: 65vh; overflow: auto; padding: 1.5rem; }
    #swdArchiveViewDialog .swd-archive-dialog-footer { padding: 1rem; text-align: center; border-top: 1px solid #e5e7eb; }
</style>
<dialog id="swdArchiveViewDialog" aria-labelledby="swdArchiveViewTitle">
    <div class="swd-archive-dialog-header">
        <h2 id="swdArchiveViewTitle"></h2>
    </div>
    <div data-swd-archive-content aria-live="polite"></div>
    <div class="swd-archive-dialog-footer">
        <button type="button" data-swd-archive-close class="rounded-lg bg-[#C41E3A] px-5 py-2 text-white">Close</button>
    </div>
</dialog>
<script>
(() => {
    const dialog = document.getElementById('swdArchiveViewDialog');
    const content = dialog.querySelector('[data-swd-archive-content]');
    let request;
    let generation = 0;
    const close = () => dialog.close();
    dialog.addEventListener('close', () => {
        generation++;
        request?.abort();
        content.replaceChildren();
    });
    dialog.addEventListener('click', event => {
        if (event.target !== dialog) return;
        const bounds = dialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close();
    });
    window.addEventListener('click', async event => {
        if (event.target.closest('#swdArchiveViewDialog [data-swd-archive-close]')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            close();
            return;
        }
        const view = event.target.closest('[data-swd-archive-view], [data-swd-archived-referral], [data-swd-archived-recipient]');
        if (!view) return;
        // Archived case links also use the shared BCPC preview. Let its handler
        // render the compact case details instead of cloning the full case page.
        if (view.dataset.swdArchiveView === 'case' && view.dataset.adminArchiveView === 'case') return;
        event.preventDefault();
        event.stopImmediatePropagation();
        window.swdArchiveNavigation?.cancelPending();
        request?.abort();
        const current = ++generation;
        const referral = view.hasAttribute('data-swd-archived-referral');
        const recipient = view.hasAttribute('data-swd-archived-recipient');
        const isCase = view.dataset.swdArchiveView === 'case';
        document.getElementById('swdArchiveViewTitle').textContent = recipient ? 'Referral Info' : referral ? 'Referral Details' : isCase ? 'Case Details' : 'Quarterly Report';
        content.textContent = 'Loading details...';
        if (!dialog.open) dialog.showModal();
        if (referral || recipient) {
            const template = document.getElementById(recipient ? view.dataset.swdArchivedRecipient : view.dataset.swdArchivedReferral);
            if (template) content.replaceChildren(template.content.cloneNode(true));
            else content.textContent = 'Unable to load the archived record.';
            return;
        }
        const load = async url => {
            request?.abort();
            const controller = new AbortController();
            request = controller;
            try {
                const response = await fetch(url, { credentials: 'same-origin', signal: controller.signal });
                if (!response.ok || response.redirected) throw new Error('Unable to load the archived record.');
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                if (!dialog.open || generation !== current || controller.signal.aborted) return;
                page.querySelectorAll('script').forEach(script => script.remove());
                if (isCase) {
                    // A full case page includes the admin shell. Only insert the
                    // case details into the dialog so the sidebar/header cannot
                    // appear over the archived preview.
                    const caseDetails = page.querySelector('.case-profile-details');
                    if (!caseDetails) throw new Error('Unable to load the archived case details.');
                    content.replaceChildren(caseDetails.cloneNode(true));
                    window.applyArchiveCaseModalDesign(content, document.getElementById('swdArchiveViewTitle'));
                } else {
                    const report = page.querySelector('.w-full.max-w-7xl.mx-auto');
                    if (!report) throw new Error('Unable to load report details.');
                    window.applyArchiveReportModalDesign(page, document.getElementById('swdArchiveViewTitle'));
                    report.querySelector(':scope > .mb-6.flex')?.remove();
                    report.querySelectorAll('form, button').forEach(control => control.remove());
                    report.className = 'w-full';
                    content.replaceChildren(report);
                }
            } catch (error) {
                if (error.name !== 'AbortError' && dialog.open && generation === current) content.textContent = error.message;
            }
        };
        content.onclick = event => {
            const link = event.target.closest('a');
            if (!link) return;
            event.preventDefault();
            event.stopPropagation();
            if (!isCase && new URL(link.href).searchParams.has('case_records_page')) load(link.href);
        };
        await load(view.href);
    }, true);
})();
</script>
@endif
