(() => {
    if (window.portalInactivityStarted) return;
    const settings = document.getElementById('inactivity-settings');
    if (!settings) return;
    window.portalInactivityStarted = true;
    const config = JSON.parse(settings.textContent);
    const timeout = 30 * 60 * 1000;
    let lastActivity = Date.now() - Math.max(0, config.serverNow - config.lastActivity);
    let ending = false, lastPing = 0, trailingPing;
    const read = () => { try { return Number(localStorage.getItem(config.key)) || 0; } catch { return 0; } };
    lastActivity = Math.max(lastActivity, read());
    const headers = {'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'};
    const expired = () => Date.now() - Math.max(lastActivity, read()) >= timeout;
    const logout = async () => {
        if (ending) return;
        ending = true;
        // Hide authenticated content immediately, including after a suspended tab wakes.
        document.body.style.visibility = 'hidden';
        try {
            await fetch(config.logoutUrl, {method: 'POST', headers: {...headers, 'Content-Type': 'application/x-www-form-urlencoded'}, body: 'inactive=1'});
        } finally { window.location.replace(config.inactiveLoginUrl || config.loginUrl); }
    };
    const checkResponse = response => {
        if (response.status === 401 || response.status === 419 || response.redirected) {
            ending = true;
            document.body.style.visibility = 'hidden';
            window.location.replace(config.loginUrl);
        }
    };
    const ping = () => {
        if (ending) return;
        if (expired()) { logout(); return; }
        lastPing = Date.now();
        fetch(config.activityUrl, {method: 'POST', headers}).then(checkResponse).catch(() => {});
    };
    const activity = event => {
        if (!event.isTrusted || ending) return;
        if (expired()) { logout(); return; }
        lastActivity = Date.now();
        try { localStorage.setItem(config.key, String(lastActivity)); } catch {}
        if (Date.now() - lastPing >= 15000) ping();
        clearTimeout(trailingPing);
        trailingPing = setTimeout(ping, 1000);
    };
    ['pointermove', 'pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(name => document.addEventListener(name, activity, {passive: true, capture: true}));
    const check = () => { if (!ending && expired()) logout(); };
    window.addEventListener('pageshow', check);
    document.addEventListener('visibilitychange', check);
    window.addEventListener('storage', event => { if (event.key === config.key) lastActivity = Math.max(lastActivity, read()); });
    setInterval(check, 1000);
    setInterval(() => {
        if (ending || expired()) { check(); return; }
        fetch(config.statusUrl, {headers}).then(checkResponse).catch(() => {});
    }, 30000);
    document.querySelectorAll('form[action*="/logout"], #logout-button, .logout-button').forEach(el => {
        el.addEventListener('click', () => { ending = true; }, true);
        el.addEventListener('submit', () => { ending = true; }, true);
    });
    check();
})();
