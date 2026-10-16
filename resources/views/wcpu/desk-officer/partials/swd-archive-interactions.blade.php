@if(in_array(auth()->user()->defaultCaseDepartment(), ['VAWC', 'BCPC'], true) && !auth()->user()->isMultiCaseDepartment())
<style>
    .swd-archive-detail { text-align: left; max-height: 65vh; overflow: auto; padding: .25rem; font-size: .875rem; color: #374151; }
    .swd-archive-detail table { width: 100%; border-collapse: collapse; }
    .swd-archive-detail :is(th, td) { padding: .75rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
    .swd-archive-detail th { background: #f9fafb; font-weight: 600; }
    .swd-archive-detail .swd-referral-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .swd-archive-detail .swd-referral-field { padding: .75rem; border: 1px solid #e5e7eb; border-radius: .5rem; min-width: 0; }
    .swd-archive-detail .swd-referral-field dt { color: #6b7280; font-size: .75rem; margin-bottom: .375rem; }
    .swd-archive-detail .swd-referral-field dd { margin: 0; white-space: pre-wrap; overflow-wrap: anywhere; }
    @media (max-width: 640px) { .swd-archive-detail .swd-referral-fields { grid-template-columns: 1fr; } }
</style>
@endif
<script>
(() => {
    const archiveModalStyles = document.createElement('style');
    archiveModalStyles.textContent = `
        .swal2-popup.desk-archive-view-popup{box-sizing:border-box;max-height:calc(100dvh - 2rem);overflow:hidden;padding:0}
        .swal2-popup.desk-archive-view-popup .swal2-title{padding:1rem 3.5rem .625rem}
        .swal2-popup.desk-archive-view-popup .swal2-html-container{box-sizing:border-box;width:100%;max-height:calc(100dvh - 5.5rem);margin:0;padding:0 .875rem .875rem;overflow:auto;overscroll-behavior:contain;text-align:left}
        .swal2-popup.desk-archive-view-popup .desk-archive-view-frame{display:block;width:100%;height:min(70dvh,52rem);border:0}
        @media(max-width:47.9375rem){.swal2-popup.desk-archive-view-popup{width:calc(100vw - 1rem)!important;max-height:calc(100dvh - 1rem)}.swal2-popup.desk-archive-view-popup .swal2-title{padding:1rem 3rem .5rem}.swal2-popup.desk-archive-view-popup .swal2-html-container{max-height:calc(100dvh - 5rem);padding:0 .75rem .75rem}.swal2-popup.desk-archive-view-popup .desk-archive-view-frame{height:calc(100dvh - 8rem);min-height:16rem}}
    `;
    document.head.appendChild(archiveModalStyles);

    const makeArchiveFrameReadOnly = frame => {
        const doc = frame.contentDocument;
        if (!doc) return;

        const frameUrl = new URL(frame.src, window.location.href);
        const caseTitle = doc.querySelector('#mainContent > header h2');
        const isArchivedDetailHeader = caseTitle?.textContent.trim() === 'Case Details'
            || caseTitle?.textContent.includes('Quarterly Compliance Report')
            || caseTitle?.textContent.includes('Case Monitoring');
        if (frameUrl.searchParams.get('archived') === '1' && isArchivedDetailHeader) {
            const headerRow = doc.querySelector('#mainContent > header > div');
            const titleGroup = headerRow?.firstElementChild;
            if (headerRow && titleGroup) {
                // The archive iframe strips the header controls, so let the detail title use the full row.
                headerRow.style.setProperty('display', 'block', 'important');
                titleGroup.style.setProperty('width', '100%', 'important');
                titleGroup.style.setProperty('max-width', 'none', 'important');
                titleGroup.style.setProperty('min-width', '0', 'important');
            }
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
    };

    const search = document.querySelector('#archived-case-search, #archived-report-search, #archived-referral-search, #monitoring-search');
    if (!search) return;
    const filters = search.form;
    const container = filters.parentElement;
    // Authorized archive records are loaded once; typing never waits on a request.
    const records = Array.from(container.querySelectorAll('table tbody tr, article')).filter(element => element.tagName === 'ARTICLE' || element.cells.length > 1).map(element => ({
        element,
        text: ((element.dataset.archiveSearch || '') + ' ' + element.textContent).toLowerCase(),
        status: (element.dataset.archiveStatus || element.cells?.[2]?.textContent || '').toLowerCase().trim(),
        period: (element.dataset.archivePeriod || (search.id === 'monitoring-search' ? element.cells?.[1]?.textContent : element.cells?.[0]?.textContent) || element.textContent).toLowerCase()
    }));
    const refresh = () => {
        const query = search.value.toLowerCase().trim();
        const status = (filters.elements.namedItem('status')?.value || '').toLowerCase();
        const quarter = filters.elements.namedItem('quarter')?.value || '';
        const year = filters.elements.namedItem('year')?.value || '';
        records.forEach(({ element, text, status: recordStatus, period }) => {
            const matchesStatus = !status || recordStatus === status || (status === 'case closed' && /closed|complete/.test(recordStatus));
            const matches = (!query || text.includes(query)) && matchesStatus && (!quarter || period.includes('q' + quarter)) && (!year || period.includes(year));
            element.style.display = matches ? '' : 'none';
        });
    };
    refresh();
    filters.addEventListener('input', event => {
        if (event.target !== search) return;
        event.stopImmediatePropagation();
        refresh();
    }, true);
    filters.addEventListener('change', event => {
        if (event.target.tagName !== 'SELECT') return;
        event.stopImmediatePropagation();
        refresh();
    }, true);

    @php($useArchiveDetailModal = in_array(auth()->user()->defaultCaseDepartment(), ['VAWC', 'BCPC'], true) && !auth()->user()->isMultiCaseDepartment())
    const useSwdDetailModal = @json($useArchiveDetailModal);
    const openSwdDetailModal = (view, isReferral, title) => {
        const isCase = view.title === 'View Case';
        const isMonitoring = view.title === 'View Record' || !!view.closest('.archived-monitoring-card');
        const url = isReferral
            ? @json(url('/desk-officer/referrals')) + '/' + view.dataset.referralId + '/details?archived=1'
            : view.href;
        Swal.fire({ title, width: isReferral ? 'min(760px, 95vw)' : 'min(1100px, 95vw)',
            html: '<div class="swd-archive-detail" aria-live="polite">Loading details...</div>',
            showCloseButton: true, showConfirmButton: true, confirmButtonText: 'Close', confirmButtonColor: '#7c3aed',
            didOpen: async () => {
                const content = Swal.getHtmlContainer()?.querySelector('.swd-archive-detail');
                const loadDetails = async (detailsUrl) => {
                    content.textContent = 'Loading details...';
                    try {
                        const response = await fetch(detailsUrl, { credentials: 'same-origin', headers: isCase || isReferral ? { 'X-Requested-With': 'XMLHttpRequest' } : {} });
                        if (!response.ok || response.redirected) throw new Error('Unable to load the archived record.');
                        const html = await response.text();
                        if (Swal.getHtmlContainer()?.querySelector('.swd-archive-detail') !== content) return;
                        const page = new DOMParser().parseFromString(html, 'text/html');
                        page.querySelectorAll('script').forEach(script => script.remove());
                        if (isReferral) {
                            const fields = document.createElement('dl');
                            fields.className = 'swd-referral-fields';
                            page.querySelectorAll('input:not([type="hidden"]), textarea, select').forEach(input => {
                                const field = document.createElement('div');
                                field.className = 'swd-referral-field';
                                const label = document.createElement('dt');
                                label.textContent = input.closest('.mb-3, .mb-4')?.querySelector('label')?.textContent.trim() || input.name.replaceAll('_', ' ');
                                const value = document.createElement('dd');
                                value.textContent = input.tagName === 'SELECT' ? input.selectedOptions[0]?.textContent || '—' : input.value || '—';
                                field.append(label, value);
                                fields.appendChild(field);
                            });
                            if (!fields.children.length) throw new Error('Unable to load referral details.');
                            content.replaceChildren(fields);
                        } else if (isCase) {
                            if (page.querySelector('#sidebar, #mainContent')) throw new Error('Unable to load case details.');
                            content.replaceChildren(...Array.from(page.body.childNodes));
                        } else if (isMonitoring) {
                            const monitoring = page.querySelector('#bcpc-monitoring-form');
                            if (!monitoring) throw new Error('Unable to load monitoring report details.');
                            monitoring.querySelectorAll('input[type="hidden"], button').forEach(control => control.remove());
                            monitoring.querySelectorAll('input, textarea, select').forEach(input => {
                                const value = document.createElement('span');
                                value.className = 'block whitespace-pre-wrap break-words text-gray-800';
                                value.textContent = input.tagName === 'SELECT' ? input.selectedOptions[0]?.textContent || '—' : input.value || '—';
                                input.replaceWith(value);
                            });
                            const details = document.createElement('div');
                            details.className = 'space-y-4';
                            details.append(...Array.from(monitoring.childNodes));
                            content.replaceChildren(details);
                        } else {
                            const report = page.querySelector('.w-full.max-w-7xl.mx-auto');
                            if (!report) throw new Error('Unable to load report details.');
                            report.querySelector(':scope > .mb-6.flex')?.remove();
                            report.querySelectorAll('form, button').forEach(control => control.remove());
                            report.className = 'w-full';
                            content.replaceChildren(report);
                        }
                    } catch (error) {
                        if (Swal.getHtmlContainer()?.querySelector('.swd-archive-detail') === content) content.textContent = error.message;
                    }
                };
                content.addEventListener('click', event => {
                    const link = event.target.closest('a');
                    if (!link) return;
                    event.preventDefault();
                    if (!isCase && !isReferral && new URL(link.href).searchParams.has('case_records_page')) loadDetails(link.href);
                });
                await loadDetails(url);
            }
        });
    };
    document.addEventListener('click', event => {
        const view = event.target.closest('a[title="View Case"], a[title="View Report"], a[title="View Record"], .archived-monitoring-card a, .archived-view-referral-btn');
        if (!view || !container.contains(view)) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        const isReferral = view.matches('.archived-view-referral-btn');
        const title = isReferral ? 'Referral Details' : view.title === 'View Case' ? 'Case Details' : view.title === 'View Record' || view.closest('.archived-monitoring-card') ? 'Monitoring Report' : 'Quarterly Report';
        if (useSwdDetailModal) {
            openSwdDetailModal(view, isReferral, title);
            return;
        }
        Swal.fire({
            title, width: isReferral ? 'min(48rem, calc(100vw - 2rem))' : 'min(68rem, 95vw)', showConfirmButton: false, showCloseButton: true,
            customClass: { popup: `archive-view-modal-popup desk-archive-view-popup${isReferral ? ' archived-referral-view-popup' : ''}` },
            didOpen: async () => {
                const content = Swal.getHtmlContainer();
                if (!isReferral) {
                    const frame = document.createElement('iframe');
                    frame.addEventListener('load', () => makeArchiveFrameReadOnly(frame));
                    frame.title = title;
                    frame.className = 'desk-archive-view-frame';
                    frame.src = view.href;
                    content.appendChild(frame);
                    return;
                }
                Swal.showLoading();
                try {
                    const response = await fetch(@json(url('/desk-officer/referrals')) + '/' + view.dataset.referralId + '/details?archived=1');
                    if (!response.ok) throw new Error('Unable to load referral details.');
                    const html = await response.text();
                    if (Swal.getHtmlContainer() === content) { Swal.hideLoading(); content.innerHTML = html; }
                } catch (error) {
                    if (Swal.getHtmlContainer() === content) { Swal.hideLoading(); content.textContent = error.message; }
                }
            }, html: '<div></div>'
        });
    }, true);

    document.addEventListener('submit', async event => {
        const form = event.target;
        if (form === filters) {
            event.preventDefault();
            event.stopImmediatePropagation();
            refresh();
            return;
        }
        if (!container.contains(form)) return;
        const restore = form.matches('.restore-case-form, .restore-confirm-form, .restore-archived-referral-form, .restore-monitoring-form');
        const remove = form.matches('.delete-archived-case-form, .delete-archived-report-form, .delete-archived-referral-form');
        if (!restore && !remove) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (form.querySelector('button[type="submit"]')?.disabled) return;
        const kind = form.matches('.restore-case-form, .delete-archived-case-form') ? 'case' : form.matches('.restore-confirm-form, .delete-archived-report-form, .restore-monitoring-form') ? 'report' : 'referral';
        const result = await Swal.fire({
            icon: restore ? 'question' : 'warning', title: `${restore ? 'Restore' : 'Delete'} archived ${kind}?`,
            text: restore ? `This ${kind} will be returned to the active list.` : `This ${kind} will be permanently deleted and cannot be restored.`,
            showCancelButton: true, confirmButtonText: `${restore ? 'Restore' : 'Delete'} ${kind}`, cancelButtonText: 'Cancel', confirmButtonColor: restore ? '#7c3aed' : '#C41E3A'
        });
        if (!result.isConfirmed) return;
        if (@json(auth()->user()->defaultCaseDepartment() === 'BCPC' || auth()->user()->isMultiCaseDepartment()) && form.matches('.restore-confirm-form, .restore-monitoring-form')) {
            Swal.fire({ title: 'Restoring report...', text: 'Please wait.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: async () => {
                    Swal.showLoading();
                    await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                    try {
                        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { Accept: 'application/json' } });
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message || 'Unable to restore the report.');
                        await Swal.fire({ icon: 'success', title: 'Report Restored', text: data.message, confirmButtonText: 'OK' });
                        location.reload();
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Unable to Restore', text: error.message });
                    }
                }
            });
            return;
        }
        Swal.fire({ title: restore ? `Restoring ${kind}...` : 'Processing...', text: 'Please wait.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); requestAnimationFrame(() => requestAnimationFrame(() => HTMLFormElement.prototype.submit.call(form))); }
        });
    }, true);
})();
</script>
