// BCPC archive frames retain their original fields and values, displayed as
// compact information rows to match the WCPU Admin case-detail modal.
window.applyBcpcArchiveCaseDesign = doc => {
    if (!document.body.classList.contains('bcpc-admin-header-theme')) return;
        if (!doc?.body.classList.contains('archive-case-modal-preview')) return;
        const profile = doc.querySelector('.case-profile-details');
        if (!profile || profile.dataset.bcpcModalRows) return;
        profile.dataset.bcpcModalRows = 'true';
        profile.querySelectorAll('.detail-row').forEach(row => row.classList.add('bcpc-case-detail-row'));
        profile.querySelectorAll(':scope > .bg-white p.text-xs').forEach(label => {
            const value = label.nextElementSibling;
            if (value) label.parentElement.classList.add('bcpc-case-detail-row');
        });
        const filed = profile.querySelector('.archive-case-filed-date');
        if (filed) {
            const text = filed.textContent;
            const separator = text.indexOf(':');
            if (separator !== -1) {
                const label = doc.createElement('span');
                label.textContent = text.slice(0, separator + 1);
                const value = doc.createElement('span');
                value.textContent = text.slice(separator + 1);
                filed.replaceChildren(label, value);
                filed.classList.add('bcpc-case-detail-row');
            }
        }
        const overview = profile.querySelector('.archive-case-overview');
        const cards = [...profile.querySelectorAll(':scope > .bg-white')].filter(card => card !== overview);
        if (overview && cards.length) cards[cards.length - 1].after(overview);
};
