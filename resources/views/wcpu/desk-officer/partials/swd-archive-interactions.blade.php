@if(in_array(auth()->user()->defaultCaseDepartment(), ['VAWC', 'BCPC'], true) && (!auth()->user()->isMultiCaseDepartment() || request()->routeIs('desk-officer.quarterly-reports.archived')))
@if(request()->routeIs('desk-officer.quarterly-reports.archived'))
@include('components.archived-monitoring-preview-assets')
@endif
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
    const isMultiRoleArchivedReports = @json(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment() && request()->routeIs('desk-officer.quarterly-reports.archived'));
    const isMultiRoleArchivedCases = @json(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment() && request()->routeIs('desk-officer.archived-barangay-cases.index'));
    const isMultiRoleArchivedReferrals = @json(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment() && request()->routeIs('desk-officer.referrals.archived'));
    const archiveModalStyles = document.createElement('style');
    archiveModalStyles.textContent = `
        .swal2-popup.desk-archive-view-popup{box-sizing:border-box;max-height:calc(100dvh - 2rem);overflow:hidden;padding:0}
        .swal2-popup.desk-archive-view-popup .swal2-title{padding:1rem 3.5rem .625rem}
        .swal2-popup.desk-archive-view-popup .swal2-html-container{box-sizing:border-box;width:100%;max-height:calc(100dvh - 5.5rem);margin:0;padding:0 .875rem .875rem;overflow:auto;overscroll-behavior:contain;text-align:left}
        .swal2-popup.desk-archive-view-popup .desk-archive-view-frame{display:block;width:100%;height:min(70dvh,52rem);border:0}
        @media(max-width:47.9375rem){.swal2-popup.desk-archive-view-popup{width:calc(100vw - 1rem)!important;max-height:calc(100dvh - 1rem)}.swal2-popup.desk-archive-view-popup .swal2-title{padding:1rem 3rem .5rem}.swal2-popup.desk-archive-view-popup .swal2-html-container{max-height:calc(100dvh - 5rem);padding:0 .75rem .75rem}.swal2-popup.desk-archive-view-popup .desk-archive-view-frame{height:calc(100dvh - 8rem);min-height:16rem}}
        .swal2-popup.multi-role-archived-report-modal{display:flex!important;flex-direction:column;height:min(88vh,60rem);height:min(88dvh,60rem);min-width:0}
        .swal2-popup.multi-role-archived-report-modal .swal2-close{position:absolute!important;top:0!important;right:0!important;left:auto!important;z-index:1}
        .swal2-popup.multi-role-archived-report-modal .swal2-title{flex:0 0 auto;margin:0;overflow-wrap:anywhere}
        .swal2-popup.multi-role-archived-report-modal .swal2-html-container{flex:1 1 auto;min-height:0;max-height:none;overflow:hidden}
        .swal2-popup.multi-role-archived-report-modal .desk-archive-view-frame{height:100%;min-height:0;max-width:100%}
        @media(max-width:47.9375rem){.swal2-popup.multi-role-archived-report-modal{height:calc(100vh - 1rem);height:calc(100dvh - 1rem)}}
    `;
    document.head.appendChild(archiveModalStyles);

    const makeArchiveFrameReadOnly = frame => {
        const doc = frame.contentDocument;
        if (!doc) return;

        const frameUrl = new URL(frame.src, window.location.href);
        window.applyArchiveReportModalDesign(doc, Swal.getTitle());
        const caseTitle = doc.querySelector('#mainContent > header h2');
        if (doc.querySelector('.case-profile-details')) {
            doc.body.classList.add('archive-case-modal-preview');
            window.applyArchiveCaseModalDesign(doc, Swal.getTitle());
        }
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
        const responsiveMonitoring = isMonitoring && @json(request()->routeIs('desk-officer.quarterly-reports.archived'));
        const url = isReferral
            ? @json(url('/desk-officer/referrals')) + '/' + view.dataset.referralId + '/details?archived=1'
            : view.href;
        Swal.fire({ title, width: isReferral ? 'min(760px, 95vw)' : 'min(1100px, 95vw)',
            customClass: { popup: `archive-view-modal-popup desk-archive-view-popup swd-archive-detail-popup${isReferral ? ' archived-referral-view-popup' : ''}${responsiveMonitoring ? ' archived-monitoring-view-popup' : ''}` },
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
                        if (isReferral && page.querySelector('input:not([type="hidden"]), textarea, select')) {
                            const grouped = !!page.querySelector('.referral-detail-section');
                            const fields = document.createElement(grouped ? 'div' : 'dl');
                            fields.className = grouped ? 'archived-referral-details space-y-3' : 'swd-referral-fields';
                            const sections = new Map();
                            page.querySelectorAll('input:not([type="hidden"]), textarea, select').forEach(input => {
                                let list = fields;
                                const sourceSection = input.closest('.referral-detail-section');
                                if (grouped && sourceSection) {
                                    if (!sections.has(sourceSection)) {
                                        const section = document.createElement('section');
                                        const heading = document.createElement('h3');
                                        heading.textContent = sourceSection.querySelector('h3')?.textContent || '';
                                        const grid = document.createElement('dl');
                                        grid.className = 'grid grid-cols-1 sm:grid-cols-2';
                                        section.append(heading, grid);
                                        fields.appendChild(section);
                                        sections.set(sourceSection, grid);
                                    }
                                    list = sections.get(sourceSection);
                                }
                                const field = document.createElement('div');
                                field.className = 'swd-referral-field';
                                if (input.closest('.referral-detail-wide')) field.classList.add('sm:col-span-2');
                                const label = document.createElement('dt');
                                label.textContent = input.closest('.referral-detail-field, .mb-3, .mb-4')?.querySelector('label')?.textContent.trim() || input.name.replaceAll('_', ' ');
                                const value = document.createElement('dd');
                                value.textContent = input.tagName === 'SELECT' ? input.selectedOptions[0]?.textContent || '—' : input.value || '—';
                                field.append(label, value);
                                list.appendChild(field);
                            });
                            if (!fields.children.length) throw new Error('Unable to load referral details.');
                            content.replaceChildren(fields);
                        } else if (isReferral) {
                            content.innerHTML = html;
                        } else if (isCase) {
                            if (page.querySelector('#sidebar, #mainContent')) throw new Error('Unable to load case details.');
                            content.replaceChildren(...Array.from(page.body.childNodes));
                            window.applyArchiveCaseModalDesign(content, Swal.getTitle());
                        } else if (isMonitoring) {
                            const monitoring = page.querySelector('#bcpc-monitoring-form');
                            if (!monitoring) throw new Error('Unable to load monitoring report details.');
                            if (responsiveMonitoring) {
                                content.replaceChildren(window.prepareArchivedMonitoringPreview(monitoring));
                                return;
                            }
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
                            window.applyArchiveReportModalDesign(page, Swal.getTitle());
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
        if (isReferral && isMultiRoleArchivedReferrals) {
            const details = document.getElementById('archived-referral-details-' + view.dataset.referralId);
            Swal.fire({
                title: 'Referral Details',
                width: 'min(48rem, calc(100vw - 2rem))',
                html: details ? details.innerHTML : 'Unable to load referral details. Please refresh the page and try again.',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: { popup: 'archive-view-modal-popup desk-archive-view-popup archived-referral-view-popup' },
            });
            return;
        }
        const title = isReferral ? 'Referral Details' : view.title === 'View Case' ? 'Case Details' : view.title === 'View Record' || view.closest('.archived-monitoring-card') ? 'Monitoring Report' : 'Quarterly Report';
        const isReportPreview = isMultiRoleArchivedReports && ['Quarterly Report', 'Monitoring Report'].includes(title);
        const isCasePreview = isMultiRoleArchivedCases && title === 'Case Details';
        // Monitoring reports share the BCPC desk officer's read-only layout across roles.
        if (useSwdDetailModal || (isMultiRoleArchivedReports && title === 'Monitoring Report')) {
            openSwdDetailModal(view, isReferral, title);
            return;
        }
        let reportPreviewClosed = false;
        let reportPreviewFrame = null;
        Swal.fire({
            title, width: isReferral ? 'min(48rem, calc(100vw - 2rem))' : 'min(68rem, 95vw)',
            showConfirmButton: isMultiRoleArchivedCases && isCasePreview,
            showCloseButton: !(isMultiRoleArchivedCases && isCasePreview),
            confirmButtonText: 'Close',
            customClass: { popup: `archive-view-modal-popup desk-archive-view-popup${isReferral ? ' archived-referral-view-popup' : ''}${isReportPreview ? ' multi-role-archived-report-modal' : ''}${isMultiRoleArchivedCases && isCasePreview ? ' multi-role-archived-case-modal' : ''}` },
            willClose: () => {
                if (!isReportPreview) return;
                reportPreviewClosed = true;
                reportPreviewFrame?.remove();
            },
            didOpen: async () => {
                const content = Swal.getHtmlContainer();
                if (!isReferral) {
                    const frame = document.createElement('iframe');
                    if (isCasePreview || isReportPreview) {
                        if (isReportPreview) reportPreviewFrame = frame;
                        // Reserve the preview's space, but do not paint the full page
                        // while its styles, fonts and read-only cleanup are pending.
                        frame.style.visibility = 'hidden';
                        content.setAttribute('aria-busy', 'true');
                    }
                    frame.addEventListener('load', async () => {
                        if (reportPreviewClosed || Swal.getHtmlContainer() !== content) return;
                        try {
                            makeArchiveFrameReadOnly(frame);
                            if (!isCasePreview && !isReportPreview) return;
                            if (!frame.contentDocument?.querySelector('#mainContent')) throw new Error('Unable to load case details.');
                            if (isReportPreview) {
                                frame.contentDocument.documentElement.style.scrollbarGutter = 'stable';
                                frame.contentDocument.documentElement.style.overscrollBehavior = 'contain';
                            }
                            if (frame.contentDocument.fonts) await frame.contentDocument.fonts.ready;
                            await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                            if (!reportPreviewClosed && Swal.getHtmlContainer() === content) {
                                if (isReportPreview) {
                                    frame.contentWindow.scrollTo(0, 0);
                                    content.scrollTop = 0;
                                }
                                frame.style.visibility = 'visible';
                                content.removeAttribute('aria-busy');
                            }
                        } catch (error) {
                            if ((isCasePreview || isReportPreview) && !reportPreviewClosed && Swal.getHtmlContainer() === content) {
                                content.removeAttribute('aria-busy');
                                content.textContent = isReportPreview
                                    ? 'Unable to load report details. Please close the popup and try again.'
                                    : 'Unable to load case details. Please close the popup and try again.';
                            }
                        }
                    });
                    frame.title = title;
                    frame.className = 'desk-archive-view-frame';
                    const previewUrl = new URL(view.href, window.location.href);
                    if (isReportPreview) previewUrl.searchParams.set('archive_report_preview', '1');
                    if (isCasePreview) previewUrl.searchParams.set('archive_case_preview', '1');
                    frame.src = previewUrl.href;
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
            try {
                if (form.matches('.restore-confirm-form, .restore-monitoring-form')) {
                    const isMonitoring = form.matches('.restore-monitoring-form');
                    Swal.fire({
                        title: isMonitoring ? 'Restoring Monitoring Report...' : 'Restoring Quarterly Report...',
                        text: isMonitoring ? 'Please wait while the monitoring report is restored.' : 'Please wait while the quarterly report is restored.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading(),
                    });
                }
                const response = await fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { Accept: 'application/json' } });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Unable to restore the report.');
                await Swal.fire({
                    icon: 'success',
                    title: form.matches('.restore-confirm-form') ? 'Success' : 'Report Restored',
                    text: data.message,
                    confirmButtonText: 'OK',
                    ...(form.matches('.restore-confirm-form') ? { confirmButtonColor: '#C41E3A' } : {}),
                });
                location.reload();
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Unable to Restore', text: error.message });
            }
            return;
        }
        if (restore) {
            if (form.matches('.restore-archived-referral-form, .restore-confirm-form, .restore-case-form, .restore-monitoring-form')) {
                const isQuarterlyReport = form.matches('.restore-confirm-form');
                const isCase = form.matches('.restore-case-form');
                const isMonitoring = form.matches('.restore-monitoring-form');
                form.setAttribute('data-skip-loading-modal', '');
                Swal.fire({
                    title: isMonitoring ? 'Restoring Monitoring Report...' : (isCase ? 'Restoring Case...' : (isQuarterlyReport ? 'Restoring Quarterly Report...' : 'Restoring Referral...')),
                    text: isMonitoring ? 'Please wait while the monitoring report is restored.' : (isCase ? 'Please wait while the case is restored.' : (isQuarterlyReport ? 'Please wait while the quarterly report is restored.' : 'Please wait while the referral is restored.')),
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        requestAnimationFrame(() => requestAnimationFrame(() => {
                            window.portalUnsavedChanges?.allowSubmission?.(form);
                            HTMLFormElement.prototype.submit.call(form);
                        }));
                    },
                });
                return;
            }
            HTMLFormElement.prototype.submit.call(form);
            return;
        }
        Swal.fire({ title: 'Processing...', text: 'Please wait.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); requestAnimationFrame(() => requestAnimationFrame(() => HTMLFormElement.prototype.submit.call(form))); }
        });
    }, true);
})();
</script>
