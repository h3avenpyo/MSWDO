<style>
@media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
    html body.portal-ui #sidebar {
        inset: 0 auto 0 0 !important;
        width: 18rem !important;
        min-width: 18rem !important;
        max-width: 18rem !important;
        height: 100dvh !important;
        max-height: none !important;
        visibility: visible !important;
        opacity: 1 !important;
        transform: none !important;
        border-radius: 0 !important;
        background: #B11116 !important;
        transition: width .3s ease !important;
    }
    html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) {
        width: 4.5rem !important;
        min-width: 4.5rem !important;
        max-width: 4.5rem !important;
    }
    html body.portal-ui #sidebar .sidebar-header {
        display: flex !important;
        min-height: 5.5rem;
        padding-inline: .75rem !important;
        align-items: center;
    }
    html body.portal-ui #sidebar .sidebar-header img {
        width: 3rem !important;
        min-width: 3rem !important;
        height: 3rem !important;
        flex-shrink: 0 !important;
    }
    html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-text { display: none !important; }
    html body.portal-ui #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) .sidebar-text {
        opacity: 1 !important;
        max-width: none !important;
    }
    html body.portal-ui #sidebarToggleBtn {
        display: none !important;
        position: fixed !important;
        top: 2.75rem !important;
        left: calc(18rem - .75rem) !important;
        transform: translateY(-50%) !important;
        z-index: 50 !important;
    }
    html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) ~ #sidebarToggleBtn { left: calc(4.5rem - .75rem) !important; }
    html body.portal-ui #sidebar ~ #mainContent {
        width: calc(100% - 18rem) !important;
        margin-left: 18rem !important;
        min-width: 0 !important;
    }
    html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) ~ #mainContent {
        width: calc(100% - 4.5rem) !important;
        margin-left: 4.5rem !important;
    }
    html body.portal-ui :is(#mobileTopBar, #sidebarOverlay, #sidebarCloseBtn, #mobileMenuToggle, #dashboardMenuToggle) { display: none !important; }
}
</style>
