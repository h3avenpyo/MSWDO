@extends('layouts.admin-tailwind')

@section('title', 'Add New Case - iSERVE SILANG Admin')

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        #add-case-form-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            width: 100%;
        }
        #add-case-form-actions > :is(a, button) {
            border-radius: .5rem !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-width: 0;
            min-height: 2.75rem;
            padding: .625rem .5rem !important;
            text-align: center;
            white-space: nowrap;
        }
    }
    @media (max-width: 40rem) {
        html body.portal-ui.add-case-admin-mobile-nav #mobileTopBar { display: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header button {
            min-width: 3rem;
            min-height: 3rem;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header {
            background: #C41E3A !important;
            border-bottom-color: #99172E !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding: max(.75rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) .75rem max(.5rem, env(safe-area-inset-left)) !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header .tablet-header-row {
            display: flex !important;
            align-items: flex-start !important;
            gap: 0 !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header .add-case-header-leading {
            display: flex !important;
            flex-direction: row !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent #mobileMenuToggle { display: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header #dashboardMenuToggle {
            position: relative !important;
            left: 0 !important;
            margin-left: 0 !important;
            transform: none !important;
            display: inline-flex !important;
            flex: 0 0 2.5rem;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #fff !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header :is(h1,h2,h3,p) { color: #fff !important; }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header p {
            display: block !important;
            margin-top: .125rem !important;
            opacity: .85 !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header div:has(> img[alt="User Profile"]) { display: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent > header#add-case-page-header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #sidebarOverlay { display: none !important; pointer-events: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar {
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
            box-shadow: 0 14px 28px rgba(0,0,0,.22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            background: #C41E3A !important;
            background-image: none !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar:is(.sidebar-collapsed,.sidebar-collapsed-init) {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-header { display: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-text { opacity: 1 !important; max-width: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-link { justify-content: flex-start !important; gap: .75rem !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar nav :is(a,button),
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-link { background: #C41E3A !important; color: #fff !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar nav :is(a,button) :is(i,svg,span),
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-link :is(i,svg,span) { color: #fff !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar nav :is(a,button):hover,
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-link:hover { background: #99172E !important; color: #fff !important; }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600,.bg-purple-700\/70),
        html body.portal-ui.add-case-admin-mobile-nav #sidebar nav [x-show] > :is(a,button).bg-purple-600 {
            background: #99172E !important;
            color: #fff !important;
            box-shadow: inset 3px 0 0 #fff !important;
        }
        html body.portal-ui.add-case-admin-mobile-nav #sidebar #sidebarCloseBtn,
        html body.portal-ui.add-case-admin-mobile-nav #sidebarToggleBtn { display: none !important; }
        html body.portal-ui.add-case-admin-mobile-nav #mainContent { width: 100% !important; min-width: 0 !important; margin-left: 0 !important; }
    }

    /* Add New Case tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header#add-case-page-header,
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header#add-case-page-header h2,
        html body.portal-ui #mainContent > header.sticky h2 {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header#add-case-page-header p,
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
@endif
@endpush

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui .admin-mobile-picker-field {
            display: block !important;
            position: relative;
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }
        html body.portal-ui .admin-mobile-picker-field > :is(input[type=date], input[type=time]) {
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
        html body.portal-ui .admin-mobile-picker-field > .admin-mobile-picker-icon {
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
        html body.portal-ui .admin-mobile-picker-field > :is(input[type=date], input[type=time])::-webkit-calendar-picker-indicator {
            opacity: 0;
        }
        html body.portal-ui .admin-mobile-picker-field > :is(input[type=date], input[type=time])::-webkit-date-and-time-value {
            text-align: left;
            min-width: 0;
        }
    }
</style>
@endif
@endpush
@section('content')

@php
    use Carbon\Carbon;
    $nowPst = Carbon::now('Asia/Manila');
    $todayPst = $nowPst->format('Y-m-d');
@endphp

<!-- Top Header -->
<header id="add-case-page-header" class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="add-case-header-leading flex items-center gap-3 min-w-0">
            @if(auth()->user()?->isDepartmentAdmin())
            <button id="dashboardMenuToggle" class="hidden" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Add New Case</h2>
                <p class="text-sm text-gray-500 {{ auth()->user()?->isDepartmentAdmin() ? '' : 'hidden sm:block' }}">Create a new case record</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('admin-tailwind.components.notifications')
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="px-4 sm:px-6 lg:px-8 pt-4 pb-4 sm:pt-6 sm:pb-6 lg:pt-8 lg:pb-8 max-w-5xl mx-auto">
    <!-- Form Note -->
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-6">
        <p class="text-sm text-amber-800">
            <strong>Note:</strong> Fields marked with <span class="text-red-500">*</span> are required.
        </p>
    </div>
    
    <!-- Case Form -->
    <form id="new-case-form" action="{{ route('cases.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Case Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="case-form-section-header bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-folder-open"></i>
                    Case Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Case Designation <span class="text-red-500">*</span>
                        </label>
                        <select id="case-designation" name="case_designation" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }} - [{{ $barangay->code }}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Case Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="case-number" name="case_number" readonly disabled
                            placeholder="Select a barangay to generate"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 text-sm">
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Case Priority <span class="text-red-500">*</span>
                        </label>
                        <select id="priority" name="priority" required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Priority</option>
                            <option value="Non-urgent" selected>Non-urgent</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Case Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="Case Filed" selected>Case Filed</option>
                            <option value="Case closed">Case Closed</option>
                            <option value="Case Referred">Case Referred</option>
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
                        <input type="text" id="victim_name" name="victim_name" required placeholder="Full Name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Age <span class="text-red-500">*</span>
                            @if($caseDepartment === 'BCPC')
                                <span class="text-xs text-red-600 font-bold">(0 - 17 only)</span>
                            @endif
                        </label>
                        <input type="number" id="victim_age" name="victim_age" required 
                            min="{{ $caseDepartment === 'BCPC' ? 0 : 1 }}" 
                            max="{{ $caseDepartment === 'BCPC' ? 17 : 120 }}" 
                            placeholder="{{ $caseDepartment === 'BCPC' ? 'Age (0-17)' : 'Age' }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        @if($caseDepartment === 'BCPC')
                            <p class="text-xs text-gray-500 mt-1">BCPC cases cover children ages 0 to 17 years old.</p>
                            <p id="victim_age_error" class="text-xs text-red-600 font-semibold mt-1 hidden">
                                <i class="fas fa-exclamation-circle"></i> BCPC cases only allow ages 17 and below. Age cannot be higher than 17.
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="victim_gender" name="victim_gender" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                            <option value="LGBTQIA+">LGBTQIA+</option>
                            <option value="Prefer not to say">Prefer not to say</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <input type="tel" id="victim_contact" name="victim_contact" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" placeholder="11-digit phone number"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="victim_address" name="victim_address" required placeholder="Street, Barangay, Municipality, Province"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
                
                <!-- Guardian Information (for minor victims) -->
                <div id="guardian-section" class="hidden mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-friends text-purple-500"></i>
                        Guardian Information
                        <span class="text-xs font-normal text-gray-500">(Required for minors)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name <span class="text-red-500">*</span></label>
                            <input type="text" id="guardian_name" name="guardian" placeholder="Full Name of Guardian"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact <span class="text-red-500">*</span></label>
                            <input type="tel" id="guardian_contact" name="guardian_contact" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" placeholder="Contact Number"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Address <span class="text-red-500">*</span></label>
                            <input type="text" id="guardian_address" name="guardian_address" placeholder="Full Address"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Victim <span class="text-red-500">*</span></label>
                            <select id="relationship_to_victim" name="relationship_to_victim"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                <option value="">Select Relationship</option>
                                <option value="Parent">Parent</option>
                                <option value="Legal Guardian">Legal Guardian</option>
                                <option value="Grandparent">Grandparent</option>
                                <option value="Aunt/Uncle">Aunt/Uncle</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Perpetrator Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#C41E3A] px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-user-slash"></i>
                    Perpetrator Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Perpetrator Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="abuser_name" name="perpetrator_name" required placeholder="Full Name"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        <label for="abuser_address" class="block text-sm font-medium text-gray-700 mt-3 mb-2">Perpetrator Address <span class="text-red-500">*</span></label>
                        <input type="text" id="abuser_address" name="abuser_address" maxlength="2000" value="{{ old('abuser_address', $case->abuser_address ?? '') }}" placeholder="Full Address" class="form-control w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Perpetrator Age <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="abuser_age" name="abuser_age" min="1" max="120" @if($caseDepartment === 'BCPC') required @endif placeholder="Age"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        @if($caseDepartment === 'BCPC')
                            <p class="text-xs text-gray-500 mt-1">Perpetrator age is mandatory for BCPC cases.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship to Victim <span class="text-red-500">*</span>
                        </label>
                        <select id="abuse_relationship" name="abuse_relationship" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select Relationship</option>
                            <option value="spouse">Spouse</option>
                            <option value="partner">Live-in Partner</option>
                            <option value="parent">Parent</option>
                            <option value="child">Child</option>
                            <option value="sibling">Sibling</option>
                            <option value="relative">Other Relative</option>
                            <option value="acquaintance">Acquaintance</option>
                            <option value="stranger">Stranger</option>
                        </select>
                    </div>
                </div>
                
                <!-- Guardian of Perpetrator (for minor perpetrators) -->
                <div id="guardian-abuser-section" class="hidden mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-red-500"></i>
                        Guardian of Perpetrator Information
                        <span class="text-xs font-normal text-gray-500">(Required for minor perpetrator)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                            <input type="text" id="guardian_abuser_name" name="guardian_abuser_name" placeholder="Full Name"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact</label>
                            <input type="tel" id="guardian_abuser_contact" name="guardian_abuser_contact" placeholder="09XXXXXXXXX"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Address</label>
                            <input type="text" id="guardian_abuser_address" name="guardian_abuser_address" placeholder="Complete Address"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Perpetrator</label>
                            <select id="relationship_to_abuser" name="relationship_to_abuser"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                                <option value="">Select Relationship</option>
                                <option value="Parent">Parent</option>
                                <option value="Legal Guardian">Legal Guardian</option>
                                <option value="Grandparent">Grandparent</option>
                                <option value="Other Relative">Other Relative</option>
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
                        <div class="admin-mobile-picker-field" style="display: contents;"><input type="date" id="incident_date" name="incident_date" required value="{{ old('incident_date', $todayPst) }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"><svg class="admin-mobile-picker-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Time of Incident <span class="text-red-500">*</span>
                        </label>
                        <div class="admin-mobile-picker-field" style="display: contents;"><input type="time" id="incident_time" name="incident_time" required value="{{ old('incident_time') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"><svg class="admin-mobile-picker-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Reported <span class="text-red-500">*</span>
                        </label>
                        <div class="admin-mobile-picker-field" style="display: contents;"><input type="date" id="date_reported" name="date_reported" required value="{{ old('date_reported', $todayPst) }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"><svg class="admin-mobile-picker-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
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
                            <option value="{{ $caseType->republic_act }}">
                                @if($caseType->republic_act) {{ $caseType->republic_act }} - @endif
                                {{ $caseType->name }}
                            </option>
                            @endif
                        @endforeach
                        <option value="Others">Others</option>
                    </select>
                </div>

                <!-- Specific Type of Abuse Explanation (Displayed when Others is selected) -->
                <div id="other-abuse-container" class="hidden p-4 rounded-xl border border-purple-200 bg-purple-50/50 transition-all duration-200">
                    <label for="other_abuse_details" class="block text-sm font-semibold text-gray-800 mb-1.5">
                        Specific Type of Abuse Explanation <span class="text-red-500">*</span>
                    </label>
                    <textarea id="other_abuse_details" name="other_abuse_details" rows="3"
                        placeholder="{{ $caseDepartment === 'BCPC' ? 'Please provide a detailed explanation of the specific type of abuse that occurred...' : 'Please explain the specific type of abuse committed...' }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white resize-none"></textarea>
                    <p class="text-xs text-gray-600 mt-1 flex items-center gap-1.5">
                        <i class="fas fa-info-circle text-purple-600"></i>
                        <span>{{ $caseDepartment === 'BCPC' ? 'Detailed explanation of the specific child abuse or offense is required when Others is selected.' : 'Detailed explanation of the specific abuse committed is required when Others is selected.' }}</span>
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Incident Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="incident_location" name="incident_location" required placeholder="Where did the incident happen?"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Incident Details <span class="text-red-500">*</span>
                    </label>
                    <textarea id="incident_details" name="incident_details" rows="4" required placeholder="Provide details about the case"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm resize-none"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Witness Name (if any)</label>
                        <input type="text" id="witness_name" name="witness_name" placeholder="Full name of witness"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Witness Contact</label>
                        <input type="tel" id="witness_contact" name="witness_contact" maxlength="11" pattern="[0-9]*" inputmode="numeric" placeholder="Contact number"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hidden Fields -->
        <input type="hidden" name="timezone_used" value="Asia/Manila">
        
        <!-- Form Actions -->
        <div id="add-case-form-actions" class="flex justify-end gap-4 pt-4">
            <a href="{{ route('admin-barangay-cases') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium shadow-sm">
                <i class="fas fa-save mr-2"></i>Save Case
            </button>
        </div>
    </form>
</main>

@push('scripts')
<script src="{{ asset('js/case-number.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const victimAgeInput = document.getElementById('victim_age');
    const guardianSection = document.getElementById('guardian-section');
    const guardianInputs = guardianSection.querySelectorAll('input, select');
    
    // Toggle guardian section based on age
    function toggleGuardianSection() {
        const age = parseInt(victimAgeInput.value);
        if (age && age < 18) {
            guardianSection.classList.remove('hidden');
            guardianInputs.forEach(input => input.required = true);
        } else {
            guardianSection.classList.add('hidden');
            guardianInputs.forEach(input => {
                input.required = false;
                input.value = '';
            });
        }
    }
    
    victimAgeInput.addEventListener('input', toggleGuardianSection);
    victimAgeInput.addEventListener('change', toggleGuardianSection);
    toggleGuardianSection();
    
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
    
    // BCPC victim age validation - auto-remove age 18+ and show warning
    const caseDepartment = '{{ $caseDepartment }}';
    const victimAgeError = document.getElementById('victim_age_error');
    
    if (caseDepartment === 'BCPC' && victimAgeInput) {
        victimAgeInput.addEventListener('input', function() {
            const age = parseInt(this.value);
            if (age > 17) {
                this.value = '';
                if (victimAgeError) {
                    victimAgeError.classList.remove('hidden');
                }
            } else {
                if (victimAgeError) {
                    victimAgeError.classList.add('hidden');
                }
            }
        });
    }
    
    // Toggle guardian of abuser section based on abuser age
    const abuserAgeInput = document.getElementById('abuser_age');
    const guardianAbuserSection = document.getElementById('guardian-abuser-section');
    
    function toggleGuardianAbuserSection() {
        const age = parseInt(abuserAgeInput.value);
        if (age && age < 18) {
            guardianAbuserSection.classList.remove('hidden');
        } else {
            guardianAbuserSection.classList.add('hidden');
            // Clear inputs when hidden
            guardianAbuserSection.querySelectorAll('input, select').forEach(input => {
                input.value = '';
            });
        }
    }
    
    abuserAgeInput.addEventListener('input', toggleGuardianAbuserSection);
    abuserAgeInput.addEventListener('change', toggleGuardianAbuserSection);
    toggleGuardianAbuserSection();
    
    // Numeric-only for phone fields
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
        });
    });
    
    // Case number generation
    const caseDesignationSelect = document.getElementById('case-designation');
    const caseNumberInput = document.getElementById('case-number');
    const caseNumberPrefix = caseDepartment === 'VAWC' ? 'SWD' : caseDepartment;
    
    caseDesignationSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`/api/cases/generate-number/${this.value}/${caseDepartment}`)
                .then(res => res.json())
                .then(data => {
                    caseNumberInput.value = data.case_number || `${caseNumberPrefix}-${this.value}-${Date.now()}`;
                })
                .catch(() => {
                    caseNumberInput.value = `${caseNumberPrefix}-${this.value}-${Math.floor(Math.random() * 1000).toString().padStart(3, '0')}`;
                });
        } else {
            caseNumberInput.value = '';
        }
    });
    
    // Form submission with confirmation
    const form = document.getElementById('new-case-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const caseTypeLabel = caseDepartment === 'BCPC' ? 'BCPC Case (Child Protection)' : 'SWD Case';
        const victimNameVal = document.getElementById('victim_name')?.value || 'N/A';
        const victimAgeVal = victimAgeInput?.value || 'N/A';
        const victimGenderVal = document.getElementById('victim_gender')?.selectedOptions[0]?.text || 'N/A';
        const victimContactVal = document.getElementById('victim_contact')?.value || 'N/A';
        const victimAddressVal = document.getElementById('victim_address')?.value || 'N/A';
        const guardianNameVal = document.getElementById('guardian_name')?.value || 'N/A';
        const guardianContactVal = document.getElementById('guardian_contact')?.value || 'N/A';
        const perpetratorNameVal = document.getElementById('abuser_name')?.value || 'N/A';
        const perpetratorAgeVal = document.getElementById('abuser_age')?.value || 'N/A';
        const perpetratorRelationVal = document.getElementById('abuse_relationship')?.selectedOptions[0]?.text || 'N/A';
        const guardianAbuserNameVal = document.getElementById('guardian_abuser_name')?.value || 'N/A';
        const guardianAbuserContactVal = document.getElementById('guardian_abuser_contact')?.value || 'N/A';
        const incidentDateVal = document.getElementById('incident_date')?.value || 'N/A';
        const incidentTimeVal = document.getElementById('incident_time')?.value || 'N/A';
        const dateReportedVal = document.getElementById('date_reported')?.value || 'N/A';
        const abuseTypeSelect = document.getElementById('abuse_type');
        let abuseTypeText = abuseTypeSelect?.selectedOptions[0]?.text || 'N/A';
        const otherAbuseInput = document.getElementById('other_abuse_details');
        if (abuseTypeSelect?.value === 'Others' && otherAbuseInput?.value.trim()) {
            abuseTypeText = 'Others: ' + otherAbuseInput.value.trim();
        }
        const incidentLocationVal = document.getElementById('incident_location')?.value || 'N/A';
        const incidentDetailsVal = document.getElementById('incident_details')?.value || 'N/A';
        const witnessNameVal = document.getElementById('witness_name')?.value || 'N/A';
        const witnessContactVal = document.getElementById('witness_contact')?.value || 'N/A';
        const priorityVal = document.getElementById('priority')?.selectedOptions[0]?.text || 'N/A';
        const statusVal = document.getElementById('status')?.selectedOptions[0]?.text || 'N/A';
        const barangayVal = document.getElementById('case-designation')?.selectedOptions[0]?.text || 'N/A';
        
        Swal.fire({
            title: 'Confirm Case Submission',
            html: `
                <div class="text-left py-2">
                    <p class="text-gray-600 mb-3 text-sm">Please review the case details before submitting:</p>
                    <div class="bg-gray-50 border border-gray-200 p-3.5 rounded-lg text-sm space-y-2 max-h-96 overflow-y-auto">
                        <p><strong>Case Type:</strong> <span class="${caseDepartment === 'BCPC' ? 'text-red-600 font-bold' : 'text-purple-700 font-bold'}">${caseTypeLabel}</span></p>
                        <p><strong>Barangay:</strong> ${barangayVal}</p>
                        <p><strong>Case Status:</strong> ${statusVal}</p>
                        <p><strong>Case Priority:</strong> ${priorityVal}</p>
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
            confirmButtonText: 'Submit Case',
            cancelButtonText: 'Review Again',
            confirmButtonColor: '#7c3aed',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Submitting Case...',
                    text: 'Please wait while we process your submission.',
                    allowOutsideClick: false, showConfirmButton: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit via AJAX
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
                        const caseUrl = @json(route('admin.barangay-cases.show', ['barangay' => 0], false)).replace(/\/0$/, '/' + data.barangay_id);
                        Swal.fire({
                            icon: 'success',
                            title: 'Case Created Successfully!',
                            text: data.message,
                            confirmButtonColor: '#7c3aed',
                            confirmButtonText: 'View Cases'
                        }).then((result) => {
                            if (result.isConfirmed) window.location.assign(caseUrl);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'An error occurred while creating the case.',
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                        confirmButtonColor: '#7c3aed'
                    });
                });
            }
        });
    });
});
</script>
@endpush

@endsection
