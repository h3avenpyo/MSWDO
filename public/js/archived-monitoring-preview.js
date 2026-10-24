// Retain the monitoring report's section/row labels for its read-only card layout.
window.prepareArchivedMonitoringPreview = monitoring => {
    monitoring.querySelectorAll('input[type="hidden"], button, [data-monitoring-pagination], .bcpc-monitoring-form-actions')
        .forEach(control => control.remove());
    monitoring.querySelectorAll('input, textarea, select').forEach(input => {
        const value = document.createElement('span');
        value.className = 'archived-monitoring-value block whitespace-pre-wrap break-words text-gray-800';
        value.textContent = input.tagName === 'SELECT'
            ? input.selectedOptions[0]?.textContent || '\u2014'
            : input.value || '\u2014';
        input.replaceWith(value);
    });
    monitoring.querySelectorAll('.bcpc-monitoring-mobile-label, .bcpc-monitoring-mobile-year').forEach(label => {
        label.removeAttribute('aria-hidden');
        label.removeAttribute('for');
    });
    const details = document.createElement('div');
    details.id = 'bcpc-monitoring-form';
    details.className = 'archived-monitoring-preview space-y-4';
    const sections = Array.from(monitoring.querySelectorAll('[data-monitoring-section]'));
    details.append(...Array.from(monitoring.childNodes));
    if (sections.length) {
        const panel = document.createElement('div');
        panel.className = 'archived-monitoring-section-filter';
        const label = document.createElement('label');
        label.htmlFor = 'archived-monitoring-section-filter';
        label.textContent = 'Show section';
        const controls = document.createElement('div');
        controls.className = 'archived-monitoring-filter-controls';
        const select = document.createElement('select');
        select.id = 'archived-monitoring-section-filter';
        const all = document.createElement('option');
        all.value = 'all';
        all.textContent = 'All sections';
        select.append(all);
        sections.forEach((section, index) => {
            const option = document.createElement('option');
            option.value = section.dataset.monitoringSection;
            option.textContent = section.querySelector('h2')?.textContent.trim() || `Section ${index + 1}`;
            select.append(option);
        });
        select.value = 'all';
        const filter = () => sections.forEach(section => {
            section.hidden = select.value !== 'all' && section.dataset.monitoringSection !== select.value;
        });
        const clear = document.createElement('button');
        clear.type = 'button';
        clear.textContent = 'Clear filter';
        select.addEventListener('change', filter);
        clear.addEventListener('click', () => { select.value = 'all'; filter(); });
        controls.append(select, clear);
        panel.append(label, controls);
        details.prepend(panel);
        filter();
    }
    return details;
};
