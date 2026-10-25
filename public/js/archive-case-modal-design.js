// Presentation only: reuse the loaded case metadata in the modal's header.
window.applyArchiveCaseModalDesign = (root, title) => {
    if (!root || !title) return;
    const partial = root.querySelector('[data-case-modal-summary]');
    const profile = root.querySelector('.case-profile-details');
    const banner = partial || profile?.querySelector(':scope > .bg-white > [class*="bg-gradient"]');
    if (!banner) return;
    const number = banner.querySelector('h1, h3, h4')?.textContent.trim();
    if (!number) return;

    const heading = document.createElement('div');
    heading.className = 'archive-case-heading';
    heading.textContent = number.startsWith('Case #') ? number : `Case #${number}`;
    const summary = document.createElement('div');
    summary.className = 'archive-case-heading-summary';
    const abuse = partial?.querySelector('p') || profile?.querySelector(':scope > .bg-white > .p-6 .grid > div > p + p');
    if (abuse) {
        const type = document.createElement('span');
        type.textContent = abuse.textContent;
        summary.append(type);
    }
    banner.querySelectorAll('.rounded-full, .badge').forEach(badge => summary.append(badge.cloneNode(true)));
    title.replaceChildren(heading, summary);
    title.classList.add('archive-case-modal-title');
    if (partial) {
        partial.classList.add('archive-case-summary-in-header');
    } else {
        banner.classList.add('archive-case-summary-in-header');
        // Keep the original filed date visible in the body.
        const filed = banner.querySelector('p');
        if (filed) {
            const date = filed.cloneNode(true);
            date.className = 'archive-case-filed-date';
            banner.after(date);
        }
        profile.querySelectorAll(':scope > .bg-white').forEach(card => {
            const label = card.querySelector('h3')?.textContent.trim();
            if (['Victim Information', 'Guardian Information', 'Perpetrator Information', 'Guardian of Perpetrator', 'Guardian of Perpetrator Information'].includes(label)) {
                card.classList.add('archive-case-person-card');
            }
        });
        const overview = banner.parentElement;
        overview.classList.add('archive-case-overview');
        const overviewTitle = document.createElement('h3');
        overviewTitle.className = 'archive-case-overview-title';
        overviewTitle.textContent = 'Case Information';
        banner.after(overviewTitle);
        const people = profile.querySelectorAll(':scope > .archive-case-person-card');
        if (people.length) people[people.length - 1].after(overview);
    }
};

window.applyArchiveReportModalDesign = (doc, title, allowBcpcMonitoring = false) => {
    if (!doc || !title) return;
    const heading = doc.querySelector('#mainContent > header h2')
        || doc.querySelector('#mainContent .max-w-7xl > .mb-6 h1');
    const report = doc.querySelector('[data-archive-report-title]');
    const reportTitle = report?.dataset.archiveReportTitle || heading?.textContent.trim();
    const isQuarterlyReport = reportTitle?.includes('Quarterly Compliance Report');
    const isBcpcMonitoring = allowBcpcMonitoring && reportTitle?.trim() === 'BCPC Case Monitoring';
    if (!isQuarterlyReport && !isBcpcMonitoring) return;
    if (isQuarterlyReport && doc.body.classList.contains('desk-officer-header-theme') && window.matchMedia('(max-width: 47.9375rem)').matches) {
        doc.querySelectorAll('#abuse-type-classification .abuse-type-table-view, #case-status-breakdown .case-status-table-view, #case-records .case-records-table-view').forEach(table => {
            table.style.setProperty('display', 'none', 'important');
        });
        doc.querySelectorAll('#abuse-type-mobile-cards, #case-status-mobile-cards, #case-records-mobile-cards').forEach(cards => {
            cards.style.setProperty('display', 'flex', 'important');
            cards.style.setProperty('flex-direction', 'column', 'important');
            cards.style.setProperty('gap', '.75rem', 'important');
            cards.style.setProperty('padding', '.75rem', 'important');
            cards.querySelectorAll(':scope > article').forEach(card => {
                card.style.setProperty('width', '100%');
                card.style.setProperty('min-width', '0');
                card.style.setProperty('overflow-wrap', 'anywhere');
                card.style.setProperty('box-sizing', 'border-box');
                card.style.setProperty('margin', '0', 'important');
                card.style.setProperty('padding', '1rem', 'important');
                card.style.setProperty('text-align', 'left', 'important');
                card.querySelectorAll('p, span').forEach(label => label.style.setProperty('text-align', 'left', 'important'));
            });
        });
        doc.querySelectorAll('#abuse-type-classification > div:first-child, #case-status-breakdown > div:first-child, #case-records > div:first-child').forEach(header => {
            header.style.setProperty('text-align', 'left', 'important');
            header.style.setProperty('padding', '.75rem 1rem', 'important');
            header.querySelectorAll('h2, p').forEach(label => {
                label.style.setProperty('text-align', 'left', 'important');
                label.style.setProperty('padding', '0', 'important');
                if (label.tagName === 'H2') {
                    label.style.setProperty('justify-content', 'flex-start', 'important');
                    label.style.setProperty('gap', '.5rem', 'important');
                    label.style.setProperty('margin', '0', 'important');
                } else {
                    label.style.setProperty('margin', '.25rem 0 0', 'important');
                    label.style.setProperty('padding-left', '.625rem', 'important');
                }
            });
        });
    }
    const name = document.createElement('div');
    name.className = 'archive-case-heading';
    name.textContent = reportTitle;
    const metadata = heading?.parentElement.querySelector('p');
    const summary = document.createElement('div');
    summary.className = 'archive-case-heading-summary';
    summary.textContent = report?.dataset.archiveReportSubtitle || metadata?.textContent.trim() || '';
    title.replaceChildren(name, summary);
    title.classList.add('archive-case-modal-title');
    doc.body.classList.add('archive-report-modal-preview');
};
