<style>
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) {
        html body.portal-ui.portal-ui > aside#sidebar {
            width: 18rem !important;
            min-width: 4.5rem !important;
            max-width: 18rem !important;
            transition: width .3s cubic-bezier(.22, .61, .36, 1) !important;
        }
        html body.portal-ui.portal-ui > aside#sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) { width: 4.5rem !important; }
        html body.portal-ui.portal-ui > #sidebarToggleBtn { transition: left .3s cubic-bezier(.22, .61, .36, 1) !important; }
        html body.portal-ui.portal-ui > #mainContent { transition: margin-left .3s cubic-bezier(.22, .61, .36, 1), width .3s cubic-bezier(.22, .61, .36, 1) !important; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) and (prefers-reduced-motion: reduce) {
        html body.portal-ui.portal-ui > :is(aside#sidebar, #sidebarToggleBtn, #mainContent) { transition: none !important; }
    }
</style>
