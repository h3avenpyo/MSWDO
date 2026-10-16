(() => {
    let pending = 0;
    const overlay = document.createElement('div');
    overlay.setAttribute('role', 'status');
    overlay.setAttribute('aria-live', 'polite');
    overlay.style.cssText = 'display:none;position:fixed;inset:0;z-index:20000;background:rgba(0,0,0,.45);align-items:center;justify-content:center';
    overlay.innerHTML = '<div style="background:white;color:#374151;padding:2rem;border-radius:1rem;text-align:center"><i class="fas fa-spinner fa-spin" aria-hidden="true" style="font-size:2rem;color:#C41E3A"></i><p style="margin-top:1rem">Processing, please wait…</p></div>';
    document.body.appendChild(overlay);
    const show = () => { overlay.style.display = 'flex'; };
    const hide = () => { if (!pending) overlay.style.display = 'none'; };
    const originalFetch = window.fetch;
    window.fetch = async function(input, options) {
        const url = new URL(input instanceof Request ? input.url : input, location.href);
        const hasSkipHeader = options && options.headers && (
            (options.headers instanceof Headers && options.headers.get('X-Skip-Loading')) ||
            options.headers['X-Skip-Loading']
        );
        const isSwalBusy = window.Swal && (Swal.isVisible() || Swal.isLoading());
        const isExcluded = /notifications|mark-seen|unread|\/session\/|refer|regenerate|quarterly-reports|bcpc-case-monitoring|case-profiles/i.test(url.pathname);
        const visible = url.origin === location.origin && !isExcluded && !hasSkipHeader && !isSwalBusy;
        if (!visible) return originalFetch.call(this, input, options);
        pending++; show();
        try {
            const response = await originalFetch.call(this, input, options);
            await response.clone().arrayBuffer();
            return response;
        } finally { pending--; hide(); }
    };
    document.addEventListener('submit', event => {
        queueMicrotask(() => {
            const form = event.target;
            const action = (form && form.action) ? form.action : '';
            const isExcludedForm = /refer|regenerate|quarterly-reports|bcpc-case-monitoring|case-profiles/i.test(action);
            if (!event.defaultPrevented && event.target.target !== '_blank' && event.target.method !== 'dialog' && !isExcludedForm && !isSwalBusy) show();
        });
    });
    const submit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function() {
        const action = this.action || '';
        const isExcluded = /refer|regenerate|quarterly-reports|bcpc-case-monitoring|case-profiles/i.test(action);
        if (this.target !== '_blank' && !isExcluded) show();
        return submit.call(this);
    };
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || link.target === '_blank' || link.hasAttribute('download') ||
            /print|download|export/i.test(url.pathname) || url.hash || !/^https?:$/.test(url.protocol)) return;
        queueMicrotask(() => { if (!event.defaultPrevented) show(); });
    });
    window.addEventListener('pageshow', event => { if (event.persisted) pending = 0; hide(); });
})();
