@extends('layouts.admin-tailwind')

@section('title', 'Calendar')

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 85.375rem) {
        html body.portal-ui .calendar-mobile-date-field {
            display: block !important;
            position: relative;
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }
        html body.portal-ui .calendar-mobile-date-field > input[type=datetime-local] {
            display: block;
            box-sizing: border-box;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            min-height: 2.75rem;
            padding-right: 2.75rem !important;
            -webkit-appearance: none;
            appearance: none;
        }
        html body.portal-ui .calendar-mobile-date-field > .calendar-mobile-date-icon {
            display: block !important;
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #64748b;
            pointer-events: none;
            z-index: 1;
        }
        html body.portal-ui .calendar-mobile-date-field > input[type=datetime-local]::-webkit-calendar-picker-indicator {
            opacity: 0;
        }
        html body.portal-ui .calendar-mobile-date-field > input[type=datetime-local]::-webkit-date-and-time-value {
            text-align: left;
            min-width: 0;
        }
    }
</style>
@endif
@endpush
@section('content')

<!-- Top Header -->
@if(auth()->user()->isDepartmentAdmin())
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Calendar</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">View and manage events</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@else
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Calendar</h2>
            <p class="text-sm text-gray-500 hidden sm:block">View and manage events</p>
        </div>
        <div class="flex items-center gap-4">
            @if(auth()->user()->isDepartmentAdmin())
            @include('admin-tailwind.components.header-account')
            @endif
            @if(!auth()->user()->isDepartmentAdmin())
            <button type="button" onclick="openEventModal()" 
               class="inline-flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-md">
                <i class="fas fa-plus-circle"></i> Add Event
            </button>
            @endif
        </div>
    </div>
</header>
@endif

<!-- Main Content -->
<main class="px-4 pt-4 pb-4 sm:p-6 lg:p-8">
    <!-- Calendar Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 {{ auth()->user()->isDepartmentAdmin() ? 'bcpc-admin-calendar' : '' }}">
        @if(auth()->user()->isDepartmentAdmin())
        <div id="calendar-list-filter" class="hidden mb-4">
            <label for="calendar-list-range" class="block text-sm font-medium text-gray-700 mb-2">Date range</label>
            <select id="calendar-list-range" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                <option value="all">Whole month</option>
                <option value="3">Days 1–3</option>
                <option value="7">Days 1–7</option>
                <option value="15">Days 1–15</option>
            </select>
        </div>
        @endif
        <div id="calendar"></div>
        @if(auth()->user()->isDepartmentAdmin())
        <div id="calendar-list-pagination" class="hidden mt-4 flex items-center justify-between gap-3 text-sm">
            <span id="calendar-list-page-info" aria-live="polite"></span>
            <div class="flex gap-2">
                <button type="button" id="calendar-list-prev" class="px-3 py-2 border border-gray-300 rounded-lg text-purple-700 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed">Previous</button>
                <button type="button" id="calendar-list-next" class="px-3 py-2 border border-gray-300 rounded-lg text-purple-700 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
        @endif
    </div>
    @if(auth()->user()->isDepartmentAdmin())
    <div class="max-w-[960px] mx-auto mt-4">
        <button type="button" onclick="openEventModal()" class="inline-flex w-full items-center justify-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-md">
            <i class="fas fa-plus-circle"></i> Add Event
        </button>
    </div>
    @endif
</main>

<style>
    @if(auth()->user()->isDepartmentAdmin())
    @media (max-width: 40rem) {
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div {
            column-gap: .75rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div > .admin-reference-mobile-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-title {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header #dashboardMenuToggle {
            display: inline-flex !important;
            position: relative !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
    }
    @endif
    #eventModalDialog {
        height: 100vh;
        height: 100dvh;
        min-height: 0;
        padding: 1rem;
        overflow: hidden;
    }
    #eventModalContent {
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    #dateEventsModalContent,
    #eventDetailsModalContent {
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }
    .fc {
        font-family: 'Raleway', 'Montserrat', sans-serif;
    }
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
    }
    .fc .fc-button {
        background-color: #7c3aed;
        border-color: #7c3aed;
        color: white;
        font-family: 'Raleway', 'Montserrat', sans-serif;
        font-weight: 500;
    }
    .fc .fc-dayGridMonth-button,
    .fc .fc-listMonth-button,
    .fc .fc-listWeek-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 4.5rem;
        margin: 0 !important;
        border-radius: .5rem !important;
    }
    .fc .fc-dayGridMonth-button {
        margin-right: .5rem !important;
    }
    .fc .fc-button:hover {
        background-color: #6d28d9;
        border-color: #6d28d9;
    }
    .fc .fc-button:active,
    .fc .fc-button.fc-button-active {
        background-color: #5b21b6;
        border-color: #5b21b6;
    }
    .fc .fc-col-header-cell-cushion {
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .fc .fc-daygrid-day-number {
        color: #374151;
        font-weight: 500;
    }
    .fc .fc-day-today {
        background-color: #f3e8ff !important;
    }
    .fc .fc-event {
        border: none;
        border-radius: 0.375rem;
        font-size: 0.8rem;
        box-sizing: border-box;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
        padding: 0.125rem 0.25rem;
        transition: filter 120ms ease, box-shadow 120ms ease;
    }
    .fc .fc-daygrid-day-events,
    .fc .fc-daygrid-event-harness {
        max-width: 100%;
        min-width: 0;
    }
    .fc .fc-daygrid-day-events {
        width: 100%;
        box-sizing: border-box;
        overflow-x: clip;
        overflow-y: visible;
    }
    .fc .fc-daygrid-event-harness { padding-inline: 0.125rem; }
    .fc .fc-daygrid-event { width: calc(100% - 0.25rem); }
    .fc .fc-daygrid-event .fc-event-main,
    .fc .fc-daygrid-event .fc-event-main-frame,
    .fc .fc-daygrid-event .fc-event-title-container,
    .fc .fc-daygrid-event .fc-event-title {
        display: block;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .fc .fc-event:hover,
    .fc .fc-event:focus-visible {
        filter: brightness(0.97);
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }
    @if(auth()->user()->isVawcAdmin())
    .fc .fc-daygrid-event-harness,
    .fc .fc-daygrid-event {
        max-width: 100%;
    }
    .fc .fc-daygrid-dot-event {
        border: 0 !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fc .fc-daygrid-event-dot {
        border-color: #7c3aed;
    }
    @endif
    .fc .calendar-event-count {
        clear: both;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        padding-inline: 0.125rem;
    }
    .fc .calendar-event-count button {
        width: auto;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        white-space: normal;
        overflow-wrap: anywhere;
        text-align: center;
    }
    @if(auth()->user()->isDepartmentAdmin())
    .bcpc-admin-calendar {
        max-width: 960px;
        margin-inline: auto;
        padding: 20px;
        border-color: #e5e7eb;
    }
    .bcpc-admin-calendar .fc,
    .bcpc-admin-calendar .fc .fc-button { font-family: inherit; }
    .bcpc-admin-calendar .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 700; }
    .bcpc-admin-calendar .fc .fc-button {
        background-color: #7e22ce;
        border-color: #7e22ce;
        font-size: .875rem;
        border-radius: .5rem;
        padding: .4rem .7rem;
    }
    .bcpc-admin-calendar .fc .fc-button:hover,
    .bcpc-admin-calendar .fc .fc-button.fc-button-active {
        background-color: #6b21a8;
        border-color: #6b21a8;
    }
    .bcpc-admin-calendar .fc { --fc-border-color: #e5e7eb; }
    .bcpc-admin-calendar .fc .fc-col-header-cell { background: #faf5ff; }
    @media (max-width: 640px) {
        .bcpc-admin-calendar { padding: 12px; }
        .bcpc-admin-calendar .fc .fc-toolbar { flex-wrap: wrap; justify-content: center; gap: .75rem; }
    }
    @media (max-width: 47.9375rem) {
        .bcpc-admin-calendar { padding: .75rem; }
        .bcpc-admin-calendar .fc .fc-header-toolbar {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "navigation views"
                "title title";
            align-items: center;
            column-gap: .5rem;
            row-gap: .75rem;
            margin-bottom: 1rem;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > :first-child {
            grid-area: navigation;
            justify-self: start;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > :nth-child(2) {
            grid-area: title;
            justify-self: center;
            min-width: 0;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > :last-child {
            grid-area: views;
            justify-self: end;
            display: inline-flex;
            gap: .625rem;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > :last-child .fc-button-group {
            display: inline-flex;
            gap: .5rem;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > :last-child .fc-button-group > .fc-button:not(:first-child) {
            margin-left: 0 !important;
            border-radius: .5rem !important;
        }
        .bcpc-admin-calendar .fc .fc-toolbar-title {
            font-size: clamp(1rem, 5vw, 1.25rem);
            text-align: center;
            white-space: nowrap;
        }
        .bcpc-admin-calendar .fc .fc-button {
            min-height: 2.75rem;
            padding: .4rem .625rem;
        }
        .bcpc-admin-calendar .fc .fc-dayGridMonth-button,
        .bcpc-admin-calendar .fc .fc-listMonth-button,
        .bcpc-admin-calendar .fc .fc-listWeek-button {
            min-width: 3.5rem;
        }
        .bcpc-admin-calendar .fc .fc-dayGridMonth-button { margin-right: 0 !important; }
    }
    @endif

    /* Calendar tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header,
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header h2,
        html body.portal-ui #mainContent > header.sticky h2 {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header p,
        html body.portal-ui #mainContent > header.sticky p {
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
        html body.portal-ui #sidebar .sidebar-header .sidebar-text,
        html body.portal-ui #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-link i {
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

<!-- Date Events Modal -->
<div id="dateEventsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDateEventsModal()"></div>
    <div class="flex items-start sm:items-center justify-center overflow-y-auto p-4">
        <div id="dateEventsModalContent" class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4" id="dateEventsTitle">Events on Date</h2>
            <div id="dateEventsList" class="space-y-3 mb-6"></div>
            <div class="flex justify-end">
                <button type="button" onclick="closeDateEventsModal()" 
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEventModal()"></div>
    <div id="eventModalDialog" class="flex items-start sm:items-center justify-center overflow-y-auto p-4">
        <div id="eventModalContent" class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Add New Event</h2>
            
            <form id="eventForm" onsubmit="submitEvent(event)">
                @csrf
                <input type="hidden" name="event_date" id="event_date">
                
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" id="location" name="location"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="event_date_input" class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <div class="calendar-mobile-date-field" style="display: contents;"><input type="datetime-local" id="event_date_input" name="event_date_input" required oninput="validateEventEndTime()"
                           min="{{ date('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><svg class="calendar-mobile-date-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                </div>
                
                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <div class="calendar-mobile-date-field" style="display: contents;"><input type="datetime-local" id="end_time" name="end_time" oninput="validateEventEndTime()" aria-describedby="eventEndTimeHint"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"><svg class="calendar-mobile-date-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
                    <p id="eventEndTimeHint" class="mt-1 text-xs text-gray-500">Optional. Choose a date and time after the event starts.</p>
                </div>
                
                <div class="mb-4">
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience <span class="text-red-500">*</span></label>
                    <select id="target_audience" name="target_audience" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="all">All Users</option>
                        <option value="admins_only">Administrators Only</option>
                        <option value="bcpc_only">BCPC Only</option>
                        <option value="vawc_only">SWD Only</option>
                    </select>
                </div>
                
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeEventModal()" 
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition">
                        Save Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conflict Modal -->
<div id="conflictModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeConflictModal()"></div>
    <div class="flex items-start sm:items-center justify-center overflow-y-auto p-4">
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                </div>
            </div>
            <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Event Conflict</h2>
            <p class="text-gray-500 text-sm text-center mb-6">
                There is already an event on this date. Do you want to continue adding this event?
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeConflictModal()" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" onclick="submitEventWithConflict()" 
                        class="flex-1 px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEventDetailsModal()"></div>
    <div class="flex items-start sm:items-center justify-center overflow-y-auto p-4">
        <div id="eventDetailsModalContent" class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <h2 id="eventTitle" class="text-xl font-bold text-gray-800 mb-4"></h2>
            <div id="eventDetails" class="space-y-3 mb-6"></div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeEventDetailsModal()" 
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Close
                </button>
                <button type="button" id="deleteEventBtn" onclick="deleteEvent()" 
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet'>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
let calendar;
let currentEventId = null;
let pendingEventData = null;
let allEvents = [];
let activeCalendarEvents = [];
let calendarEventsByDate = new Map();

document.addEventListener('DOMContentLoaded', function() {
    const events = @json($events);
    allEvents = events;
    
    // Format events for FullCalendar
    const formattedEvents = events.map(function(event) {
        let colors = { background: '#ecfdf5', border: '#a7f3d0', text: '#065f46' };
        if (event.target_audience === 'admins_only') {
            colors = { background: '#fef2f2', border: '#fecaca', text: '#991b1b' };
        } else if (event.target_audience === 'bcpc_only') {
            colors = { background: '#f5f3ff', border: '#ddd6fe', text: '#5b21b6' };
        } else if (event.target_audience === 'vawc_only') {
            colors = { background: '#fffbeb', border: '#fde68a', text: '#92400e' };
        }
        return {
            id: event.id,
            title: event.title,
            start: event.calendar_start || event.event_date,
            extendedProps: { calendarDate: event.calendar_date || eventDateKey(event.event_date) },
            backgroundColor: colors.background,
            borderColor: colors.border,
            textColor: colors.text
        };
    });
    setActiveCalendarEvents(formattedEvents);

    const calendarEl = document.getElementById('calendar');
    @if(auth()->user()->isDepartmentAdmin())
    const listRange = document.getElementById('calendar-list-range');
    let listPage = 1;
    function applyListDateRange(view) {
        const isList = view.type === 'listMonth';
        document.getElementById('calendar-list-filter').classList.toggle('hidden', !isList);
        document.getElementById('calendar-list-pagination').classList.toggle('hidden', !isList);
        const month = new Intl.DateTimeFormat('en', { month: 'short' }).format(view.currentStart);
        Array.from(listRange.options).forEach(option => {
            option.textContent = option.value === 'all' ? 'Whole month' : `${month} 1–${option.value}`;
        });
        const matchingEvents = calendar.getEvents().filter(event =>
            event.start >= view.currentStart && event.start < view.currentEnd
            && (listRange.value === 'all' || Number(event.startStr.slice(8, 10)) <= Number(listRange.value))
        ).sort((a, b) => a.start - b.start || a.title.localeCompare(b.title) || String(a.id).localeCompare(String(b.id)));
        const pageCount = Math.max(1, Math.ceil(matchingEvents.length / 5));
        listPage = Math.min(listPage, pageCount);
        const pageIds = new Set(matchingEvents.slice((listPage - 1) * 5, listPage * 5).map(event => event.id));
        document.getElementById('calendar-list-page-info').textContent = `Page ${listPage} of ${pageCount} · ${matchingEvents.length} events`;
        document.getElementById('calendar-list-prev').disabled = listPage === 1;
        document.getElementById('calendar-list-next').disabled = listPage === pageCount;
        calendar.batchRendering(function() {
            calendar.setOption('height', isList ? 'auto' : 560);
            calendar.getEvents().forEach(event => {
                const dateEvents = getCalendarDateEvents(event.extendedProps.calendarDate);
                const isGroupedMonthEvent = view.type === 'dayGridMonth' && dateEvents.length >= 2;
                const isOutsideListPage = isList && !pageIds.has(event.id);
                event.setProp('display', isGroupedMonthEvent || isOutsideListPage ? 'none' : 'auto');
            });
        });
    }
    listRange.addEventListener('change', function() { listPage = 1; applyListDateRange(calendar.view); });
    document.getElementById('calendar-list-prev').addEventListener('click', function() { listPage--; applyListDateRange(calendar.view); });
    document.getElementById('calendar-list-next').addEventListener('click', function() { listPage++; applyListDateRange(calendar.view); });
    @endif
    calendar = new FullCalendar.Calendar(calendarEl, {
        @if(auth()->user()->isDepartmentAdmin())
        height: 560,
        @endif
        datesSet: function(info) {
            @if(auth()->user()->isDepartmentAdmin())
            listPage = 1;
            applyListDateRange(info.view);
            @else
            syncCalendarViewEventVisibility(info.view.type);
            @endif
        },
        initialView: 'dayGridMonth',
        timeZone: 'Asia/Manila',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: '{{ auth()->user()->isDepartmentAdmin() ? "dayGridMonth,listMonth" : "dayGridMonth,listWeek" }}'
        },
        events: activeCalendarEvents,
        @if(auth()->user()->isVawcAdmin())
        eventDisplay: 'list-item',
        @endif
        dayCellDidMount: function(info) {
            if (info.view.type !== 'dayGridMonth') return;
            const date = info.el.dataset.date || info.date.toISOString().slice(0, 10);
            renderDateEventCount(info.el, date);
        },
        eventDidMount: function(info) {
            if (info.view.type === 'dayGridMonth') return;
            if (info.view.type !== 'listMonth') return;
            info.el.style.cursor = 'pointer';
            info.el.tabIndex = 0;
            info.el.setAttribute('role', 'button');
            info.el.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    showEventDetails(info.event.id);
                }
            });
            const today = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
            if (info.event.startStr.slice(0, 10) < today) {
                const badge = document.createElement('span');
                badge.textContent = 'Done';
                badge.className = 'ml-2 inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700';
                info.el.querySelector('.fc-list-event-title')?.appendChild(badge);
            }
        },
        eventClick: function(info) {
            @if(auth()->user()->isDepartmentAdmin())
            if (info.view.type === 'listMonth') {
                showEventDetails(info.event.id);
                return;
            }
            @endif
            const eventDate = info.event.extendedProps.calendarDate;
            const dateEvents = getCalendarDateEvents(eventDate);
            if (dateEvents.length === 1) {
                showEventDetails(info.event.id);
                return;
            }
            showEventsOnDate(eventDate);
        },
        dateClick: function(info) {
            showEventsOnDate(info.dateStr);
        }
    });
    calendar.render();
    const notificationEventId = Number(new URLSearchParams(window.location.search).get('event'));
    if (Number.isInteger(notificationEventId) && notificationEventId > 0) {
        showEventDetails(notificationEventId);
    }
    @if(auth()->user()->isDepartmentAdmin())
    applyListDateRange(calendar.view);
    @endif
});

function showEventsOnDate(dateStr) {
    const rawEventsById = new Map(allEvents.map(event => [String(event.id), event]));
    const eventsOnDate = getCalendarDateEvents(dateStr)
        .map(event => rawEventsById.get(String(event.id)))
        .filter(Boolean);
    
    if (eventsOnDate.length === 0) {
        openEventModal(dateStr);
        return;
    }
    
    const dateEventsList = document.getElementById('dateEventsList');
    dateEventsList.innerHTML = '';
    
    eventsOnDate.forEach(function(event) {
        let color = '#059669';
        if (event.target_audience === 'admins_only') {
            color = '#dc2626';
        } else if (event.target_audience === 'bcpc_only') {
            color = '#7c3aed';
        } else if (event.target_audience === 'vawc_only') {
            color = '#f59e0b';
        }
        
        const eventItem = document.createElement('div');
        eventItem.className = 'p-4 rounded-lg border cursor-pointer hover:bg-gray-50 transition';
        eventItem.style.borderLeft = '4px solid ' + color;
        const title = document.createElement('h3');
        title.className = 'font-semibold text-gray-800';
        title.textContent = event.title;
        const date = document.createElement('p');
        date.className = 'text-sm text-gray-500';
        date.textContent = formatCalendarDateTime(event.event_date);
        eventItem.append(title, date);
        eventItem.onclick = function() {
            closeDateEventsModal();
            showEventDetails(event.id);
        };
        dateEventsList.appendChild(eventItem);
    });
    
    const dateEventsTitle = document.getElementById('dateEventsTitle');
    dateEventsTitle.textContent = 'Events on ' + dateStr;
    
    document.getElementById('dateEventsModal').classList.remove('hidden');
}

function formatCalendarDateTime(value) {
    if (!value) return '';
    const date = new Date(value);
    return Number.isNaN(date.getTime())
        ? value
        : date.toLocaleString('en-PH', {
            dateStyle: 'medium',
            timeStyle: 'short',
            timeZone: 'Asia/Manila'
        });
}

function getCalendarDateEvents(dateStr) {
    return calendarEventsByDate.get(dateStr) || [];
}

function setActiveCalendarEvents(events) {
    const uniqueEvents = new Map();
    const groupedEvents = new Map();
    events.forEach(event => {
        const id = String(event.id);
        if (uniqueEvents.has(id)) return;
        const date = event.extendedProps?.calendarDate || eventDateKey(event.start);
        if (!date) return;
        const normalizedEvent = {
            ...event,
            extendedProps: { ...event.extendedProps, calendarDate: date }
        };
        uniqueEvents.set(id, normalizedEvent);
        if (!groupedEvents.has(date)) groupedEvents.set(date, []);
        groupedEvents.get(date).push(normalizedEvent);
    });
    activeCalendarEvents = Array.from(uniqueEvents.values()).map(event => ({
        ...event,
        display: groupedEvents.get(event.extendedProps.calendarDate).length >= 2 ? 'none' : 'auto'
    }));
    calendarEventsByDate = groupedEvents;
}

function syncCalendarViewEventVisibility(viewType) {
    if (!calendar) return;
    calendar.batchRendering(function() {
        calendar.getEvents().forEach(event => {
            const dateEvents = getCalendarDateEvents(event.extendedProps.calendarDate);
            event.setProp('display', viewType === 'dayGridMonth' && dateEvents.length >= 2 ? 'none' : 'auto');
        });
    });
}

function renderDateEventCount(dayCell, dateStr) {
    if (!dayCell || (dayCell.dataset.date && dayCell.dataset.date !== dateStr)) return;
    const eventsContainer = dayCell.querySelector('.fc-daygrid-day-events');
    if (!eventsContainer) return;
    const events = getCalendarDateEvents(dateStr);
    let button = eventsContainer.querySelector(`[data-event-count-date="${dateStr}"]`);
    if (events.length < 2) {
        button?.closest('.calendar-event-count')?.remove();
        return;
    }
    if (!button) {
        const wrapper = document.createElement('div');
        wrapper.className = 'calendar-event-count flex w-full min-w-0 justify-center';
        button = document.createElement('button');
        button.type = 'button';
        button.dataset.eventCountDate = dateStr;
        button.className = 'inline-flex max-w-full items-center justify-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700 hover:bg-purple-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-700';
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            showEventsOnDate(dateStr);
        });
        wrapper.appendChild(button);
        eventsContainer.appendChild(wrapper);
    }
    button.textContent = `${events.length} Events`;
    button.setAttribute('aria-label', `${events.length} Events. View events for ${dateStr}`);
}

function eventDateKey(value) {
    if (!value) return '';
    if (typeof value === 'string') {
        const localDate = value.match(/^(\d{4}-\d{2}-\d{2})(?:$|[T ])/);
        const hasOffset = /(?:Z|[+-]\d{2}(?::?\d{2})?)$/i.test(value);
        if (localDate && !hasOffset) return localDate[1];
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return String(value).slice(0, 10);
    const parts = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit'
    }).formatToParts(date).reduce((values, part) => {
        if (part.type !== 'literal') values[part.type] = part.value;
        return values;
    }, {});
    return `${parts.year}-${parts.month}-${parts.day}`;
}

function closeDateEventsModal() {
    document.getElementById('dateEventsModal').classList.add('hidden');
}

function openEventModal(date = null) {
    document.getElementById('eventModal').classList.remove('hidden');
    if (date) {
        document.getElementById('event_date').value = date;
        document.getElementById('event_date_input').value = date;
        validateEventEndTime();
    }
}

function closeEventModal() {
    document.getElementById('eventModal').classList.add('hidden');
    document.getElementById('eventForm').reset();
    validateEventEndTime();
    pendingEventData = null;
}

function toggleVisibilityOptions() {
    const visibility = document.getElementById('visibility').value;
    const container = document.getElementById('visible_to_container');
    if (visibility === 'specific') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

function validateEventEndTime() {
    const start = document.getElementById('event_date_input');
    const end = document.getElementById('end_time');
    const hint = document.getElementById('eventEndTimeHint');
    if (!start || !end) return;

    const invalid = Boolean(start.value && end.value && end.value <= start.value);
    end.min = start.value || '';
    end.setCustomValidity(invalid ? 'Choose an end date and time later than the event start.' : '');
    if (hint) {
        hint.textContent = invalid
            ? 'Choose an end date and time later than the event start.'
            : 'Optional. Choose a date and time after the event starts.';
        hint.classList.toggle('text-red-600', invalid);
    }
}
function submitEvent(e) {
    e.preventDefault();
    validateEventEndTime();
    if (!document.getElementById('eventForm').reportValidity()) return;
    
    const formData = new FormData(document.getElementById('eventForm'));
    formData.set('event_date', document.getElementById('event_date_input').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    Swal.fire({
        title: 'Saving Event...',
        text: 'Please wait while the event is saved.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch('{{ route("admin.calendar.store") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.has_conflict) {
            Swal.close();
            pendingEventData = formData;
            document.getElementById('conflictModal').classList.remove('hidden');
        } else if (data.success) {
            closeEventModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: data.message, confirmButtonText: 'OK' })
                .then(() => location.reload());
        } else {
            Swal.fire('Error', data.message || 'Unable to save the event.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Unable to save the event.', 'error'));
}

function submitEventWithConflict() {
    if (!pendingEventData) return;
    
    pendingEventData.set('_token', document.querySelector('meta[name="csrf-token"]').content);

    Swal.fire({ title: 'Saving Event...', text: 'Please wait while the event is saved.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
    
    fetch('{{ route("admin.calendar.store-with-conflict") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: pendingEventData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeConflictModal();
            closeEventModal();
            Swal.fire({ icon: 'success', title: 'Saved!', text: data.message, confirmButtonText: 'OK' })
                .then(() => location.reload());
        } else {
            Swal.fire('Error', data.message || 'Unable to save the event.', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Unable to save the event.', 'error'));
}

function closeConflictModal() {
    document.getElementById('conflictModal').classList.add('hidden');
}

function showEventDetails(eventId) {
    currentEventId = eventId;
    
    fetch('{{ route("admin.calendar.show", ":id") }}'.replace(':id', eventId))
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch event details');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('eventTitle').textContent = data.title || 'Event';
            document.getElementById('eventDetails').innerHTML = `
                <p><strong>Description:</strong> ${data.description || 'No description'}</p>
                <p><strong>Location:</strong> ${data.location || 'No location specified'}</p>
                <p><strong>Date:</strong> ${formatCalendarDateTime(data.event_date) || 'No date specified'}</p>
                <p><strong>Target Audience:</strong> ${data.target_audience || 'Not specified'}</p>
                ${data.creator ? `<p><strong>Created by:</strong> ${data.creator.first_name} ${data.creator.last_name}</p>` : ''}
            `;
            
            const deleteBtn = document.getElementById('deleteEventBtn');
            const userId = {{ Auth::id() }};
            const isAdmin = {{ strtolower(Auth::user()->position) === 'administrator' ? 'true' : 'false' }};
            
            if (data.created_by === userId || isAdmin) {
                deleteBtn.classList.remove('hidden');
            } else {
                deleteBtn.classList.add('hidden');
            }
            
            document.getElementById('eventDetailsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Load Event', text: 'Failed to load event details. Please try again.' });
            else alert('Failed to load event details. Please try again.');
        });
}

function closeEventDetailsModal() {
    document.getElementById('eventDetailsModal').classList.add('hidden');
    currentEventId = null;
}

async function deleteEvent() {
    if (!currentEventId) return;
    const eventId = currentEventId;
    
    @if(auth()->user()->isBcpcAdmin())
    const confirmation = await Swal.fire({
        title: 'Delete Event',
        text: 'Are you sure you want to delete this event?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#7e22ce',
        focusCancel: true,
        reverseButtons: true
    });
    if (!confirmation.isConfirmed) return;
    @else
    const confirmation = await Swal.fire({
        title: 'Delete Event',
        text: 'Are you sure you want to delete this event?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#C41E3A',
        width: 'min(28rem, calc(100vw - 2rem))'
    });
    if (!confirmation.isConfirmed) return;
    @endif
    
    fetch('{{ route("admin.calendar.destroy", ":id") }}'.replace(':id', eventId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEventDetailsModal();
            location.reload();
        } else {
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Delete Event', text: data.message });
            else alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Delete Event', text: 'Unable to delete the event. Please try again.' });
    });
}
</script>
@endsection
