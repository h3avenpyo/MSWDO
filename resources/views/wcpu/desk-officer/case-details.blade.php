@extends('layouts.desk-officer')

@section('title', 'Case #' . ($case->case_number ?? 'N/A'))

@push('styles')
<style>
@if(auth()->user()->defaultCaseDepartment() === 'BCPC')
/* Match the BCPC Case Profile details visual system without altering case data or actions. */
.case-page-wrapper { max-width: 64rem; margin: 0 auto; padding: 2rem 1rem; }
.case-page-wrapper > .d-flex { position: sticky; top: 0; z-index: 30; margin: -2rem -1rem 1.5rem; padding: 1rem; background: #fff; border-bottom: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,.04); }
.case-page-wrapper h3 { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
.case-page-wrapper .text-muted { color: #6b7280 !important; font-size: .875rem; }
.case-actions { flex-wrap: wrap; }
.case-actions .btn, #editCaseBtn { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem 1rem; border-radius: .5rem; font-size: .875rem; font-weight: 500; box-shadow: none; }
.case-actions .btn-primary, #editCaseBtn { background: #16a34a; border-color: #16a34a; color: #fff; }
.case-actions .btn-outline-secondary { background: #fff; border-color: #d1d5db; color: #374151; }
.btn-modern { background: #6d28d9; border-radius: .5rem; padding: .5rem 1rem; font-size: .875rem; }
.case-report { max-width: none; padding: 0; background: transparent; box-shadow: none; }
.case-details .case-header { display: none; }
.case-details .row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; margin: 0; }
.case-details .row > [class*="col-"] { width: auto; padding: 0; }
.case-details .card { margin: 0 0 1.5rem; border: 1px solid #f3f4f6; border-radius: .75rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,.05); background: #fff; }
.case-details .card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; background: #fff !important; color: #1f2937; }
.case-details .card-header h5 { font-size: 1.125rem; font-weight: 600; }
.case-details .card-body { padding: 1.5rem; }
.case-details dl { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 0; }
.case-details dt, .case-details dd { margin: 0; padding: .5rem 0; border-bottom: 1px solid #f9fafb; font-size: .875rem; overflow-wrap: anywhere; }
.case-details dt { color: #6b7280; font-weight: 400; }
.case-details dd { color: #1f2937; font-weight: 500; text-align: right; }
.case-details > .card { margin-top: 1.5rem; }
@media (max-width: 640px) {
    .case-page-wrapper { padding: 1rem; }
    .case-page-wrapper > .d-flex { margin: -1rem -1rem 1rem; padding: 1rem; align-items: flex-start !important; gap: 1rem; }
    .case-actions { width: 100%; }
    .case-details .row { grid-template-columns: 1fr; gap: 0; }
    .case-details dl { grid-template-columns: 1fr; }
    .case-details dd { padding-top: 0; text-align: left; }
}
@endif
/* Screen layout */
.case-page-wrapper {
    padding: 1.5rem;
}
.case-actions { display:flex; gap:.5rem; align-items:center; }
.case-report {
    background:#fff;
    margin:0 auto;
    max-width:980px;
    padding:1rem 1.25rem;
    border-radius:6px;
    box-shadow:0 1px 4px rgba(0,0,0,.04);
}

/* Phone-first Case Profile layout for 320px–430px devices and compact
   phone landscape viewports. The desktop/tablet composition is unchanged. */
@media (max-width: 26.875rem), (orientation: landscape) and (max-height: 30rem) and (max-width: 58rem) {
    .case-page-wrapper {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        padding-top: max(1rem, env(safe-area-inset-top)) !important;
        padding-right: max(1rem, env(safe-area-inset-right)) !important;
        padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
        padding-left: max(1rem, env(safe-area-inset-left)) !important;
    }
    .case-page-wrapper > .d-flex {
        flex-direction: column;
        align-items: stretch !important;
        gap: 1rem;
        min-width: 0;
    }
    .case-page-wrapper h3 { font-size: clamp(1.25rem, 6vw, 1.5rem); }
    .case-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        width: 100%;
        gap: .5rem;
    }
    .case-actions .btn,
    #editCaseBtn,
    .refer-modal .modal-actions button {
        display: inline-flex;
        align-items: center;
        width: 100%;
        min-width: 0;
        min-height: 3rem;
        padding: .5rem .75rem !important;
        justify-content: center;
        text-align: center;
    }
    .case-actions .btn[style],
    #referToggle[style] { margin-left: 0 !important; }
    .case-report,
    .case-details,
    .case-details .row,
    .case-details .card { min-width: 0; max-width: 100%; }
    .case-details .row {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 0;
        margin: 0;
    }
    .case-details .row > [class*="col-"] { width: auto; padding: 0; }
    .case-details .card-body { padding: 1rem; }
    .case-details dl {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
    }
    .case-details dt,
    .case-details dd {
        min-width: 0;
        overflow-wrap: anywhere;
        white-space: normal;
    }
    .case-details dd { text-align: left; padding-top: 0; }
    .refer-modal-overlay {
        padding-top: max(1rem, env(safe-area-inset-top));
        padding-right: max(1rem, env(safe-area-inset-right));
        padding-bottom: max(1rem, env(safe-area-inset-bottom));
        padding-left: max(1rem, env(safe-area-inset-left));
    }
    .refer-modal { max-height: calc(100dvh - 2rem); overflow-y: auto; padding: 1rem; }
    .refer-modal .modal-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    #referCloseX { width: 3rem; height: 3rem; display: inline-flex; align-items: center; justify-content: center; }
}

/* Print tweaks kept minimal; full print handled by case-print view */
@media print {
    .no-print { display:none !important; }
    body { background:#fff; color:#000; }
    .case-report { box-shadow:none; padding:6mm; max-width:100%; }
    .case-page-wrapper { padding:0; }
}

.refer-modal-overlay{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:1050;padding:1rem;}
.refer-modal{background:#fff;border-radius:10px;max-width:480px;width:100%;padding:1.25rem 1.5rem;box-shadow:0 10px 28px -6px rgba(0,0,0,.35);position:relative;}
.refer-modal h5{margin:0 0 .6rem;font-weight:600;font-size:1rem;}
.refer-recipient-list{background:#f8f9fa;border:1px solid #e5e7eb;border-radius:6px;padding:.7rem .85rem;max-height:170px;overflow:auto;}
.refer-modal .modal-actions{display:flex;gap:.6rem;margin-top:1rem;flex-wrap:wrap;}
.btn-modern{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:6px;font-weight:500;border:none;cursor:pointer;text-decoration:none;color:#fff;background:linear-gradient(135deg,#7c3299,#6a1b9a);}
.btn-cancel-gray{background:#e2e6ea;border:1px solid #d0d5da;color:#333;}
@if(auth()->user()->defaultCaseDepartment() === 'BCPC')
.case-page-wrapper { max-width: 64rem; margin: 0 auto; padding: 2rem 1rem; }
.case-report { max-width: none; padding: 0; background: transparent; box-shadow: none; }
.btn-modern { background: #6d28d9; border-radius: .5rem; padding: .5rem 1rem; font-size: .875rem; }
@endif
</style>
@endpush

@section('content')
@php
    // Resolve refer recipients (DB list or env CSV, same as admin)
    $useDb = (bool) config('wcpu.recipients.use_db', env('USE_DB_RECIPIENTS', false));
    $referRecipients = [];
    if($useDb){
        $referRecipients = \App\Models\Wcpu\ReportRecipient::active()
            ->orderBy('id')
            ->pluck('email')
            ->filter(fn($e)=>filter_var($e,FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();
    } else {
        $raw = config('wcpu.recipients.fallback_list', env('CASE_REPORT_RECIPIENTS',''));
        $referRecipients = $raw
            ? collect(explode(',',$raw))->map(fn($e)=>trim($e))
                ->filter(fn($e)=>filter_var($e,FILTER_VALIDATE_EMAIL))->values()->all()
            : [];
        if(empty($referRecipients)){
            $single = config('wcpu.recipients.fallback_single', env('CASE_REPORT_FALLBACK'));
            if(filter_var($single,FILTER_VALIDATE_EMAIL)) $referRecipients = [$single];
        }
    }

    $isDeskOfficer = auth()->user()?->position === 'Desk Officer';
    $caseTypeLower = strtolower($case->type ?? ($case->case_type ?? 'bcpc'));
    if ($caseTypeLower === 'swd') $caseTypeLower = 'vawc';

    if ($isDeskOfficer) {
        $casesListUrl = route('desk-officer.cases.index', auth()->user()->isMultiCaseDepartment() ? ['type' => $caseTypeLower] : []);
        $addNewCaseUrl = auth()->user()->isMultiCaseDepartment()
            ? route('desk-officer.cases.create', ['type' => $caseTypeLower, 'fresh' => 1])
            : route('desk-officer.cases.create');
    } else {
        $casesListUrl = route('admin-barangay-cases');
        $addNewCaseUrl = route('admin-add-new-case');
    }
@endphp

<div class="case-page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="{{ request()->boolean('archived') || $case->trashed() ? 'flex-1 min-w-0' : '' }}">
            <h3 class="mb-0">Case #{{ $case->case_number ?? 'N/A' }}</h3>
            <small class="text-muted">Viewing case details</small>
        </div>

        <div class="case-actions no-print">
            <a href="{{ $casesListUrl }}" class="btn btn-outline-secondary btn-sm" aria-label="Back to lists">
                <i class="fas fa-arrow-left me-1"></i> Back to lists
            </a>
            <a href="{{ $addNewCaseUrl }}" class="btn btn-primary btn-sm" aria-label="Add new case" style="margin-left:.25rem; background: #6d28d9; border-color: #6d28d9; color: #fff;">
                <i class="fas fa-plus me-1"></i> Add New Case
            </a>

            {{-- Open generated PDF in new tab using encrypted id --}}
            @if(request()->boolean('archived') || $case->deleted_at)
            <button type="button" disabled class="btn btn-secondary btn-sm" aria-label="Print unavailable for archived case">
                <i class="fas fa-print me-1"></i> Print
            </button>
            @else
            <a href="{{ route('desk-officer.cases.print', ['hash' => encrypt($case->id)]) }}"
               target="_blank"
               rel="noopener"
               class="btn btn-primary btn-sm"
               aria-label="Print case">
                <i class="fas fa-print me-1"></i> Print
            </a>
            @endif

            {{-- Refer Case (same as in admin) --}}
            <button type="button" id="referToggle" class="btn-modern btn-sm" style="margin-left:.25rem;">
                <i class="fas fa-share-square"></i><span>Refer Case</span>
            </button>
        </div>
    </div>

    <div class="case-report">
        {{-- Render the existing partial that contains the case fields --}}
        @includeIf('desk-officer._case-details', ['case' => $case])
    </div>

    <div class="no-print mt-3">
        <a href="{{ route('desk-officer.cases.edit', ['hash' => encrypt($case->id)]) }}" class="btn btn-primary" id="editCaseBtn">
            <i class="fas fa-edit me-1"></i> Edit Case
        </a>
    </div>
</div>

{{-- Refer Case Modal --}}
<div class="refer-modal-overlay no-print" id="referModal" role="dialog" aria-modal="true" aria-labelledby="referModalTitle">
    <div class="refer-modal">
        <button type="button" id="referCloseX" aria-label="Close"
            style="position:absolute;top:6px;right:10px;background:transparent;border:none;font-size:20px;line-height:1;color:#555;cursor:pointer;">&times;</button>
        <h5 id="referModalTitle"><i class="fas fa-share-square me-1"></i>Refer Case</h5>
        <p style="font-size:.85rem;margin:0 0 .6rem;">Select recipient(s) to send this case report:</p>

        <div class="refer-recipient-list">
            @if(count($referRecipients))
                <div style="margin-bottom:.5rem;display:flex;align-items:center;gap:.5rem;">
                    <input type="checkbox" id="selectAllRecipients" style="transform:scale(1.1);cursor:pointer;">
                    <label for="selectAllRecipients" style="font-size:.75rem;cursor:pointer;">Select All</label>
                </div>
                @foreach($referRecipients as $email)
                    <div style="display:flex;align-items:center;gap:.45rem;margin-bottom:.35rem;">
                        <input type="checkbox"
                               name="recipients[]"
                               value="{{ $email }}"
                               class="recipient-checkbox"
                               form="referSubmitForm"
                               style="cursor:pointer;">
                        <span style="font-size:.85rem;">{{ $email }}</span>
                    </div>
                @endforeach
            @else
                <span style="font-size:.8rem;color:#dc3545;">No recipients configured.</span>
            @endif
        </div>

        <small style="display:block;margin-top:.5rem;font-size:.7rem;color:#666;">
            A PDF copy of the case report will be attached.
        </small>

        <form id="referSubmitForm" method="POST" action="{{ route('admin.case.email', $case->id) }}">
            @csrf
            <div class="modal-actions">
                <button type="button" class="btn-cancel-gray" id="referCancelBtn">
                    <i class="fas fa-times"></i><span> Cancel</span>
                </button>
                <button type="submit" class="btn-modern">
                    <i class="fas fa-paper-plane"></i><span> Send Now</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
@push('scripts')
<script>
(function(){
    // Refer modal
    const referToggle  = document.getElementById('referToggle');
    const referModal   = document.getElementById('referModal');
    const referCancel  = document.getElementById('referCancelBtn');
    const referCloseX  = document.getElementById('referCloseX');
    const selectAll    = document.getElementById('selectAllRecipients');
    const boxes        = () => Array.from(document.querySelectorAll('.recipient-checkbox'));
    function openRefer(){ if(referModal){ referModal.style.display='flex'; document.body.style.overflow='hidden'; } }
    function closeRefer(){ if(referModal){ referModal.style.display='none'; document.body.style.overflow=''; } }
    referToggle?.addEventListener('click', openRefer);
    referCancel?.addEventListener('click', closeRefer);
    referCloseX?.addEventListener('click', closeRefer);
    referModal?.addEventListener('click', e=>{ if(e.target===referModal) closeRefer(); });
    document.addEventListener('keydown', e=>{ if(e.key==='Escape' && referModal?.style.display==='flex') closeRefer(); });
    selectAll?.addEventListener('change', ()=> boxes().forEach(cb=> cb.checked = selectAll.checked));
    // ensure at least one recipient
    document.getElementById('referSubmitForm')?.addEventListener('submit', e=>{
        if(!boxes().some(cb=>cb.checked)){
            e.preventDefault();
            alert('Select at least one recipient.');
        }
    });
})();
</script>
@endpush
@endsection
