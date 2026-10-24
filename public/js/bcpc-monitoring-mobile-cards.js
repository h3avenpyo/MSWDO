(() => {
    const initialize = () => {
        const form = document.getElementById('bcpc-monitoring-form');
        if (!form) return;
        const cardView = window.matchMedia(`(max-width: ${form.dataset.cardMaxWidth || '47.9375rem'})`);
        const sectionElements = [...form.querySelectorAll('[data-monitoring-section]')];
        const sectionFilter = document.getElementById('bcpc-monitoring-section-filter');
        const sections = [];

        const applySectionFilter = () => {
            sectionElements.forEach(section => {
                section.dataset.mobileSectionHidden = String(sectionFilter.value !== 'all'
                    && section.dataset.monitoringSection !== sectionFilter.value);
            });
        };

        if (sectionFilter && sectionElements.length) {
            sectionFilter.value = sectionElements[0].dataset.monitoringSection;
            sectionFilter.addEventListener('change', applySectionFilter);
            document.getElementById('bcpc-monitoring-clear-section-filter')?.addEventListener('click', () => {
                sectionFilter.value = 'all';
                applySectionFilter();
            });
            applySectionFilter();
            form.dataset.sectionFilterReady = 'true';
            sectionFilter.closest('[data-monitoring-filter]').hidden = false;
        }

        sectionElements.forEach(section => {
            const table = section.querySelector('[data-monitoring-cards]');
            const navigation = section.querySelector('[data-monitoring-pagination]');
            if (!table?.tBodies[0] || !navigation) return;
            const rows = [...table.tBodies[0].rows];
            const pageSize = Math.max(1, Number(section.dataset.pageSize) || 1);
            const pageCount = Math.max(1, Math.ceil(rows.length / pageSize));
            const previous = navigation.querySelector('[data-monitoring-previous]');
            const next = navigation.querySelector('[data-monitoring-next]');
            const status = navigation.querySelector('[data-monitoring-page-status]');
            let current = 1;

            const render = () => {
                const start = (current - 1) * pageSize;
                rows.forEach((row, index) => {
                    row.dataset.mobileHidden = String(index < start || index >= start + pageSize);
                });
                previous.disabled = current === 1;
                next.disabled = current === pageCount;
                status.textContent = `Card ${rows.length ? current : 0} of ${rows.length}`;
                navigation.hidden = pageCount <= 1;
            };

            const goTo = page => {
                current = Math.max(1, Math.min(pageCount, page));
                render();
            };

            previous.addEventListener('click', () => goTo(current - 1));
            next.addEventListener('click', () => goTo(current + 1));
            sections.push({ element: section, rows, reveal: index => goTo(Math.floor(index / pageSize) + 1) });
            render();
        });

        // Reveal the first invalid field before native validation tries to focus it.
        let revealingInvalidField = false;
        form.addEventListener('invalid', event => {
            if (!cardView.matches || revealingInvalidField) return;
            revealingInvalidField = true;
            setTimeout(() => { revealingInvalidField = false; }, 0);
            const row = event.target.closest('tr');
            const section = sections.find(item => item.rows.includes(row));
            if (section) {
                if (sectionFilter && sectionFilter.value !== 'all') {
                    sectionFilter.value = section.element.dataset.monitoringSection;
                    applySectionFilter();
                }
                section.reveal(section.rows.indexOf(row));
            }
        }, true);
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
})();
