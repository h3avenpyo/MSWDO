(() => {
    const key = 'desk-referral-case-modal';
    const readSaved = () => {
        try { return JSON.parse(sessionStorage.getItem(key)); } catch (_) { return null; }
    };
    const isReturnNavigation = saved => saved?.returnRequested
        || performance.getEntriesByType('navigation')[0]?.type === 'back_forward';
    let openedCase = null;
    window.addEventListener('open-case-modal', event => {
        const detail = event.detail;
        if (!detail?.caseId) return;
        let referredCases = [];
        try { referredCases = JSON.parse(sessionStorage.getItem('desk-successful-referrals') || '[]'); } catch (_) {}
        const doc = new DOMParser().parseFromString(detail.content || '', 'text/html');
        const metadata = doc.querySelector('[data-case-status]');
        const status = metadata?.dataset.caseStatus || detail.caseStatus || '';
        if (metadata?.dataset.hasReferral === '1' || /referred|closed|complet/i.test(status)
            || referredCases.includes(String(detail.caseId))) {
            detail.canRefer = false;
            detail.caseStatus = /closed|complet/i.test(status) ? status : 'Case Referred';
        }
    }, true);
    window.hasPendingDeskCaseReturn = () => {
        try {
            const saved = JSON.parse(sessionStorage.getItem(key));
            return saved?.page === location.href && isReturnNavigation(saved);
        } catch (_) { return false; }
    };
    window.addEventListener('open-case-modal', event => {
        if (event.detail?.caseId) openedCase = event.detail;
    });
    window.addEventListener('close-case-modal', () => { openedCase = null; });
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link) return;
        if (link.closest('#sidebar')) {
            try { sessionStorage.removeItem(key); } catch (_) {}
            openedCase = null;
            return;
        }
        if (document.getElementById('referral-form') && /back|cancel/i.test(link.textContent || '')) {
            const saved = readSaved();
            if (saved) {
                saved.returnRequested = true;
                try { sessionStorage.setItem(key, JSON.stringify(saved)); } catch (_) {}
            }
        }
        if (!openedCase) return;
        const destination = new URL(link.href, location.href);
        if (destination.origin === location.origin && /\/refer\/?$/.test(destination.pathname)) {
            try {
                sessionStorage.setItem(key, JSON.stringify({ page: location.href, detail: openedCase }));
            } catch (_) { /* Navigation remains available if storage is disabled. */ }
        }
    }, true);
    window.addEventListener('pageshow', event => {
        let saved;
        try { saved = JSON.parse(sessionStorage.getItem(key)); } catch (_) { return; }
        if (!saved || saved.page !== location.href) return;
        if (!event.persisted && !isReturnNavigation(saved)) {
            try { sessionStorage.removeItem(key); } catch (_) {}
            return;
        }
        window.restoringDeskCasePopup = true;
        try { sessionStorage.removeItem(key); } catch (_) {}
        // Wait until the restored page and Alpine modal listeners have settled.
        requestAnimationFrame(() => requestAnimationFrame(() => {
            window.dispatchEvent(new CustomEvent('open-case-modal', { detail: saved.detail }));
            window.restoringDeskCasePopup = false;
        }));
    });
})();
