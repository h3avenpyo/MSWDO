/**
 * Financial Assistance Step 1 Module JavaScript
 */

/**
 * Phones use a drawer; tablets and desktops use a persistent collapsible rail.
 */
function isFinancialSidebarDrawer() {
    return window.matchMedia('(max-width: 767.98px)').matches;
}

function toggleSidebar() {
    if (!isFinancialSidebarDrawer()) return;
    const sidebar = document.getElementById('sidebar');
    setFinancialSidebarDrawer(!sidebar?.classList.contains('show'));
}

function setFinancialSidebarDrawer(open, restoreFocus = true) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const trigger = document.querySelector('.top-navbar button[onclick="toggleSidebar()"]');
    const isMobile = isFinancialSidebarDrawer();
    const isOpen = isMobile && open;
    const wasOpen = sidebar?.classList.contains('show');
    sidebar?.classList.toggle('show', isOpen);
    overlay?.classList.toggle('active', isOpen);
    document.body.classList.toggle('financial-sidebar-open', isOpen);
    trigger?.setAttribute('aria-expanded', String(isOpen));
    if (sidebar) {
        sidebar.inert = isMobile && !isOpen;
        sidebar.setAttribute('aria-hidden', String(isMobile && !isOpen));
    }
    const main = document.querySelector('.main-content');
    if (main) main.inert = isOpen;
    if (isOpen) document.getElementById('sidebarCollapseBtn')?.focus();
    else if (wasOpen && restoreFocus && isMobile) trigger?.focus();
}

/**
 * Desktop collapsible sidebar toggle (contracts to icons-only / expands to full width)
 * @param {boolean|null} forceState Optional explicit state to set (true = collapsed, false = expanded)
 */
function toggleSidebarCollapse(forceState = null, persist = true) {
    if (isFinancialSidebarDrawer()) {
        setFinancialSidebarDrawer(false);
        return;
    }
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');

    // Determine new state
    const currentState = document.body.classList.contains('sidebar-collapsed') ||
        document.documentElement.classList.contains('sidebar-collapsed') ||
        (sidebar && sidebar.classList.contains('collapsed'));

    const willCollapse = forceState !== null ? forceState : !currentState;

    if (willCollapse) {
        document.body.classList.add('sidebar-collapsed');
        document.documentElement.classList.add('sidebar-collapsed');
        if (sidebar) sidebar.classList.add('collapsed');
        if (collapseBtn) {
            collapseBtn.setAttribute('title', 'Expand sidebar');
            collapseBtn.setAttribute('aria-label', 'Expand sidebar');
            const icon = collapseBtn.querySelector('.toggle-icon');
            if (icon) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            }
        }
        try {
            if (persist) localStorage.setItem('mswdo_financial_sidebar_collapsed', 'true');
        } catch (e) { }
    } else {
        document.body.classList.remove('sidebar-collapsed');
        document.documentElement.classList.remove('sidebar-collapsed');
        if (sidebar) sidebar.classList.remove('collapsed');
        if (collapseBtn) {
            collapseBtn.setAttribute('title', 'Collapse sidebar');
            collapseBtn.setAttribute('aria-label', 'Collapse sidebar');
            const icon = collapseBtn.querySelector('.toggle-icon');
            if (icon) {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
            }
        }
        try {
            if (persist) localStorage.setItem('mswdo_financial_sidebar_collapsed', 'false');
        } catch (e) { }
    }
}

/**
 * Handle brand icon click: if collapsed, expand the sidebar
 */
function handleBrandClick() {
    const isCollapsed = document.body.classList.contains('sidebar-collapsed') ||
        document.documentElement.classList.contains('sidebar-collapsed');
    if (isCollapsed && !isFinancialSidebarDrawer()) {
        toggleSidebarCollapse(false);
    }
}

/**
 * Toggle sidebar dropdown submenu (e.g., Step 1 Intake collapsible menu)
 */
function toggleSidebarDropdown(el, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const isCollapsed = document.body.classList.contains('sidebar-collapsed') ||
        document.documentElement.classList.contains('sidebar-collapsed');

    // If sidebar is collapsed on desktop, expand it first so user sees the submenu
    if (isCollapsed && !isFinancialSidebarDrawer()) {
        toggleSidebarCollapse(false);
    }

    const parentLi = el.closest('.sidebar-dropdown');
    if (parentLi) {
        parentLi.classList.toggle('open');
        el.setAttribute('aria-expanded', String(parentLi.classList.contains('open')));
    }
}

function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    const el = document.getElementById('currentDateTime');
    if (el) {
        el.textContent = now.toLocaleDateString('en-US', options);
    }
}

function confirmLogout(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Logout',
        html: '<svg class="logout-modal-symbol" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M12 5l7 7-7 7v-4H7V9h5V5Z" fill="currentColor"/></svg><p class="logout-modal-message">Are you sure you want to log out?</p>',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonColor: '#b11116',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, log me out',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-4 shadow-lg swal2-logout-popup app-logout-modal',
            confirmButton: 'swal2-logout-confirm-btn fw-bold text-white',
            cancelButton: 'swal2-logout-cancel-btn text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    updateDateTime();
    setInterval(updateDateTime, 60000);

    const sidebar = document.getElementById('sidebar');
    const trigger = document.querySelector('.top-navbar button[onclick="toggleSidebar()"]');
    const drawerViewport = window.matchMedia('(max-width: 767.98px)');
    const tabletViewport = window.matchMedia('(min-width: 768px) and (max-width: 1199.98px)');
    trigger?.setAttribute('aria-controls', 'sidebar');
    const syncSidebarLayout = () => {
        setFinancialSidebarDrawer(false, false);
        if (isFinancialSidebarDrawer()) {
            // Desktop preference stays in storage; it must not hide drawer submenus.
            document.documentElement.classList.remove('sidebar-collapsed');
            document.body.classList.remove('sidebar-collapsed');
            sidebar?.classList.remove('collapsed');
            const closeButton = document.getElementById('sidebarCollapseBtn');
            closeButton?.setAttribute('title', 'Close sidebar');
            closeButton?.setAttribute('aria-label', 'Close sidebar');
            const closeIcon = closeButton?.querySelector('.toggle-icon');
            closeIcon?.classList.remove('fa-chevron-right', 'fa-xmark');
            closeIcon?.classList.add('fa-chevron-left');
        } else {
            document.getElementById('sidebarCollapseBtn')?.querySelector('.toggle-icon')?.classList.remove('fa-xmark');
            let stored = null;
            try {
                stored = localStorage.getItem('mswdo_financial_sidebar_collapsed');
            } catch (e) { }
            toggleSidebarCollapse(stored === 'true' || (stored === null && tabletViewport.matches), false);
        }
    };
    syncSidebarLayout();
    drawerViewport.addEventListener('change', syncSidebarLayout);
    tabletViewport.addEventListener('change', syncSidebarLayout);
    sidebar?.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        const href = link?.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
            setFinancialSidebarDrawer(false);
        }
    });
    sidebar?.querySelectorAll('.dropdown-toggle-btn').forEach((button) => {
        button.setAttribute('aria-expanded', String(button.closest('.sidebar-dropdown').classList.contains('open')));
    });

    // Keyboard shortcut for toggling sidebar: Alt + S or Ctrl + [
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar?.classList.contains('show')) {
            setFinancialSidebarDrawer(false);
        }
        if (e.key === 'Tab' && sidebar?.classList.contains('show')) {
            const items = [...sidebar.querySelectorAll('a[href], button:not([disabled])')]
                .filter((item) => item.getClientRects().length > 0);
            const first = items[0];
            const last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last?.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first?.focus();
            }
        }
        if ((e.altKey && (e.key === 's' || e.key === 'S')) || (e.ctrlKey && e.key === '[')) {
            e.preventDefault();
            if (!isFinancialSidebarDrawer()) {
                toggleSidebarCollapse();
            } else {
                toggleSidebar();
            }
        }
    });
});
