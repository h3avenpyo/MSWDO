<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Between Birthday Cash Gift - Eligibility List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4,h5{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}
        .app{display:flex;min-height:100vh;flex-direction:row;}
        .page-header{margin-bottom:24px;}
        .page-header h1{font-size:28px;font-weight:700;margin:0 0 8px 0;color:var(--text-primary);}
        .page-header p{margin:0;color:var(--text-secondary);}
        
        /* Filter Bar */
        .archive-filter-bar{display:block;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px;}
        .archive-filter-bar #summaryGrid{margin-bottom:0;}
        #summaryGrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;}
        .filter-field{display:flex;flex-direction:column;gap:4px;}
        .filter-label{font-size:13px;font-weight:500;color:#6B7280;margin-bottom:2px;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#111827;background:#fff;cursor:pointer;transition:all .15s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:#1A237E;box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        .input-group{display:flex;align-items:center;}
        .input-group input{flex:1;min-width:0;height:44px;border:1px solid #E5E7EB;border-right:none;border-radius:8px 0 0 8px;padding:0 1rem;font-size:14px;color:#111827;background:#fff;transition:all .15s ease;font-family:inherit;}
        .input-group input:focus{outline:none;border-color:#1A237E;box-shadow:0 0 0 3px rgba(30,58,138,.15);}
        .search-btn{background:#1A237E;color:#ffffff;border:none;padding:0 1.1rem;border-radius:0 8px 8px 0;cursor:pointer;height:44px;width:48px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:background .15s;}
        .search-btn:hover{background:#121858;}
        .search-btn svg{width:18px;height:18px;}
        
        /* Table */
        .archive-table-wrap{border:2px solid #CBD5E1;border-radius:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;max-height:500px;overflow-y:auto;}
        .archive-table{width:100%;border-collapse:collapse;}
        .archive-table thead{background:#E2E8F0;position:sticky;top:0;z-index:10;}
        .archive-table th{padding:14px 16px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#1E293B;text-align:left;border-bottom:2px solid #94A3B8;white-space:nowrap;}
        .archive-table td{padding:14px 16px;font-size:13px;color:#111827;border-bottom:1px solid #CBD5E1;vertical-align:middle;white-space:normal;word-break:break-word;}
        .archive-table tbody tr:last-child td{border-bottom:none;}
        .archive-table tbody tr:hover{background:#F9FAFB;}
        
        /* Mobile (<768px) */
        @media (max-width:767px){
            .archive-panel-wrap{padding:.75rem;}
            .archive-table-wrap{border:none;border-radius:0;overflow:visible;}
            .archive-table thead{display:none;}
            .archive-table tbody tr{display:block;background:#fff;border:1px solid #D1D5DB;border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
            .archive-table tbody tr:last-child{margin-bottom:0;}
            .archive-table tbody td{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border:none;font-size:.82rem;gap:8px;text-align:right;}
            .archive-table tbody td:not(:last-child){border-bottom:1px solid #E5E7EB;}
            .archive-table tbody td::before{content:attr(data-label);font-weight:600;color:#6B7280;font-size:.72rem;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:80px;text-align:left;}
            .archive-table tbody td[data-label="Action"]{justify-content:flex-end;padding-top:8px;border-bottom:none;}
            .archive-table tbody td[data-label="Action"]::before{display:none;}
        }
        
        /* Status Badges */
        .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:500;}
        .badge-success{background:#D1FAE5;color:#059669;}
        .badge-info{background:#DBEAFE;color:#2563EB;}
        .badge-danger{background:#FEE2E2;color:#DC2626;}
        
        /* Buttons */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;border:1px solid #E5E7EB;border-radius:8px;font-family:var(--font-family);font-size:13px;font-weight:500;cursor:pointer;transition:all .15s ease;padding:8px 16px;background:#fff;color:#374151;height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:#1A237E;color:#1A237E;transform:none;}
        .btn-primary{background:#1A237E;color:#fff;border-color:#1A237E;}
        .btn-primary:hover{background:#121858;border-color:#121858;color:#fff;}
        .btn-info{background:#3B82F6;color:#fff;border-color:#3B82F6;}
        .btn-info:hover{background:#2563EB;border-color:#2563EB;color:#fff;}
        .btn-clear{background:#FEF2F2;color:#DC2626;border-color:#FECACA;}
        .btn-clear:hover{background:#FEE2E2;border-color:#DC2626;}
        .btn:disabled{opacity:0.5;cursor:not-allowed;pointer-events:none;}
        .btn-clear:hover{background:#FEE2E2;border-color:#DC2626;}
        
        /* Pagination */
        .pagination-wrap{display:flex;justify-content:center;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid #E5E7EB;}
        .archive-pagination-info{font-size:0.875rem;color:#6B7280;text-align:center;padding-top:0.75rem;}
        .sc-pagination{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:14px;flex-shrink:0;padding:4px 0;flex-wrap:wrap;}
        .sc-pagination-info{font-size:0.813rem;color:#6B7280;font-weight:500;}
        .sc-pagination-controls{display:flex;gap:4px;flex-wrap:wrap;}
        .sc-page-btn{height:36px;min-width:36px;padding:0 10px;border:1px solid #E5E7EB;border-radius:6px;background:#fff;color:#374151;font-size:0.813rem;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:4px;transition:all .15s;text-decoration:none;}
        .sc-page-btn:hover:not(:disabled){background:#F3F4F6;border-color:#D1D5DB;}
        .sc-page-btn.active{background:#1A237E;color:#fff;border-color:#1A237E;font-weight:700;}
        .sc-page-btn:disabled{opacity:0.4;cursor:not-allowed;}
        
        @media (max-width:767px){
            #summaryGrid{grid-template-columns:1fr !important;gap:12px !important;}
            .filter-field{width:100%;}
            .filter-label{display:block !important;}
            .input-group{width:100%;}
            .input-group input{width:100%;}
            .filter-select{width:100%;}
            .sc-pagination{position:fixed !important;bottom:0 !important;left:0 !important;right:0 !important;background:#fff !important;padding:15px 0 !important;z-index:100 !important;border-top:1px solid #E5E7EB !important;flex-direction:column;align-items:center;gap:8px;}
            .sc-pagination-controls{justify-content:flex-end;padding-right:20px;}
        }
        
        @media (min-width:1200px){
            .filter-select,.input-group input,.search-btn{height:48px !important;min-height:48px !important;}
            .filter-select{font-size:14px !important;}
            .input-group input{font-size:15px !important;}
            .search-btn{width:52px !important;}
        }
        
        @media (min-width:1200px) and (max-width:1399px){
            .filter-select,.input-group input,.search-btn{height:46px !important;min-height:46px !important;}
            .filter-select{font-size:13px !important;}
            .input-group input{font-size:14px !important;}
            .search-btn{width:50px !important;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'in-between', 'mobileSubtitle' => 'In-Between Benefits'])
    
    <div class="main">
        <div class="main-scroll">
            <!-- Filters -->
            <form method="GET" action="{{ url()->current() }}" id="filterForm">
                <div class="archive-filter-bar">
                    <div id="summaryGrid">
                        <div class="filter-field">
                            <label class="filter-label">Search</label>
                            <div class="input-group">
                                <input type="text" id="searchInput" name="search" placeholder="Search by name or ID..." value="{{ request('search') }}">
                                <button type="submit" class="search-btn" aria-label="Search"><i data-lucide="search"></i></button>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label">Barangay</label>
                            <select id="barangayFilter" name="barangay" class="filter-select">
                                <option value="">All Barangays</option>
                                @php
                                    $barangays = \App\Models\Senior\SeniorCitizenRecord::where('status', 'active')
                                        ->whereNotNull('barangay')
                                        ->pluck('barangay')
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label">Eligibility Interval</label>
                            <select id="intervalFilter" name="interval" class="filter-select">
                                <option value="">All Intervals</option>
                                <option value="81-84" {{ request('interval') == '81-84' ? 'selected' : '' }}>81-84</option>
                                <option value="86-89" {{ request('interval') == '86-89' ? 'selected' : '' }}>86-89</option>
                                <option value="91-94" {{ request('interval') == '91-94' ? 'selected' : '' }}>91-94</option>
                                <option value="96-99" {{ request('interval') == '96-99' ? 'selected' : '' }}>96-99</option>
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label">Status</label>
                            <select id="statusFilter" name="status" class="filter-select">
                                <option value="">All Status</option>
                                <option value="eligible" {{ request('status') == 'eligible' ? 'selected' : '' }}>Eligible</option>
                                <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Claimed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label">&nbsp;</label>
                            <button type="button" onclick="clearFilters()" class="btn btn-clear" style="width:100%;"><i data-lucide="x" style="width:16px;height:16px;"></i> Clear All</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="archive-table-wrap">
                <table class="archive-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Barangay</th>
                            <th>Age</th>
                            <th>Interval</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seniors as $senior)
                            <tr>
                                <td data-label="Name">
                                    <div style="font-weight:500;">{{ $senior->full_name }}</div>
                                </td>
                                <td data-label="Barangay">{{ $senior->barangay ?? 'N/A' }}</td>
                                <td data-label="Age">{{ $senior->age }}</td>
                                <td data-label="Interval">{{ $senior->eligibility_interval ?? 'N/A' }}</td>
                                <td data-label="Status">
                                    @if($senior->is_eligible)
                                        <span class="badge badge-success">Eligible</span>
                                    @elseif($senior->has_claimed)
                                        <span class="badge badge-info">Claimed</span>
                                    @else
                                        <span class="badge badge-danger">Not Eligible</span>
                                    @endif
                                </td>
                                <td data-label="Amount">₱{{ number_format($senior->benefit_amount ?? 0, 2) }}</td>
                                <td data-label="Action" style="white-space:nowrap;min-width:120px;">
                                    @if($senior->is_eligible ?? false)
                                        <button onclick="processClaim({{ $senior->id }})" class="btn btn-primary">Process Claim</button>
                                    @elseif($senior->has_claimed ?? false)
                                        <button onclick="viewClaim({{ $senior->id }})" class="btn btn-info">View Claim</button>
                                    @else
                                        <button onclick="viewClaim({{ $senior->id }})" class="btn" style="color:#6B7280;">View Details</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:48px;text-align:center;color:#6B7280;">
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                                        <i data-lucide="users" style="width:48px;height:48px;color:#D1D5DB;"></i>
                                        <div style="font-size:16px;font-weight:600;">No eligible seniors found</div>
                                        <div style="font-size:14px;">Try adjusting your filters</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="border-top: 2px solid #94A3B8; margin: 20px 0;"></div>

            <!-- Pagination -->
            @if($seniors->hasPages())
                <div class="sc-pagination">
                    <div class="sc-pagination-info">
                        @if($seniors->total() === 0)
                            Showing 0 of 0 Seniors
                        @else
                            Showing {{ $seniors->firstItem() }}–{{ $seniors->lastItem() }} of {{ $seniors->total() }} Seniors
                        @endif
                    </div>
                    <div class="sc-pagination-controls">
                        @if($seniors->count() > 0 && $seniors->hasPages())
                            @if($seniors->onFirstPage())
                                <span class="sc-page-btn" disabled>Previous</span>
                            @else
                                <a href="{{ $seniors->appends(request()->except('page'))->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                            @endif

                            <span class="sc-page-btn active">{{ $seniors->currentPage() }}</span>

                            @if($seniors->hasMorePages())
                                <a href="{{ $seniors->appends(request()->except('page'))->nextPageUrl() }}" class="sc-page-btn">Next</a>
                            @else
                                <span class="sc-page-btn" disabled>Next</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function clearFilters() {
        document.getElementById('filterForm').reset();
        window.location.href = '{{ url()->current() }}';
    }

    // Auto-submit form when filters change
    document.addEventListener('DOMContentLoaded', function() {
        const filters = ['barangayFilter', 'intervalFilter', 'statusFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            if (filter) {
                filter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });
    });

    function processClaim(seniorId) {
        Swal.fire({
            title: 'Process In-Between Birthday Cash Gift',
            html: `
                <div style="text-align:left;">
                    <p><strong>Senior ID:</strong> <span id="seniorId"></span></p>
                    <p><strong>Name:</strong> <span id="seniorName"></span></p>
                    <p><strong>Age:</strong> <span id="seniorAge"></span></p>
                    <p><strong>Eligibility Interval:</strong> <span id="eligibilityInterval"></span></p>
                    <p><strong>Amount:</strong> <span id="benefitAmount"></span></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Process Claim',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/admin/senior/in-between/check-eligibility/${seniorId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.is_eligible) {
                            throw new Error(data.reason);
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error);
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                document.getElementById('seniorId').textContent = data.senior_id;
                document.getElementById('seniorName').textContent = data.full_name;
                document.getElementById('seniorAge').textContent = data.current_age;
                document.getElementById('eligibilityInterval').textContent = data.eligibility_interval;
                document.getElementById('benefitAmount').textContent = '₱' + data.benefit_amount.toFixed(2);
                
                Swal.fire({
                    title: 'Confirm Claim',
                    text: 'Are you sure you want to process this claim?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Process',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch(`/admin/senior/in-between/process-claim/${seniorId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ confirmation: true })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.message);
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(error);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Claim processed successfully',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            }
        });
    }

    function viewClaim(seniorId) {
        window.location.href = `/admin/senior/in-between/senior-card/${seniorId}`;
    }

    lucide.createIcons();
</script>
</body>
</html>
