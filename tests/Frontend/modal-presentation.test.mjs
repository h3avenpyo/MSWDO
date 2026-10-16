import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';

// Exercise the presentation observer without a browser or application database.
const source = fs.readFileSync(new URL('../../public/js/portal-modal-presentation.js', import.meta.url), 'utf8');
function element(classes = [], queries = {}) {
    const names = new Set(classes);
    return {
        changes: 0,
        classList: {
            contains: name => names.has(name),
            toggle(name, enabled) {
                if (enabled) names.add(name); else names.delete(name);
            },
        },
        matches(selector) {
            return selector.split(',').some(part => {
                part = part.trim();
                return part.startsWith('.') ? names.has(part.slice(1)) : part === this.tag || part === '[x-data]' && this.alpine;
            });
        },
        querySelector(selector) { return queries[selector] || null; },
        querySelectorAll(selector) { return queries[selector] || []; },
        getClientRects() { return this.visible ? [{}] : []; },
        contains(child) { return child === this; },
    };
}
function runtime(popups = [], roots = []) {
    const body = element(['portal-ui']);
    const document = {
        body,
        addEventListener() {},
        querySelectorAll(selector) { return selector === '.swal2-popup' ? popups : roots; },
    };
    const context = { document, getComputedStyle: () => ({ visibility: 'visible' }) };
    vm.runInNewContext(source.replace("    document.addEventListener('DOMContentLoaded'", "    globalThis.audit = { classifyPopup, update };\n    document.addEventListener('DOMContentLoaded'"), context);
    return { ...context.audit, body };
}
const detailContent = 'iframe, .case-modal-details, .archived-referral-details, .swd-archive-detail';
const formContent = '.swal2-html-container form, .swal2-html-container input, .swal2-html-container select, .swal2-html-container textarea';

test('confirmations, loading dialogs and result messages share centered alert presentation', () => {
    const popup = element(['swal2-popup', 'existing-custom-class']);
    const { update } = runtime([popup]);
    update();
    assert.equal(popup.classList.contains('portal-alert-popup'), true);
    assert.equal(popup.classList.contains('existing-custom-class'), true);
    update();
    assert.equal(popup.classList.contains('portal-alert-popup'), true);
});

test('archive views, fetched details and Close-only popups retain detail presentation', () => {
    const { classifyPopup } = runtime();
    for (const name of ['admin-archive-view-popup', 'desk-archive-view-popup', 'admin-archived-referral-popup', 'archived-referral-view-popup', 'swd-archive-detail-popup']) {
        assert.equal(classifyPopup(element([name])), 'detail');
    }
    assert.equal(classifyPopup(element([], { [detailContent]: {} })), 'detail');
    assert.equal(classifyPopup(element([], { '.swal2-confirm': { textContent: 'Close' } })), 'detail');
});

test('password forms are distinguished from simple confirmation dialogs', () => {
    assert.equal(runtime().classifyPopup(element([], { [formContent]: {} })), 'form');
});

test('logout and toast presentations are left unchanged', () => {
    const toast = element(['swal2-toast']);
    const logout = element(['app-logout-modal']);
    runtime([toast, logout]).update();
    for (const popup of [toast, logout]) {
        for (const name of ['alert', 'form', 'detail']) assert.equal(popup.classList.contains(`portal-${name}-popup`), false);
    }
});

test('reusing a SweetAlert clears its previous presentation class', () => {
    const popup = element(['portal-detail-popup']);
    runtime([popup]).update();
    assert.equal(popup.classList.contains('portal-detail-popup'), false);
    assert.equal(popup.classList.contains('portal-alert-popup'), true);
});

test('presentation updates leave existing content and callbacks intact', () => {
    const popup = element(['swal2-popup']);
    let calls = 0;
    const callback = () => calls++;
    popup.onclick = callback;
    popup.innerHTML = '<p>Existing record details</p>';
    runtime([popup]).update();
    assert.equal(popup.onclick, callback);
    assert.equal(popup.innerHTML, '<p>Existing record details</p>');
    assert.equal(calls, 0);
});

test('native page scroll lock remains until the last visible modal closes', () => {
    const first = element(), second = element();
    first.visible = true;
    second.visible = true;
    const roots = [first, second].map(panel => element(['fixed'], { '.modal-content, .refer-modal': panel }));
    const { update, body } = runtime([], roots);
    update();
    assert.equal(body.classList.contains('portal-native-modal-open'), true);
    first.visible = false;
    update();
    assert.equal(body.classList.contains('portal-native-modal-open'), true);
    second.visible = false;
    update();
    assert.equal(body.classList.contains('portal-native-modal-open'), false);
});

test('closed native dialogs never lock the page, even if a style exposes their box', () => {
    const dialog = element();
    dialog.tag = 'dialog';
    dialog.visible = true;
    dialog.open = false;
    const { update, body } = runtime([], [dialog]);
    update();
    assert.equal(body.classList.contains('portal-native-modal-open'), false);
    dialog.open = true;
    update();
    assert.equal(body.classList.contains('portal-native-modal-open'), true);
    dialog.open = false;
    update();
    assert.equal(body.classList.contains('portal-native-modal-open'), false);
});

test('shared flex styling is restricted to open native dialogs', () => {
    const css = fs.readFileSync(new URL('../../public/css/portal-modals.css', import.meta.url), 'utf8');
    assert.match(css, /dialog:not\(\[open\]\)\s*\{\s*display:\s*none\s*!important/);
    assert.match(css, /dialog\.portal-modal-panel\.portal-modal-framed\[open\]/);
    assert.doesNotMatch(css, /\.portal-modal-panel\.portal-modal-framed\s*\{\s*display:\s*flex/);
});
