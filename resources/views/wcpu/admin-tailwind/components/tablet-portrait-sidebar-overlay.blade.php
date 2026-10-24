<style>
    /* Tablet portrait sidebar: expand over the content while keeping the rail's space. */
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        html body.portal-ui.portal-ui > aside#sidebar {
            position: fixed !important;
            inset: 0 auto 0 0 !important;
            display: flex !important;
            flex-direction: column !important;
            width: 18rem !important;
            min-width: 4.5rem !important;
            max-width: 18rem !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: hidden !important;
            border-radius: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            z-index: 60 !important;
            transition: width .3s ease !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) {
            width: 4.5rem !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar > .sidebar-header {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: flex-start !important;
            box-sizing: border-box;
            height: 7rem !important;
            min-height: 7rem !important;
            flex: 0 0 7rem !important;
            padding: 1rem .75rem 0 !important;
        }
        @if(request()->routeIs('case-profiles.index', 'cases.show', 'admin-barangay-cases', 'admin.barangay-cases.show', 'admin-add-new-case', 'admin.users.index', 'admin.users.create', 'admin.users.logs', 'admin.referrals', 'admin.referrals.index', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show', 'admin.quarterly-reports.edit', 'admin.calendar.index', 'admin.referrals.archived', 'admin.users.archived', 'admin.bcpc-case-monitoring.archived') || (request()->routeIs('admin.bcpc-case-monitoring.edit') && !request()->has('add')) || (request()->routeIs('admin.quarterly-reports.archived') && in_array(request('section'), ['barangay-cases', 'quarterly'], true)))
        html body.portal-ui.portal-ui > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) > .sidebar-header {
            height: 5.5rem !important;
            min-height: 5.5rem !important;
            flex-basis: 5.5rem !important;
            align-items: center !important;
            padding: 1.25rem .75rem !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) ~ #sidebarToggleBtn {
            top: 2.75rem !important;
            @if(request()->routeIs('cases.show', 'admin.quarterly-reports.show'))
            left: 18rem !important;
            transform: translate(-50%, -50%) !important;
            @else
            left: calc(18rem - .75rem) !important;
            transform: translateY(-50%) !important;
            @endif
        }
        @endif
        html body.portal-ui.portal-ui > aside#sidebar > .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            min-width: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar > nav.sidebar-menu {
            flex: 1 1 0 !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overscroll-behavior-y: contain;
        }
        html body.portal-ui.portal-ui > aside#sidebar > div.sidebar-menu {
            flex: 0 0 auto !important;
            height: auto !important;
            margin-top: auto !important;
            overflow: visible !important;
            padding-bottom: max(1rem, env(safe-area-inset-bottom)) !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) .sidebar-text {
            display: inline-block !important;
            max-width: none !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) :is(.sidebar-link, nav a, nav button) {
            justify-content: flex-start !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) nav button.sidebar-link {
            justify-content: space-between !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) .sidebar-chevron {
            display: inline-block !important;
        }
        html body.portal-ui.portal-ui > #sidebarToggleBtn {
            display: flex !important;
            position: fixed !important;
            left: 18rem !important;
            top: 2.5rem !important;
            transform: translate(-50%, -50%) !important;
            z-index: 70 !important;
            transition: left .3s ease, top .3s ease !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) ~ #sidebarToggleBtn {
            left: 2.25rem !important;
            top: 5.25rem !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar ~ #mainContent {
            margin-left: 4.5rem !important;
            width: calc(100% - 4.5rem) !important;
        }
    }
</style>
