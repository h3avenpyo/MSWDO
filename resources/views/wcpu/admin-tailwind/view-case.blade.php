@extends(auth()->user()->position === 'Desk Officer' ? 'layouts.desk-officer' : 'layouts.admin-tailwind')

@section('title', 'View Case - iSERVE SILANG Admin')

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 40rem) {
        html body.portal-ui.case-details-admin-mobile-nav #mobileTopBar { display: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header {
            background: #C41E3A !important;
            border-bottom-color: #99172E !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding: max(.75rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) .75rem max(.5rem, env(safe-area-inset-left)) !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header .tablet-header-row {
            display: flex !important;
            align-items: flex-start !important;
            gap: 0 !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header .portal-header-clock[hidden] {
            display: none !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header .case-details-header-leading {
            display: flex !important;
            flex-direction: row !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent #mobileMenuToggle { display: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header #dashboardMenuToggle {
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
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header :is(h1,h2,h3,p) { color: #fff !important; }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header p {
            display: block !important;
            margin-top: .125rem !important;
            opacity: .85 !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header div:has(> img[alt="User Profile"]) { display: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent > header#case-details-page-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #sidebarOverlay { display: none !important; pointer-events: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar {
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
        html body.portal-ui.case-details-admin-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar:is(.sidebar-collapsed,.sidebar-collapsed-init) {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-header { display: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-text { opacity: 1 !important; max-width: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-link { justify-content: flex-start !important; gap: .75rem !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar nav :is(a,button),
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-link { background: #C41E3A !important; color: #fff !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar nav :is(a,button) :is(i,svg,span),
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-link :is(i,svg,span) { color: #fff !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar nav :is(a,button):hover,
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-link:hover { background: #99172E !important; color: #fff !important; }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600,.bg-purple-700\/70),
        html body.portal-ui.case-details-admin-mobile-nav #sidebar nav [x-show] > :is(a,button).bg-purple-600 {
            background: #99172E !important;
            color: #fff !important;
            box-shadow: inset 3px 0 0 #fff !important;
        }
        html body.portal-ui.case-details-admin-mobile-nav #sidebar #sidebarCloseBtn,
        html body.portal-ui.case-details-admin-mobile-nav #sidebarToggleBtn { display: none !important; }
        html body.portal-ui.case-details-admin-mobile-nav #mainContent { width: 100% !important; min-width: 0 !important; margin-left: 0 !important; }
    }
</style>
@endif
@endpush

@section('content')

@php
    use App\Models\Barangay;
    $createdAt = $case->created_at ?? null;
    $updatedAt = $case->updated_at ?? null;
    
    // Resolve barangay name
    $barangayDisplay = null;
    if(isset($case->barangay) && $case->barangay && isset($case->barangay->name)){
        $barangayDisplay = $case->barangay->name;
    } elseif(isset($case->case_designation) && $case->case_designation !== '') {
        $fk = trim((string)$case->case_designation);
        if(ctype_digit($fk)){
            $b = Barangay::select('id','name')->find((int)$fk);
            $barangayDisplay = $b->name ?? $fk;
        } else {
            $barangayDisplay = $fk;
        }
    }
    $barangayDisplay = $barangayDisplay ?: 'N/A';
    
    // Status badge colors
    $statusColors = [
        'Case Filed' => 'bg-green-100 text-green-800',
        'Case Closed' => 'bg-red-100 text-red-800',
        'Under Investigation' => 'bg-blue-100 text-blue-800',
        'Active' => 'bg-orange-100 text-orange-800',
        'Resolved' => 'bg-green-100 text-green-800',
        'Closed' => 'bg-gray-100 text-gray-800',
    ];
    $statusClass = $statusColors[$case->status] ?? 'bg-gray-100 text-gray-800';
    if (($case->status ?? '') === 'Case closed') $caseStatusDisplay = 'Case Closed'; else $caseStatusDisplay = $case->status ?? 'N/A';
    $isSwdDeskOfficer = auth()->user()->position === 'Desk Officer'
        && auth()->user()->defaultCaseDepartment() === 'VAWC';
    $otherAbuseDetails = null;
    if ($isSwdDeskOfficer && strcasecmp(trim((string) $case->abuse_type), 'Others') === 0) {
        preg_match('/Specific Type of Abuse \(Others\):\s*(.+?)(?:\R{2,}|$)/s', (string) $case->incident_details, $otherAbuseMatch);
        $otherAbuseDetails = trim($otherAbuseMatch[1] ?? '');
    }
    $isDeskOfficer = auth()->user()?->position === 'Desk Officer';
    $archivedCasesBackUrl = $isDeskOfficer
        ? route('desk-officer.archived-barangay-cases.index')
    : (auth()->user()?->isBcpcAdmin()
        ? route('admin.quarterly-reports.archived', ['section' => 'barangay-cases'])
        : route('admin.quarterly-reports.archived'));
    $caseTypeLower = strtolower($case->type ?? ($case->case_type ?? 'bcpc'));
    if ($caseTypeLower === 'swd') $caseTypeLower = 'vawc';

    if ($isDeskOfficer) {
        $returnToCaseProfiles = in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)
            && request('from') === 'case-profiles';
        $casesListUrl = $returnToCaseProfiles
            ? route('case-profiles.index')
            : route('desk-officer.cases.index', auth()->user()->isMultiCaseDepartment() ? ['type' => $caseTypeLower] : []);
        $addNewCaseUrl = auth()->user()->isMultiCaseDepartment()
            ? route('desk-officer.cases.create', ['type' => $caseTypeLower, 'fresh' => 1])
            : route('desk-officer.cases.create');
    } else {
        $returnToCaseProfiles = auth()->user()->isDepartmentAdmin() && request('from') === 'case-profiles';
        $casesListUrl = $returnToCaseProfiles ? route('case-profiles.index') : route('admin-barangay-cases');
        $addNewCaseUrl = route('admin-add-new-case');
    }
@endphp
@if(((auth()->user()->isBcpcAdmin() || auth()->user()->isVawcAdmin()) && request()->boolean('bcpc_modal_preview')) || (auth()->user()->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment() && request()->boolean('archive_case_preview')))
<main class="case-profile-details bcpc-shared-case-preview">
    @include('admin-tailwind._case-details-modal', ['case' => $case, 'useAdminArchivePresentation' => true])
    @if($case->witness_name || $case->witness_contact)
    <section class="bcpc-preview-record-information">
        <h4>Witness Information</h4>
        <div><span>Name:</span><span>{{ $case->witness_name ?? 'N/A' }}</span></div>
        <div><span>Contact:</span><span>{{ $case->witness_contact ?? 'N/A' }}</span></div>
    </section>
    @endif
</main>
@else

@if($case->trashed() || auth()->user()->isBcpcAdmin() || auth()->user()->isVawcAdmin() || auth()->user()->defaultCaseDepartment() === 'BCPC' || (auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'VAWC'))
<style>
    .case-profile-details .detail-row { display: grid; grid-template-columns: max-content minmax(0, 1fr); gap: .5rem; align-items: start; }
    .case-profile-details .detail-row > :last-child { text-align: left; overflow-wrap: anywhere; }
    @media (max-width: 640px) {
        .case-profile-details { width: 100%; overflow-x: hidden; }
        .case-profile-details .detail-row { grid-template-columns: 1fr; gap: .25rem; }
        .case-profile-details > .print\:hidden { align-items: stretch; }
        .case-profile-details > .print\:hidden > * { width: 100%; }
    }
</style>
@endif

@if(auth()->user()->position === 'Desk Officer')
<style>
    @media (max-width: 26.875rem), (orientation: landscape) and (max-height: 30rem) and (max-width: 58rem) {
        .case-profile-details .case-details-actions { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; align-items: stretch; }
        .case-profile-details .case-details-primary-actions,
        .case-profile-details .case-details-print-actions { display: contents; }
        .case-profile-details .case-details-action-back { grid-column: 1 / -1; grid-row: 1; }
        .case-profile-details .case-details-action-add { grid-column: 1; grid-row: 2; }
        .case-profile-details .case-details-action-print { grid-column: 2; grid-row: 2; }
        .case-profile-details .case-details-actions :is(a, button) { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-width: 0; min-height: 3rem; padding: .5rem .75rem; text-align: center; }

        /* Archived Desk Officer view: keep Print at the action area's top-right. */
        .case-profile-details.archived-case-details .case-details-actions {
            position: relative;
            display: flex !important;
            min-height: 3rem;
            padding-right: 5rem;
            align-items: center;
        }
        .case-profile-details.archived-case-details .case-details-actions > a { width: auto; }
        .case-profile-details.archived-case-details .case-details-print-actions {
            position: absolute;
            top: 0;
            right: 0;
            display: flex !important;
            width: auto !important;
        }
        .case-profile-details.archived-case-details .case-details-action-print {
            width: 3rem !important;
            min-width: 3rem !important;
            padding: 0 !important;
        }
        .case-profile-details.archived-case-details .case-details-action-print span { display: none; }
    }
</style>
@endif

<!-- Top Header -->
<header id="case-details-page-header" class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm print:hidden">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="case-details-header-leading {{ $case->trashed() || request()->boolean('archived') ? 'flex-1 min-w-0' : '' }}">
            @if(auth()->user()?->isDepartmentAdmin())
            <button id="dashboardMenuToggle" class="hidden" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="min-w-0">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Case Details</h2>
            <p class="text-sm text-gray-500 {{ auth()->user()?->isDepartmentAdmin() || ($isDeskOfficer && ($case->trashed() || request()->boolean('archived'))) ? 'mt-1 block leading-5' : 'hidden sm:block' }}">{{ $case->case_number ?? 'N/A' }}</p>
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
<main class="case-profile-details px-4 sm:px-6 lg:px-8 pt-4 pb-4 sm:pt-6 sm:pb-6 lg:pt-8 lg:pb-8 max-w-5xl mx-auto {{ $case->trashed() ? 'archived-case-details' : '' }}">
    
    <!-- Action Buttons -->
    <div class="case-details-actions flex flex-wrap items-center justify-between gap-4 mb-6 print:hidden">
        @if($case->trashed())
        <a href="{{ $archivedCasesBackUrl }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-600 hover:text-purple-800 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Archived Barangay Cases</span>
        </a>
        @else
        <div class="case-details-primary-actions flex flex-wrap items-center gap-3">
            <a href="{{ $casesListUrl }}" class="case-details-action-back inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>{{ isset($returnToCaseProfiles) && $returnToCaseProfiles ? 'Back to Case Profile' : 'Back to lists' }}</span>
            </a>
            <a href="{{ $addNewCaseUrl }}" class="case-details-action-add inline-flex items-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition-colors text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Add New Case</span>
            </a>
        </div>
        @endif
        <div class="case-details-print-actions flex flex-wrap gap-3">
            @if($case->trashed())
            <button type="button" disabled class="case-details-action-print inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 border border-red-200 rounded-lg cursor-not-allowed text-sm font-medium shadow-sm" aria-label="Print unavailable for archived case">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            @else
            <a href="{{ route('admin.case.print', $case->id) }}" target="_blank" class="case-details-action-print inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </a>
            @endif
            @unless($case->trashed() || auth()->user()->isDepartmentAdmin() || auth()->user()->defaultCaseDepartment() === 'BCPC' || (auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'VAWC'))
            @php $referralUnavailable = auth()->user()->isBcpcAdmin() && preg_match('/referred|closed|complet/i', trim((string) $case->status)); @endphp
            <button type="button" id="referCaseBtn" {{ $referralUnavailable ? 'disabled' : '' }} class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium {{ $referralUnavailable ? 'cursor-not-allowed border border-red-200 bg-red-50 text-red-800' : 'bg-purple-700 text-white hover:bg-purple-800 transition-colors' }}">
                <i class="fas fa-share-square"></i>
                <span>Refer Case</span>
            </button>
            @endunless
        </div>
    </div>

    <!-- Case Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $case->case_number ?? 'N/A' }}</h1>
                    <p class="text-purple-200 text-sm mt-1">Filed: {{ $createdAt ? $createdAt->format('F j, Y') : 'N/A' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $statusClass }}">{{ $caseStatusDisplay }}</span>
                    @php $priorityClass = ['Emergency' => 'bg-red-100 text-red-800', 'Urgent' => 'bg-amber-100 text-amber-800', 'Non-urgent' => 'bg-green-100 text-green-800'][$case->priority] ?? 'bg-gray-100 text-gray-800'; @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $priorityClass }}">{{ $case->priority ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ $isSwdDeskOfficer ? 'Types of Abuse' : 'Type of Abuse' }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $case->abuse_type ?? 'N/A' }}</p>
                </div>
                @if($isSwdDeskOfficer && strcasecmp(trim((string) $case->abuse_type), 'Others') === 0)
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Other Abuse Details</p>
                    <p class="text-sm font-medium text-gray-800 whitespace-pre-wrap">{{ $otherAbuseDetails ?: 'No details provided.' }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Barangay</p>
                    <p class="text-sm font-medium text-gray-800">{{ $barangayDisplay }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date Reported</p>
                    <p class="text-sm font-medium text-gray-800">{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('F j, Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Victim Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-shield text-blue-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Victim Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Full Name</span>
                    <span class="text-sm font-medium text-gray-800">{{ censorLastName($case->victim_name) }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Age</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->victim_age ?? 'N/A' }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Gender</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->victim_gender ?? 'N/A' }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Contact</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->victim_contact ?? 'N/A' }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50 sm:col-span-2">
                    <span class="text-sm text-gray-500">Address</span>
                    <span class="text-sm font-medium text-gray-800 text-right">{{ $case->victim_address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Guardian Information (if exists) -->
    @if($case->guardian || $case->guardian_contact || $case->guardian_address || $case->relationship_to_victim)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-friends text-indigo-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Guardian Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                @if($case->guardian)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Guardian Name</span>
                    <span class="text-sm font-medium text-gray-800">{{ censorLastName($case->guardian) }}</span>
                </div>
                @endif
                @if($case->guardian_contact)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Contact</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->guardian_contact }}</span>
                </div>
                @endif
                @if($case->guardian_address)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50 sm:col-span-2">
                    <span class="text-sm text-gray-500">Address</span>
                    <span class="text-sm font-medium text-gray-800 text-right">{{ $case->guardian_address }}</span>
                </div>
                @endif
                @if($case->relationship_to_victim)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Relationship</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->relationship_to_victim }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    <!-- Perpetrator Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-slash text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Perpetrator Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Full Name</span>
                    <span class="text-sm font-medium text-gray-800">{{ censorLastName($case->abuser_name) }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Age</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->abuser_age ?? 'N/A' }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Relationship to Victim</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->abuse_relationship ?? 'N/A' }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50 sm:col-span-2">
                    <span class="text-sm text-gray-500">Address of the Perpetrator</span>
                    <span class="text-sm font-medium text-gray-800 text-right">{{ $case->abuser_address ?: 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Guardian of Perpetrator Information (if exists) -->
    @if($case->guardian_abuser_name || $case->guardian_abuser_contact || $case->guardian_abuser_address || $case->relationship_to_abuser)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-tie text-orange-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Guardian of Perpetrator Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                @if($case->guardian_abuser_name)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Guardian Name</span>
                    <span class="text-sm font-medium text-gray-800">{{ censorLastName($case->guardian_abuser_name) }}</span>
                </div>
                @endif
                @if($case->guardian_abuser_contact)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Contact</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->guardian_abuser_contact }}</span>
                </div>
                @endif
                @if($case->guardian_abuser_address)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50 sm:col-span-2">
                    <span class="text-sm text-gray-500">Address</span>
                    <span class="text-sm font-medium text-gray-800 text-right">{{ $case->guardian_abuser_address }}</span>
                </div>
                @endif
                @if($case->relationship_to_abuser)
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Relationship</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->relationship_to_abuser }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    

    <!-- Incident Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-orange-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Incident Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date of Incident</p>
                    <p class="text-sm font-medium text-gray-800">{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Time of Incident</p>
                    <p class="text-sm font-medium text-gray-800">{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Location</p>
                    <p class="text-sm font-medium text-gray-800">{{ $case->incident_location ?? 'N/A' }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Description</p>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap">{{ $case->incident_details ?? 'No details provided.' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Witness Information (if exists) -->
    @if($case->witness_name || $case->witness_contact)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-eye text-teal-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Witness Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Name</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->witness_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row flex justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Contact</span>
                    <span class="text-sm font-medium text-gray-800">{{ $case->witness_contact ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Record Metadata -->
    @unless(auth()->user()->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment() && request()->boolean('archive_case_preview'))
    <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-500 print:bg-white print:border print:border-gray-200">
        <div class="flex flex-wrap justify-between gap-4">
            <div>
                <p>Created: <span class="font-medium text-gray-700">{{ $createdAt ? $createdAt->format('F j, Y \a\t g:i A') : 'N/A' }}</span></p>
                <p>Last Updated: <span class="font-medium text-gray-700">{{ $updatedAt ? $updatedAt->format('F j, Y \a\t g:i A') : 'N/A' }}</span></p>
            </div>
            <p class="uppercase tracking-wider">All times Asia/Manila (PST)</p>
        </div>
    </div>
    @endunless
</main>

@push('scripts')
<script>
document.getElementById('referCaseBtn')?.addEventListener('click', function() {
    Swal.fire({
        title: 'Refer Case',
        text: 'This will send the case details to the configured recipients.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        confirmButtonText: 'Send Referral',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement referral logic here
            Swal.fire('Sent!', 'The case referral has been sent.', 'success');
        }
    });
});
</script>
@endpush

@endif
@endsection
