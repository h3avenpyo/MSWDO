let financialPreviewOpen = false;

function openDocumentPreview(file, trigger) {
    if (financialPreviewOpen) return;
    financialPreviewOpen = true;
    const controller = new AbortController();
    let previewUrl = null;
    const reviewElement = document.getElementById('reviewModal');
    const previewElement = document.getElementById('documentPreviewModal');
    const body = document.getElementById('documentPreviewBody');
    const reviewModal = bootstrap.Modal.getOrCreateInstance(reviewElement);
    const previewModal = bootstrap.Modal.getOrCreateInstance(previewElement);
    document.getElementById('documentPreviewTitle').textContent = file.name;
    body.textContent = 'Kinukuha ang dokumento...';

    // Switch dialogs so focus, Escape and the backdrop belong to one modal.
    reviewElement.addEventListener('hidden.bs.modal', () => previewModal.show(), { once: true });
    previewElement.addEventListener('hidden.bs.modal', () => {
        controller.abort();
        body.replaceChildren();
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
        financialPreviewOpen = false;
        reviewElement.addEventListener('shown.bs.modal', () => trigger.focus(), { once: true });
        reviewModal.show();
    }, { once: true });
    reviewModal.hide();

    fetch(file.preview_url, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
        signal: controller.signal,
    })
        .then(async response => {
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error('Nag-expire ang session o wala kang pahintulot. Mag-login muli.');
                }
                if (response.status === 404) throw new Error('Hindi makita ang dokumento.');
                throw new Error('Hindi maipakita ang dokumento. Pakisubukang muli.');
            }
            const blob = await response.blob();
            if (controller.signal.aborted) return;
            if (!['application/pdf', 'image/jpeg', 'image/png'].includes(blob.type)) {
                throw new Error('Hindi suportado ang preview ng dokumentong ito.');
            }
            previewUrl = URL.createObjectURL(blob);
            const viewer = document.createElement(blob.type === 'application/pdf' ? 'iframe' : 'img');
            viewer.src = previewUrl;
            if (blob.type === 'application/pdf') {
                viewer.title = file.name;
                viewer.style.cssText = 'width:100%;height:100%;border:0;display:block;';
            } else {
                viewer.alt = file.name;
                viewer.style.cssText = 'max-width:100%;height:auto;display:block;margin:0 auto;';
                viewer.addEventListener('error', () => {
                    body.textContent = 'Hindi mabasa ang larawan. Maaaring sira ang file.';
                });
            }
            body.replaceChildren(viewer);
        })
        .catch(error => {
            if (!controller.signal.aborted) body.textContent = error.message;
        });
}
