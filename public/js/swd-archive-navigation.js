(() => {
    const main = document.getElementById('mainContent');
    if (!main) return;

    let pageEvents = new AbortController();
    window.swdArchiveNavigation = { cancelPending() {} };

    // Run archive page scripts on a full page load and clean up their listeners
    // when the page is left. Archive menu links use normal browser navigation.
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

        for (const script of scripts) {
            if (script.type && script.type !== 'text/javascript') continue;
            if (script.src) {
                const response = await fetch(script.src, { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Unable to initialize the archive page.');
                new Function('document', 'window', await response.text())(scopedDocument, scopedWindow);
            } else {
                const callbacks = ['openRestoreModal', 'closeRestoreModal'].map(name =>
                    `if (typeof ${name} === 'function') window.${name} = ${name};`).join('\n');
                new Function('document', 'window', script.textContent + '\n' + callbacks)(scopedDocument, scopedWindow);
            }
        }
    };

    const initialize = () => {
        const scripts = document.getElementById('swd-archive-page-scripts');
        if (scripts) {
            runPageScripts(scripts.content.querySelectorAll('script')).catch(error => {
                Swal.fire({ icon: 'error', title: 'Unable to Initialize Archive', text: error.message });
            });
        }
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
    else initialize();
})();
