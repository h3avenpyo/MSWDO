/**
 * Financial Assistance Step 1 Module JavaScript
 */

/**
 * Mobile drawer toggle (slides in/out from left on screens < 1024px)
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.toggle('show');
    if (overlay) overlay.classList.toggle('active');
}

/**
 * Desktop collapsible sidebar toggle (contracts to icons-only / expands to full width)
 * @param {boolean|null} forceState Optional explicit state to set (true = collapsed, false = expanded)
 */
function toggleSidebarCollapse(forceState = null) {
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
            localStorage.setItem('mswdo_financial_sidebar_collapsed', 'true');
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
            localStorage.setItem('mswdo_financial_sidebar_collapsed', 'false');
        } catch (e) { }
    }
}

/**
 * Handle brand icon click: if collapsed, expand the sidebar
 */
function handleBrandClick() {
    const isCollapsed = document.body.classList.contains('sidebar-collapsed') ||
        document.documentElement.classList.contains('sidebar-collapsed');
    if (isCollapsed && window.innerWidth >= 1024) {
        toggleSidebarCollapse(false);
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
        title: 'Are you sure?',
        text: 'Do you really want to log out?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1A237E',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, log out',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-4 shadow-lg'
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

    // Initialize sidebar collapse state from localStorage
    try {
        const savedCollapsed = localStorage.getItem('mswdo_financial_sidebar_collapsed') === 'true';
        if (savedCollapsed && window.innerWidth >= 1024) {
            toggleSidebarCollapse(true);
        } else {
            // Ensure classes match expanded state if not saved as collapsed
            if (window.innerWidth >= 1024 && !savedCollapsed) {
                toggleSidebarCollapse(false);
            }
        }
    } catch (e) { }

    // Keyboard shortcut for toggling sidebar: Alt + S or Ctrl + [
    document.addEventListener('keydown', function (e) {
        if ((e.altKey && (e.key === 's' || e.key === 'S')) || (e.ctrlKey && e.key === '[')) {
            e.preventDefault();
            if (window.innerWidth >= 1024) {
                toggleSidebarCollapse();
            } else {
                toggleSidebar();
            }
        }
    });
});
