(() => {
    const main = document.getElementById('mainContent');
    if (!main) return;
<<<<<<< HEAD

    let pageEvents = new AbortController();
    window.swdArchiveNavigation = { cancelPending() {} };

    // Run archive page scripts on a full page load and clean up their listeners
    // when the page is left. Archive menu links use normal browser navigation.
=======
    const archivePaths = new Set(Array.from(document.querySelectorAll('#sidebar a[title^="Archived"]'), link => new URL(link.href).pathname));
    let pageEvents = new AbortController();
    let request;
    window.swdArchiveNavigation = {
        cancelPending() {
            request?.abort();
            main.removeAttribute('aria-busy');
        }
    };

    // Each archive page owns its listeners; remove them before changing pages.
>>>>>>> 93ba7b912d918f581ced5753318a7376a38e8496
    const runPageScripts = async scripts => {
        const signal = pageEvents.signal;
        const scopedWindow = new Proxy(window, {
            get(target, property) {
                if (property === 'addEventListener') return (type, callback, options) => {
                    target.addEventListener(type, callback, { ...(typeof options === 'boolean' ? { capture: options } : options), signal });
                };
                if (property === 'setInterval' || property === 'setTimeout') return (callback, delay, ...args) => {
                    const id = target[property](callback, delay, ...args);
                    signal.addEventListener('abort', () => {
                        target[property === 'setInterval' ? 'clearInterval' : 'clearTimeout'](id);
                    }, { once: true });
                    return id;
                };
                const value = Reflect.get(target, property, target);
                return typeof value === 'function' ? value.bind(target) : value;
            }
        });
        const scopedDocument = new Proxy(document, {
            get(target, property) {
                if (property === 'addEventListener') return (type, callback, options) => {
                    if (type === 'DOMContentLoaded') {
                        callback.call(document, new Event(type));
                        return;
                    }
                    target.addEventListener(type, callback, { ...(typeof options === 'boolean' ? { capture: options } : options), signal });
                };
                const value = Reflect.get(target, property, target);
                return typeof value === 'function' ? value.bind(target) : value;
            }
        });
<<<<<<< HEAD

=======
>>>>>>> 93ba7b912d918f581ced5753318a7376a38e8496
        for (const script of scripts) {
            if (script.type && script.type !== 'text/javascript') continue;
            if (script.src) {
                const response = await fetch(script.src, { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Unable to initialize the archive page.');
                new Function('document', 'window', await response.text())(scopedDocument, scopedWindow);
            } else {
<<<<<<< HEAD
=======
                // Preserve the existing restore modal's inline button callbacks.
>>>>>>> 93ba7b912d918f581ced5753318a7376a38e8496
                const callbacks = ['openRestoreModal', 'closeRestoreModal'].map(name =>
                    `if (typeof ${name} === 'function') window.${name} = ${name};`).join('\n');
                new Function('document', 'window', script.textContent + '\n' + callbacks)(scopedDocument, scopedWindow);
            }
        }
    };

<<<<<<< HEAD
    const initialize = () => {
        const scripts = document.getElementById('swd-archive-page-scripts');
        if (scripts) {
            runPageScripts(scripts.content.querySelectorAll('script')).catch(error => {
                Swal.fire({ icon: 'error', title: 'Unable to Initialize Archive', text: error.message });
            });
        }
    };

=======
    const navigate = async (href, push = true) => {
        request?.abort();
        const controller = new AbortController();
        request = controller;
        main.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(href, { credentials: 'same-origin', signal: controller.signal });
            if (!response.ok || response.redirected) throw new Error('Unable to open the selected archive page.');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const nextMain = page.getElementById('mainContent');
            const pageScripts = page.getElementById('swd-archive-page-scripts');
            if (!nextMain || !pageScripts) throw new Error('Unable to open the selected archive page.');
            const scripts = [...nextMain.querySelectorAll('script'), ...pageScripts.content.querySelectorAll('script')];
            nextMain.querySelectorAll('script').forEach(script => script.remove());
            pageEvents.abort();
            pageEvents = new AbortController();
            const archiveDialog = document.getElementById('swdArchiveViewDialog');
            if (archiveDialog?.open) archiveDialog.close();
            const mobileMenuButton = main.querySelector('#mobileMenuToggle');
            main.replaceChildren(...Array.from(nextMain.childNodes));
            if (mobileMenuButton) main.querySelector(':scope > header > div')?.prepend(mobileMenuButton);
            main.dataset.pageTitle = nextMain.dataset.pageTitle;
            // Include the selected page's responsive styles with its content.
            page.head.querySelectorAll('style').forEach(style => main.prepend(style.cloneNode(true)));
            document.title = page.title;
            if (push) history.pushState({ swdArchive: true }, '', href);
            document.querySelectorAll('#sidebar a[title^="Archived"]').forEach(link => {
                const next = Array.from(page.querySelectorAll('#sidebar a[title^="Archived"]')).find(item => item.title === link.title);
                if (next) link.className = next.className;
            });
            await runPageScripts(scripts);
            window.scrollTo(0, 0);
        } catch (error) {
            if (error.name !== 'AbortError') Swal.fire({ icon: 'error', title: 'Unable to Open Archive', text: error.message });
        } finally {
            if (request === controller) main.removeAttribute('aria-busy');
        }
    };

    window.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank') return;
        const url = new URL(link.href);
        if (url.origin !== location.origin || !archivePaths.has(url.pathname)) return;
        if (!link.closest('#sidebar, #mainContent') || link.hasAttribute('data-swd-archive-view')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        const open = () => navigate(url.href);
        if (window.portalUnsavedChanges?.isDirty()) window.portalUnsavedChanges.confirmExit(open);
        else open();
    }, true);

    window.addEventListener('submit', event => {
        const form = event.target;
        if (!form.closest('#mainContent') || form.method.toUpperCase() !== 'GET') return;
        const url = new URL(form.action);
        if (!archivePaths.has(url.pathname)) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        new FormData(form).forEach((value, key) => url.searchParams.set(key, value));
        navigate(url.href);
    }, true);

    window.addEventListener('popstate', () => {
        if (archivePaths.has(location.pathname)) navigate(location.href, false);
    });
    const initialize = () => {
        const scripts = document.getElementById('swd-archive-page-scripts');
        if (scripts) {
            history.replaceState({ ...history.state, swdArchive: true }, '', location.href);
            runPageScripts(scripts.content.querySelectorAll('script')).catch(error => {
                Swal.fire({ icon: 'error', title: 'Unable to Open Archive', text: error.message });
            });
        }
    };
>>>>>>> 93ba7b912d918f581ced5753318a7376a38e8496
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
    else initialize();
})();
