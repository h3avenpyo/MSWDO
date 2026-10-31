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

    const originals = new Map();
    let active = null;
    const browserPrint = window.print.bind(window);
    const genericPdfPrintUrl = @json(route('print.edited-pdf'));
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
        const printWindow = window.open('', '_blank');
        const preview = document.documentElement.cloneNode(true);
        preview.querySelectorAll('script, .print-edit-controls, .report-back-button').forEach(element => element.remove());
        preview.querySelectorAll('[contenteditable], [data-print-editing], [spellcheck]').forEach(element => {
            element.removeAttribute('contenteditable');
            element.removeAttribute('data-print-editing');
            element.removeAttribute('spellcheck');
        });

        fetch(document.body.dataset.pdfPrintUrl || genericPdfPrintUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/pdf',
                        'X-CSRF-TOKEN': document.body.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token())
            },
            body: JSON.stringify({ html: `<!doctype html>${preview.outerHTML}` })
        })
            .then(response => {
                if (!response.ok) throw new Error('Unable to prepare the printable PDF.');
                return response.blob();
            })
            .then(pdf => {
                const pdfUrl = URL.createObjectURL(pdf);
                if (printWindow) printWindow.location.replace(pdfUrl);
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
