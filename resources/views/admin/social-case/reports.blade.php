@extends('layouts.admin')

@section('title', 'Generate Reports')
@section('navbar-title', 'Generate Reports')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div><h2 class="h4 mb-1">Generated Case Reports</h2><p class="text-muted mb-0">Select reports to export their case register, or open individual PDFs.</p></div>
    </div>

    <form id="reportExportForm" method="POST" action="{{ route('admin.social-case.reports.export') }}">
        @csrf
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span class="fw-semibold">Available reports</span>
                <div class="d-flex gap-2">
                    <button type="button" id="openSelected" class="btn btn-sm btn-outline-primary" disabled><i class="fas fa-file-pdf me-1"></i> Open selected PDFs</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="exportSelected" disabled><i class="fas fa-file-csv me-1"></i> Export selected CSV</button>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($cases as $case)
                    @if($loop->first)
                        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th><input class="form-check-input" type="checkbox" id="selectAll" aria-label="Select all reports"></th><th>Case</th><th>Client</th><th>Generated</th><th>Status</th><th></th></tr></thead><tbody>
                    @endif
                    <tr>
                        <td><input class="form-check-input report-checkbox" type="checkbox" name="case_ids[]" value="{{ $case->id }}" data-pdf-url="{{ $case->report ? route('admin.social-case-studies.reports.pdf', $case) : '' }}" aria-label="Select {{ $case->case_number }}"></td>
                        <td><strong>{{ $case->case_number }}</strong></td><td>{{ $case->client?->full_name ?? '—' }}</td><td>{{ optional($case->report?->generated_at)->format('M d, Y h:i A') ?? '—' }}</td><td>{{ $case->status }}</td>
                        <td>@if($case->report)<a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('admin.social-case-studies.reports.pdf', $case) }}">PDF</a>@else<span class="text-muted small">Report file unavailable</span>@endif</td>
                    </tr>
                    @if($loop->last)</tbody></table></div>@endif
                @empty
                    <div class="text-center py-5 px-3"><i class="fas fa-file-circle-plus fa-2x text-muted mb-3"></i><h3 class="h5">No reports generated yet</h3><p class="text-muted mb-0">Generate a Social Case Study Report from an active case to make it available here.</p></div>
                @endforelse
            </div>
        </div>
    </form>
    @if($cases->hasPages())<div class="mt-4">{{ $cases->links() }}</div>@endif
@endsection

@section('page-scripts')
<script>
    const checkboxes = [...document.querySelectorAll('.report-checkbox')];
    const selectAll = document.getElementById('selectAll');
    const exportButton = document.getElementById('exportSelected');
    const openButton = document.getElementById('openSelected');
    const updateSelection = () => {
        const selected = checkboxes.filter(box => box.checked);
        exportButton.disabled = selected.length === 0;
        openButton.disabled = selected.filter(box => box.dataset.pdfUrl).length === 0;
        if (selectAll) selectAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
    };
    selectAll?.addEventListener('change', () => { checkboxes.forEach(box => box.checked = selectAll.checked); updateSelection(); });
    checkboxes.forEach(box => box.addEventListener('change', updateSelection));
    openButton?.addEventListener('click', () => checkboxes.filter(box => box.checked && box.dataset.pdfUrl).forEach(box => window.open(box.dataset.pdfUrl, '_blank')));
</script>
@endsection
