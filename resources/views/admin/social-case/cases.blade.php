@extends('admin.social-case.layout')
@section('title', 'All Social Case Studies')

@section('content')
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases" class="active"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="page-head">
        <div><h1>All Social Case Studies</h1><p>Manage and track all social case study records.</p></div>
        <button class="btn primary" onclick="window.location.href='/admin/social-case/new'"><i data-lucide="plus" style="width:16px;height:16px"></i> New Case</button>
    </div>

    <!-- Summary Stats Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="num" id="totalCases">0</div>
            <div class="label">Total Cases</div>
        </div>
        <div class="summary-card">
            <div class="num" id="draftCases">0</div>
            <div class="label">Draft</div>
        </div>
        <div class="summary-card">
            <div class="num" id="reviewCases">0</div>
            <div class="label">Review</div>
        </div>
        <div class="summary-card">
            <div class="num" id="approvedCases">0</div>
            <div class="label">Approved</div>
        </div>
        <div class="summary-card">
            <div class="num" id="releasedCases">0</div>
            <div class="label">Released</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by Client Name, Control No, Assistance Type, or Barangay">
            <i data-lucide="search" class="search-icon" style="width:20px;height:20px"></i>
        </div>
        <select id="statusFilter">
            <option value="All">All Status</option>
            <option value="Draft">Draft</option>
            <option value="Review">Review</option>
            <option value="Approved">Approved</option>
            <option value="Printed">Printed</option>
            <option value="Released">Released</option>
        </select>
        <select id="assistanceFilter">
            <option value="All">All Assistance Types</option>
            <option value="PCSO">PCSO</option>
            <option value="DOH">DOH</option>
            <option value="AICS">AICS</option>
            <option value="Burial">Burial</option>
            <option value="Medical">Medical</option>
        </select>
        <select id="barangayFilter">
            <option value="All">All Barangays</option>
            <option value="Biluso">Biluso</option>
            <option value="Poblacion IV">Poblacion IV</option>
            <option value="Tubuan">Tubuan</option>
            <option value="Batas">Batas</option>
            <option value="Bigaa">Bigaa</option>
        </select>
        <div class="btn-group">
            <button class="action-btn primary" onclick="applyFilters()">
                <i data-lucide="filter" style="width:14px;height:14px"></i> Filter
            </button>
            <button class="action-btn" onclick="resetFilters()">
                <i data-lucide="x" style="width:14px;height:14px"></i> Reset
            </button>
        </div>
        <div class="btn-group" style="margin-left:auto">
            <button class="action-btn" onclick="bulkDelete()">
                <i data-lucide="trash" style="width:14px;height:14px"></i> Delete
            </button>
            <button class="action-btn" onclick="exportExcel()">
                <i data-lucide="file-spreadsheet" style="width:14px;height:14px"></i> Export Excel
            </button>
            <button class="action-btn" onclick="exportPDF()">
                <i data-lucide="file" style="width:14px;height:14px"></i> Export PDF
            </button>
            <button class="action-btn" onclick="printReport()">
                <i data-lucide="printer" style="width:14px;height:14px"></i> Print
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="panel" style="padding:0;overflow:hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="sortable" onclick="sortBy('controlNo')">Control No. <i data-lucide="chevron-up-down" style="width:14px;height:14px"></i></th>
                    <th class="sortable" onclick="sortBy('clientName')">Client <i data-lucide="chevron-up-down" style="width:14px;height:14px"></i></th>
                    <th class="sortable" onclick="sortBy('assistance')">Assistance Type <i data-lucide="chevron-up-down" style="width:14px;height:14px"></i></th>
                    <th class="sortable" onclick="sortBy('barangay')">Barangay <i data-lucide="chevron-up-down" style="width:14px;height:14px"></i></th>
                    <th class="sortable" onclick="sortBy('status')">Status <i data-lucide="chevron-up-down" style="width:14px;height:14px"></i></th>
                    <th class="sortable" onclick="sortBy('created')">Created <i data-lucide="chevron-up-down" style="width:14px;height:14px"></i></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="casesTableBody"></tbody>
        </table>
        
        <!-- Empty State -->
        <div id="emptyState" class="empty-state" style="display:none;">
            <i data-lucide="folder-open" class="icon" style="width:64px;height:64px"></i>
            <h3>No Social Case Studies Found</h3>
            <p>Create your first Social Case Study to begin managing case records.</p>
            <button class="btn primary" onclick="window.location.href='/admin/social-case/new'">
                <i data-lucide="plus" style="width:16px;height:16px"></i> Create New Case
            </button>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-info" id="paginationInfo">Showing 0 of 0 Social Case Studies</div>
        <div class="pagination-controls" id="paginationControls">
            <button class="pagination-btn" id="prevBtn" disabled>
                <i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous
            </button>
            <button class="pagination-btn active" id="page1">1</button>
            <button class="pagination-btn" id="page2">2</button>
            <button class="pagination-btn" id="page3">3</button>
            <button class="pagination-btn" id="nextBtn">
                Next <i data-lucide="chevron-right" style="width:14px;height:14px"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadCaseList();
    });
</script>
@endpush
