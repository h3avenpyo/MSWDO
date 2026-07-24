@extends('layouts.admin')

@section('title', 'Released Assistance')
@section('navbar-title', 'Released Assistance')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="h4 mb-1">Released Cases</h2>
            <p class="text-muted mb-0">Cases with recorded assistance release or closed cases with assistance.</p>
        </div>
        <a href="{{ route('admin.social-case-studies.index') }}" class="btn btn-outline-primary"><i class="fas fa-folder-open me-1"></i> Active cases</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @forelse($cases as $case)
                @if($loop->first)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Case</th><th>Client</th><th>Assistance</th><th>Released</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                @endif
                <tr>
                    <td><strong>{{ $case->case_number }}</strong></td>
                    <td>{{ $case->client?->full_name ?? '—' }}</td>
                    <td>{{ $case->assistance_amount !== null ? '₱'.number_format($case->assistance_amount, 2) : 'Recorded assistance' }}</td>
                    <td>{{ optional($case->assistance_date)->format('M d, Y') ?? '—' }}</td>
                    <td><span class="badge text-bg-{{ $case->status === 'Closed' ? 'secondary' : 'success' }}">{{ $case->status }}</span></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.social-case-studies.edit', $case) }}">View case</a></td>
                </tr>
                @if($loop->last)
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                <div class="text-center py-5 px-3">
                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                    <h3 class="h5">No released cases yet</h3>
                    <p class="text-muted mb-0">Released assistance and closed cases with assistance will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($cases->hasPages())<div class="mt-4">{{ $cases->links() }}</div>@endif
@endsection
