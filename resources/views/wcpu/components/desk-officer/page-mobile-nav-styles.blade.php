<style>
    /* Shared Desk Officer page navbar + sidebar for phones and tablets,
       matching /desk-officer/bcpc-dashboard. Applied via body.desk-officer-page-mobile-nav. */
    @media (max-width: 47.9375rem) {
        body.desk-officer-page-mobile-nav #mobileTopBar,
        body.desk-officer-page-mobile-nav #mobileMenuToggle { display: none !important; }

        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 30 !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-bottom: 1rem !important;
            padding-left: max(.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div > div:first-child {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: .625rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            margin: 0 !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div > div:first-child > div {
            width: auto !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            margin: 0 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div > div:first-child :is(h1, h2, h3, h4, p, span, strong) {
            color: #ffffff !important;
            margin: 0 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav.desk-officer-page-mobile-nav #mainContent > header.portal-page-header,
        html body.portal-ui.desk-officer-page-mobile-nav.desk-officer-page-mobile-nav #mainContent > header.portal-page-header :is(h1, h2, h3, h4, h5, h6, p, span, strong, small, label):not(.notification-dropdown *):not(.notification-badge):not([data-unread-badge]):not(#notifications-unread-count) {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header #pageMobileMenuToggle {
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: 1 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header #pageMobileMenuToggle > i {
            display: inline-flex !important;
            font-size: 1.25rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            margin: 0 !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div > div:nth-child(2) {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div > div:nth-child(2) > div.relative {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
            background: var(--mswdo-profile, #BC0202) !important;
            border: 1px solid #8f0202 !important;
            box-shadow: 0 2px 5px rgba(188, 2, 2, .18) !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header button[aria-label="Notifications"] > i {
            display: inline-flex !important;
            font-size: 1.05rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            margin: 0 !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -0.25rem !important;
            right: -0.25rem !important;
            font-size: 0.75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 9999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
            border: 2px solid #C41E3A !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav .notification-dropdown {
            color: #1f2937;
            position: fixed !important;
            top: calc(max(1rem, env(safe-area-inset-top)) + 3.75rem) !important;
            left: 1rem !important;
            right: 1rem !important;
            width: auto !important;
            max-width: calc(100vw - 2rem) !important;
            z-index: 1000 !important;
        }

        /* Sidebar dropdown drawer */
        body.desk-officer-page-mobile-nav #sidebar {
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: auto !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            max-height: calc(100vh - 60px) !important;
            overflow-y: auto !important;
            z-index: 999 !important;
            border-radius: 0 0 14px 14px !important;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            background: #C41E3A !important;
            background-image: none !important;
        }
        body.desk-officer-page-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        body.desk-officer-page-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        body.desk-officer-page-mobile-nav #sidebar .sidebar-header { display: none !important; }
        body.desk-officer-page-mobile-nav #sidebar .sidebar-text { opacity: 1 !important; max-width: none !important; }
        body.desk-officer-page-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        body.desk-officer-page-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        body.desk-officer-page-mobile-nav #sidebar #sidebarCloseBtn { display: none !important; }
        body.desk-officer-page-mobile-nav #sidebarOverlay { display: none !important; pointer-events: none !important; }
        body.portal-ui.desk-officer-page-mobile-nav #sidebar nav :is(a, button),
        body.portal-ui.desk-officer-page-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        body.portal-ui.desk-officer-page-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #sidebar nav :is(a, button):hover,
        body.portal-ui.desk-officer-page-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        body.portal-ui.desk-officer-page-mobile-nav #sidebar .sidebar-link:is([class~="bg-purple-600"], [class~="bg-purple-700/70"]),
        body.portal-ui.desk-officer-page-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
        body.desk-officer-page-mobile-nav #mainContent {
            width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
        }
    }

    /* Tablet view: collapsed rail, full-width content and clean white header. */
    @media (min-width: 48rem) and (max-width: 80rem) {
        body.desk-officer-page-mobile-nav #mobileTopBar,
        body.desk-officer-page-mobile-nav #mobileMenuToggle,
        body.desk-officer-page-mobile-nav #pageMobileMenuToggle,
        body.desk-officer-page-mobile-nav #sidebarCloseBtn,
        body.desk-officer-page-mobile-nav #sidebarToggleBtn { display: none !important; }
        body.desk-officer-page-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-page-mobile-nav #mainContent > header.portal-page-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        body.desk-officer-page-mobile-nav #mainContent > header.portal-page-header > div {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            min-height: 4.25rem !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }
        body.desk-officer-page-mobile-nav #sidebar {
            inset: 0 auto 0 0 !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            right: auto !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            background: #C41E3A !important;
            border-radius: 0 !important;
            z-index: 40 !important;
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
            transition: width 0.3s ease !important;
        }
        body.desk-officer-page-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 4.25rem !important;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        body.desk-officer-page-mobile-nav #sidebar:not(.sidebar-expanded) .sidebar-text { display: none !important; }
        body.desk-officer-page-mobile-nav #sidebar ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            min-width: 0 !important;
            margin-left: 4.5rem !important;
        }
    }
</style>
