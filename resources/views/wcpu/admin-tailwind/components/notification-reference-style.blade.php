<style>
@if($notificationMobileOnly ?? false)
@media (max-width: 47.9375rem) {
@endif
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown {
        width: 20rem !important;
        max-width: calc(100vw - 1rem) !important;
        max-height: min(29.625rem, calc(100dvh - 6rem)) !important;
        background: #fff !important;
        border: 1px solid #dbe1e8 !important;
        border-radius: .75rem !important;
        box-shadow: 0 2px 6px rgb(0 0 0 / 5%) !important;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown > div:first-child {
        flex-shrink: 0;
        min-height: 3.5rem;
        padding: 1rem 1.5rem !important;
        background: #c41e3a !important;
        color: #fff !important;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown h4 { color: #fff !important; white-space: nowrap; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationReadButton ?? '#markAllReadBtn' }} {
        min-width: 0 !important;
        min-height: 0 !important;
        padding: .25rem .625rem !important;
        background: #fff !important;
        color: #c41e3a !important;
        border: 0 !important;
        border-radius: .625rem !important;
        font-size: .75rem !important;
        white-space: nowrap;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationItemSelector ?? '.notification-item' }} {
        min-height: 5.75rem;
        align-items: center !important;
        gap: 1rem !important;
        padding: 1.5rem !important;
        background: #fff !important;
        border-color: #f3f4f6 !important;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationItemSelector ?? '.notification-item' }}:hover { background: #f9fafb !important; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationItemSelector ?? '.notification-item' }} > div:first-child {
        background: #ffe4e8 !important;
        color: #d4143f !important;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationItemSelector ?? '.notification-item' }} > div:first-child > i { color: #d4143f !important; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationItemSelector ?? '.notification-item' }} p {
        color: #284c6b !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} {{ $notificationItemSelector ?? '.notification-item' }} span { color: #ff2548 !important; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown > div:last-child {
        flex-shrink: 0;
        padding: .75rem 1.5rem !important;
        background: #f9fafb !important;
        border-color: #f3f4f6 !important;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown-list { scrollbar-width: auto; scrollbar-color: #929292 #fff; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown-list::-webkit-scrollbar { width: .75rem; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown-list::-webkit-scrollbar-track { background: #fff; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} .notification-dropdown-list::-webkit-scrollbar-thumb { background: #929292; border: 2px solid #fff; border-radius: 1rem; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} button[aria-label="Notifications"] {
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
        color: #fff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        align-self: center !important;
        position: relative !important;
        margin: 0 !important;
        line-height: 1 !important;
    }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} button[aria-label="Notifications"]:hover { background: #8f0202 !important; }
    {{ $notificationStyleScope ?? 'html body.portal-ui #mainContent#mainContent [data-admin-notification-reference]' }} button[aria-label="Notifications"] > i {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 1rem !important;
        line-height: 1 !important;
        color: #fff !important;
        vertical-align: middle !important;
    }
@if($notificationMobileOnly ?? false)
}
@endif
</style>
