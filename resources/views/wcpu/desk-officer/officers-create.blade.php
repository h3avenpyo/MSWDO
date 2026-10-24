@extends('layouts.desk-officer')

@section('title', 'Add ' . ($isBCPCCase ? 'BCPC' : 'SWD') . ' Case - iSERVE SILANG Desk Officer')
@push('styles')
<style>
    @media (min-width: 80.0625rem) {
        html body.portal-ui #mainContent > header#officers-create-header > div > div {
            display: flex !important;
            width: 100% !important;
            flex: 1 1 auto !important;
            column-gap: 1rem !important;
        }
        html body.portal-ui #mainContent > header#officers-create-header > div > div > div:first-child {
            flex: 1 1 0 !important;
            min-width: 0 !important;
        }
        html body.portal-ui #mainContent > header#officers-create-header > div > div > .portal-page-clock {
            flex: 0 0 auto !important;
            order: 0 !important;
            margin-left: auto !important;
            margin-right: 0 !important;
        }
        html body.portal-ui #mainContent > header#officers-create-header > div > div > div:last-child {
            flex: 0 0 auto !important;
            column-gap: 1rem !important;
        }
    }
    /* Ensure no duplicate mobile top bar or duplicate toggle */
    html body.portal-ui #mobileTopBar,
    body.portal-ui #mobileTopBar,
    body #mobileTopBar,
    #mobileTopBar {
        display: none !important;
    }

    html body.portal-ui #mobileMenuToggle,
    body.portal-ui #mobileMenuToggle,
    body #mobileMenuToggle,
    #mobileMenuToggle,
    html body.portal-ui #sidebarCloseBtn,
    html body.portal-ui #sidebarOverlay {
        display: none !important;
        pointer-events: none !important;
    }

    /* Page header styling matching reference Case Profiles */
    @media (min-width: 48rem) {
        #officers-create-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        #officers-create-header :is(h1, h2, h3) {
            color: #1f2937 !important;
        }

        #officers-create-header p {
            color: #6b7280 !important;
        }

        body.portal-ui #officers-create-header #pageMobileMenuToggle {
            display: none !important;
        }

        body.portal-ui #officers-create-header .desk-officer-user-profile,
        body.portal-ui #officers-create-header div:has(> img[alt="User Profile"]) {
            display: flex !important;
            align-items: center !important;
        }

        body.portal-ui #officers-create-header div:has(> img[alt="User Profile"])>div {
            display: block !important;
        }
    }

    @media (max-width: 47.9375rem) {
        #officers-create-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }

        #officers-create-header :is(h1, h2, h3) {
            color: #ffffff !important;
        }

        #officers-create-header p,
        #officers-create-header p :is(span, strong) {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Keep the title, department badge, and subtitle evenly spaced on phones. */
        body.portal-ui #mainContent #officers-create-header .officers-create-header-leading {
            gap: .25rem !important;
            min-width: 0 !important;
        }

        body.portal-ui #mainContent #officers-create-header .officers-create-header-title-group {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: .375rem !important;
            min-width: 0 !important;
        }

        body.portal-ui #mainContent #officers-create-header .officers-create-header-title-group > h2 {
            margin: 0 !important;
            line-height: 1.35 !important;
        }

        body.portal-ui #mainContent #officers-create-header .officers-create-header-leading > p {
            margin: 0 !important;
            font-size: .75rem !important;
            line-height: 1.45 !important;
            white-space: normal !important;
            overflow-wrap: anywhere;
        }

        body.portal-ui #officers-create-header #pageMobileMenuToggle {
            position: relative !important;
            font-size: 1.125rem !important;
            left: 0 !important;
            margin-left: 0 !important;
            transform: none !important;
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            min-width: 2.5rem !important;
            min-height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            border: 0 !important;
            border-radius: 0.375rem !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
            padding: 0 !important;
            cursor: pointer;
            margin: 0 !important;
        }

        body.portal-ui #officers-create-header #pageMobileMenuToggle i {
            color: inherit !important;
            font-size: inherit !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Use the dashboard notification button for both multirole case types. */
        body.portal-ui #mainContent #officers-create-header [data-multirole-notifications] button[aria-label="Notifications"] {
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
            align-self: center !important;
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        body.portal-ui #mainContent #officers-create-header [data-multirole-notifications] button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        body.portal-ui #mainContent #officers-create-header [data-multirole-notifications] button[aria-label="Notifications"]>i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            vertical-align: middle !important;
        }

        body.portal-ui #mainContent #officers-create-header [data-multirole-notifications] #notifications-unread-count {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            padding: 0 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-size: .75rem !important;
            line-height: 1 !important;
            font-weight: 700 !important;
            border: 2px solid #C41E3A !important;
            border-radius: 9999px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2) !important;
        }
    }

    @media (max-width: 40rem) {

        body.portal-ui #officers-create-header .desk-officer-user-profile,
        body.portal-ui #officers-create-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
    }

    /* Active SWD/BCPC case banner action buttons */
    .case-type-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
        width: 100% !important;
    }

    .case-type-actions .case-type-action {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.375rem !important;
        padding: 0.5rem 0.875rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
        min-height: 2.375rem !important;
        color: #000000 !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        text-decoration: none !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out !important;
    }

    .case-type-actions .case-type-action:hover,
    .case-type-actions .case-type-action:focus {
        background-color: #f9fafb !important;
        color: #000000 !important;
    }

    .case-type-actions .case-type-action i {
        color: #000000 !important;
    }

    /* Small screens (<= 420px): stack action buttons on separate rows so labels never clip or overflow */
    @media (max-width: 26.25rem) {
        .case-type-actions {
            flex-direction: column !important;
            width: 100% !important;
        }

        .case-type-actions .case-type-action {
            flex: 1 1 100% !important;
            width: 100% !important;
            justify-content: center !important;
        }
    }

    /* Medium-small screens (421px to 991px): buttons sit side-by-side with equal width */
    @media (min-width: 26.251rem) and (max-width: 61.9375rem) {
        .case-type-actions {
            flex-direction: row !important;
            width: 100% !important;
        }

        .case-type-actions .case-type-action {
            flex: 1 1 calc(50% - 0.25rem) !important;
            max-width: calc(50% - 0.25rem) !important;
            justify-content: center !important;
        }
    }

    /* Desktop (>= 992px): natural auto width aligned to the right */
    @media (min-width: 62rem) {
        .case-type-actions {
            flex-direction: row !important;
            width: auto !important;
            justify-content: flex-end !important;
        }

        .case-type-actions .case-type-action {
            flex: 0 0 auto !important;
            width: auto !important;
        }
    }

    /* Form padding adjustments on mobile */
    @media (max-width: 47.9375rem) {
        #new-case-form .p-6 {
            padding: 1rem !important;
        }

        #new-case-form .case-form-section-header {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
</style>
@endpush

@section('content')

@php
use Carbon\Carbon;
$nowPst = Carbon::now('Asia/Manila');
$todayPst = $nowPst->format('Y-m-d');
$isMultiroleDeskOfficer = Auth::user()->position === 'Desk Officer' && Auth::user()->isMultiCaseDepartment();
$isSwdDeskOfficer = Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'VAWC' &&
!$isMultiroleDeskOfficer;
$isSwdCase = !$isBCPCCase;
@endphp

<!-- Top Header -->
<header id="officers-create-header" class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="px-3 px-sm-4 px-lg-8 py-2.5 sm:py-3.5">
        <div class="d-flex align-items-center justify-content-between gap-2.5 sm:gap-4 flex-nowrap">
            <div class="d-flex align-items-center gap-2.5 sm:gap-3 min-w-0 flex-grow-1">
                <button type="button" id="pageMobileMenuToggle"
                    class="inline-flex flex-none items-center justify-center text-white focus:outline-none"
                    aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
                <div class="officers-create-header-leading d-flex flex-column align-items-start min-w-0 flex-grow-1">
                    <div class="officers-create-header-title-group d-flex flex-wrap align-items-center gap-1.5 sm:gap-2">
                        <h2 class="text-lg sm:text-2xl font-bold text-gray-800 m-0">Add New Case</h2>
                        @if($isBCPCCase)
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-red-600 text-white rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                            <i class="fas fa-child"></i> BCPC Case
                        </span>
                        @else
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 text-white rounded-full text-xs font-bold uppercase tracking-wider shadow-sm"
                            style="background-color: #C41E3A !important;">
                            <i class="fas fa-shield-alt"></i> SWD Case
                        </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 m-0 mt-0.5">
                        Creating a new <span class="font-semibold text-gray-700">{{ $isBCPCCase ? 'BCPC' : 'SWD'
                            }}</span> record for <strong>{{ $userBarangay->name }}</strong>
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 sm:gap-3 flex-shrink-0 ms-auto">
                @include('desk-officer.components.notifications')
                <div class="desk-officer-user-profile d-flex align-items-center gap-2 gap-sm-3">
                    <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover bg-gray-100">
                    <div class="text-end text-right">
                        <p class="text-xs sm:text-sm font-bold text-gray-800 m-0">{{ Auth::user()->first_name }} {{
                            Auth::user()->last_name }}</p>
                        <p class="text-[11px] sm:text-xs text-gray-500 m-0">{{ Auth::user()->position ?? ($isBCPCCase ?
                            'BCPC Desk Officer' :
                            'SWD Desk Officer') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="px-3 sm:px-6 lg:px-8 pt-4 pb-4 sm:pt-6 sm:pb-6 lg:pt-8 lg:pb-8 max-w-5xl mx-auto">
    <!-- Active Case Type Banner & Form Note -->
    <div
        class="mb-4 sm:mb-6 rounded-xl border overflow-hidden shadow-sm {{ $isMultiroleDeskOfficer || $isBCPCCase ? 'border-[#C41E3A] bg-[#C41E3A]' : 'border-purple-200 bg-gradient-to-r from-purple-50 via-white to-purple-50/30' }}">
        <div
            class="p-3 p-sm-4 p-md-5 d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 gap-md-4">
            <div class="d-flex align-items-center gap-3 min-w-0 w-100 w-lg-auto">
                <div
                    class="w-12 h-12 rounded-xl d-flex align-items-center justify-content-center flex-shrink-0 bg-white text-red-600 shadow-md">
                    <i
                        class="fas {{ $isBCPCCase ? 'fa-child text-xl text-red-600' : 'fa-shield-alt text-xl' }} {{ $isMultiroleDeskOfficer ? 'text-red-600' : '' }}"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <h3
                            class="text-base sm:text-lg font-bold m-0 {{ $isMultiroleDeskOfficer || $isBCPCCase ? 'text-white' : 'bg-purple-700 text-white px-3 py-1 rounded-lg' }}">
                            {{ $isBCPCCase ? 'BCPC Case (Child Protection)' : 'SWD Cases' }}
                        </h3>
                    </div>
                    <p
                        class="text-xs sm:text-sm text-white m-0 mt-1 {{ $isMultiroleDeskOfficer || $isBCPCCase ? '' : 'bg-purple-700 px-3 py-1.5 rounded-lg' }}">
                        Recording for <strong class="text-white">{{ $userBarangay->name }}</strong> &bull; Fields marked
                        with <span class="text-white font-bold">*</span> are required.
                    </p>
                </div>
            </div>
            <div class="case-type-actions d-flex flex-wrap align-items-center gap-2 w-100 w-lg-auto flex-shrink-0">
                <a href="{{ route('desk-officer.cases.index', ['type' => $type]) }}"
                    class="case-type-action inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg border transition shadow-sm {{ $isBCPCCase ? 'border-gray-200 bg-white text-black hover:bg-gray-50' : 'border-gray-300 bg-white text-black hover:bg-gray-50' }}">
                    <i class="fas fa-arrow-left" style="color: #000000 !important;"></i>
                    <span style="color: #000000 !important;">Back to Cases</span>
                </a>
                @if(Auth::user()->isMultiCaseDepartment())
                <button type="button" id="change-case-type"
                    data-change-case-type-url="{{ route('desk-officer.cases.create', ['type' => $isBCPCCase ? 'vawc' : 'bcpc']) }}"
                    class="case-type-action inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg border {{ $isBCPCCase ? 'border-red-300 text-black bg-white hover:bg-red-50' : 'border-purple-300 text-black bg-white hover:bg-purple-50' }} transition shadow-sm"
                    style="color: #000000 !important;">
                    <i class="fas fa-exchange-alt" style="color: #000000 !important;"></i>
                    <span style="color: #000000 !important;">Change Case Type</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Error Summary -->
    @if(isset($errors) && $errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-6">
        <div class="flex items-center gap-2 text-red-700 font-semibold mb-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>Please correct the errors below:</span>
        </div>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Case Form -->
    <form id="new-case-form" action="{{ route('desk-officer.cases.store') }}" method="POST"
        enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="case_type" value="{{ $type }}">
        <input type="hidden" name="timezone_used" value="Asia/Manila">

        <!-- Case Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div
                class="case-form-section-header bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-folder-open"></i>
                    Case Information
                </h3>
                <span
                    class="text-xs text-white/90 font-medium uppercase tracking-wider bg-white/10 px-2.5 py-1 rounded">
                    {{ $isBCPCCase ? 'BCPC Case Record' : 'SWD Case Record' }}
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Case Designation <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" readonly disabled
                                value="{{ $userBarangay->name }} - [{{ strtoupper($userBarangay->code ?? 'N/A') }}]"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 text-sm font-medium cursor-not-allowed">
                            <input type="hidden" id="case-designation" name="case_designation"
                                value="{{ $userBarangay->id }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-lock text-gray-400"></i> Cases are automatically designated to your
                            barangay.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Case Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="case-number" name="case_number" readonly
                            placeholder="Generating case number..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 text-sm font-mono font-medium">
                        <p class="text-xs text-gray-500 mt-1.5">
                            Auto-generated case sequence.
                        </p>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Case Priority <span class="text-red-500">*</span>
                        </label>
                        <select id="priority" name="case_priority" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Priority</option>
                            <option value="Non-urgent" {{ old('case_priority', 'Non-urgent' )=='Non-urgent' ? 'selected'
                                : '' }}>Non-urgent</option>
                            <option value="Urgent" {{ old('case_priority')=='Urgent' ? 'selected' : '' }}>Urgent
                            </option>
                            <option value="Emergency" {{ old('case_priority')=='Emergency' ? 'selected' : '' }}>
                                Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Case Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="Case Filed" {{ old('status', 'Case Filed' )=='Case Filed' ? 'selected' : ''
                                }}>Case Filed</option>
                            <option value="Case Closed" {{ old('status')=='Case Closed' ? 'selected' : '' }}>Case Closed
                            </option>
                            <option value="Case Referred" {{ old('status')=='Case Referred' ? 'selected' : '' }}>Case
                                Referred</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Victim Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="case-form-section-header bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-user-shield"></i>
                    Victim Information
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="victim_name" name="victim_name" required value="{{ old('victim_name') }}"
                            placeholder="Full Name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Age <span class="text-red-500">*</span>
                            @unless($isSwdCase)<span class="text-xs text-red-600 font-bold">(0 - 17
                                only)</span>@endunless
                        </label>
                        <input type="number" id="victim_age" name="victim_age" required min="0" @unless($isSwdCase)
                            max="17" @endunless value="{{ old('victim_age') }}"
                            placeholder="{{ $isSwdCase ? 'Age' : 'Age (0-17)' }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        @unless($isSwdCase)<p class="text-xs text-gray-500 mt-1">Victim age must be from 0 to 17 years
                            old.</p>@endunless
                        <p id="victim_age_error" class="text-xs text-red-600 font-semibold mt-1 hidden">
                            <i class="fas fa-exclamation-circle"></i> Victim age can only be 17 and below.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="victim_gender" name="victim_gender" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Gender</option>
                            <option value="Female" {{ old('victim_gender')=='Female' ? 'selected' : '' }}>Female
                            </option>
                            <option value="Male" {{ old('victim_gender')=='Male' ? 'selected' : '' }}>Male</option>
                            <option value="LGBTQIA+" {{ old('victim_gender')=='LGBTQIA+' ? 'selected' : '' }}>LGBTQIA+
                            </option>
                            <option value="Prefer not to say" {{ old('victim_gender')=='Prefer not to say' ? 'selected'
                                : '' }}>Prefer not to say</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <input type="tel" id="victim_contact" name="victim_contact" maxlength="11" pattern="[0-9]{11}"
                            inputmode="numeric" value="{{ old('victim_contact') }}"
                            placeholder="11-digit phone number (e.g. 09123456789)"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="victim_address" name="victim_address" required
                        value="{{ old('victim_address') }}" placeholder="Street, Barangay, Municipality, Province"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>

                <!-- Guardian Information (for minor victims / BCPC) -->
                <div id="guardian-section" class="{{ $isBCPCCase ? '' : 'hidden' }} mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-friends text-purple-500"></i>
                        Guardian Information
                        <span class="text-xs font-normal text-gray-500">
                            {{ $isBCPCCase ? '(Required for BCPC child cases)' : '(Required for minors under 18)' }}
                        </span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guardian Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="guardian_name" name="guardian" value="{{ old('guardian') }}"
                                placeholder="Full Name of Guardian"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guardian Contact <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="guardian_contact" name="guardian_contact" maxlength="11"
                                pattern="[0-9]{11}" inputmode="numeric" value="{{ old('guardian_contact') }}"
                                placeholder="Contact Number (11 digits)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guardian Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="guardian_address" name="guardian_address"
                                value="{{ old('guardian_address') }}" placeholder="Full Address"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Relationship to Victim <span class="text-red-500">*</span>
                            </label>
                            <select id="relationship_to_victim" name="relationship_to_victim"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                <option value="">Select Relationship</option>
                                <option value="Parent" {{ old('relationship_to_victim')=='Parent' ? 'selected' : '' }}>
                                    Parent</option>
                                <option value="Legal Guardian" {{ old('relationship_to_victim')=='Legal Guardian'
                                    ? 'selected' : '' }}>Legal Guardian</option>
                                <option value="Grandparent" {{ old('relationship_to_victim')=='Grandparent' ? 'selected'
                                    : '' }}>Grandparent</option>
                                <option value="Aunt/Uncle" {{ old('relationship_to_victim')=='Aunt/Uncle' ? 'selected'
                                    : '' }}>Aunt/Uncle</option>
                                <option value="Sibling" {{ old('relationship_to_victim')=='Sibling' ? 'selected' : ''
                                    }}>Sibling</option>
                                <option value="Other" {{ old('relationship_to_victim')=='Other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perpetrator Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#C41E3A] px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-user-slash"></i>
                    Perpetrator Information
                </h3>
                <span
                    class="text-xs text-white/90 font-medium uppercase tracking-wider bg-white/10 px-2.5 py-1 rounded">
                    Mandatory Details
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Perpetrator Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="abuser_name" name="perpetrator_name" required
                            value="{{ old('perpetrator_name') }}" placeholder="Full Name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <label for="abuser_address"
                            class="block text-sm font-medium text-gray-700 mt-3 mb-2">Perpetrator Address <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="abuser_address" name="abuser_address" maxlength="2000"
                            value="{{ old('abuser_address', $case->abuser_address ?? '') }}" placeholder="Full Address"
                            class="form-control w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Perpetrator Age <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="abuser_age" name="abuser_age" min="1" max="120" required
                            value="{{ old('abuser_age') }}" placeholder="Age (e.g. 25)"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Perpetrator age is mandatory.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship to Victim <span class="text-red-500">*</span>
                        </label>
                        <select id="abuse_relationship" name="abuse_relationship" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Relationship</option>
                            <option value="spouse" {{ old('abuse_relationship')=='spouse' ? 'selected' : '' }}>Spouse
                            </option>
                            <option value="partner" {{ old('abuse_relationship')=='partner' ? 'selected' : '' }}>Live-in
                                Partner</option>
                            <option value="parent" {{ old('abuse_relationship')=='parent' ? 'selected' : '' }}>Parent
                            </option>
                            <option value="child" {{ old('abuse_relationship')=='child' ? 'selected' : '' }}>Child
                            </option>
                            <option value="sibling" {{ old('abuse_relationship')=='sibling' ? 'selected' : '' }}>Sibling
                            </option>
                            <option value="relative" {{ old('abuse_relationship')=='relative' ? 'selected' : '' }}>Other
                                Relative</option>
                            <option value="acquaintance" {{ old('abuse_relationship')=='acquaintance' ? 'selected' : ''
                                }}>Acquaintance</option>
                            <option value="stranger" {{ old('abuse_relationship')=='stranger' ? 'selected' : '' }}>
                                Stranger</option>
                        </select>
                    </div>
                </div>

                <!-- Guardian of Perpetrator (for minor perpetrators under 18) -->
                <div id="guardian-abuser-section" class="hidden mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-red-500"></i>
                        Guardian of Perpetrator Information
                        <span class="text-xs font-semibold text-red-600">(Required when perpetrator is a minor under
                            18)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guardian Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="guardian_abuser_name" name="guardian_abuser_name"
                                value="{{ old('guardian_abuser_name') }}" placeholder="Full Name of Guardian"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guardian Contact <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="guardian_abuser_contact" name="guardian_abuser_contact" maxlength="11"
                                pattern="[0-9]{11}" inputmode="numeric" value="{{ old('guardian_abuser_contact') }}"
                                placeholder="09XXXXXXXXX (11 digits)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Guardian Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="guardian_abuser_address" name="guardian_abuser_address"
                                value="{{ old('guardian_abuser_address') }}" placeholder="Complete Address"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Relationship to Perpetrator <span class="text-red-500">*</span>
                            </label>
                            <select id="relationship_to_abuser" name="relationship_to_abuser"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                <option value="">Select Relationship</option>
                                <option value="Parent" {{ old('relationship_to_abuser')=='Parent' ? 'selected' : '' }}>
                                    Parent</option>
                                <option value="Legal Guardian" {{ old('relationship_to_abuser')=='Legal Guardian'
                                    ? 'selected' : '' }}>Legal Guardian</option>
                                <option value="Grandparent" {{ old('relationship_to_abuser')=='Grandparent' ? 'selected'
                                    : '' }}>Grandparent</option>
                                <option value="Other Relative" {{ old('relationship_to_abuser')=='Other Relative'
                                    ? 'selected' : '' }}>Other Relative</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incident Details Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#C41E3A] px-6 py-4" style="background: linear-gradient(90deg, #C41E3A 0%, #A01830 100%);">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    Incident Details
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Incident <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="incident_date" name="incident_date" required
                            value="{{ old('incident_date', $todayPst) }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Time of Incident <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="incident_time" name="incident_time" required
                            value="{{ old('incident_time') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Reported <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_reported" name="date_reported" required
                            value="{{ old('date_reported', $todayPst) }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type of Abuse <span class="text-red-500">*</span>
                    </label>
                    <select id="abuse_type" name="abuse_type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                        <option value="">Select type of abuse</option>
                        @foreach($caseTypes as $caseType)
                        @if(strcasecmp($caseType->republic_act, 'Others') !== 0)
                        <option value="{{ $caseType->republic_act }}" {{ old('abuse_type')==$caseType->republic_act ?
                            'selected' : '' }}>
                            @if($caseType->republic_act) {{ $caseType->republic_act }} - @endif
                            {{ $caseType->name }}
                        </option>
                        @endif
                        @endforeach
                        <option value="Others" {{ old('abuse_type')=='Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>

                <!-- Specific Type of Abuse Explanation (Displayed when Others is selected) -->
                <div id="other-abuse-container"
                    class="{{ old('abuse_type') == 'Others' ? '' : 'hidden' }} p-4 rounded-xl border border-purple-200 bg-purple-50/50 transition-all duration-200">
                    <label for="other_abuse_details" class="block text-sm font-semibold text-gray-800 mb-1.5">
                        Specific Type of Abuse Explanation <span class="text-red-500">*</span>
                    </label>
                    <textarea id="other_abuse_details" name="other_abuse_details" rows="3"
                        placeholder="{{ $isBCPCCase ? 'Please provide a detailed explanation of the specific type of abuse that occurred...' : 'Please explain the specific type of abuse committed...' }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white resize-none">{{ old('other_abuse_details') }}</textarea>
                    <p class="text-xs text-gray-600 mt-1 flex items-center gap-1.5">
                        <i class="fas fa-info-circle text-purple-600"></i>
                        <span>{{ $isBCPCCase ? 'Detailed explanation of the specific child abuse or offense is required
                            when Others is selected.' : 'Detailed explanation of the specific abuse committed is
                            required when Others is selected.' }}</span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Incident Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="incident_location" name="incident_location" required
                        value="{{ old('incident_location') }}" placeholder="Where did the incident happen?"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Incident Details <span class="text-red-500">*</span>
                    </label>
                    <textarea id="incident_details" name="incident_details" rows="4" required
                        placeholder="Provide details about the case"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm resize-none">{{ old('incident_details') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Witness Name (if any)</label>
                        <input type="text" id="witness_name" name="witness_name" value="{{ old('witness_name') }}"
                            placeholder="Full name of witness"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Witness Contact</label>
                        <input type="tel" id="witness_contact" name="witness_contact" maxlength="11" pattern="[0-9]*"
                            inputmode="numeric" value="{{ old('witness_contact') }}"
                            placeholder="Contact number (11 digits)"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 pt-4">
            <a href="{{ route('desk-officer.cases.index', ['type' => $type]) }}"
                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-center w-full sm:w-auto">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition-colors text-sm font-medium shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                <i class="fas fa-save"></i>
                <span>Save {{ $isBCPCCase ? 'BCPC' : 'SWD' }} Case</span>
            </button>
        </div>
    </form>
</main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const isBCPCCase = {{ $isBCPCCase ? 'true' : 'false' }};
    const isSwdCase = !isBCPCCase;
    const victimAgeInput = document.getElementById('victim_age');
    const victimAgeError = document.getElementById('victim_age_error');
    let hasVictimAgeLimitError = false;
    const guardianSection = document.getElementById('guardian-section');
    const guardianInputs = guardianSection ? guardianSection.querySelectorAll('input, select') : [];
    
    // Enforce the 17-and-below limit for victim information only.
    function enforceVictimAgeRule() {
        if (!victimAgeInput || isSwdCase) return;
        const val = parseInt(victimAgeInput.value);
        if (!isNaN(val) && val > 17) {
            victimAgeInput.value = '';
            hasVictimAgeLimitError = true;
            if (victimAgeError) victimAgeError.classList.remove('hidden');
            victimAgeInput.setCustomValidity('Victim age can only be 17 and below.');
        } else if (!isNaN(val) && val < 0) {
            victimAgeInput.value = 0;
            hasVictimAgeLimitError = false;
            if (victimAgeError) victimAgeError.classList.add('hidden');
            victimAgeInput.setCustomValidity('');
        } else if (isNaN(val) && hasVictimAgeLimitError) {
            if (victimAgeError) victimAgeError.classList.remove('hidden');
            victimAgeInput.setCustomValidity('Victim age can only be 17 and below.');
        } else {
            hasVictimAgeLimitError = false;
            if (victimAgeError) victimAgeError.classList.add('hidden');
            victimAgeInput.setCustomValidity('');
        }
    }
    
    // Toggle guardian section based on age or BCPC requirement
    function toggleGuardianSection() {
        if (!victimAgeInput || !guardianSection) return;
        enforceVictimAgeRule();
        const age = parseInt(victimAgeInput.value);
        if (isBCPCCase || (age && age < 18)) {
            guardianSection.classList.remove('hidden');
            guardianInputs.forEach(input => input.required = true);
        } else {
            guardianSection.classList.add('hidden');
            guardianInputs.forEach(input => {
                input.required = false;
            });
        }
    }
    
    if (victimAgeInput) {
        victimAgeInput.addEventListener('input', toggleGuardianSection);
        victimAgeInput.addEventListener('change', toggleGuardianSection);
        victimAgeInput.addEventListener('keyup', toggleGuardianSection);
        toggleGuardianSection();
    }
    
    // Toggle guardian of abuser section based on abuser age
    const abuserAgeInput = document.getElementById('abuser_age');
    const guardianAbuserSection = document.getElementById('guardian-abuser-section');
    const guardianAbuserInputs = guardianAbuserSection ? guardianAbuserSection.querySelectorAll('input, select') : [];
    
    function toggleGuardianAbuserSection() {
        if (!abuserAgeInput || !guardianAbuserSection) return;
        const age = parseInt(abuserAgeInput.value);
        if (age && age < 18) {
            guardianAbuserSection.classList.remove('hidden');
            guardianAbuserInputs.forEach(input => input.required = true);
        } else {
            guardianAbuserSection.classList.add('hidden');
            guardianAbuserInputs.forEach(input => {
                input.required = false;
                input.value = '';
            });
        }
    }
    
    if (abuserAgeInput) {
        abuserAgeInput.addEventListener('input', toggleGuardianAbuserSection);
        abuserAgeInput.addEventListener('change', toggleGuardianAbuserSection);
        toggleGuardianAbuserSection();
    }
    
    // Toggle 'Others' abuse explanation field
    const abuseTypeSelect = document.getElementById('abuse_type');
    const otherAbuseContainer = document.getElementById('other-abuse-container');
    const otherAbuseInput = document.getElementById('other_abuse_details');
    
    function toggleOtherAbuseSection() {
        if (!abuseTypeSelect || !otherAbuseContainer) return;
        if (abuseTypeSelect.value === 'Others') {
            otherAbuseContainer.classList.remove('hidden');
            if (otherAbuseInput) {
                otherAbuseInput.required = true;
            }
        } else {
            otherAbuseContainer.classList.add('hidden');
            if (otherAbuseInput) {
                otherAbuseInput.required = false;
                otherAbuseInput.value = '';
            }
        }
    }
    
    if (abuseTypeSelect) {
        abuseTypeSelect.addEventListener('change', toggleOtherAbuseSection);
        toggleOtherAbuseSection();
    }
    
    // Numeric-only for phone fields
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
        });
    });
    
    // Case number auto-generation on load
    const caseNumberInput = document.getElementById('case-number');
    const barangayId = document.getElementById('case-designation')?.value;
    const caseType = '{{ $isBCPCCase ? 'BCPC' : 'VAWC' }}';
    const displayCaseType = @json($isMultiroleDeskOfficer && !$isBCPCCase ? 'SWD' : ($isBCPCCase ? 'BCPC' : 'VAWC'));
    const displayCaseNumber = value => @json($isMultiroleDeskOfficer && !$isBCPCCase) ? String(value || '').replace(/^VAWC-/, 'SWD-') : value;
    
    if (barangayId && caseNumberInput) {
        caseNumberInput.value = 'Generating...';
        fetch(@if($isMultiroleDeskOfficer) @json(route('api.cases.generate-number', ['barangayId' => $userBarangay->id, 'caseType' => $isBCPCCase ? 'BCPC' : 'VAWC'])) @else `/api/cases/generate-number/${barangayId}/${caseType}` @endif)
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                caseNumberInput.value = displayCaseNumber(data.case_number) || `${@json(($isMultiroleDeskOfficer || $isSwdDeskOfficer) && !$isBCPCCase ? 'SWD' : ($isBCPCCase ? 'BCPC' : 'VAWC'))}-${barangayId}-${Date.now()}`;
            })
            .catch(() => {
                // Fallback attempt without /api prefix
                fetch(`/cases/generate-number/${barangayId}/${caseType}`)
                    .then(res => res.json())
                    .then(data => {
                        caseNumberInput.value = displayCaseNumber(data.case_number);
                    })
                    .catch(() => {
                        caseNumberInput.value = `${displayCaseType}-${barangayId}-${Math.floor(Math.random() * 1000).toString().padStart(3, '0')}`;
                    });
            });
    }
    
    // Form submission with SweetAlert2 confirmation
    const form = document.getElementById('new-case-form');
    if (form) {
        let hasFormChanges = false;
        const markFormChanged = () => {
            hasFormChanges = true;
            window.portalUnsavedChanges?.setDirty(true);
        };
        form.addEventListener('input', markFormChanged);
        form.addEventListener('change', markFormChanged);

        const changeCaseTypeButton = document.getElementById('change-case-type');
        if (changeCaseTypeButton) {
            changeCaseTypeButton.addEventListener('click', (event) => {
                event.preventDefault();
                const switchCaseType = () => {
                    window.portalUnsavedChanges?.reset();
                    window.location.assign(changeCaseTypeButton.dataset.changeCaseTypeUrl);
                };

                if (hasFormChanges || window.portalUnsavedChanges?.isDirty()) {
                    if (window.portalUnsavedChanges) {
                        window.portalUnsavedChanges.setDirty(true);
                        window.portalUnsavedChanges.confirmExit(switchCaseType);
                    } else {
                        switchCaseType();
                    }
                } else {
                    switchCaseType();
                }
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Re-validate the victim age limit.
            if (victimAgeInput && !isSwdCase) {
                const vAge = parseInt(victimAgeInput.value);
                if (isNaN(vAge) || vAge > 17 || vAge < 0) {
                    victimAgeInput.focus();
                    victimAgeInput.reportValidity();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Victim Age',
                        text: 'Victim age can only be 17 and below.',
                        confirmButtonColor: '#7c3aed'
                    });
                    return;
                }
            }

            // Re-validate Perpetrator age is present
            if (abuserAgeInput && (!abuserAgeInput.value || parseInt(abuserAgeInput.value) <= 0)) {
                abuserAgeInput.focus();
                abuserAgeInput.reportValidity();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perpetrator Age Required',
                    text: 'Please enter a valid age for the perpetrator.',
                    confirmButtonColor: '#7c3aed'
                });
                return;
            }

            // Re-validate Others explanation if Others is selected
            if (abuseTypeSelect && abuseTypeSelect.value === 'Others') {
                const explanation = otherAbuseInput ? otherAbuseInput.value.trim() : '';
                if (!explanation) {
                    if (otherAbuseInput) {
                        otherAbuseInput.focus();
                        otherAbuseInput.reportValidity();
                    }
                    Swal.fire({
                        icon: 'warning',
                        title: 'Abuse Explanation Required',
                        text: 'Please provide an explanation of the specific type of abuse when selecting "Others".',
                        confirmButtonColor: '#7c3aed'
                    });
                    return;
                }
            }
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const caseTypeLabel = isBCPCCase ? 'BCPC Case (Child Protection)' : 'SWD Case';
            const victimNameVal = document.getElementById('victim_name')?.value || 'N/A';
            const victimAgeVal = victimAgeInput?.value || 'N/A';
            const victimGenderVal = document.getElementById('victim_gender')?.selectedOptions[0]?.text || 'N/A';
            const victimContactVal = document.getElementById('victim_contact')?.value || 'N/A';
            const victimAddressVal = document.getElementById('victim_address')?.value || 'N/A';
            const guardianNameVal = document.getElementById('guardian_name')?.value || 'N/A';
            const guardianContactVal = document.getElementById('guardian_contact')?.value || 'N/A';
            const perpetratorNameVal = document.getElementById('abuser_name')?.value || 'N/A';
            const perpetratorAgeVal = abuserAgeInput?.value || 'N/A';
            const perpetratorRelationVal = document.getElementById('abuse_relationship')?.selectedOptions[0]?.text || 'N/A';
            const guardianAbuserNameVal = document.getElementById('guardian_abuser_name')?.value || 'N/A';
            const guardianAbuserContactVal = document.getElementById('guardian_abuser_contact')?.value || 'N/A';
            const incidentDateVal = document.getElementById('incident_date')?.value || 'N/A';
            const incidentTimeVal = document.getElementById('incident_time')?.value || 'N/A';
            const dateReportedVal = document.getElementById('date_reported')?.value || 'N/A';
            let abuseTypeText = abuseTypeSelect?.selectedOptions[0]?.text || 'N/A';
            if (abuseTypeSelect?.value === 'Others' && otherAbuseInput?.value.trim()) {
                abuseTypeText = 'Others: ' + otherAbuseInput.value.trim();
            }
            const incidentLocationVal = document.getElementById('incident_location')?.value || 'N/A';
            const incidentDetailsVal = document.getElementById('incident_details')?.value || 'N/A';
            const witnessNameVal = document.getElementById('witness_name')?.value || 'N/A';
            const witnessContactVal = document.getElementById('witness_contact')?.value || 'N/A';
            const prioritySelect = document.getElementById('priority');
            const priorityVal = prioritySelect?.selectedOptions?.[0]?.text || prioritySelect?.value || 'Non-urgent';
            const statusSelect = document.getElementById('status');
            const statusVal = statusSelect?.selectedOptions?.[0]?.text || statusSelect?.value || 'Case Filed';
            
            Swal.fire({
                title: 'Confirm Case Submission',
                html: `
                    <div class="text-left py-2">
                        <p class="text-gray-600 mb-3 text-sm">Please review the case details before submitting:</p>
                        <div class="bg-gray-50 border border-gray-200 p-3.5 rounded-lg text-sm space-y-2 max-h-96 overflow-y-auto">
                            <p><strong>Case Type:</strong> <span class="${isBCPCCase ? 'text-red-600 font-bold' : 'text-purple-700 font-bold'}">${caseTypeLabel}</span></p>
                            <p><strong>Barangay:</strong> {{ $userBarangay->name }}</p>
                            <p><strong>Case Status:</strong> <span class="font-medium text-gray-900">${statusVal.toLowerCase() === 'case closed' ? 'Case Closed' : statusVal}</span></p>
                            <p><strong>Case Priority:</strong> <span class="font-medium text-gray-900">${priorityVal}</span></p>
                            <hr class="my-2">
                            <p class="font-semibold text-gray-700">Victim Information:</p>
                            <p><strong>Name:</strong> ${victimNameVal}</p>
                            <p><strong>Age:</strong> ${victimAgeVal} yrs old</p>
                            <p><strong>Gender:</strong> ${victimGenderVal}</p>
                            <p><strong>Contact:</strong> ${victimContactVal}</p>
                            <p><strong>Address:</strong> ${victimAddressVal}</p>
                            ${guardianNameVal !== 'N/A' ? `<p><strong>Guardian Name:</strong> ${guardianNameVal}</p>` : ''}
                            ${guardianContactVal !== 'N/A' ? `<p><strong>Guardian Contact:</strong> ${guardianContactVal}</p>` : ''}
                            <hr class="my-2">
                            <p class="font-semibold text-gray-700">Perpetrator Information:</p>
                            <p><strong>Name:</strong> ${perpetratorNameVal}</p>
                            <p><strong>Age:</strong> ${perpetratorAgeVal} yrs old</p>
                            <p><strong>Relationship to Victim:</strong> ${perpetratorRelationVal}</p>
                            ${guardianAbuserNameVal !== 'N/A' ? `<p><strong>Perpetrator Guardian Name:</strong> ${guardianAbuserNameVal}</p>` : ''}
                            ${guardianAbuserContactVal !== 'N/A' ? `<p><strong>Perpetrator Guardian Contact:</strong> ${guardianAbuserContactVal}</p>` : ''}
                            <hr class="my-2">
                            <p class="font-semibold text-gray-700">Incident Details:</p>
                            <p><strong>Date of Incident:</strong> ${incidentDateVal}</p>
                            <p><strong>Time of Incident:</strong> ${incidentTimeVal}</p>
                            <p><strong>Date Reported:</strong> ${dateReportedVal}</p>
                            <p><strong>Type of Abuse:</strong> <span class="font-medium text-gray-900">${abuseTypeText}</span></p>
                            <p><strong>Location:</strong> ${incidentLocationVal}</p>
                            <p><strong>Details:</strong> ${incidentDetailsVal}</p>
                            <hr class="my-2">
                            <p class="font-semibold text-gray-700">Witness Information:</p>
                            <p><strong>Witness Name:</strong> ${witnessNameVal}</p>
                            <p><strong>Witness Contact:</strong> ${witnessContactVal}</p>
                            <hr class="my-2">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save mr-1"></i> Submit Case',
                cancelButtonText: 'Review Again',
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting Case...',
                        text: 'Please wait while we record the case details.',
                        allowOutsideClick: false, showConfirmButton: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Case Created Successfully!',
                                text: data.message,
                                confirmButtonColor: '#7c3aed',
                                confirmButtonText: 'View Cases'
                            }).then(() => {
                                if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                                window.location.assign(data.case_url || data.redirect);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Error',
                                text: data.message || 'An error occurred while creating the case.',
                                confirmButtonColor: '#7c3aed'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Fallback: submit normally if AJAX fails
                        form.submit();
                    });
                }
            });
        });
    }
});
</script>
@endpush

@endsection
