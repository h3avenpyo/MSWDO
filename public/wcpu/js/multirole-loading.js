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
    const isSwalBusy = () => window.Swal && (Swal.isVisible() || Swal.isLoading());
    const isWriteMethod = method => !['GET', 'HEAD', 'OPTIONS'].includes(String(method || 'GET').toUpperCase());
    const hasSkipHeader = headers => new Headers(headers || {}).has('X-Skip-Loading');
    const isBackgroundRequest = pathname => /\/session\/|notifications|mark-seen|mark-read|unread/i.test(pathname);

    const originalFetch = window.fetch;
    window.fetch = async function(input, options) {
        const request = input instanceof Request ? input : null;
        const url = new URL(request?.url || input, location.href);
        const method = options?.method || request?.method || 'GET';
        const headers = options?.headers || request?.headers;
        const visible = url.origin === location.origin && isWriteMethod(method) && !isBackgroundRequest(url.pathname)
            && !hasSkipHeader(headers) && !isSwalBusy();
        if (!visible) return originalFetch.call(this, input, options);

        pending++;
        show();
        try {
            const response = await originalFetch.call(this, input, options);
            await response.clone().arrayBuffer();
            return response;
        } finally {
            pending--;
            hide();
        }
    };

    document.addEventListener('submit', event => {
        queueMicrotask(() => {
            const form = event.target;
            if (!event.defaultPrevented && form.target !== '_blank' && isWriteMethod(form.method) && !isSwalBusy()) show();
        });
    });

    const submit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function() {
        if (this.target !== '_blank' && isWriteMethod(this.method)) show();
        return submit.call(this);
    };

    window.addEventListener('pageshow', event => {
        if (event.persisted) pending = 0;
        hide();
    });
})();
