import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source = fs.readFileSync(new URL('../../public/js/archived-monitoring-preview.js', import.meta.url), 'utf8');
const partial = fs.readFileSync(new URL('../../resources/views/wcpu/desk-officer/partials/swd-archive-interactions.blade.php', import.meta.url), 'utf8');

function preview() {
    const controls = [
        { tagName: 'INPUT', value: '0' },
        { tagName: 'INPUT', value: '123' },
        { tagName: 'INPUT', value: '' },
        { tagName: 'INPUT', value: '<script>alert(1)</script>' },
        { tagName: 'TEXTAREA', value: 'Line one\nLine two' },
        { tagName: 'SELECT', selectedOptions: [{ textContent: 'Selected agency' }] },
        { tagName: 'SELECT', selectedOptions: [] },
    ].map(input => ({ ...input, replaceWith(value) { this.replacement = value; } }));
    const removed = ['token', 'save', 'send', 'pagination', 'actions'].map(name => ({ name, remove() { this.removed = true; } }));
    const labels = [{ attributes: new Set(['aria-hidden']) }, { attributes: new Set(['aria-hidden', 'for']) }];
    labels.forEach(label => label.removeAttribute = name => label.attributes.delete(name));
    const sections = [{ name: 'Crime counts', rows: 7 }, { name: 'Organizations', rows: 3 }].map((section, index) => ({
        ...section, hidden: false, dataset: { monitoringSection: `section_${index}` },
        querySelector: () => ({ textContent: section.name }),
    }));
    const monitoring = {
        childNodes: sections,
        querySelectorAll(selector) {
            if (selector.startsWith('input[type=')) return removed;
            if (selector === 'input, textarea, select') return controls;
            if (selector === '[data-monitoring-section]') return sections;
            return labels;
        },
    };
    const window = {};
    vm.runInNewContext(source, { window, document: { createElement: tagName => ({
        tagName, childNodes: [], listeners: {},
        append(...children) { this.childNodes.push(...children); },
        prepend(...children) { this.childNodes.unshift(...children); },
        addEventListener(type, handler) { this.listeners[type] = handler; },
    }) } });
    return { root: window.prepareArchivedMonitoringPreview(monitoring), controls, removed, labels, sections };
}

test('read-only preview retains the report sections and card-layout root', () => {
    const { root, sections } = preview();
    assert.equal(root.id, 'bcpc-monitoring-form');
    assert.match(root.className, /archived-monitoring-preview/);
    assert.deepEqual(root.childNodes.slice(1), sections);
});

test('preserves zero, counts, multiline text, selected options, and empty values', () => {
    const { controls } = preview();
    assert.deepEqual(controls.map(input => input.replacement.textContent), [
        '0', '123', '\u2014', '<script>alert(1)</script>', 'Line one\nLine two', 'Selected agency', '\u2014',
    ]);
    for (const input of controls) {
        assert.equal(input.replacement.tagName, 'span');
        assert.equal(input.replacement.innerHTML, undefined, 'report values are rendered as text');
    }
});

test('removes edit actions and pagination without removing report data', () => {
    const { removed, controls } = preview();
    assert.ok(removed.every(control => control.removed));
    assert.equal(controls.length, 7);
});

test('year, sex, and organization labels remain available to assistive technology', () => {
    const { labels } = preview();
    assert.ok(labels.every(label => !label.attributes.has('aria-hidden')));
    assert.ok(labels.every(label => !label.attributes.has('for')));
});

test('phone and tablet assets cover portrait and landscape through 1366px', () => {
    assert.match(partial, /bcpc-monitoring-card-layout\.css[^\n]+media="screen and \(max-width: 85\.375rem\)"/);
    assert.match(partial, /bcpc-monitoring-tablet-cards\.css[^\n]+media="screen and \(min-width: 48rem\) and \(max-width: 85\.375rem\)"/);
    const start = partial.indexOf("@if(request()->routeIs('desk-officer.quarterly-reports.archived'))");
    const end = partial.indexOf('@endif', start);
    assert.doesNotMatch(partial.slice(start, end), /orientation:/);
    const cards = fs.readFileSync(new URL('../../public/css/bcpc-monitoring-card-layout.css', import.meta.url), 'utf8');
    const tablet = fs.readFileSync(new URL('../../public/css/bcpc-monitoring-tablet-cards.css', import.meta.url), 'utf8');
    assert.match(cards, /grid-template-columns: repeat\(3, minmax\(0, 1fr\)\)/);
    assert.match(tablet, /grid-template-columns: repeat\(9, minmax\(0, 1fr\)\)/);
});

test('responsive renderer is used only for monitoring on the quarterly archive route', () => {
    assert.match(partial, /const responsiveMonitoring = isMonitoring && @json\(request\(\)->routeIs\('desk-officer\.quarterly-reports\.archived'\)\)/);
    assert.match(partial, /if \(responsiveMonitoring\) \{\s*content.replaceChildren\(window.prepareArchivedMonitoringPreview\(monitoring\)\);\s*return;/);
});

test('filter lists every report section and initially shows all sections', () => {
    const { root, sections } = preview();
    const [label, controls] = root.childNodes[0].childNodes;
    const [select, clear] = controls.childNodes;
    assert.equal(label.htmlFor, select.id);
    assert.equal(select.value, 'all');
    assert.deepEqual(select.childNodes.map(option => option.textContent), ['All sections', 'Crime counts', 'Organizations']);
    assert.ok(sections.every(section => !section.hidden));
    assert.equal(clear.type, 'button');
});

test('selecting a section filters only report sections without altering values', () => {
    const { root, sections, controls } = preview();
    const select = root.childNodes[0].childNodes[1].childNodes[0];
    const originalValues = controls.map(input => input.replacement.textContent);
    select.value = 'section_1'; select.listeners.change();
    assert.deepEqual(sections.map(section => section.hidden), [true, false]);
    select.value = 'section_0'; select.listeners.change();
    assert.deepEqual(sections.map(section => section.hidden), [false, true]);
    assert.deepEqual(controls.map(input => input.replacement.textContent), originalValues);
});

test('Clear filter restores all sections and resets the dropdown', () => {
    const { root, sections } = preview();
    const [select, clear] = root.childNodes[0].childNodes[1].childNodes;
    select.value = 'section_1'; select.listeners.change();
    clear.listeners.click();
    assert.equal(select.value, 'all');
    assert.ok(sections.every(section => !section.hidden));
});

test('manually selecting All sections also restores the full report', () => {
    const { root, sections } = preview();
    const select = root.childNodes[0].childNodes[1].childNodes[0];
    select.value = 'section_0'; select.listeners.change();
    select.value = 'all'; select.listeners.change();
    assert.ok(sections.every(section => !section.hidden));
});
