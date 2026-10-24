<script>
document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.getElementById('mainContent');
    if (!mainContent) return;

    let headerRow = mainContent.querySelector(':scope > header > div');
    @if(auth()->user()?->position === 'Desk Officer' && request()->routeIs('desk-officer.cases.create'))
    headerRow = mainContent.querySelector('#officers-create-header > div > div') || headerRow;
    @endif
    if (!headerRow) {
        const clockOnlyHeader = document.createElement('div');
        clockOnlyHeader.className = 'portal-clock-only-header print:hidden';
        headerRow = document.createElement('div');
        headerRow.className = 'portal-clock-only-row';
        clockOnlyHeader.append(headerRow);
        mainContent.prepend(clockOnlyHeader);
    }

    if (headerRow.querySelector('[data-portal-clock]')) return;
    const clock = document.createElement('time');
    clock.className = 'portal-header-clock portal-page-clock';
    clock.dataset.portalClock = '';
    clock.setAttribute('aria-live', 'polite');
    const update = () => {
        const now = new Date();
        const day = new Intl.DateTimeFormat(undefined, { weekday: 'long' }).format(now);
        const date = new Intl.DateTimeFormat(undefined, { month: 'long', day: 'numeric', year: 'numeric' }).format(now);
        const time = new Intl.DateTimeFormat(undefined, { hour: 'numeric', minute: '2-digit' }).format(now);
        clock.dateTime = now.toISOString();
        clock.innerHTML = `<span class="portal-clock-segment portal-clock-calendar"><i class="far fa-calendar-alt" aria-hidden="true"></i><span class="portal-clock-day">${day}</span><span class="portal-clock-separator" aria-hidden="true">&bull;</span><span class="portal-clock-date">${date}</span></span><span class="portal-clock-divider" aria-hidden="true"></span><span class="portal-clock-segment portal-clock-time"><i class="far fa-clock" aria-hidden="true"></i><span>${time}</span></span>`;
    };
    const controls = headerRow.children.length > 1 ? headerRow.lastElementChild : null;
    controls ? headerRow.insertBefore(clock, controls) : headerRow.append(clock);

    const tabletLayout = window.matchMedia('(min-width: 641px) and (max-width: 1280px)');
    const phoneLayout = window.matchMedia('(max-width: 640px)');
    const isDeskOfficerHeader = document.body.classList.contains('desk-officer-header-theme');
    const isDepartmentAdminHeader = document.body.classList.contains('bcpc-admin-header-theme')
        || document.body.classList.contains('swd-admin-header-theme');
    const supportsTabletHeader = document.body.classList.contains('desk-officer-header-theme')
        || document.body.classList.contains('bcpc-admin-header-theme')
        || document.body.classList.contains('swd-admin-header-theme');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const roleBadge = document.querySelector('.desk-officer-tablet-role-badge, .department-admin-tablet-role-badge');
    const clockParent = clock.parentElement;
    const clockNextSibling = clock.nextSibling;
    const mobileMenuParent = mobileMenuToggle?.parentElement;
    const mobileMenuNextSibling = mobileMenuToggle?.nextSibling;
    const roleBadgeParent = roleBadge?.parentElement;
    const roleBadgeNextSibling = roleBadge?.nextSibling;

    const restoreElement = (element, parent, nextSibling) => {
        if (!element || !parent) return;
        parent.insertBefore(element, nextSibling?.parentNode === parent ? nextSibling : null);
    };

    const syncTabletHeader = () => {
        if ((supportsTabletHeader && tabletLayout.matches) || ((isDeskOfficerHeader || isDepartmentAdminHeader) && phoneLayout.matches)) {
            clock.hidden = true;
            if (mobileMenuToggle) headerRow.insertBefore(mobileMenuToggle, headerRow.firstElementChild);
            headerRow.classList.add('tablet-header-row');
            if (roleBadge && controls) controls.append(roleBadge);
            return;
        }

        clock.hidden = false;
        headerRow.classList.remove('tablet-header-row');
        restoreElement(clock, clockParent, clockNextSibling);
        restoreElement(mobileMenuToggle, mobileMenuParent, mobileMenuNextSibling);
        restoreElement(roleBadge, roleBadgeParent, roleBadgeNextSibling);
    };

    tabletLayout.addEventListener('change', syncTabletHeader);
    phoneLayout.addEventListener('change', syncTabletHeader);
    syncTabletHeader();
    update();
    window.setInterval(update, 1000);
});
</script>
