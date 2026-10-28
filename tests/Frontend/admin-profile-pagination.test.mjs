import fs from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

const template = fs.readFileSync('resources/views/wcpu/admin-tailwind/components/profile-pagination.blade.php', 'utf8');
const source = template.match(/<script>([\s\S]*?)<\/script>/)[1]
    .replace('@json($paginationRegions)', '["#results"]')
    .replace('@json($paginationLinks)', '".pagination a[href]"');

function setup({ fail = false } = {}) {
    const listeners = {};
    const attributes = {};
    const messages = [];
    const replacement = { setAttribute() {}, focus() {} };
    let replaced = 0;
    let pushed = 0;
    let fetched = 0;
    const current = {
        setAttribute(name, value) { attributes[name] = value; },
        removeAttribute(name) { delete attributes[name]; },
        replaceWith(value) { assert.equal(value, replacement); replaced++; },
        querySelector() { return null; },
        appendChild(message) { messages.push(message); },
    };
    const document = {
        querySelector() { return current; },
        addEventListener(name, callback) { listeners[name] = callback; },
        createElement() { return { dataset: {}, setAttribute() {} }; },
    };
    const context = {
        document,
        window: { addEventListener(name, callback) { listeners[name] = callback; } },
        location: { href: 'http://localhost/list?search=alice', origin: 'http://localhost', pathname: '/list' },
        history: { pushState() { pushed++; } },
        URL, AbortController,
        DOMParser: class { parseFromString() { return { querySelector() { return replacement; } }; } },
        fetch: async () => { fetched++; return { ok: !fail, redirected: false, text: async () => '<html></html>' }; },
    };
    vm.runInNewContext(source, context);
    const click = (options = {}) => {
        let prevented = false;
        const link = { href: 'http://localhost/list?search=alice&page=2', target: '', getAttribute: () => '?search=alice&page=2', classList: { contains: () => false } };
        listeners.click({ button: 0, target: { closest: () => link }, preventDefault() { prevented = true; }, stopImmediatePropagation() {}, ...options });
        return prevented;
    };
    return { click, listeners, messages, attributes, counts: () => ({ replaced, pushed, fetched }) };
}
const settle = () => new Promise(resolve => setImmediate(resolve));

test('pagination replaces results and pushes history; Back restores without pushing', async () => {
    const app = setup();
    assert.equal(app.click(), true);
    await settle();
    assert.deepEqual(app.counts(), { replaced: 1, pushed: 1, fetched: 1 });
    await app.listeners.popstate();
    assert.deepEqual(app.counts(), { replaced: 2, pushed: 1, fetched: 2 });
});

test('failed pagination keeps the current results and reports an inline error', async () => {
    const app = setup({ fail: true });
    app.click();
    await settle();
    assert.deepEqual(app.counts(), { replaced: 0, pushed: 0, fetched: 1 });
    assert.match(app.messages[0].textContent, /try the page link again/);
    assert.equal(app.attributes['aria-busy'], undefined);
});

test('modified clicks preserve normal browser behavior', async () => {
    const app = setup();
    assert.equal(app.click({ ctrlKey: true }), false);
    await settle();
    assert.equal(app.counts().fetched, 0);
});
