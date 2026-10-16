<dialog id="bcpc-report-result" aria-labelledby="bcpc-report-result-title" style="margin:auto;border:0;border-radius:12px;padding:2rem;width:min(32rem,calc(100vw - 2rem));text-align:center;background:white;color:#374151;">
    <h2 id="bcpc-report-result-title" style="font-size:1.5rem;font-weight:600;margin-bottom:1rem;"></h2>
    <p id="bcpc-report-result-message" style="margin-bottom:1.5rem;"></p>
    <button type="button" id="bcpc-report-result-ok" style="padding:.5rem 1.5rem;border:0;border-radius:.5rem;background:#C41E3A;color:white;">OK</button>
</dialog>
<style>#bcpc-report-result::backdrop { background:rgba(0,0,0,.45); }</style>
<script>
(() => {
    const form = document.getElementById(@json($formId));
    const operation = @json($operation);
    const dialog = document.getElementById('bcpc-report-result');
    const button = form.querySelector('button[type="submit"]');
    let pending = false;
    let redirectUrl = null;
    form.noValidate = true;
    form.setAttribute('data-skip-loading-modal', '');
    const showResult = (success, message) => {
        if (window.Swal) {
            Swal.fire({
                icon: success ? 'success' : 'error',
                title: success ? 'Report Generated' : 'Report Not Generated',
                text: message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#C41E3A',
            }).then(() => {
                if (success && redirectUrl) window.location.assign(redirectUrl);
            });
            return;
        }
        document.getElementById('bcpc-report-result-title').textContent = success ? 'Success!' : `Can't ${operation} report`;
        document.getElementById('bcpc-report-result-message').textContent = message;
        if (!dialog.open) dialog.showModal();
    };
    dialog.addEventListener('cancel', event => event.preventDefault());
    document.getElementById('bcpc-report-result-ok').addEventListener('click', () => {
        dialog.close();
        if (redirectUrl) window.location.assign(redirectUrl);
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (pending) return;
        redirectUrl = null;
        if (!form.checkValidity()) {
            showResult(false, 'Please select an available quarter and year and complete the required fields.');
            return;
        }
        pending = true;
        button.disabled = true;
        if (window.Swal) {
            Swal.fire({
                title: 'Generating Report...',
                text: 'Please wait while the report is prepared.',
                allowOutsideClick: false, showConfirmButton: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });
        }
        try {
            const response = await fetch(form.action, {
                method: 'POST', body: new FormData(form), credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json().catch(() => ({ success: false, message: `Unable to ${operation} the report. Please refresh the page and try again.` }));
            if (!response.ok || !data.success) throw new Error(data.message || `Unable to ${operation} the report.`);
            redirectUrl = data.redirect_url;
            if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
            showResult(true, data.message);
        } catch (error) {
            showResult(false, error.message || `Unable to ${operation} the report.`);
        } finally {
            if (window.Swal && Swal.isLoading()) Swal.close();
            pending = false;
            button.disabled = false;
        }
    });
})();
</script>
