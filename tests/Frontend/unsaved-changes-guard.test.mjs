import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';

const source = fs.readFileSync(new URL('../../public/js/unsaved-changes-guard.js', import.meta.url), 'utf8');
const tick = () => new Promise(resolve => setImmediate(resolve));

function runtime({ method = 'POST', loading = false, nativeModal = false } = {}) {
    const listeners = new Map(), windowListeners = new Map();
    const alerts = [], answers = [], navigations = [], timers = [], dialogs = [];
    let observer;
    const makeForm = (value = 'Original', options = {}) => {
        const form = {
            method: method || 'GET', isConnected: true, id: '', elements: [], filtered: false,
            hasAttribute: name => name === 'method' && method !== null,
            matches() { return this.filtered; },
            visible: true, getClientRects() { return this.visible ? [{}] : []; },
        };
        const field = {
            name: 'details', id: 'details', type: 'text', value, disabled: false, readOnly: false,
            classList: { contains: () => false },
            matches: () => true,
            closest: selector => selector === 'form' ? form : null,
            ...options,
        };
        form.elements.push(field);
        return { form, field };
    };
    const { form, field } = makeForm();
    const forms = [form];
    const addListener = (map, name, callback, capture = false) => {
        if (!map.has(name)) map.set(name, []);
        map.get(name).push({ callback, capture });
    };
    const event = (target, extra = {}) => ({
        target, defaultPrevented: false, stopped: false,
        preventDefault() { this.defaultPrevented = true; },
        stopPropagation() { this.stopped = true; },
        stopImmediatePropagation() { this.stopped = true; },
        ...extra,
    });
    const dispatch = (type, target, between, extra) => {
        const e = event(target, extra);
        for (const listener of listeners.get(type) || []) {
            if (listener.capture) listener.callback(e);
            if (e.stopped) return e;
        }
        between?.(e);
        for (const listener of listeners.get(type) || []) {
            if (!listener.capture) listener.callback(e);
            if (e.stopped) break;
        }
        return e;
    };
    const button = ({ href, text = 'Cancel', popup = false, onClick = () => {} } = {}) => {
        const classes = new Set(popup ? ['swal2-cancel'] : []);
        const btn = {
            tagName: href ? 'A' : 'BUTTON', type: 'button', textContent: text, id: '', href,
            target: '', replayed: 0,
            matches(selector) {
                return selector.split(',').some(s => s.trim().startsWith('.') && classes.has(s.trim().slice(1)));
            },
            closest(selector) {
                if (selector === 'button, a') return this;
                if (popup && selector.includes('swal2')) return { querySelector: () => form };
                return null;
            },
            hasAttribute: () => false, querySelector: () => null, getAttribute: () => null,
            click() {
                const e = dispatch('click', this);
                if (!e.defaultPrevented) { this.replayed++; onClick(); }
            },
        };
        return btn;
    };
    const document = {
        readyState: loading ? 'loading' : 'complete', documentElement: {}, referrer: '/previous',
        body: { append: dialog => dialogs.push(dialog) },
        addEventListener: (name, callback, capture) => addListener(listeners, name, callback, capture),
        querySelectorAll: () => forms,
        querySelector: selector => nativeModal && selector.includes('dialog[open]') ? form : null,
        getElementById: () => null,
        createElement() {
            const controls = new Map();
            const dialog = {
                open: false, removed: false, style: {}, setAttribute() {},
                showModal() { this.open = true; }, close() { this.open = false; }, remove() { this.removed = true; },
                querySelector(selector) {
                    if (!controls.has(selector)) controls.set(selector, { addEventListener(type, fn) { this[type] = fn; }, focus() {} });
                    return controls.get(selector);
                },
                addEventListener(type, callback) { this[type] = callback; },
            };
            return dialog;
        },
    };
    const window = {
        addEventListener: (name, callback) => addListener(windowListeners, name, callback),
        location: { href: '/edit', assign: url => navigations.push(url) },
        history: { back: () => navigations.push('history.back') },
        dispatchEvent() {},
        Swal: { fire(options) {
            alerts.push(options);
            if (!options?.showCancelButton) return Promise.resolve({ isConfirmed: true });
            return new Promise(resolve => answers.push(resolve));
        } },
    };
    vm.runInNewContext(source, {
        document, window, queueMicrotask, setTimeout: fn => timers.push(fn),
        MutationObserver: class { constructor(callback) { observer = callback; } observe() {} },
        CustomEvent: class {},
    });
    return {
        window, document, form, field, forms, alerts, navigations, dialogs, dispatch, button,
        guard: window.portalUnsavedChanges,
        initialize: () => dispatch('DOMContentLoaded', document),
        edit(value) { field.value = value; dispatch('input', field); },
        answer(discard) { assert.ok(answers.length, 'confirmation is open'); answers.shift()({ isConfirmed: discard }); },
        click(btn) {
            const e = dispatch('click', btn);
            if (!e.defaultPrevented && btn.href) navigations.push(btn.href);
            return e;
        },
        unload() {
            const e = event(window);
            for (const { callback } of windowListeners.get('beforeunload') || []) callback(e);
            return e.defaultPrevented;
        },
        submit(prevented = true) { return dispatch('submit', form, e => { if (prevented) e.preventDefault(); }); },
        insertForm(value, options) {
            const added = makeForm(value, options); forms.push(added.form); observer(); return added;
        },
        observe: () => observer(),
        timers() { timers.splice(0).forEach(fn => fn()); },
    };
}

for (const module of ['Cases', 'Referrals', 'Quarterly Reports', 'BCPC Monitoring', 'User Management', 'Calendar']) {
    test(`${module}: untouched Cancel leaves immediately`, () => {
        const r = runtime();
        r.click(r.button({ href: '/list' }));
        assert.deepEqual(r.navigations, ['/list']);
        assert.equal(r.alerts.length, 0);
    });
}

test('Stay on Page preserves entered values and keeps navigation blocked', async () => {
    const r = runtime(); r.edit('Unsaved entry');
    r.click(r.button({ href: '/list' }));
    assert.equal(r.alerts[0].confirmButtonText, 'Discard Changes');
    assert.equal(r.alerts[0].cancelButtonText, 'Stay on Page');
    r.answer(false); await tick();
    assert.equal(r.field.value, 'Unsaved entry');
    assert.deepEqual(r.navigations, []);
    assert.equal(r.guard.isDirty(), true);
    assert.equal(r.unload(), true);
});

test('Discard Changes exits once, without submitting or a second browser warning', async () => {
    const r = runtime(); r.edit('Discard me');
    r.click(r.button({ href: '/list' })); r.answer(true); await tick(); r.timers();
    assert.deepEqual(r.navigations, ['/list']);
    assert.equal(r.guard.isDirty(), false);
    assert.equal(r.unload(), false);
});

test('reverting a field to its initial value needs no confirmation', () => {
    const r = runtime(); r.edit('Changed'); r.edit('Original');
    r.click(r.button({ href: '/list' }));
    assert.equal(r.alerts.length, 0);
    assert.deepEqual(r.navigations, ['/list']);
});

test('saved form exits immediately, but further edits require confirmation again', () => {
    const r = runtime(); r.edit('Saved'); r.guard.reset();
    r.click(r.button({ href: '/list' })); assert.equal(r.alerts.length, 0);
    r.edit('Another change'); r.click(r.button({ href: '/list' }));
    assert.equal(r.alerts.length, 1);
});

test('invalid AJAX submission does not clear unsaved changes', () => {
    const r = runtime(); r.edit('Incomplete'); r.submit();
    assert.equal(r.guard.isDirty(), true);
    assert.equal(r.unload(), true);
    r.click(r.button({ href: '/list' })); assert.equal(r.alerts.length, 1);
});

test('failed saving and loading alerts retain protection', async () => {
    const r = runtime(); r.edit('Keep this'); r.submit();
    await r.window.Swal.fire({ title: 'Saving...', showConfirmButton: false });
    assert.equal(r.unload(), true);
    await r.window.Swal.fire({ icon: 'error', title: 'Unable to save changes' });
    assert.equal(r.guard.isDirty(), true);
    assert.equal(r.unload(), true);
});

test('cancelling the Save review still guards Cancel', async () => {
    const r = runtime(); r.edit('Needs review'); r.submit();
    const review = r.window.Swal.fire({ icon: 'question', title: 'Save?', showCancelButton: true });
    r.answer(false); await review;
    r.click(r.button({ href: '/list' }));
    assert.equal(r.alerts.at(-1).title, 'Unsaved Changes');
});

test('successful AJAX save updates the baseline without permanent bypass', async () => {
    const r = runtime(); r.edit('Saved'); r.submit();
    await r.window.Swal.fire({ icon: 'success', title: 'Updated' });
    assert.equal(r.guard.isDirty(), false);
    r.edit('New edit'); assert.equal(r.guard.isDirty(), true);
});

test('edits made while saving remain unsaved after the request succeeds', async () => {
    const r = runtime(); r.edit('Submitted'); r.submit(); r.edit('Typed while saving');
    await r.window.Swal.fire({ icon: 'success', title: 'Saved' });
    assert.equal(r.guard.isDirty(), true);
    r.edit('Submitted'); assert.equal(r.guard.isDirty(), false);
});

test('unrelated success alerts and messages containing saved do not clear edits', async () => {
    const r = runtime(); r.edit('Unsaved');
    await r.window.Swal.fire({ icon: 'success', title: 'Another record archived' });
    assert.equal(r.guard.isDirty(), true);
    await r.window.Swal.fire({ icon: 'warning', text: 'Changes could not be saved' });
    assert.equal(r.guard.isDirty(), true);
});

test('native form submission bypasses unload without falsely saving the baseline', () => {
    const r = runtime(); r.edit('Submitted'); r.submit(false);
    assert.equal(r.unload(), false);
    assert.equal(r.guard.isDirty(), true);
});

test('saving monitoring before choosing not to send still counts as saved', () => {
    const r = runtime(); r.edit('Saved monitoring'); r.guard.markSaved(r.form);
    r.click(r.button({ href: '/monitoring' }));
    assert.equal(r.alerts.length, 0);
    assert.deepEqual(r.navigations, ['/monitoring']);
});

test('GET filters are ignored, but an editable age field is protected', () => {
    const filter = runtime({ method: 'GET' }); filter.edit('query');
    assert.equal(filter.guard.isDirty(), false);
    const r = runtime(); r.field.name = 'age'; r.field.id = 'age'; r.guard.reset(); r.edit('12');
    assert.equal(r.guard.isDirty(), true);
});

test('AJAX calendar, recipient, user, and referral forms without method are protected', () => {
    const r = runtime({ method: null }); r.edit('AJAX editor change');
    r.click(r.button({ href: '/list' }));
    assert.equal(r.alerts.length, 1); assert.equal(r.guard.isDirty(), true);
});

test('checkboxes, multiple selects, and files are compared against saved values', () => {
    for (const options of [
        { type: 'checkbox', checked: false },
        { multiple: true, selectedOptions: [{ value: 'a' }] },
        { type: 'file', files: [] },
    ]) {
        const r = runtime(); Object.assign(r.field, options); r.guard.reset();
        if (r.field.type === 'checkbox') r.field.checked = true;
        else if (r.field.multiple) r.field.selectedOptions.push({ value: 'b' });
        else r.field.files.push({ name: 'report.pdf', size: 123, lastModified: 1 });
        assert.equal(r.guard.isDirty(), true);
    }
});

test('readonly generated values and hidden CSRF changes do not trigger warnings', () => {
    const r = runtime(); r.field.readOnly = true; r.guard.reset(); r.edit('Auto-generated');
    assert.equal(r.guard.isDirty(), false);
    r.field.readOnly = false; r.field.type = 'hidden'; r.guard.reset(); r.edit('New token');
    assert.equal(r.guard.isDirty(), false);
});

test('injected edit forms receive a baseline and disconnected forms are discarded', () => {
    const r = runtime(); const added = r.insertForm('Loaded from server');
    assert.equal(r.guard.isDirty(), false);
    added.field.value = 'Edited'; r.dispatch('input', added.field);
    assert.equal(r.guard.isDirty(), true);
    added.form.isConnected = false;
    assert.equal(r.guard.isDirty(), false);
});

test('opening a prefilled modal establishes clean values before editing', () => {
    const r = runtime(); r.form.visible = false; r.observe();
    r.field.value = 'Loaded record'; r.form.visible = true; r.observe();
    assert.equal(r.guard.isDirty(), false);
    r.edit('Edited record'); assert.equal(r.guard.isDirty(), true);
    // Validation or presentation changes within the open form must not reset it.
    r.observe(); assert.equal(r.guard.isDirty(), true);
});

test('resetting and reopening a form does not warn for untouched values', async () => {
    const r = runtime(); r.edit('Discarded'); r.guard.reset();
    r.field.value = 'Original'; r.dispatch('reset', r.form); await tick();
    assert.equal(r.guard.isDirty(), false);
    r.edit('New entry'); assert.equal(r.guard.isDirty(), true);
});

test('native confirmation Discard runs the existing modal Cancel handler once', async () => {
    const r = runtime({ nativeModal: true }); r.edit('Dialog entry'); let closed = 0;
    r.click(r.button({ onClick: () => closed++ }));
    r.dialogs[0].querySelector('[data-discard]').click(); await tick();
    assert.equal(closed, 1); assert.equal(r.guard.isDirty(), false);
});

test('Escape dismisses only the confirmation and preserves the original dialog data', async () => {
    const r = runtime({ nativeModal: true }); r.edit('Dialog entry');
    r.click(r.button({ href: '/list' }));
    let prevented = false;
    r.dialogs[0].cancel({ preventDefault() { prevented = true; } }); await tick();
    assert.equal(prevented, true); assert.equal(r.field.value, 'Dialog entry');
    assert.equal(r.guard.isDirty(), true); assert.deepEqual(r.navigations, []);
});

test('initial defaults set during DOMContentLoaded are clean', async () => {
    const r = runtime({ loading: true });
    r.initialize(); r.field.value = 'Initialized value'; await tick();
    assert.equal(r.guard.isDirty(), false);
});

test('modal Cancel does not run its closing handler until Discard Changes', async () => {
    const r = runtime(); r.edit('Modal entry'); let closed = 0;
    const cancel = r.button({ onClick: () => closed++ });
    r.click(cancel); r.answer(false); await tick();
    assert.equal(closed, 0); assert.equal(r.field.value, 'Modal entry');
    r.click(cancel); r.answer(true); await tick();
    assert.equal(closed, 1); assert.equal(cancel.replayed, 1);
});

test('confirmation above an existing dialog preserves its form on Stay', async () => {
    const r = runtime({ nativeModal: true }); r.edit('Dialog entry');
    r.click(r.button({ href: '/list' }));
    assert.equal(r.alerts.length, 0);
    const dialog = r.dialogs[0]; assert.equal(dialog.open, true);
    dialog.querySelector('[data-stay]').click(); await tick();
    assert.equal(r.field.value, 'Dialog entry');
    assert.deepEqual(r.navigations, []); assert.equal(dialog.removed, true);
});

test('repeated Cancel clicks show one confirmation and navigate once', async () => {
    const r = runtime(); r.edit('Unsaved'); const cancel = r.button({ href: '/list' });
    r.click(cancel); r.click(cancel);
    assert.equal(r.alerts.length, 1); r.answer(true); await tick();
    assert.deepEqual(r.navigations, ['/list']);
});

test('print/download links do not discard work or intercept new tabs', () => {
    const r = runtime(); r.edit('Keep editing');
    const print = r.button({ href: '/print', text: 'Print' }); print.target = '_blank';
    assert.equal(r.dispatch('click', print).defaultPrevented, false);
    assert.equal(r.alerts.length, 0); assert.equal(r.guard.isDirty(), true);
});

test('both public asset paths deliver identical Cancel behavior', () => {
    const mirror = fs.readFileSync(new URL('../../public/wcpu/js/unsaved-changes-guard.js', import.meta.url), 'utf8');
    assert.equal(source, mirror);
});
