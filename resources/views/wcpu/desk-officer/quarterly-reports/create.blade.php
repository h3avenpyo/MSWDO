@extends('layouts.desk-officer')

@php
    $type = $requestedType ?? (request()->filled('type') ? strtoupper(request('type')) : (!Auth::user()->isMultiCaseDepartment() ? Auth::user()->defaultCaseDepartment() : null));
    $displayType = $type === 'VAWC' ? 'SWD' : $type;
@endphp

@section('title', 'Generate ' . ($displayType ? $displayType . ' ' : '') . 'Quarterly Report')

@push('styles')
<style>
    body.portal-ui #quarterly-create-actions > button[type="submit"] > .fa-file-pdf {
        color: #ffffff !important;
    }
    @media (max-width: 47.9375rem) {
        body.portal-ui #quarterly-create-actions {
            display: flex !important;
            flex-direction: column !important;
            gap: .75rem !important;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }
        body.portal-ui #quarterly-create-actions > :is(a, button) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            min-height: 3rem !important;
            margin: 0 !important;
            padding: .75rem 1rem !important;
            border-radius: .5rem !important;
            font-size: .8125rem !important;
            text-align: center !important;
        }
        body.portal-ui #quarterly-create-actions > button[type="submit"] {
            order: 0;
            background: #C41E3A !important;
            color: #fff !important;
            gap: .5rem !important;
        }
        body.portal-ui #cancel-generate-report {
            order: 1;
            background: #fff !important;
            color: #16324f !important;
            border: 1px solid #cbd5e1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }
    }
</style>
<style>
    @media (max-width: 80rem) {
        #mainContent > .quarterly-create-content {
            padding-top: .75rem !important;
            margin-top: 0 !important;
        }
        #mainContent > .quarterly-create-content > .quarterly-create-navigation {
            margin-top: 0 !important;
            margin-bottom: 1rem;
        }
        #mainContent .quarterly-create-content .quarterly-create-back {
            display: inline-flex;
            align-items: center;
            min-height: 2.5rem;
            margin-top: 0 !important;
        }
    }
    @media (max-width: 47.9375rem) {
        @if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) && !auth()->user()?->isMultiCaseDepartment())
        html body.portal-ui #mainContent > header#desk-officer-dashboard-header > div > div:last-child > .relative {
            display: flex !important;
            align-items: center !important;
            align-self: center !important;
            line-height: 1 !important;
        }
        html body.portal-ui #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] {
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
        html body.portal-ui #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }
        html body.portal-ui #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 1.05rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
        }
        html body.portal-ui #mainContent > header#desk-officer-dashboard-header #notifications-unread-count {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            padding: 0 !important;
            font-size: .75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border: 2px solid #C41E3A !important;
            border-radius: 9999px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2) !important;
        }
        @endif

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileTopBar,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle {
            display: inline-flex !important;
        }
    }
</style>
@endpush


@push('styles')
@if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
<style>
@media (max-width: 47.9375rem) {
    html body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle {
        flex: 0 0 2.25rem !important;
        width: 2.25rem !important; min-width: 2.25rem !important; max-width: 2.25rem !important;
        height: 2.25rem !important; min-height: 2.25rem !important; max-height: 2.25rem !important;
        padding: 0 !important; border-radius: .375rem !important; font-size: 1.125rem !important;
    }
    html body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle > i {
        display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 18px !important; height: 18px !important; font-size: 18px !important; line-height: 1 !important;
    }
    html body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle > i::before { font-size: 18px !important; }
}
</style>
@endif
@endpush

@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Generate ' . ($displayType ? $displayType . ' ' : '') . 'Quarterly Report', 'subtitle' => 'Create a ' . ($displayType ? $displayType . ' ' : '') . 'compliance monitoring report for ' . ($barangay->name ?? 'Acacia')])
<div class="quarterly-create-content max-w-3xl mx-auto py-4 px-2 sm:px-4">
    <!-- Header -->
    <div class="quarterly-create-navigation mb-6">
        @unless(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
        <h1 class="text-2xl font-bold text-gray-800">Generate {{ $displayType ? $displayType . ' ' : '' }}Quarterly Report</h1>
        <p class="text-gray-500 text-sm mt-1">Create a {{ $displayType ? $displayType . ' ' : '' }}compliance monitoring report for {{ $barangay->name ?? 'Acacia' }}</p>
        @endunless
        <a href="{{ route('desk-officer.quarterly-reports.index') }}" 
           class="quarterly-create-back inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 transition mt-4 ml-3 sm:ml-4">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>

    <!-- Error Messages -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-file-alt"></i> Report Details
            </h2>
        </div>
        
        <form id="generate-quarterly-report-form" action="{{ route('desk-officer.quarterly-reports.store') }}" method="POST" class="p-6">
            @csrf
            @if($type)
            <input type="hidden" name="type" value="{{ $type }}">
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Quarter Selection -->
                <div>
                    <label for="quarter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i> Quarter
                    </label>
                    <select name="quarter" id="quarter" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        <option value="">Select Quarter</option>
                        <option value="1" {{ old('quarter') == 1 ? 'selected' : '' }}>Q1 (January - March)</option>
                        <option value="2" {{ old('quarter') == 2 ? 'selected' : '' }}>Q2 (April - June)</option>
                        <option value="3" {{ old('quarter') == 3 ? 'selected' : '' }}>Q3 (July - September)</option>
                        <option value="4" {{ old('quarter') == 4 ? 'selected' : '' }}>Q4 (October - December)</option>
                    </select>
                    @error('quarter')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Year Selection -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Year
                    </label>
                    <select name="year" id="year" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        <option value="">Select Year</option>
                        @foreach($years as $year)
                        <option value="{{ $year }}" {{ old('year', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    @error('year')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Barangay Info -->
            <div class="bg-purple-50 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Report for Barangay</p>
                        <p class="font-bold text-purple-700">{{ $barangay->name }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Info Note -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">What gets included:</p>
                        <ul class="list-disc list-inside text-blue-600 space-y-1">
                            @if($type)
                                <li>All {{ $displayType }} cases reported during the selected quarter</li>
                            @elseif(Auth::user()->isMultiCaseDepartment())
                                <li>Both BCPC and SWD cases reported during the selected quarter</li>
                                <li>Separate reports will be generated for each case type</li>
                            @else
                                <li>All {{ Auth::user()->defaultCaseDepartment() }} cases reported during the selected quarter</li>
                            @endif
                            <li>Abuse type classification breakdown</li>
                            <li>Case status and disposition summary</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Existing Reports Warning -->
            @if(count($existingReports) > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                    <div class="text-sm text-yellow-700">
                        <p class="font-medium mb-1">Already generated for {{ $displayType ?? 'this period' }}:</p>
                        <p class="text-yellow-600">
                            @foreach($existingReports as $existing)
                                <span class="inline-block bg-yellow-100 px-2 py-0.5 rounded mr-1 mb-1">
                                    {{ 'Q' . explode('-', $existing)[0] . ' ' . explode('-', $existing)[1] }}
                                </span>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Submit Button -->
            <div id="quarterly-create-actions" class="flex justify-end gap-3">
                <a href="{{ route('desk-officer.quarterly-reports.index') }}" 
                   id="cancel-generate-report"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-purple-700 hover:bg-purple-800 text-white rounded-lg font-medium transition flex items-center gap-2 shadow-md">
                    <i class="fas fa-file-pdf"></i> Generate {{ $displayType ? $displayType . ' ' : '' }}Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'BCPC' || auth()->user()?->isMultiCaseDepartment()))
@include('desk-officer.quarterly-reports._bcpc-result', ['formId' => 'generate-quarterly-report-form', 'operation' => 'create'])
@endif
@if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC' && !auth()->user()?->isMultiCaseDepartment())
<dialog id="generated-report-result" aria-labelledby="generated-report-title" style="margin: auto; border: 0; border-radius: 12px; padding: 2rem; width: min(32rem, calc(100vw - 2rem)); text-align: center;">
    <h2 id="generated-report-title" class="text-2xl font-semibold mb-4"></h2>
    <p id="generated-report-message" class="mb-6"></p>
    <button type="button" id="generated-report-ok" class="px-6 py-2 rounded-lg bg-purple-700 text-white">OK</button>
</dialog>
<style>#generated-report-result::backdrop { background: rgba(0, 0, 0, .45); }</style>
<script>
(() => {
    const form = document.getElementById('generate-quarterly-report-form');
    const dialog = document.getElementById('generated-report-result');
    const submitButton = form.querySelector('button[type="submit"]');
    let redirectUrl = null;
    let pending = false;
    dialog.addEventListener('cancel', event => event.preventDefault());
    document.getElementById('generated-report-ok').addEventListener('click', () => {
        dialog.close();
        if (redirectUrl) window.location.assign(redirectUrl);
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (pending) return;
        pending = true;
        submitButton.disabled = true;
        redirectUrl = null;
        if (window.Swal) {
            Swal.fire({
                title: 'Generating Report...',
                text: 'Please wait while the report is prepared.',
                allowOutsideClick: false, showConfirmButton: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });
        }
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Unable to generate the quarterly report.');
            redirectUrl = data.redirect_url;
            if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
            if (window.Swal) {
                Swal.fire({ icon: 'success', title: 'Report Generated', text: data.message, confirmButtonText: 'OK', confirmButtonColor: '#7e22ce' })
                    .then(() => { if (redirectUrl) window.location.assign(redirectUrl); });
                return;
            }
            document.getElementById('generated-report-title').textContent = 'Success!';
            document.getElementById('generated-report-message').textContent = data.message;
        } catch (error) {
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Report Not Generated', text: error.message || 'Unable to generate the quarterly report.', confirmButtonText: 'OK', confirmButtonColor: '#7e22ce' });
                return;
            }
            document.getElementById('generated-report-title').textContent = 'Report Not Generated';
            document.getElementById('generated-report-message').textContent = error.message || 'Unable to generate the quarterly report.';
        } finally {
            if (window.Swal && Swal.isLoading()) Swal.close();
            pending = false;
            submitButton.disabled = false;
            if (!window.Swal) dialog.showModal();
        }
    });
})();
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', () => {
    const existingReports = @json($existingReports);
    const quarter = document.getElementById('quarter');
    const year = document.getElementById('year');
    if (!quarter || !year) return;

    const updateQuarterAvailability = () => {
        const selectedYear = year.value;
        [...quarter.options].forEach(option => {
            if (!option.value) return;
            const exists = selectedYear && existingReports.includes(`${option.value}-${selectedYear}`);
            option.disabled = exists;
            option.textContent = option.textContent.replace(' (Already generated)', '') + (exists ? ' (Already generated)' : '');
            if (exists && option.selected) quarter.value = '';
        });
    };

    year.addEventListener('change', updateQuarterAvailability);
    updateQuarterAvailability();

    // Wire unsaved changes modal on Cancel
    const cancelBtn = document.getElementById('cancel-generate-report');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetUrl = cancelBtn.href;
            const confirmExit = () => {
                if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                window.location.assign(targetUrl);
            };

            if (window.portalUnsavedChanges) {
                window.portalUnsavedChanges.confirmExit(confirmExit);
            } else {
                confirmExit();
            }
        });
    }
});
</script>
@endpush
