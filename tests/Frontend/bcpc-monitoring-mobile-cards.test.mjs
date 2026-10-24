import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';

const source = fs.readFileSync(new URL('../../public/js/bcpc-monitoring-mobile-cards.js', import.meta.url), 'utf8');
const blade = fs.readFileSync(new URL('../../resources/views/wcpu/desk-officer/bcpc-case-monitoring-form.blade.php', import.meta.url), 'utf8');

function element() {
    return {
        dataset: {}, children: [], attributes: {}, listeners: {}, hidden: true, disabled: false,
        setAttribute(name, value) { this.attributes[name] = value; },
        removeAttribute(name) { delete this.attributes[name]; },
        addEventListener(name, callback) { this.listeners[name] = callback; },
        append(child) { this.children.push(child); },
        click() { if (!this.disabled) this.listeners.click?.(); },
    };
}

function fixture(counts, { cardMaxWidth, viewportWidth = 375 } = {}) {
    const timers = [];
    const mobile = { matches: true };
    const sections = counts.map((count, sectionIndex) => {
        const rows = Array.from({ length: count }, (_, index) => {
            const inputs = Array.from({ length: 9 }, () => ({ ...element(), value: '0', readOnly: false }));
            return {
                dataset: {}, inputs,
                cells: [
                    { textContent: index === count - 1 ? 'TOTAL' : `Category ${index + 1}` },
                    ...inputs.map(input => ({ querySelector: () => input })),
                ],
            };
        });
        const table = {
            ...element(), tBodies: [{ rows }],
            querySelectorAll: () => rows.flatMap(row => row.inputs).filter(input => !input.readOnly),
        };
        const previous = element();
        const next = element();
        const status = element();
        const navigation = {
            ...element(),
            querySelector: selector => ({
                '[data-monitoring-previous]': previous, '[data-monitoring-next]': next,
                '[data-monitoring-page-status]': status,
            })[selector],
        };
        return {
            ...element(), dataset: { pageSize: '1', monitoringSection: `section_${sectionIndex}` }, rows, table, previous, next, navigation, status,
            querySelector: selector => ({ '[data-monitoring-cards]': table, '[data-monitoring-pagination]': navigation })[selector],
            scrollIntoView() { this.scrolled = true; },
        };
    });
    const form = { ...element(), querySelectorAll: () => sections };
    if (cardMaxWidth) form.dataset.cardMaxWidth = cardMaxWidth;
    const filterPanel = element();
    const filter = { ...element(), value: '', closest: () => filterPanel };
    const clearFilter = element();
    const document = {
        readyState: 'complete',
        getElementById: id => ({ 'bcpc-monitoring-form': form, 'bcpc-monitoring-section-filter': filter,
            'bcpc-monitoring-clear-section-filter': clearFilter })[id],
        createElement: element,
        querySelectorAll: () => sections.map(section => section.table),
    };
    const context = vm.createContext({ document, window: { matchMedia: query => {
        mobile.matches = viewportWidth <= Number(query.match(/max-width: ([\d.]+)rem/)[1]) * 16;
        return mobile;
    } }, setTimeout: callback => timers.push(callback) });
    vm.runInContext(source, context);
    return { sections, form, mobile, context, timers, filter, filterPanel, clearFilter };
}

const visibleIndexes = section => section.rows.flatMap((row, index) => row.dataset.mobileHidden === 'false' ? [index] : []);
const advance = (section, count) => { for (let index = 0; index < count; index++) section.next.click(); };
const visibleSections = sections => sections.filter(section => section.dataset.mobileSectionHidden === 'false').map(section => section.dataset.monitoringSection);

test('the first section is selected by default', () => {
    const { sections, filter, filterPanel, form } = fixture([10, 5, 3]);
    assert.equal(filter.value, 'section_0');
    assert.deepEqual(visibleSections(sections), ['section_0']);
    assert.equal(filterPanel.hidden, false);
    assert.equal(form.dataset.sectionFilterReady, 'true');
});

test('the filter selects one section or shows all sections', () => {
    const { sections, filter } = fixture([10, 5, 3]);
    filter.value = 'section_2';
    filter.listeners.change();
    assert.deepEqual(visibleSections(sections), ['section_2']);
    filter.value = 'all';
    filter.listeners.change();
    assert.deepEqual(visibleSections(sections), ['section_0', 'section_1', 'section_2']);
});

test('changing sections preserves edits and each section card position', () => {
    const { sections, filter } = fixture([10, 5]);
    advance(sections[0], 4);
    sections[0].rows[4].inputs[0].value = '17';
    filter.value = 'section_1';
    filter.listeners.change();
    sections[1].next.click();
    filter.value = 'section_0';
    filter.listeners.change();
    assert.deepEqual(visibleIndexes(sections[0]), [4]);
    assert.equal(sections[0].rows[4].inputs[0].value, '17');
    assert.deepEqual(visibleIndexes(sections[1]), [1]);
    assert.equal(sections[1].rows.length, 5);
});

test('clearing the section filter shows every section without resetting edits or card positions', () => {
    const { sections, filter, clearFilter } = fixture([10, 5], { cardMaxWidth: '80rem', viewportWidth: 1024 });
    advance(sections[0], 4);
    sections[0].rows[4].inputs[0].value = '17';
    sections[1].next.click();
    filter.value = 'section_1';
    filter.listeners.change();
    clearFilter.click();
    assert.equal(filter.value, 'all');
    assert.deepEqual(visibleSections(sections), ['section_0', 'section_1']);
    assert.deepEqual(visibleIndexes(sections[0]), [4]);
    assert.deepEqual(visibleIndexes(sections[1]), [1]);
    assert.equal(sections[0].rows[4].inputs[0].value, '17');
    clearFilter.click();
    assert.deepEqual(visibleSections(sections), ['section_0', 'section_1']);
});

test('validation reveals a hidden section and its invalid card', () => {
    const { sections, filter, form, timers } = fixture([10, 5]);
    form.listeners.invalid({ target: { closest: () => sections[1].rows[3] } });
    assert.equal(filter.value, 'section_1');
    assert.deepEqual(visibleSections(sections), ['section_1']);
    assert.deepEqual(visibleIndexes(sections[1]), [3]);
    timers.splice(0).forEach(callback => callback());
    filter.value = 'all';
    filter.listeners.change();
    form.listeners.invalid({ target: { closest: () => sections[0].rows[7] } });
    assert.equal(filter.value, 'all');
    assert.deepEqual(visibleSections(sections), ['section_0', 'section_1']);
    assert.deepEqual(visibleIndexes(sections[0]), [7]);
});

test('each section has independent pages and correct boundaries', () => {
    const { sections: [first, second] } = fixture([10, 5]);
    assert.deepEqual(visibleIndexes(first), [0]);
    assert.equal(first.previous.disabled, true);
    advance(first, 9);
    assert.deepEqual(visibleIndexes(first), [9]);
    assert.equal(first.next.disabled, true);
    assert.deepEqual(visibleIndexes(second), [0]);
    assert.equal(first.status.textContent, 'Card 10 of 10');
    assert.equal(first.scrolled, undefined);
    first.previous.click();
    assert.deepEqual(visibleIndexes(first), [8]);
    assert.equal(first.next.disabled, false);
    assert.equal(first.scrolled, undefined);
});

test('single-page organization sections do not show unnecessary navigation', () => {
    const { sections: [organizations] } = fixture([1]);
    assert.equal(organizations.navigation.hidden, true);
    assert.deepEqual(visibleIndexes(organizations), [0]);
});

test('organization cards can be browsed individually with next and previous', () => {
    const { sections: [organizations] } = fixture([3]);
    assert.equal(organizations.navigation.hidden, false);
    assert.deepEqual(visibleIndexes(organizations), [0]);
    organizations.next.click();
    assert.deepEqual(visibleIndexes(organizations), [1]);
    assert.equal(organizations.status.textContent, 'Card 2 of 3');
    organizations.next.click();
    assert.deepEqual(visibleIndexes(organizations), [2]);
    assert.equal(organizations.next.disabled, true);
    organizations.previous.click();
    assert.deepEqual(visibleIndexes(organizations), [1]);
});

test('navigation preserves every field and its edited value', () => {
    const { sections: [section] } = fixture([10]);
    const field = section.rows[7].inputs[0];
    advance(section, 7);
    field.value = '17';
    section.previous.click();
    section.next.click();
    assert.equal(section.rows[7].inputs[0], field);
    assert.equal(field.value, '17');
    assert.equal(section.table.tBodies[0].rows.length, 10);
    assert.equal(section.rows.flatMap(row => row.inputs).length, 90);
    assert.equal(section.rows.some(row => Object.hasOwn(row, 'hidden')), false);
});

test('validation reveals the first invalid page and keeps it visible during the validation pass', () => {
    const { sections: [section], form, timers } = fixture([10]);
    form.listeners.invalid({ target: { closest: () => section.rows[7] } });
    assert.deepEqual(visibleIndexes(section), [7]);
    form.listeners.invalid({ target: { closest: () => section.rows[3] } });
    assert.deepEqual(visibleIndexes(section), [7]);
    timers.splice(0).forEach(callback => callback());
    form.listeners.invalid({ target: { closest: () => section.rows[3] } });
    assert.deepEqual(visibleIndexes(section), [3]);
});

test('desktop validation does not change the selected mobile page', () => {
    const { sections: [section], form, mobile } = fixture([10]);
    mobile.matches = false;
    form.listeners.invalid({ target: { closest: () => section.rows[7] } });
    assert.deepEqual(visibleIndexes(section), [0]);
    assert.equal(section.scrolled, undefined);
});

test('tablet editing reveals an invalid field in another section and card', () => {
    const { sections, form, filter } = fixture([10, 5], { cardMaxWidth: '80rem', viewportWidth: 1024 });
    form.listeners.invalid({ target: { closest: () => sections[1].rows[3] } });
    assert.equal(filter.value, 'section_1');
    assert.deepEqual(visibleSections(sections), ['section_1']);
    assert.deepEqual(visibleIndexes(sections[1]), [3]);
});

test('tablet add forms retain table validation behavior', () => {
    const { sections: [section], form } = fixture([10], { viewportWidth: 1024 });
    form.listeners.invalid({ target: { closest: () => section.rows[7] } });
    assert.deepEqual(visibleIndexes(section), [0]);
});

test('desktop editing retains table validation behavior', () => {
    const { sections: [section], form } = fixture([10], { cardMaxWidth: '80rem', viewportWidth: 1440 });
    form.listeners.invalid({ target: { closest: () => section.rows[7] } });
    assert.deepEqual(visibleIndexes(section), [0]);
});

test('calculated totals include values from all pages', () => {
    const { sections: [section], context } = fixture([10]);
    section.rows[0].inputs[0].value = '2';
    section.rows[0].inputs[1].value = '3';
    section.rows[4].inputs[0].value = '4';
    section.rows[4].inputs[1].value = '7';
    const start = blade.indexOf("document.querySelectorAll('.bcpc-monitoring-table')");
    const calculation = blade.slice(start, blade.indexOf('</script>', start))
        .replace("{{ $isView ? 'true' : 'false' }}", 'false');
    vm.runInContext(calculation, context);
    assert.equal(Number(section.rows[9].inputs[2].value), 16);
    assert.equal(section.rows[9].inputs[2].readOnly, true);
    advance(section, 4);
    section.rows[4].inputs[0].value = '9';
    section.table.listeners.input();
    assert.equal(Number(section.rows[4].inputs[2].value), 16);
    assert.equal(Number(section.rows[9].inputs[2].value), 21);
});
