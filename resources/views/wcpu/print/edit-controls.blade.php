@php
        $quarterlyDownloadFilename = null;
        $quarterlyAllDownloadFilename = $quarterlyAllDownloadFilename ?? null;
        $barangayDownloadFilename = null;
        $caseDownloadFilename = null;
        $referralRegistryDownloadFilename = null;
        $referralDownloadFilename = null;
        if (request()->routeIs('desk-officer.quarterly-reports.download')
            && auth()->user()?->position === 'Desk Officer'
            && (in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || auth()->user()?->isMultiCaseDepartment())) {
            $quarterlyDownloadFilename = (!empty($reportData['is_bcpc_report']) ? 'BCPC' : 'SWD')
                . '_Quarterly_Report_' . \Illuminate\Support\Str::slug($barangay->name, '_')
                . '_Q' . $quarter . '_' . $year . '.pdf';
        }
        if (request()->routeIs('admin.barangay-cases.print-individual', 'desk-officer.cases.print-all')
            && auth()->user()?->position === 'Desk Officer'
            && (in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || auth()->user()?->isMultiCaseDepartment())) {
            $printDepartments = collect($cases ?? [])->pluck('type')->filter()->unique();
            $printDepartmentLabel = $printDepartments->count() > 1 ? 'BCPC_SWD'
                : str_replace('VAWC', 'SWD', $printDepartments->first() ?? $department ?? auth()->user()->defaultCaseDepartment());
            $barangayDownloadFilename = $printDepartmentLabel . '_Barangay_Cases_'
                . \Illuminate\Support\Str::slug($barangay->name, '_') . '.pdf';
        }
        if (request()->routeIs('desk-officer.cases.print', 'admin.case.print')
            && auth()->user()?->position === 'Desk Officer'
            && (in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || auth()->user()?->isMultiCaseDepartment())) {
            $caseDownloadFilename = str_replace('VAWC', 'SWD', strtoupper((string) $case->type))
                . '_Case_Report_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', trim((string) $case->case_number)) . '.pdf';
        }
        if (request()->routeIs('desk-officer.referrals.print-all')
            && auth()->user()?->position === 'Desk Officer'
            && (in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || auth()->user()?->isMultiCaseDepartment())) {
            $registryDepartments = collect($referrals ?? [])->pluck('case.type')->filter()->unique();
            $registryDepartment = $registryDepartments->count() > 1 ? 'BCPC_SWD'
                : str_replace('VAWC', 'SWD', $department ?? $registryDepartments->first() ?? auth()->user()->defaultCaseDepartment());
            $registryBarangay = \App\Models\Wcpu\Barangay::where('name', auth()->user()->barangay)
                ->orWhere('id', auth()->user()->barangay)->value('name');
            $referralRegistryDownloadFilename = $registryDepartment . '_Referral_Registry_'
                . \Illuminate\Support\Str::slug((string) $registryBarangay, '_') . '.pdf';
        }
        if (request()->routeIs('desk-officer.referrals.print')
            && auth()->user()?->position === 'Desk Officer'
            && (in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || auth()->user()?->isMultiCaseDepartment())) {
            $referralDownloadFilename = str_replace('VAWC', 'SWD', strtoupper((string) ($referral->case?->type ?? auth()->user()->defaultCaseDepartment())))
                . '_Referral_' . \Illuminate\Support\Str::slug((string) ($referral->referral_number ?: $referral->id), '_') . '.pdf';
        }
    @endphp
<script>
(() => {
    const style = document.createElement('style');
    style.textContent = `
        .print-edit-controls { position:fixed; top:12px; right:12px; z-index:2147483000; display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#1f2937; box-shadow:0 2px 8px rgba(0,0,0,.15); font:13px/1.3 Arial,sans-serif; }
        .print-edit-controls button { border:0; border-radius:5px; padding:7px 10px; background:#374151; color:#fff; cursor:pointer; font:inherit; }
        .print-edit-controls button[data-print-action="print"] { background:#166534; }
        [data-print-editing="true"] { cursor:text; overflow-wrap:anywhere !important; word-break:normal; white-space:pre-wrap; }
        [data-print-editing="true"]:focus { outline:1px dashed #64748b; outline-offset:2px; }
        @media print { .print-edit-controls { display:none !important; } [data-print-editing="true"] { outline:none !important; overflow:visible !important; } }
    `;
    document.head.appendChild(style);

    const controls = document.createElement('div');
    controls.className = 'print-edit-controls';
    controls.dataset.printEditControls = '';
    controls.setAttribute('role', 'group');
    controls.setAttribute('aria-label', 'Print preview controls');
    controls.innerHTML = '<span>Click form text to edit. Changes are temporary.</span><button type="button" data-print-action="reset">Reset Changes</button><button type="button" data-print-action="print">Print</button>';
    document.body.appendChild(controls);
    @if((auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin.quarterly-reports.download', 'admin.quarterly-reports.print-all', 'admin.bcpc-case-monitoring.print', 'admin.referrals.print-all', 'admin.referrals.print', 'admin.barangay-cases.print-individual', 'admin.case.print')) || (auth()->user()?->position === 'Desk Officer' && request()->routeIs('admin.barangay-cases.print-individual', 'desk-officer.referrals.print', 'desk-officer.referrals.print-all', 'desk-officer.quarterly-reports.download', 'desk-officer.quarterly-reports.print-all', 'desk-officer.bcpc-case-monitoring.print', 'desk-officer.cases.print', 'admin.case.print') && (auth()->user()->canAccessCaseDepartment('BCPC') || auth()->user()->canAccessCaseDepartment('VAWC'))) || (auth()->user()?->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment() && request()->routeIs('desk-officer.cases.print-all')))
    const backButton = document.createElement('a');
    backButton.textContent = 'Back';
    backButton.href = @json(request()->routeIs('admin.referrals.print-all', 'admin.referrals.print') ? route('admin.referrals') : (request()->routeIs('admin.bcpc-case-monitoring.print') ? route('admin.bcpc-case-monitoring.edit') : route('admin.quarterly-reports.index')));
    @if(request()->routeIs('admin.barangay-cases.print-individual'))
    backButton.href = @json(request('back') === 'barangay-list' ? route('admin-barangay-cases') : route('admin.barangay-cases.show', ['barangay' => request()->route('barangay')]));
    @endif
    @if(auth()->user()?->position === 'Desk Officer' && request()->routeIs('desk-officer.*'))
    backButton.href = @json(request()->routeIs('desk-officer.referrals.*') ? route('desk-officer.referrals') : (request()->routeIs('desk-officer.bcpc-case-monitoring.print') ? route('desk-officer.bcpc-case-monitoring.edit') : route('desk-officer.quarterly-reports.index')));
    @endif
    @if(auth()->user()?->position === 'Desk Officer' && request()->routeIs('desk-officer.cases.print', 'admin.case.print'))
    backButton.href = @json(request()->filled('back_barangay') && ctype_digit((string) request('back_barangay')) ? route('admin.barangay-cases.show', ['barangay' => request('back_barangay')]) : route('desk-officer.cases.index'));
    @endif
    @if(auth()->user()?->position === 'Desk Officer' && request()->routeIs('desk-officer.cases.print', 'admin.case.print') && request()->boolean('back_case'))
    backButton.href = @json(route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from' => request('from') === 'case-profiles' ? 'case-profiles' : null]));
    @endif
    @if(auth()->user()?->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment() && request()->routeIs('desk-officer.cases.print-all'))
    @php($casePrintBackUrl = route('desk-officer.cases.index', request()->only(['type', 'status', 'date_from', 'date_to'])))
    backButton.href = @json($casePrintBackUrl);
    @endif
    @if(auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin.case.print'))
    @php($adminCasePrintBackUrl = route('admin.barangay-cases.show', ['barangay' => request()->filled('back_barangay') && ctype_digit((string) request('back_barangay')) ? request('back_barangay') : $case->case_designation]))
    backButton.href = @json($adminCasePrintBackUrl);
    @endif
    backButton.style.cssText = 'padding:7px 10px;border-radius:5px;background:#C41E3A;color:#fff;text-decoration:none;';
    controls.prepend(backButton);
    @endif

    const isCasePhoneModal = Boolean(@json(request()->boolean('print_modal')))
        && Boolean(@json($caseDownloadFilename))
        && window.matchMedia('(max-width: 47.9375rem)').matches;
    if (isCasePhoneModal) {
        const previewStyle = document.createElement('style');
        previewStyle.dataset.previewOnly = '';
        const fitReport = () => {
            const scale = Math.min(1, (window.innerWidth - 24) / 718);
            previewStyle.textContent = `
                @media screen {
                    body { padding-top:112px !important; background:#fff; }
                    .print-edit-controls { top:8px; left:8px; right:8px; display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:6px; padding:8px; }
                    .print-edit-controls > span { grid-column:1 / -1; grid-row:1; font-size:12px; }
                    .print-edit-controls > :is(a,button) { grid-row:2; min-width:0; width:auto; box-sizing:border-box; white-space:nowrap; text-align:center; padding:12px 4px !important; font-size:12px; }
                    .container { width:718px !important; max-width:none !important; box-sizing:border-box; zoom:${scale}; }
                    footer.footer { display:none; }
                }
            `;
        };
        document.head.appendChild(previewStyle);
        fitReport();
        window.addEventListener('resize', fitReport);
        const modalBack = controls.querySelector('a');
        if (modalBack) modalBack.addEventListener('click', (event) => {
            const dialog = window.frameElement?.closest('dialog');
            if (dialog) { event.preventDefault(); dialog.close(); }
        });
    }

    const originals = new Map();
    let active = null;
    const browserPrint = window.print.bind(window);
    const genericPdfPrintUrl = @json(route('print.edited-pdf'));

    const quarterlyDownloadFilename = @json($quarterlyDownloadFilename);
    const quarterlyAllDownloadFilename = @json($quarterlyAllDownloadFilename);
    const barangayDownloadFilename = @json($barangayDownloadFilename);
    const caseDownloadFilename = @json($caseDownloadFilename);
    const referralRegistryDownloadFilename = @json($referralRegistryDownloadFilename);
    const referralDownloadFilename = @json($referralDownloadFilename);
    const isProtectedTitle = (element) => Boolean(element?.closest(
        'h1, h2, h3, h4, h5, h6, caption, thead, th, label, [data-print-locked], [class*="title"], [class*="header"], [class*="label"]'
    ));
    const isEditableData = (element) => Boolean(element?.closest(
        'td, dd, [data-print-editable], [class~="value"]'
    )) && !isProtectedTitle(element);

    const setActive = (element) => {
        if (!isEditableData(element)) return;
        if (active && active !== element) active.removeAttribute('contenteditable');
        active = element;
        if (!originals.has(element)) originals.set(element, element.innerHTML);
        element.setAttribute('contenteditable', 'true');
        element.setAttribute('data-print-editing', 'true');
        element.setAttribute('spellcheck', 'false');
    };

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-print-edit-controls]')) return;
        if (event.target.closest('a, button, input, textarea, select, script, style, nav')) return;
        if (!isEditableData(event.target)) return;

        let element = null;
        let caretRange = null;
        if (document.caretPositionFromPoint) {
            const position = document.caretPositionFromPoint(event.clientX, event.clientY);
            if (position?.offsetNode) {
                element = position.offsetNode.nodeType === Node.TEXT_NODE ? position.offsetNode.parentElement : position.offsetNode;
                caretRange = document.createRange();
                caretRange.setStart(position.offsetNode, position.offset);
                caretRange.collapse(true);
            }
        } else if (document.caretRangeFromPoint) {
            caretRange = document.caretRangeFromPoint(event.clientX, event.clientY);
            if (caretRange?.startContainer) element = caretRange.startContainer.nodeType === Node.TEXT_NODE
                ? caretRange.startContainer.parentElement
                : caretRange.startContainer;
        }

        if (!element || element === document.body || element.tagName === 'HTML' || element.closest('[data-print-edit-controls]') || !isEditableData(element)) return;
        if (!element.textContent.trim() && element.children.length > 0) return;
        if (!element.textContent.trim() && !['TD', 'DD', 'SPAN'].includes(element.tagName)) return;
        event.preventDefault();
        setActive(element);
        element.focus();
        if (caretRange) {
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(caretRange);
        }
    });

    document.addEventListener('paste', (event) => {
        if (!active || !active.isContentEditable) return;
        event.preventDefault();
        document.execCommand('insertText', false, event.clipboardData?.getData('text/plain') ?? '');
    });

    const openCleanPdf = () => {
        if (active) active.removeAttribute('contenteditable');
        const phoneWidth = Math.min(window.innerWidth || Infinity, window.screen?.width || Infinity);
        const isPhoneView = window.matchMedia('(max-width: 47.9375rem)').matches
            || (window.frameElement && window.frameElement.getBoundingClientRect().width < 768)
            || (/Android|iPhone|iPod/i.test(navigator.userAgent) && phoneWidth <= 480);
        const isSwdDesktopPrint = document.body.hasAttribute('data-swd-quarterly-print') && !isPhoneView;
        const downloadFilename = (isSwdDesktopPrint ? null : quarterlyDownloadFilename) || (isPhoneView ? (quarterlyAllDownloadFilename || referralDownloadFilename || referralRegistryDownloadFilename || caseDownloadFilename || barangayDownloadFilename) : null);
        const printWindow = downloadFilename ? null : window.open('', '_blank');
        const preview = document.documentElement.cloneNode(true);
        preview.querySelectorAll('script, .print-edit-controls, .report-back-button, [data-preview-only]').forEach(element => element.remove());
        preview.querySelectorAll('[contenteditable], [data-print-editing], [spellcheck]').forEach(element => {
            element.removeAttribute('contenteditable');
            element.removeAttribute('data-print-editing');
            element.removeAttribute('spellcheck');
        });

        if (isPhoneView && (quarterlyAllDownloadFilename || referralRegistryDownloadFilename)) {
            const downloadForm = document.createElement('form');
            downloadForm.method = 'POST';
            downloadForm.action = document.body.dataset.pdfPrintUrl || genericPdfPrintUrl;
            downloadForm.target = '_top';
            downloadForm.hidden = true;
            const fields = {
                html: `<!doctype html>${preview.outerHTML}`,
                filename: quarterlyAllDownloadFilename || referralRegistryDownloadFilename,
                _token: document.body.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token())
            };
            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                downloadForm.appendChild(input);
            });
            document.body.appendChild(downloadForm);
            downloadForm.submit();
            window.setTimeout(() => downloadForm.remove(), 60000);
            return;
        }

        fetch(document.body.dataset.pdfPrintUrl || genericPdfPrintUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/pdf',
                        'X-CSRF-TOKEN': document.body.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token())
            },
            body: JSON.stringify({ html: `<!doctype html>${preview.outerHTML}`, ...(isPhoneView && referralRegistryDownloadFilename ? { filename: referralRegistryDownloadFilename } : {}) })
        })
            .then(response => {
                if (!response.ok) throw new Error('Unable to prepare the printable PDF.');
                return response.blob();
            })
            .then(pdf => {
                const pdfUrl = URL.createObjectURL(pdf);
                if (downloadFilename) {
                    const download = document.createElement('a');
                    download.href = pdfUrl;
                    download.download = downloadFilename;
                    document.body.appendChild(download);
                    download.click();
                    download.remove();
                    window.setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
                    return;
                }
                if (printWindow) printWindow.location.replace(pdfUrl);
                else if (isSwdDesktopPrint) window.location.assign(pdfUrl);
                else window.open(pdfUrl, '_blank', 'noopener');
            })
            .catch(() => {
                if (printWindow) printWindow.close();
                browserPrint();
            });
    };

    window.print = openCleanPdf;

    controls.addEventListener('click', (event) => {
        const action = event.target.closest('button')?.dataset.printAction;
        if (action === 'print') {
            openCleanPdf();
        } else if (action === 'reset') {
            originals.forEach((html, element) => {
                element.innerHTML = html;
                element.removeAttribute('contenteditable');
                element.removeAttribute('data-print-editing');
                element.removeAttribute('spellcheck');
            });
            originals.clear();
            active = null;
        }
    });
})();
</script>
