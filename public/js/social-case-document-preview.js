function openSocialCaseDocumentPreview(file, trigger) {
    if (document.querySelector('.or-document-dialog')) return;

    const dialog = document.createElement('dialog');
    dialog.className = 'or-document-dialog';
    dialog.setAttribute('aria-labelledby', 'orDocumentPreviewTitle');

    const header = document.createElement('div');
    header.className = 'or-document-header';
    const title = document.createElement('h2');
    title.id = 'orDocumentPreviewTitle';
    title.textContent = file.name || 'Document preview';
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'or-document-close';
    close.textContent = 'Close preview';
    close.addEventListener('click', () => dialog.close());
    header.append(title, close);

    const body = document.createElement('div');
    body.className = 'or-document-body';
    body.setAttribute('aria-live', 'polite');
    body.textContent = 'Loading document...';
    dialog.append(header, body);
    document.body.append(dialog);

    const controller = new AbortController();
    let blobUrl = null;
    // Keep Escape and Tab within the document dialog, above the request modal.
    dialog.addEventListener('keydown', event => event.stopPropagation());
    dialog.addEventListener('close', () => {
        controller.abort();
        body.replaceChildren();
        if (blobUrl) URL.revokeObjectURL(blobUrl);
        dialog.remove();
        if (trigger?.isConnected) trigger.focus();
    }, { once: true });
    dialog.showModal();

    (async () => {
        try {
            const url = new URL(file.url, window.location.href);
            if (url.origin !== window.location.origin) {
                throw new Error('The document URL does not match this site. Please contact the administrator.');
            }
            const response = await fetch(url, { credentials: 'same-origin', signal: controller.signal });
            if (!response.ok) {
                throw new Error(response.status === 404 ? 'The uploaded document could not be found.' : 'Unable to load the document. Please try again.');
            }
            const blob = await response.blob();
            if (controller.signal.aborted) return;
            let mime = blob.type.split(';')[0].toLowerCase();
            // Some hosts serve PDFs as generic binary files.
            if ((!mime || mime === 'application/octet-stream') && /\.pdf$/i.test(file.name || '')) {
                if ((await blob.slice(0, 5).text()) === '%PDF-') mime = 'application/pdf';
            }
            if (!['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(mime)) {
                throw new Error('Preview is available for PDF and image files. This document format cannot be displayed in the browser.');
            }
            if (controller.signal.aborted) return;
            blobUrl = URL.createObjectURL(new Blob([blob], { type: mime }));
            const viewer = document.createElement(mime === 'application/pdf' ? 'iframe' : 'img');
            viewer.src = blobUrl;
            if (mime === 'application/pdf') viewer.title = file.name || 'Document preview';
            else {
                viewer.alt = file.name || 'Uploaded document';
                viewer.addEventListener('error', () => {
                    body.textContent = 'Unable to display this image. The file may be damaged.';
                });
            }
            body.replaceChildren(viewer);
        } catch (error) {
            if (!controller.signal.aborted) body.textContent = error.message;
        }
    })();
}
