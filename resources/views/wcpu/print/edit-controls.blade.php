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
        preview.querySelectorAll('script, .print-edit-controls').forEach(element => element.remove());
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
