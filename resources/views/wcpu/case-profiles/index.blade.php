@extends($isAdmin ? 'layouts.admin-tailwind' : 'layouts.desk-officer')

@section('title', 'Victim and Perpetrator Profiles')

@php
    $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'VAWC' || auth()->user()?->isMultiCaseDepartment());
@endphp

@push('styles')
<style>
    .guardian-dialog::backdrop {
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(4px) !important;
    }
    @if(auth()->user()?->isVawcAdmin() || $isSwdDeskOfficer)
    html { scrollbar-gutter: stable; }
    body.swal2-shown { padding-right: 0 !important; }
    @endif
    @media (max-width: 47.9375rem) {
        .case-profiles-table-view { display: none !important; }

        #case-profiles-content {
            width: 100% !important;
            max-width: 100vw !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            overflow-x: hidden !important;
            padding-top: max(0.75rem, env(safe-area-inset-top)) !important;
            padding-right: max(0.75rem, env(safe-area-inset-right)) !important;
            padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
            padding-left: max(0.75rem, env(safe-area-inset-left)) !important;
        }

        #case-profiles-results {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        #case-profiles-results > section {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }

        .case-profiles-mobile-cards {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.75rem !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            padding: 0.75rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card {
            position: relative;
            display: flex !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 9.75rem;
            height: auto;
            flex-direction: column;
            box-sizing: border-box !important;
            overflow: hidden !important;
            padding: 1rem !important;
            margin: 0 !important;
        }

        .case-profiles-mobile-cards .case-profile-card * {
            box-sizing: border-box !important;
        }

        .case-profiles-mobile-cards .case-profile-card :is(h1, h2, h3, h4, p, a, span, dt, dd, div) {
            min-width: 0;
            overflow-wrap: break-word !important;
            word-break: normal !important;
        }

        .case-profiles-mobile-cards .case-profile-card .grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.75rem !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        .case-profiles-mobile-cards .case-profile-card .grid > * {
            min-width: 0 !important;
            max-width: 100% !important;
        }

        .case-profiles-mobile-cards .case-profile-card-heading {
            min-height: 0;
            margin-bottom: 0 !important;
        }

        .case-profiles-mobile-cards .case-profile-card-identity {
            display: none !important;
        }

        .case-profiles-mobile-cards .case-profile-card-count {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.625rem !important;
            font-size: 0.6875rem !important;
            line-height: 1rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card-details {
            display: block !important;
            width: 100% !important;
            margin-bottom: 0.875rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card-details > :not(.case-profile-card-cases) {
            display: none !important;
        }

        .case-profiles-mobile-cards .case-profile-card-cases {
            display: block !important;
            padding-right: 4.5rem;
        }

        .case-profiles-mobile-cards .case-profile-card-cases > p {
            margin-bottom: 0.375rem !important;
            font-size: 0.6875rem !important;
            line-height: 1rem;
        }

        .case-profiles-mobile-cards .case-profile-card-cases a {
            font-size: 0.8125rem !important;
            line-height: 1.25rem;
        }

        .case-profiles-mobile-cards .case-profile-card-guardian {
            margin-top: auto;
            padding-top: 0.75rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card-guardian > span {
            font-size: 0.8125rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card-action {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 2.75rem !important;
            min-width: 2.75rem !important;
            height: 2.75rem !important;
            min-height: 2.75rem !important;
            flex: 0 0 2.75rem !important;
            padding: 0 !important;
        }

        #case-profiles-filter-form {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            gap: 0.75rem !important;
            padding: 0.75rem !important;
        }

        .case-profiles-search-age-row {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        .case-profiles-search-control,
        .case-profiles-search-age-row .case-profiles-age-control {
            width: 100% !important;
            max-width: 100% !important;
        }

        #case-profiles-filter-form > button,
        #case-profiles-filter-form > .case-profiles-clear-button {
            width: 100% !important;
        }

        .case-profiles-department-switcher {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.5rem !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        .case-profiles-department-switcher > a,
        #case-profiles-filter-form :is(input, button, a) {
            min-height: 2.75rem;
        }

        .case-profiles-department-switcher > a,
        #case-profiles-filter-form .case-profiles-clear-button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.5rem !important;
            text-align: center !important;
        }

        .case-profiles-mobile-cards [data-pagination-label] {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            padding: 0.75rem 0.25rem !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 0.5rem !important;
            text-align: center !important;
        }

        .case-profiles-mobile-cards [data-pagination-label] > div {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            justify-content: center !important;
            text-align: center !important;
            flex-wrap: wrap !important;
        }

        .case-profiles-mobile-cards [data-pagination-label] .flex.items-center.gap-2 {
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 0.25rem !important;
            max-width: 100% !important;
        }
    }

    /* Tablet profile cards: reuse the mobile card content in a compact 2 x 2 layout. */
    @media (min-width: 48rem) and (max-width: 80rem) {
        .case-profiles-table-view {
            display: none !important;
        }

        .case-profiles-mobile-cards {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem !important;
            width: 100%;
            min-width: 0;
            padding: 1rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card {
            position: relative;
            display: flex !important;
            min-width: 0;
            min-height: 9.5rem;
            height: 100%;
            flex-direction: column;
            padding: 0.875rem !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
        }

        .case-profile-card-heading {
            min-height: 0;
            margin-bottom: 0 !important;
        }

        .case-profile-card-identity {
            display: none !important;
        }

        .case-profile-card-count {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.625rem !important;
            line-height: 1rem !important;
        }

        .case-profile-card-details {
            display: block !important;
            width: 100%;
            margin-bottom: 0.75rem !important;
        }

        .case-profile-card-details > :not(.case-profile-card-cases) {
            display: none !important;
        }

        .case-profile-card-cases {
            display: block !important;
            padding-right: 4.25rem;
        }

        .case-profile-card-cases > p {
            margin-bottom: 0.25rem !important;
            font-size: 0.6875rem !important;
            line-height: 1rem;
        }

        .case-profile-card-cases a {
            font-size: 0.75rem !important;
            line-height: 1.125rem;
        }

        .case-profile-card-guardian {
            margin-top: auto;
            padding-top: 0.75rem !important;
        }

        .case-profile-card-guardian > span {
            font-size: 0.75rem !important;
        }

        .case-profiles-mobile-cards .case-profile-card-action {
            display: inline-flex !important;
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
            min-height: 2.25rem !important;
            flex: 0 0 2.25rem !important;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            border-radius: 0.5rem !important;
        }

        .case-profiles-mobile-cards > .case-profiles-pagination-container,
        .case-profiles-mobile-cards > .case-profile-empty-state {
            grid-column: 1 / -1;
        }

        .case-profiles-mobile-cards > .case-profile-empty-state:not(.hidden) {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
            width: 100%;
            margin: 0 !important;
            padding: 2rem 1rem;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .case-profiles-mobile-cards [data-pagination-label] {
            display: flex !important;
            width: 100%;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 0.75rem;
            padding: 0.75rem 0.125rem 0 !important;
        }

        .case-profiles-mobile-cards [data-pagination-label] > div:last-child {
            flex-wrap: nowrap !important;
            gap: 0.25rem !important;
        }

        .case-profiles-mobile-cards [data-pagination-label] :is(a, span).w-9 {
            width: 2rem !important;
            height: 2rem !important;
            font-size: 0.6875rem !important;
            border-radius: 0.5rem !important;
        }
    }

    /* Give the search field its own row before the tablet portrait actions. */
    @media (min-width: 48rem) and (max-width: 80rem) and (orientation: portrait) {
        body.case-profiles-desk-mobile-nav #case-profiles-filter-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            align-items: center;
            gap: .75rem;
        }

        body.case-profiles-desk-mobile-nav #case-profiles-filter-form .case-profiles-search-age-row {
            display: grid;
            grid-column: 1 / -1;
            grid-template-columns: minmax(0, 1fr) 7rem;
            width: 100%;
            min-width: 0;
            gap: .75rem;
        }

        body.case-profiles-desk-mobile-nav #case-profiles-filter-form :is(.case-profiles-search-control, .case-profiles-age-control) {
            width: 100%;
            min-width: 0;
        }

        body.case-profiles-desk-mobile-nav #case-profiles-filter-form :is(#profile-search-input, #profile-age-input) {
            font-size: .8125rem;
            line-height: 1.25rem;
        }

        body.case-profiles-desk-mobile-nav #case-profiles-filter-form .case-profiles-department-switcher {
            display: grid;
            grid-column: span 2;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            min-width: 0;
        }

        body.case-profiles-desk-mobile-nav #case-profiles-filter-form .case-profiles-department-switcher > a {
            text-align: center;
        }
    }

    @media (max-width: 26rem) {
        .case-profiles-search-age-row {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .case-profiles-search-age-row .case-profiles-age-control {
            width: 100% !important;
        }
    }
    @media (min-width: 37.5rem) and (max-width: 79.9375rem) and (orientation: portrait) {
        .case-profiles-table-view .case-profiles-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed;
        }
        .case-profiles-table-view .case-profiles-table :is(th, td) {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .case-profiles-table-view .case-profiles-table :is(th, td):nth-child(2) { width: 28%; }
        .case-profiles-table-view .case-profiles-table :is(th, td):nth-child(5) { width: 17%; }
        .case-profiles-table-view .case-profiles-table td:nth-child(5) > div { min-width: 0; }
    }
    @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment())
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        #case-profiles-filter-form .case-profiles-clear-button {
            display: inline-flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
    }
    @endif

    /* Dashboard-style red navbar for mobile/tablet, matching /admin-dashboard.
       The shared layouts keep their mobile top bar visible up to 80rem, so hide
       it across that whole range (otherwise its recolored dark red shows). */
    @media (max-width: 80rem) {
        html body.portal-ui #mobileTopBar {
            display: none !important;
        }
    }

    @media (max-width: 47.9375rem) {
        /* Mobile header: horizontal flex layout with hamburger on left, title/subtitle beside it, notification on right */
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mobileMenuToggle,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) .tablet-header-row > #mobileMenuToggle,
        body:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mobileMenuToggle,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebarCloseBtn,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header>div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:first-of-type,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header>div>div:first-of-type,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header .case-profiles-header-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:first-of-type>div,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header>div>div:first-of-type>div,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header .case-profiles-header-leading>div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
            font-size: 1.125rem !important;
            position: relative !important;
            left: 0 !important;
            margin-left: 0 !important;
            transform: none !important;
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
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
            font-size: inherit !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:last-child,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header>div>div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header button[aria-label="Notifications"],
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header button[aria-label="Notifications"]>i,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header button[aria-label="Notifications"]>i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #mainContent>header #notifications-unread-count,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }

        /* Match the multirole dashboard bell and badge in the mobile header. */
        html body.portal-ui.case-profiles-desk-mobile-nav #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"] {
            background: var(--mswdo-profile, #BC0202) !important;
            border: 1px solid #8f0202 !important;
            box-shadow: 0 2px 5px rgba(188, 2, 2, .18) !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        html body.portal-ui.case-profiles-desk-mobile-nav #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        html body.portal-ui.case-profiles-desk-mobile-nav #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"]>i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            color: #ffffff !important;
            vertical-align: middle !important;
        }

        html body.portal-ui.case-profiles-desk-mobile-nav #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] #notifications-unread-count {
            position: absolute !important;
            min-width: 1.25rem !important;
            padding: 0 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border: 2px solid #C41E3A !important;
            border-radius: 9999px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2) !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header div:has(> img[alt="User Profile"]),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar nav :is(a, button),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar nav :is(a, button):hover,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-link:is([class~="bg-purple-600"], [class~="bg-purple-700/70"]),
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar {
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

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar.sidebar-collapsed,
        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-header {
            display: none !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }

        html body.portal-ui:is(.case-profiles-desk-mobile-nav, .case-profiles-admin-mobile-nav) #sidebar #sidebarCloseBtn {
            display: none !important;
        }
    }

    /* Case Profiles tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #pageMobileMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header > div > div:first-of-type :is(h1, h2, h3) {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header > div > div:first-of-type p {
            color: #6b7280 !important;
        }
        html body.portal-ui #sidebar {
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
        html body.portal-ui #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui #sidebar .sidebar-header .sidebar-text {
            display: none !important;
            opacity: 0 !important;
        }
        html body.portal-ui #sidebar,
        html body.portal-ui #sidebar.sidebar-collapsed,
        html body.portal-ui #sidebar.sidebar-collapsed-init {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }
        html body.portal-ui #sidebar .sidebar-text,
        html body.portal-ui #sidebar.sidebar-collapsed .sidebar-text,
        html body.portal-ui #sidebar.sidebar-collapsed-init .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #sidebar .sidebar-menu,
        html body.portal-ui #sidebar.sidebar-collapsed .sidebar-menu,
        html body.portal-ui #sidebar.sidebar-collapsed-init .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui #sidebar .sidebar-link,
        html body.portal-ui #sidebar.sidebar-collapsed .sidebar-link,
        html body.portal-ui #sidebar.sidebar-collapsed-init .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-link i,
        html body.portal-ui #sidebar.sidebar-collapsed .sidebar-link i,
        html body.portal-ui #sidebar.sidebar-collapsed-init .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }
        html body.portal-ui #sidebar ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed-init ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            min-width: 0 !important;
        }
        html body.portal-ui #sidebarToggleBtn {
            display: none !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
        }
    }
</style>
@endpush

@section('content')
@if($isAdmin && auth()->user()?->isDepartmentAdmin())
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="case-profiles-header-leading flex items-center gap-3 min-w-0">
            <button id="dashboardMenuToggle" class="hidden" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Victim and Perpetrator Profiles</h2>
                <p class="text-sm text-gray-500 {{ auth()->user()?->isDepartmentAdmin() ? '' : 'hidden sm:block' }}">View and manage case profiles</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Victim and Perpetrator Profiles', 'subtitle' => 'View and manage case profiles'])
<div id="case-profiles-content" class="p-4 sm:p-6 lg:p-8 space-y-8">
    @unless(auth()->user()?->isDepartmentAdmin() || (auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Victim and Perpetrator Profiles</h1>
    </div>
    @endunless
    @if((auth()->user()?->isDepartmentAdmin() || !$isAdmin))
        <form id="case-profiles-filter-form" method="GET" action="{{ route('case-profiles.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-4 bg-white border border-gray-200 rounded-xl p-4">
            @if(!$isAdmin)
                <input type="hidden" name="type" value="{{ $profileDepartment }}">
            @endif
            <div class="case-profiles-search-age-row flex w-full items-center gap-2 sm:flex-1">
                <div class="case-profiles-search-control min-w-0 flex-1">
                    <span class="relative block w-full">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400" aria-hidden="true"></i>
                        <input type="search" id="profile-search-input" name="search" maxlength="255" value="{{ request('search') }}" placeholder="Search name or address" aria-label="Search profiles" class="block w-full border border-gray-300 rounded-lg py-2 pl-9 pr-3">
                    </span>
                </div>
                <span class="case-profiles-age-control relative block w-28 shrink-0">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400" aria-hidden="true"></i>
                    <input type="number" id="profile-age-input" name="age" min="0" max="120" value="{{ request('age') }}" placeholder="All ages" aria-label="Search profiles by age" class="block w-full border border-gray-300 rounded-lg py-2 pl-8 pr-3">
                </span>
            </div>
            <button class="px-4 py-2 bg-purple-700 text-white rounded-lg" type="submit">Filter</button>
            <a href="{{ route('case-profiles.index') }}" class="case-profiles-clear-button inline-flex items-center justify-center text-center px-4 py-2 border border-gray-300 rounded-lg">Clear</a>
            @if(!$isAdmin && auth()->user()?->isMultiCaseDepartment())
            <div class="case-profiles-department-switcher flex gap-2" aria-label="Profile department">
                @foreach(auth()->user()?->caseDepartments() ?? [] as $department)
                <a href="{{ route('case-profiles.index', ['type' => $department, 'age' => request('age'), 'search' => request('search')]) }}" class="px-4 py-2 rounded-lg {{ $profileDepartment === $department ? 'bg-purple-700 text-white' : 'bg-purple-50 text-purple-700' }}">{{ $department === 'VAWC' ? 'SWD' : $department }}</a>
                @endforeach
            </div>
            @endif
        </form>
    @endif
    <div id="case-profiles-results" class="space-y-8">
    @foreach(['Victim Profiles' => $victims, 'Perpetrator Profiles' => $perpetrators] as $heading => $profiles)
        @php
            $isVictim = $heading === 'Victim Profiles';
            $hasProfileFilter = request()->filled('search') || request()->filled('age');
            $hideEmptySwdProfileTable = auth()->user()?->defaultCaseDepartment() === 'VAWC'
                && $hasProfileFilter
                && $profiles->total() === 0;
            $hideEmptyBcpcProfileTable = auth()->user()?->defaultCaseDepartment() === 'BCPC'
                && $hasProfileFilter
                && $profiles->total() === 0;
        @endphp
        @if(!$hideEmptySwdProfileTable && !$hideEmptyBcpcProfileTable)
        <section data-profile-section="{{ $isVictim ? 'victims' : 'perpetrators' }}" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <h2 class="px-5 py-4 text-lg font-semibold text-purple-800">{{ $heading }}</h2>
            <div class="case-profiles-table-view">
            <div class="overflow-x-auto">
                <table class="case-profiles-table w-full min-w-[900px] table-fixed text-sm text-left">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 27%">
                        <col style="width: 10%">
                        <col style="width: 25%">
                        <col style="width: 20%">
                    </colgroup>
                    <thead class="bg-purple-700 text-white">
                        <tr>
                            @foreach(['Cases', 'Type of Case', 'Age', 'Address', 'Guardian Information'] as $column)
                                <th class="px-5 py-3 {{ $loop->last ? 'text-center align-middle' : '' }}">{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($profiles as $records)
                            @php $person = $records->first(); @endphp
                            <tr class="align-top hover:bg-purple-50" data-profile-name="{{ $isVictim ? $person->victim_name : $person->abuser_name }}" data-profile-age="{{ ($isVictim ? $person->victim_age : $person->abuser_age) ?? 'Not recorded' }}">
                                <td class="px-5 py-4">
                                    <details>
                                        <summary class="cursor-pointer text-purple-700">{{ $records->count() }} {{ $records->count() === 1 ? 'case' : 'cases' }}</summary>
                                        @foreach($records as $case)
                                            <a class="block mt-2 text-purple-700 underline break-words [overflow-wrap:anywhere]" href="{{ $isAdmin ? route('cases.show', ['id' => $case->id, 'from' => 'case-profiles']) : route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from' => in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'case-profiles' : null]) }}">{{ $case->case_number }}</a>
                                        @endforeach
                                    </details>
                                </td>
                                <td class="px-5 py-4 break-words">{{ $records->map(fn($case) => ($isSwdDeskOfficer && $case->type === 'VAWC' ? 'SWD' : $case->type) . ' — ' . ($case->caseType?->name ?? $case->abuse_type))->unique()->implode(', ') }}</td>
                                <td class="px-5 py-4">{{ ($isVictim ? $person->victim_age : $person->abuser_age) ?? 'Not recorded' }}</td>
                                <td class="px-5 py-4 break-words">{{ ($isVictim ? $person->victim_address : $person->abuser_address) ?: 'Not recorded' }}</td>
                                <td class="px-5 py-4 align-middle text-center">
                                    @php
                                        $guardians = $records->map(fn($case) => $isVictim
                                            ? [$case->guardian, $case->relationship_to_victim, $case->guardian_contact, $case->guardian_address]
                                            : [$case->guardian_abuser_name, $case->relationship_to_abuser, $case->guardian_abuser_contact, $case->guardian_abuser_address]
                                        )->filter(fn($info) => count(array_filter($info)) > 0)->unique(fn($info) => json_encode($info));
                                    @endphp
                                    @if($guardians->isNotEmpty())
                                        @php $guardianDialogId = 'guardian-' . ($isVictim ? 'victim-' : 'perpetrator-') . $person->id; @endphp
                                        <div class="flex min-h-10 items-center justify-center">
                                            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-purple-700 text-white hover:bg-purple-800 text-xs font-medium shadow-xs transition" aria-haspopup="dialog" aria-controls="{{ $guardianDialogId }}" onclick="document.getElementById('{{ $guardianDialogId }}').showModal()">
                                                <i class="fas fa-eye flex h-4 w-4 shrink-0 items-center justify-center leading-none text-xs" aria-hidden="true"></i>
                                                <span class="sr-only">View</span>
                                            </button>
                                        </div>
                                        <dialog id="{{ $guardianDialogId }}" aria-labelledby="{{ $guardianDialogId }}-title" class="guardian-dialog rounded-2xl p-0 shadow-2xl w-full max-w-lg text-gray-800 border border-gray-100 overflow-hidden backdrop:bg-slate-900/60 backdrop:backdrop-blur-xs">
                                            {{-- Modal Header --}}
                                            <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4 flex items-center justify-between text-white">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-white text-base">
                                                        <i class="fas fa-user-shield"></i>
                                                    </div>
                                                    <div>
                                                        <h3 id="{{ $guardianDialogId }}-title" class="text-base font-bold text-white leading-tight">Guardian Information</h3>
                                                        <p class="text-xs text-purple-200 mt-0.5">{{ $isVictim ? 'Victim' : 'Perpetrator' }}: <span class="text-white font-semibold">{{ $isVictim ? $person->victim_name : $person->abuser_name }}</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal Content --}}
                                            <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
                                                @foreach($guardians as $gIndex => $info)
                                                    <div class="bg-gray-50/80 border border-gray-200 rounded-xl p-4">
                                                        @if($guardians->count() > 1)
                                                            <div class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center gap-1.5">
                                                                <i class="fas fa-id-card"></i> Guardian #{{ $gIndex + 1 }}
                                                            </div>
                                                        @endif
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Guardian Name</span>
                                                                <span class="font-bold text-gray-900 break-words">{{ $info[0] ?: 'Not recorded' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Relationship</span>
                                                                <span class="font-medium text-gray-800 break-words">{{ $info[1] ?: 'Not recorded' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Contact Number</span>
                                                                <span class="font-medium text-gray-800 break-words">{{ $info[2] ?: 'Not recorded' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Address</span>
                                                                <span class="font-medium text-gray-800 break-words">{{ $info[3] ?: 'Not recorded' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Modal Footer --}}
                                            <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-200 flex justify-end">
                                                <button type="button" class="px-4 py-2 rounded-xl bg-purple-700 text-white text-xs font-semibold hover:bg-purple-800 transition shadow-xs" onclick="this.closest('dialog').close()">
                                                    Close
                                                </button>
                                            </div>
                                        </dialog>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No profiles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if((auth()->user()?->isDepartmentAdmin() || !$isAdmin) && $profiles->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing {{ $profiles->firstItem() ?? 0 }} - {{ $profiles->lastItem() ?? 0 }} of {{ $profiles->total() }} profiles
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $profiles->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $profiles->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </a>
                        @php
                            $current = $profiles->currentPage();
                            $last = $profiles->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                        @endphp
                        <div class="flex items-center gap-1">
                            @if($start > 1)
                                <a href="{{ $profiles->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">1</a>
                                @if($start > 2)
                                    <span class="px-2 text-gray-400">…</span>
                                @endif
                            @endif
                            @for($i = $start; $i <= $end; $i++)
                                <a href="{{ $profiles->url($i) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $current == $i ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">
                                    {{ $i }}
                                </a>
                            @endfor
                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="px-2 text-gray-400">…</span>
                                @endif
                                <a href="{{ $profiles->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">{{ $last }}</a>
                            @endif
                        </div>
                        <a href="{{ $profiles->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $profiles->currentPage() == $profiles->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </a>
                    </div>
                </div>
            @endif
            </div>

            <!-- Mobile profile cards -->
            <div class="case-profiles-mobile-cards hidden p-3">
                @forelse($profiles as $records)
                    @php
                        $person = $records->first();
                        $profileAge = ($isVictim ? $person->victim_age : $person->abuser_age) ?? 'Not recorded';
                        $guardians = $records->map(fn($case) => $isVictim
                            ? [$case->guardian, $case->relationship_to_victim, $case->guardian_contact, $case->guardian_address]
                            : [$case->guardian_abuser_name, $case->relationship_to_abuser, $case->guardian_abuser_contact, $case->guardian_abuser_address]
                        )->filter(fn($info) => count(array_filter($info)) > 0)->unique(fn($info) => json_encode($info));
                        $guardianDialogId = 'guardian-' . ($isVictim ? 'victim-' : 'perpetrator-') . $person->id;
                    @endphp
                    <article class="case-profile-card bg-white rounded-xl border border-gray-200 shadow-sm w-full max-w-full min-w-0 box-border overflow-hidden p-4" data-profile-age="{{ $profileAge }}">
                        <div class="case-profile-card-heading flex items-start justify-between gap-3 mb-4 min-w-0">
                            <div class="case-profile-card-identity flex-1 min-w-0">
                                <p class="text-xs text-gray-500 mb-1">{{ $isVictim ? 'Victim' : 'Perpetrator' }}</p>
                                <p class="font-semibold text-gray-800 break-words">{{ $isVictim ? $person->victim_name : $person->abuser_name }}</p>
                            </div>
                            <span class="case-profile-card-count shrink-0 rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700 whitespace-nowrap">{{ $records->count() }} {{ $records->count() === 1 ? 'case' : 'cases' }}</span>
                        </div>
                        <div class="case-profile-card-details grid grid-cols-2 gap-3 text-sm mb-4 w-full min-w-0">
                            <div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Age</p><p class="text-gray-800 break-words">{{ $profileAge }}</p></div>
                            <div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Address</p><p class="text-gray-800 break-words">{{ ($isVictim ? $person->victim_address : $person->abuser_address) ?: 'Not recorded' }}</p></div>
                            <div class="col-span-2 min-w-0"><p class="text-xs text-gray-500 mb-1">Type of Case</p><p class="text-gray-800 break-words">{{ $records->map(fn($case) => ($isSwdDeskOfficer && $case->type === 'VAWC' ? 'SWD' : $case->type) . ' - ' . ($case->caseType?->name ?? $case->abuse_type))->unique()->implode(', ') }}</p></div>
                            <div class="case-profile-card-cases col-span-2 min-w-0"><p class="text-xs text-gray-500 mb-1">Cases</p><div class="flex flex-wrap gap-x-3 gap-y-1 min-w-0">@foreach($records as $case)<a class="text-sm text-purple-700 underline break-words" href="{{ $isAdmin ? route('cases.show', ['id' => $case->id, 'from' => 'case-profiles']) : route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from' => in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'case-profiles' : null]) }}">{{ $case->case_number }}</a>@endforeach</div></div>
                        </div>
                        @if($guardians->isNotEmpty())
                        <div class="case-profile-card-guardian flex items-center justify-between gap-3 pt-3 border-t border-gray-100 min-w-0"><span class="text-sm font-medium text-gray-700 flex-1 min-w-0 break-words">Guardian Information</span><button type="button" class="case-profile-card-action shrink-0 rounded-lg bg-purple-700 text-white hover:bg-purple-800 transition" aria-label="View guardian information" title="View guardian information" aria-haspopup="dialog" aria-controls="{{ $guardianDialogId }}" onclick="const guardianDialog = document.getElementById('{{ $guardianDialogId }}'); if (guardianDialog && !guardianDialog.open) { document.body.appendChild(guardianDialog); guardianDialog.showModal(); }"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View guardian information</span></button></div>
                        @endif
                    </article>
                @empty
                <div class="case-profile-empty-state rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500">No profiles found.</div>
                @endforelse
                @if($profiles->isNotEmpty())
                <div class="case-profile-empty-state case-profile-search-empty hidden rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500" role="status">No results found.</div>
                @endif
                @if((auth()->user()?->isDepartmentAdmin() || !$isAdmin) && $profiles->hasPages())
                    <div class="case-profiles-pagination-container w-full min-w-0 overflow-x-hidden">
                        @include('components.archived-pagination', ['paginator' => $profiles, 'label' => 'profiles'])
                    </div>
                @endif
            </div>
        </section>
        @endif
    @endforeach
    @if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['VAWC', 'BCPC'], true) && (request()->filled('search') || request()->filled('age')) && $victims->total() === 0 && $perpetrators->total() === 0)
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-8 text-center text-gray-500">
            No results found.
        </div>
    @endif
    </div>
</div>
<style>
    .guardian-dialog { margin: auto; max-width: min(32rem, calc(100vw - 2rem)); max-height: 85vh; overflow-y: auto; }
    .guardian-dialog::backdrop { background: rgba(0, 0, 0, .5); }
</style>
@if(auth()->user()?->defaultCaseDepartment() === 'BCPC' || (auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC'))
@push('scripts')
<script>
(() => {
    const searchInput = document.getElementById('profile-search-input') || document.querySelector('#case-profiles-filter-form input[name="search"]');
    const ageInput = document.getElementById('profile-age-input') || document.querySelector('#case-profiles-filter-form input[name="age"]');

    function filterProfileRows() {
        const q = (searchInput?.value || '').toLowerCase().trim();
        const ageVal = (ageInput?.value || '').trim();

        const isCardProfileView = window.matchMedia('(max-width: 80rem)').matches;
        const profileItems = isCardProfileView
            ? document.querySelectorAll('.case-profile-card')
            : document.querySelectorAll('#case-profiles-results table tbody tr');
        profileItems.forEach(row => {
            if (!isCardProfileView && row.cells.length < 5) return;
            const text = `${row.textContent} ${row.dataset.profileName || ''}`.toLowerCase();
            const ageCell = isCardProfileView
                ? (row.dataset.profileAge || '').trim()
                : (row.dataset.profileAge || row.cells[2]?.textContent || '').trim();
            const matchesSearch = !q || text.includes(q);
            const matchesAge = !ageVal || ageCell === ageVal;
            if (matchesSearch && matchesAge) {
                row.style.removeProperty('display');
            } else {
                row.style.setProperty('display', 'none', 'important');
            }
        });
        document.querySelectorAll('.case-profiles-mobile-cards').forEach(group => {
            const emptyState = group.querySelector('.case-profile-search-empty');
            const hasMatch = Array.from(group.querySelectorAll('.case-profile-card')).some(card => card.style.display !== 'none');
            emptyState?.classList.toggle('hidden', !isCardProfileView || (!q && !ageVal) || hasMatch);
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterProfileRows);
    if (ageInput) ageInput.addEventListener('input', filterProfileRows);

    // Reset unsaved changes state when clicking Clear
    document.querySelectorAll('#case-profiles-filter-form a').forEach(a => {
        if (a.textContent.trim().toLowerCase() === 'clear') {
            a.addEventListener('click', () => {
                if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
            });
        }
    });
})();
</script>
@endpush
@endif
@if(auth()->user()?->isVawcAdmin())
@push('scripts')
<script>
(() => {
    let activeRequest;
    const refreshProfiles = async (url) => {
        activeRequest?.abort();
        const request = new AbortController();
        activeRequest = request;
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: request.signal,
            });
            if (!response.ok) return;
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const replacement = page.querySelector('#case-profiles-results');
            const current = document.querySelector('#case-profiles-results');
            if (!replacement || !current || request.signal.aborted || activeRequest !== request) return;
            replacement.style.minHeight = `${current.offsetHeight}px`;
            current.replaceWith(replacement);
            history.replaceState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') throw error;
        }
    };

    document.addEventListener('input', (event) => {
        const form = event.target.closest('#case-profiles-filter-form');
        if (!form || !['search', 'number'].includes(event.target.type)) return;
        const url = new URL(form.action, location.origin);
        url.search = new URLSearchParams(new FormData(form)).toString();
        refreshProfiles(url);
    });

    document.addEventListener('profile-pagination-start', () => activeRequest?.abort());
})();
</script>
@endpush
@endif

@push('scripts')
<script>
(() => {
    let pending;
    const pageNames = ['victims_page', 'perpetrators_page'];
    async function loadProfiles(url, sectionName, pushHistory) {
        pending?.abort();
        const request = new AbortController();
        pending = request;
        document.dispatchEvent(new Event('profile-pagination-start'));
        const selector = sectionName ? `[data-profile-section="${sectionName}"]` : '#case-profiles-results';
        const current = document.querySelector(selector);
        if (!current) return;
        current.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: request.signal });
            if (!response.ok || response.redirected) throw new Error('Unable to load profiles. Please try again.');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const replacement = page.querySelector(selector);
            if (!replacement) throw new Error('Unable to load profiles. Please try again.');
            if (request.signal.aborted || pending !== request) return;
            current.replaceWith(replacement);
            if (pushHistory) history.pushState({}, '', url);
            // Refresh the untouched section's links with the current filters and other page number.
            document.querySelectorAll('[data-profile-section] a[href]').forEach(link => {
                const target = new URL(link.href, location.href);
                const owner = link.closest('[data-profile-section]').dataset.profileSection + '_page';
                if (!target.searchParams.has(owner)) return;
                const number = target.searchParams.get(owner);
                target.search = new URLSearchParams(url.searchParams).toString();
                target.searchParams.set(owner, number);
                link.href = target.href;
            });
            const focusTarget = replacement.querySelector('h2');
            if (pushHistory && focusTarget) {
                focusTarget.setAttribute('tabindex', '-1');
                focusTarget.focus({ preventScroll: true });
            }
        } catch (error) {
            if (error.name === 'AbortError' || pending !== request) return;
            let message = current.querySelector('[data-pagination-error]');
            if (!message) {
                message = document.createElement('p');
                message.dataset.paginationError = '';
                message.className = 'px-5 py-3 text-sm text-red-600';
                message.setAttribute('role', 'alert');
                current.appendChild(message);
            }
            message.textContent = 'Unable to load profiles. Please try the page link again.';
        } finally {
            if (pending === request) current.removeAttribute('aria-busy');
        }
    }
    document.addEventListener('click', event => {
        if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest('[data-profile-section] a[href]');
        if (!link || link.target === '_blank' || link.getAttribute('href') === '#') return;
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || url.pathname !== location.pathname || !pageNames.some(name => url.searchParams.has(name))) return;
        event.preventDefault();
        if (url.href === location.href) return;
        loadProfiles(url, link.closest('[data-profile-section]').dataset.profileSection, true);
    });
    document.addEventListener('input', event => {
        if (event.target.closest('#case-profiles-filter-form')) pending?.abort();
    }, true);
    window.addEventListener('popstate', () => loadProfiles(new URL(location.href), null, false));
})();
</script>
<script>
(function () {
    var body = document.body;
    var themed = body.classList.contains('bcpc-admin-header-theme')
        || body.classList.contains('swd-admin-header-theme')
        || body.classList.contains('desk-officer-header-theme');
    if (!themed) return;

    var mq = window.matchMedia('(max-width: 47.9375rem)');

    var apply = function () {
        if (mq.matches) {
            body.classList.add('case-profiles-mobile-nav');
        } else {
            body.classList.remove('case-profiles-mobile-nav');
        }

        var sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        var open = mq.matches && sidebar.classList.contains('sidebar-open');
        if (open) {
            var header = document.querySelector('#mainContent > header') || document.querySelector('#mainContent > .portal-clock-only-header');
            if (header) {
                var bottom = Math.max(0, header.getBoundingClientRect().bottom);
                sidebar.style.setProperty('top', bottom + 'px', 'important');
                sidebar.style.setProperty('max-height', 'calc(100vh - ' + bottom + 'px)', 'important');
            }
            body.style.overflow = 'hidden';
            document.documentElement.style.setProperty('overflow-y', 'hidden', 'important');
        } else {
            sidebar.style.removeProperty('top');
            sidebar.style.removeProperty('max-height');
            body.style.overflow = '';
            document.documentElement.style.removeProperty('overflow-y');
        }
    };

    var sidebar = document.getElementById('sidebar');
    if (sidebar) {
        new MutationObserver(apply).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    }
    mq.addEventListener('change', apply);
    window.addEventListener('resize', apply);
    document.addEventListener('DOMContentLoaded', apply);
    apply();
})();
</script>
@endpush
@endsection
